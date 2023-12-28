<?php

namespace App\Http\Controllers\Client\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ProductCategoryDataTable;
use App\Http\Requests\ProductCategoryRequest;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

/**
 * ProductCategoriesController
 */
class ProductCategoriesController extends Controller
{

    protected $productCategoryRepository;
    protected $ecomPlatformRepository;


    /**
     * __construct
     *
     * @param  mixed $productCategoryRepository
     * @return void
     */
    public function __construct(
        ProductCategoryRepositoryInterface $productCategoryRepository,
        SystemEcomPlatformRepositoryInterface $ecomPlatformRepository
    ) {
        $this->productCategoryRepository = $productCategoryRepository;
        $this->ecomPlatformRepository = $ecomPlatformRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Custom_Category' . '|' . saasPermission('Category') . '|' . saasPermission('Assign_SKU_to_Category')));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductCategoryDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Category'));

        set_page_meta('Categories');
        return $dataTable->render('client.product_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add Category');
        return view('client.product_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCategoryRequest $request)
    {
        $data = $request->validated();

        if ($this->productCategoryRepository->storeOrUpdate($data)) {
            flash('Product Category added successfully')->success();
        } else {
            flash('Product Category added fail')->error();
        }

        return redirect()->route('client.product-categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = $this->productCategoryRepository->get($id);

        set_page_meta('Edit Category');
        return view('client.product_categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductCategoryRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->productCategoryRepository->storeOrUpdate($data, $id)) {
            flash('Product Category updated successfully')->success();
        } else {
            flash('Product Category updated fail')->error();
        }

        return redirect()->route('client.product-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->productCategoryRepository->delete($id)) {
            flash('Product Category delete successfully')->success();
        } else {
            flash('Product Category delete fail')->error();
        }

        return redirect()->route('client.product-categories.index');
    }

    public function assignSku()
    {
        // Check saas user permission
        checkPermission(saasPermission('Assign_SKU_to_Category'));

        $platforms = $this->ecomPlatformRepository->get();
        $categories = $this->productCategoryRepository->get();

        set_page_meta('Assing SKU');
        return view('client.product_categories.assign_sku', compact('platforms', 'categories'));
    }
}
