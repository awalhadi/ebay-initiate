<?php

namespace App\Http\Controllers\Client\Product;

use App\Repositories\ProductCondition\ProductConditionRepository;
use PDF;
use Excel;
use Carbon\Carbon;
use App\Models\EbayPolicy;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Models\ProductCondition;
use App\Models\ResourceSyncTrack;
use App\Jobs\CreateOfferOnEbayJob;
use App\DataTables\ProductDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\EbayCategoryCondition;
use Illuminate\Support\Facades\Session;
use App\DataTables\SystemProductDataTable;
use App\Http\Requests\SystemProductRequest;
use App\DataTables\ProductStockLevelDataTable;
use App\Http\Requests\ProductBasicInfoRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\EcomPlatforms\EcomProductService;
use App\Services\EcomPlatforms\EcomPlatformService;
use App\Services\EcomPlatforms\SystemProductService;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\EbayCategory\EbayCategoryRepositoryInterface;
use App\Repositories\EtsyCategory\EtsyCategoryRepositoryInterface;
use App\Repositories\EtsyTaxonomy\EtsyTaxonomyRepositoryInterface;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class ProductsController extends Controller
{

    protected $productRepository;
    protected $ecomPlatformService;
    protected $systemEcomPlatformRepository;
    protected $productCategoryRepository;
    protected $ecomProductService;
    protected $warehouseRepository;
    protected $ebayCategoryRepository;
    protected $etsyTaxonomyRepository;
    protected $systemProductService;
    protected $userRepository;
    protected $productConditionRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EcomPlatformService $ecomPlatformService,
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository,
        ProductCategoryRepositoryInterface $productCategoryRepository,
        EcomProductService $ecomProductService,
        SystemProductService $systemProductService,
        WarehouseRepositoryInterface $warehouseRepository,
        EbayCategoryRepositoryInterface $ebayCategoryRepository,
        EtsyTaxonomyRepositoryInterface $etsyTaxonomyRepository,
        UserRepositoryInterface $userRepository,
        ProductConditionRepository $productConditionRepository
    ) {
        $this->productRepository = $productRepository;
        $this->ecomPlatformService = $ecomPlatformService;
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->ecomProductService = $ecomProductService;
        $this->systemProductService = $systemProductService;
        $this->ebayCategoryRepository = $ebayCategoryRepository;
        $this->etsyTaxonomyRepository = $etsyTaxonomyRepository;
        $this->userRepository = $userRepository;
        $this->productConditionRepository = $productConditionRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Product') . '|' . saasPermission('Ecom_Product') . '|' . saasPermission('System_Product') . '|' . saasPermission('Product_Stock'));
    }

    public function index(Request $request, ProductDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Ecom_Product'));


        $selected['reference_id'] = $request->reference_id ?? '';
        $selected['variant_id'] = $request->variant_id ?? '';
        $selected['name'] = $request->name ?? '';
        $selected['sku'] = $request->sku ?? '';
        $selected['quantity'] = $request->quantity ?? '';
        $selected['channel'] = $request->channel ?? '';
        $selected['status'] = $request->status ?? '';

        $platforms = $this->ecomPlatformService->userConnectedEcoms()->pluck('ecom_platform_id')->toArray();
        $connected_platforms = $this->systemEcomPlatformRepository->platformsWhereUserConnected($platforms);
        $channels = $this->systemEcomPlatformRepository->get();

        set_page_meta('Products');
        return $dataTable->render('client.products.index', compact('connected_platforms', 'channels', 'selected'));
    }

    public function systemProducts(Request $request, SystemProductDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('System_Product'));


        $selected['reference_id'] = $request->reference_id ?? '';
        $selected['variant_id'] = $request->variant_id ?? '';
        $selected['name'] = $request->name ?? '';
        $selected['sku'] = $request->sku ?? '';
        $selected['quantity'] = $request->quantity ?? '';
        $selected['channel'] = $request->channel ?? '';
        $selected['status'] = $request->status ?? '';

        $platforms = $this->ecomPlatformService->userConnectedEcoms()->pluck('ecom_platform_id')->toArray();
        $channels = $this->systemEcomPlatformRepository->get();

        set_page_meta('System Products');
        return $dataTable->render('client.system_products.index', compact('channels', 'selected'));
    }

    public function stockLevel(Request $request, ProductStockLevelDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Product_Stock'));


        $selected['reference_id'] = $request->reference_id ?? '';
        $selected['variant_id'] = $request->variant_id ?? '';
        $selected['name'] = $request->name ?? '';
        $selected['sku'] = $request->sku ?? '';
        $selected['quantity'] = $request->quantity ?? '';
        $selected['channel'] = $request->channel ?? '';
        $selected['status'] = $request->status ?? '';

        $platforms = $this->ecomPlatformService->userConnectedEcoms()->pluck('ecom_platform_id')->toArray();
        $channels = $this->systemEcomPlatformRepository->get();

        set_page_meta('Products');
        return $dataTable->render('client.products.stock_levels', compact('channels', 'selected'));
    }


    public function sync(Request $request, $platform)
    {
        // TODO: refactor
        // Free user product sync limit check per day
        $user = $this->userRepository->getAppUsers(Auth::user()->business_id);

        if ($user->subscription_type == $this->userRepository->model::SUBSCRIPTION_FREE) {
            // Find today last sync
            $today_sync = ResourceSyncTrack::where('business_id', $user->business_id)
                ->where('date', Carbon::now()->format('Y-m-d'))
                ->where('type', ResourceSyncTrack::SYNC_PRODUCT)
                ->first();

            if ($today_sync) {
                if ($today_sync->total_sync < config('custom.free_sync_limit_per_day.product')) {
                    $today_sync->total_sync = $today_sync->total_sync + 1;
                    $today_sync->save();
                } else {
                    Session::flash('sync_limit', true);
                    return redirect()->back();
                }
            } else {
                $new_sync = new ResourceSyncTrack();
                $new_sync->business_id = Auth::user()->business_id;
                $new_sync->date = Carbon::now()->format('Y-m-d');
                $new_sync->type = ResourceSyncTrack::SYNC_PRODUCT;
                $new_sync->total_sync = 1;
                $new_sync->save();
            }
        }
        $this->ecomProductService->syncProductWithDB([$platform]);


        flash("Product is fetching from $platform.")->success();
        return redirect()->route('client.products.index');
    }

    public function create()
    {
        $categories = $this->productCategoryRepository->get();
        $ebay_categories = $this->ebayCategoryRepository->getParentByMarketplace(config('custom.ebay.marketplace'), ['children']);
        $etsy_taxonomy = $this->etsyTaxonomyRepository->getParentTaxonomy(['children']);

        // Already connected ecom
        $connected_platforms = $this->ecomPlatformService->userConnectedEcoms(null, ['platform']);

        $is_connected_to_ebay = in_array($this->systemEcomPlatformRepository->model::PLATFORM_EBAY, $connected_platforms->pluck('platform.slug')->toArray());

        $warehouses = $this->warehouseRepository->getByPriority();

        // TODO:: Convert to repository
        $fulfillment_policies = EbayPolicy::where('policy_type', 'fulfillment')->orderBy('name')->get();
        $payment_policies = EbayPolicy::where('policy_type', 'payment')->orderBy('name')->get();
        $return_policies = EbayPolicy::where('policy_type', 'return')->orderBy('name')->get();

        set_page_meta('Add Product');
        return view('client.products.system.create', compact('categories', 'ebay_categories', 'is_connected_to_ebay', 'connected_platforms', 'warehouses', 'fulfillment_policies', 'payment_policies', 'return_policies', 'etsy_taxonomy'));
    }

    public function checkForPlatform($platforms, $slug)
    {
        $platform = $platforms->firstWhere('slug', $slug);

        return $platform ? true : false;
    }

    public function store(SystemProductRequest $request)
    {
        // dd($request->all());
        // Process data
        $basic_data = [
            'ecom_platform_ids' => $request->ecom_platform_ids,
            'name' => $request->name,
            'sku' => str_replace(' ', '_', $request->sku),
            'barcode' => $request->barcode,
            'ecom_category_id' => $request->ecom_category_id,
            'etsy_taxonomy_id' => $request->etsy_taxonomy_id,
            'product_condition_id' => $request->product_condition_id,
            'price' => $request->price,
            'brand' => $request->brand,
            'manufacturer' => $request->manufacturer,
            'model_number' => $request->model_number,
            'product_weight' => $request->weight,
            'product_weight_unit' => $request->product_weight_unit,
            'product_depth' => $request->dimensions_l,
            'product_width' => $request->dimensions_w,
            'product_height' => $request->dimensions_h,
            'product_measure_unit' => $request->product_measure_unit,
            'notes' => $request->notes,
            'source' => $this->productRepository->model::SOURCE_SYSTEM,

            'quantity' => 0,
        ];

        // Process data
        $desc_data = [
            'description' => $request->description
        ];

        // Process data
        $img_data = [
            'image' => $request->image,
        ];

        // Process data
        $inventory_data = [
            'inventory' => $request->inventory,
        ];

        // Process data
        $tags_data = [
            'tags' => $request->tags,
        ];

        $product = $this->productRepository->storeOrUpdateBasicInfo($basic_data);

        // Store or udpate data
        if ($product) {

            // Desc
            $this->productRepository->storeOrUpdateDesc($desc_data, $product->id);

            $image = $this->productRepository->storeOrUpdateImage($img_data, $product->id);
            $img_data['image'] = $image;

            $this->productRepository->storeOrUpdateInventory($inventory_data, $product->id);
            $this->productRepository->storeOrUpdateTags($tags_data, $product->id);

            $total_quantity = $this->productRepository->totalStockQuantity($product->id);

            // update qty in product table
            $product = $this->productRepository->storeOrUpdateBasicInfo(['quantity' => $total_quantity], $product->id);

            $basic_data['quantity'] = $total_quantity;

            if (count($request->ecom_platform_ids)) {
                // Update to ecom
                // $this->systemProductService->updateProductDescription($desc_data, $product->id, $product->ecom_platform_ids);
                // $this->systemProductService->updateProductImage($img_data, $product->id, $product->ecom_platform_ids);
                // $this->systemProductService->updateProductTags($tags_data, $product->id, $product->ecom_platform_ids);

                $platforms = $this->systemEcomPlatformRepository->platformsWhereUserConnected($request->ecom_platform_ids);

                // dd($platforms, $this->checkForPlatform($platforms, $this->systemEcomPlatformRepository->model::PLATFORM_EBAY));

                if ($this->checkForPlatform($platforms, $this->systemEcomPlatformRepository->model::PLATFORM_EBAY)) {
                    $default_warehouse = $this->warehouseRepository->getDefault();
                    $warehouse = $default_warehouse ?: $this->warehouseRepository->getFirstByPriority();

                    if ($warehouse && $warehouse->merchant_location_key) {

                        $ebay_additional_info = [
                            'business_id' => $this->productRepository->authUserBusinessId(),
                            'marketplace_id' => setting('default_marketplace'),
                            'product_reference_id' => $product->sku,
                            'format' => 'FIXED_PRICE',
                            'listing_description' => $request->listing_description,
                            // TODO: need to update when inventory part is complete
                            'offer_quantity' => $total_quantity,
                            'merchant_location_key' => $warehouse->merchant_location_key,

                            'listing_duration' => 'GTC',
                        ];

                        if ($request->fulfillment_policy && $request->payment_policy && $request->return_policy) {
                            $ebay_additional_info['listing_policies'] = [
                                'fulfillmentPolicyId' => $request->fulfillment_policy,
                                'paymentPolicyId' => $request->payment_policy,
                                'returnPolicyId' => $request->return_policy,
                            ];
                        }

                        if ($request->tax) {
                            $ebay_additional_info['tax'] = [
                                'vatPercentage' => $request->fulfillment_policy,
                                'applyTax' => true,
                            ];
                        }

                        // Create or update
                        $this->productRepository->storeOrUpdateEbayAdditionalInfo($ebay_additional_info, $product->id);
                    }
                }
                $this->systemProductService->updateProductInfo($basic_data, $product->id, $product->ecom_platform_ids);
            }

            flash('Product create successfull')->success();
            return back();
        }

        flash('Product create failed')->warning();
        return back();
    }

    public function editBasicInfo($id)
    {
        $product = $this->productRepository->get($id);
        $categories = $this->productCategoryRepository->get();

        set_page_meta('Product List - Basic info');
        return view('client.products.edit', compact('product', 'categories'));
    }

    public function editDesc($id)
    {
        $product = $this->productRepository->get($id);

        set_page_meta('Product List - Description');
        return view('client.products.edit_desc', compact('product'));
    }

    public function editImages($id)
    {
        $product = $this->productRepository->get($id, ['images']);

        set_page_meta('Product List - Images');
        return view('client.products.edit_images', compact('product'));
    }

    public function editInventory($id)
    {
        $product = $this->productRepository->get($id, ['inventory_stocks']);
        $warehouses = $this->warehouseRepository->getByPriority();

        $product_stocks = [];

        foreach ($product->inventory_stocks as $stock) {
            $product_stocks[$stock->warehouse_id][] = [
                'total_quantity' => $stock->total_quantity,
                'available_quantity' => $stock->stock,
            ];
        }

        set_page_meta('Product List - Inventory');
        return view('client.products.edit_inventory', compact('product', 'warehouses', 'product_stocks'));
    }

    public function editTags($id)
    {
        $product = $this->productRepository->get($id);

        set_page_meta('Product List - Tags');
        return view('client.products.edit_tags', compact('product'));
    }

    public function updateBasicInfo(ProductBasicInfoRequest $request, $id)
    {
        // Process data
        $data = [
            'name' => $request->name,
            'sku' => str_replace(' ', '_', $request->sku),
            'barcode' => $request->barcode,
            'price' => $request->price,
            'brand' => $request->brand,
            'manufacturer' => $request->manufacturer,
            'model_number' => $request->model_number,
            'product_weight' => $request->weight,
            'product_depth' => $request->dimensions_l,
            'product_width' => $request->dimensions_w,
            'product_height' => $request->dimensions_h,
            'notes' => $request->notes,
        ];

        // Store or udpate data
        if ($this->productRepository->storeOrUpdateBasicInfo($data, $id)) {

            // Update to ecom
            $this->ecomProductService->updateProductInfo($data, $id);

            flash('Basic info update successfull')->success();

            if ($request->save_btn == 'save_close') {
                return redirect()->route('client.products.index');
            } else {
                return redirect()->route('client.products.edit.desc', $id);
            }
        }

        flash('Basic info update failed')->warning();
        return back();
    }

    public function updateDesc(Request $request, $id)
    {
        // Process data
        $data = [
            'description' => $request->description,
        ];

        // Store or udpate data
        if ($this->productRepository->storeOrUpdateDesc($data, $id)) {

            // Update to ecom
            $this->ecomProductService->updateProductDescription($data, $id);


            flash('Description update successfull')->success();

            if ($request->save_btn == 'save_close') {
                return redirect()->route('client.products.index');
            } else {
                return redirect()->route('client.products.edit.images', $id);
            }
        }

        flash('Description update failed')->warning();
        return back();
    }

    public function updateImages(Request $request, $id)
    {

        $this->validate($request, [
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        // Process data
        $data = [
            'image' => $request->image,
        ];

        // Store or udpate data
        if ($image = $this->productRepository->storeOrUpdateImage($data, $id)) {
            $data['image'] = $image;

            // Update to ecom
            $this->ecomProductService->updateProductImage($data, $id);

            flash('Image update successfull')->success();

            if ($request->save_btn == 'save_close') {
                return redirect()->route('client.products.index');
            } else {
                return redirect()->route('client.products.edit.inventory', $id);
            }
        }

        flash('Image update failed')->warning();
        return back();
    }

    public function updateInventory(Request $request, $id)
    {
        $this->validate($request, [
            'inventory' => ['required', 'array'],
            'inventory.*' => ['required'],
        ]);

        // Process data
        $data = [
            'inventory' => $request->inventory,
        ];

        // Store or udpate data
        if ($inventory = $this->productRepository->storeOrUpdateInventory($data, $id)) {
            $total_quantity = $this->productRepository->totalStockQuantity($id);

            // update qty in product table
            $this->productRepository->storeOrUpdateBasicInfo(['quantity' => $total_quantity], $id);

            // Update to ecom
            $this->ecomProductService->updateProductStock($data, $id);

            flash('Inventory update successfull')->success();

            if ($request->save_btn == 'save_close') {
                return redirect()->route('client.products.index');
            } else {
                return redirect()->route('client.products.edit.tags', $id);
            }
        }

        flash('Inventory update failed')->warning();
        return back();
    }

    public function updateTags(Request $request, $id)
    {
        $this->validate($request, [
            'tags' => ['nullable', 'max:255'],
        ]);

        // Process data
        $data = [
            'tags' => $request->tags,
        ];

        // Store or udpate data
        if ($this->productRepository->storeOrUpdateTags($data, $id)) {

            // Update to ecom
            $this->ecomProductService->updateProductTags($data, $id);


            flash('Tag update successfull')->success();

            if ($request->save_btn == 'save_close') {
                return redirect()->route('client.products.index');
            } else {
                return redirect()->route('client.products.index', $id);
            }
        }

        flash('Tag update failed')->error();
        return back();
    }

    public function deleteProductImage($product_id, $image_id)
    {
        if ($this->ecomProductService->deleteProductImage($product_id, $image_id)) {
            flash('Image delete successfull')->success();

            return redirect()->back();
        }

        flash('image delete failed')->error();
        return back();
    }

    public function destroy($id)
    {
        $product = $this->productRepository->get($id);
        if ($product) {
            if ($this->productRepository->deleteProducts($product->ecom_platform_id, $product->id)) {

                if ($product->source == $this->productRepository->model::SOURCE_ECOM) {
                    $this->ecomProductService->deleteProduct($product);
                }

                flash('Product delete successfull')->success();

                return redirect()->back();
            }
        }

        flash('Product delete failed')->error();
        return back();
    }


    public function exportProduct(Request $request)
    {
        $data = $this->productRepository->filteredData($request, ['platform']);

        $type = $request->export;

        $name = 'Products';
        if ($type == 'pdf') {
            // return view('client.products.pdf.list', compact('data'));
            $pdf = PDF::loadView('client.products.pdf.list', ['data' => $data]);
            return $pdf->setPaper('a4', 'landscape')->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new ProductExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ProductExport($data), $name . '.xlsx');
        }
    }

    public function exportSystemProduct(Request $request)
    {
        $data = $this->productRepository->filteredSystemData($request, ['platform']);

        $type = $request->export;

        $name = 'Products';
        if ($type == 'pdf') {
            // return view('client.products.pdf.list', compact('data'));
            $pdf = PDF::loadView('client.products.pdf.list', ['data' => $data]);
            return $pdf->setPaper('a4', 'landscape')->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new ProductExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ProductExport($data), $name . '.xlsx');
        }
    }

    public function exportStockLevel(Request $request)
    {

        $level = request()->level ?? '';
        $data = $this->productRepository->stockWiseData($request, ['platform'], $level);

        $type = $request->export;

        $name = 'Products';
        if ($type == 'pdf') {
            // return view('client.products.pdf.list', compact('data'));
            $pdf = PDF::loadView('client.products.pdf.list', ['data' => $data]);
            return $pdf->setPaper('a4', 'landscape')->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new ProductExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ProductExport($data), $name . '.xlsx');
        }
    }









    // ............HANDLE API REQUEST
    public function productSkuByPlatform($platform_id)
    {
        $skip = request()->skip ?? null;
        $take = request()->take ?? null;
        $select = ['id', 'sku', 'ecom_platform_id'];
        $products = $this->productRepository->getProductByPlatform($platform_id, $select, [], $skip, $take);

        return response()->json([
            'success' => true,
            'total' => $this->productRepository->totalProductByPlatform($platform_id),
            'data' => $products
        ]);
    }

    public function productSkuSearch($q)
    {
        $select = ['id', 'sku', 'ecom_platform_id'];
        $products = $this->productRepository->getProductBySearch($q, $select);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function productSearchByNameSku($q)
    {
        $select = ['id', 'sku', 'ecom_platform_id', 'name', 'price'];
        $products = $this->productRepository->getProductByNameSku($q, $select);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function getChildrenForTree($id)
    {
        $category = $this->ebayCategoryRepository->getCategoryWithChildrenByMarketplace(config('custom.ebay.marketplace'), ['children'], $id);
        $html = '';
        if ($category) {
            foreach ($category->children as $key => $child) {
                $html .= "<li><a href='javascript:void(0)' title='" . $child->ebay_name . "' id='parent-" . $child->ebay_id . "' class='ic-test-class' data-isparent='" . count($child->children) . "' data-categoryid='" . $child->ebay_id . "'>" . $child->ebay_name . "</a>";
                if (count($child->children)) {
                    $html .= "<ul id='" . $child->ebay_id . "'></ul></li>";
                } else {
                    $html .= "</li>";
                }
            }
        }
        return response()->json(['data' => $html], 200);
    }

    public function getChildrenForEtsyTree($id)
    {
        $taxonomy = $this->etsyTaxonomyRepository->getTaxonomyWithChildren(['children'], $id);
        $html = '';
        if ($taxonomy) {
            foreach ($taxonomy->children as $key => $child) {
                $html .= "<li><a href='javascript:void(0)' title='" . $child->taxonomy_name . "' id='etsy-parent-" . $child->taxonomy_id . "' class='ic-taxonomy-class' data-isparenttaxonomy='" . count($child->children) . "' data-taxonomyid='taxonomy-" . $child->taxonomy_id . "'>" . $child->taxonomy_name . "</a>";
                if (count($child->children)) {
                    $html .= "<ul id='taxonomy-" . $child->taxonomy_id . "'></ul></li>";
                } else {
                    $html .= "</li>";
                }
            }
        }
        return response()->json(['data' => $html], 200);
    }

    public function getCategoryCondition(Request $request)
    {
        $conditions = $this->productConditionRepository->getByPlatformIds($request->ecom_platform_ids);

        return response()->json(['data' => $conditions], 200);
    }
}
