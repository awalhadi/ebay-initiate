<?php

namespace App\Http\Controllers\Client\Settings;

use App\Models\EbayPolicy;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Jobs\EbayCategoryJob;
use App\Jobs\EtsyTaxonomyJob;
use App\Models\EbayMarketplace;
use App\Http\Controllers\Controller;
use App\Services\EcomPlatforms\EbayService;
use App\Services\EcomPlatforms\EtsyService;
use App\Services\EcomPlatforms\ShopifyService;
use App\Services\EcomPlatforms\EcomPlatformService;
use App\Repositories\EbayCategory\EbayCategoryRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class SiteSettingController extends Controller
{
    protected $ebayCategoryRepository;
    protected $ebayService;
    protected $etsyService;
    protected $shopifyService;
    protected $ecomPlatformService;
    protected $systemEcomPlatformRepository;

    public function __construct(
        EbayCategoryRepositoryInterface $ebayCategoryRepository,
        EbayService $ebayService,
        EtsyService $etsyService,
        ShopifyService $shopifyService,
        EcomPlatformService $ecomPlatformService,
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository
    ) {
        set_page_meta('Site Settings');
        $this->ebayCategoryRepository = $ebayCategoryRepository;
        $this->ebayService = $ebayService;
        $this->etsyService = $etsyService;
        $this->shopifyService = $shopifyService;
        $this->ecomPlatformService = $ecomPlatformService;
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Settings'));
    }

    public function index()
    {
        $marketplaces = EbayMarketplace::orderBy('identifier', 'desc')->get();

        $connected_platforms = $this->ecomPlatformService->userConnectedEcoms(null, ['platform'])->pluck('platform.slug')->toArray();

        $is_connected_to_ebay = in_array($this->systemEcomPlatformRepository->model::PLATFORM_EBAY, $connected_platforms);
        $is_connected_to_shopify = in_array($this->systemEcomPlatformRepository->model::PLATFORM_SHOPIFY, $connected_platforms);
        $is_connected_to_etsy = in_array($this->systemEcomPlatformRepository->model::PLATFORM_ETSY, $connected_platforms);

        return view('client.settings.site_setting.index', compact('marketplaces', 'is_connected_to_ebay', 'is_connected_to_shopify', 'is_connected_to_etsy'));
    }

    /**
     * Update site settings like email, address, default marketplace,
     * System logo, etc.
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $allData = $request->except('_token', 'logo', 'small_logo');
        foreach ($allData as $key => $data) {
            if ($key == 'social_icons' || $key == 'special_offer') {
                $data = json_encode($data);
            }
            SiteSetting::updateOrCreate(
                ['key' => $key, 'business_id' => $this->ebayCategoryRepository->authUserBusinessId()],
                ['value' => $data]
            );
        }

        // if ($request->hasFile('logo')) {
        //     $path = $request->file('logo')->store('images');
        //     $image = Image::make(Storage::get($path))->resize(null, 80, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })->encode();
        //     Storage::put($path, $image);
        //     SiteSetting::updateOrCreate(['key' => 'logo'], ['value' => $path]);
        // }

        // if ($request->hasFile('small_logo')) {
        //     $path = $request->file('small_logo')->store('images');
        //     $image = Image::make(Storage::get($path))->resize(null, 80, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })->encode();
        //     Storage::put($path, $image);
        //     SiteSetting::updateOrCreate(['key' => 'small_logo'], ['value' => $path]);
        // }

        $this->checkAndUpdateCategoryTreeId();

        flash('Site Settings Updated Successfully');
        return redirect()->back();
    }

    /**
     * Check category tree id is exist in setting or not
     *
     * @return string
     */
    public function checkAndUpdateCategoryTreeId()
    {
        $default_marketplace = setting('default_marketplace');
        $business_id = $this->ebayCategoryRepository->authUserBusinessId();

        $ebay_categories = $this->ebayCategoryRepository->filterByMarketplace($default_marketplace);

        if (!$ebay_categories->count()) {
            $category_tree_id = SiteSetting::where([
                ['key', 'category_tree_id'],
            ])->first();

            if ($category_tree_id) {
                $category_tree_id = $this->updateCategoryTreeId($category_tree_id, $default_marketplace, $business_id);
            } else {
                $category_tree_id = new SiteSetting();
                $category_tree_id = $this->updateCategoryTreeId($category_tree_id, $default_marketplace, $business_id);
            }

            EbayCategoryJob::dispatch($business_id, $category_tree_id->value ?? 0, $default_marketplace)->onQueue('category')->delay(now()->addSeconds(config('custom.job.delay')));
        }

        return 'Category Tree ID Updated!';
    }

    public function syncEtsyTaxonomy()
    {
        $business_id = $this->ebayCategoryRepository->authUserBusinessId();

        // dd($this->etsyService->getUserDetails($business_id));
        EtsyTaxonomyJob::dispatch($business_id)->onQueue('category')->delay(now()->addSeconds(config('custom.job.delay')));

        flash('Etsy taxonomy updating!');
        return redirect()->back();
    }

    public function syncEtsyShops()
    {
        $business_id = $this->ebayCategoryRepository->authUserBusinessId();

        try {
            $shops = $this->etsyService->getShopForLoggedInUser($business_id);

            dd($shops);
            foreach ($shops as $key => $shop) {
                # code...
            }

            flash('Etsy shops updated successfully.');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Update category tree id in settings table for default
     * further use.
     *
     * @param [type] $category_tree_id
     */
    public function updateCategoryTreeId($category_tree_id, $default_marketplace, $business_id)
    {
        $ebay_cti = $this->ebayService->getDefaultCategoryTreeIdFromEbay($business_id, $default_marketplace);

        $category_tree_id->business_id = $business_id;
        $category_tree_id->key = 'category_tree_id';
        $category_tree_id->value = $ebay_cti ? $ebay_cti->categoryTreeId ?? 0 : 0;
        $category_tree_id->save();

        return $category_tree_id;
    }

    // TODO:: call after eBay connection

    /**
     * Sync all available eBay Policies to the system
     *
     * @return void
     */
    public function sync_policies()
    {
        $default_marketplace = setting('default_marketplace');
        $business_id = $this->ebayCategoryRepository->authUserBusinessId();

        $fulfillment_policies = $this->fulfillment_policy($business_id, $default_marketplace);
        $payment_policies = $this->payment_policy($business_id, $default_marketplace);
        $return_policies = $this->return_policy($business_id, $default_marketplace);

        return redirect()->back();
    }

    // TODO:: call after shopify connection
    public function syncShopifyLocations()
    {
        $business_id = $this->ebayCategoryRepository->authUserBusinessId();

        $locations = $this->shopifyService->getInventoryLocations($business_id);

        // dd($locations);

        $shopify_location = SiteSetting::where([
            ['key', 'shopify_location'],
        ])->first();

        if (!$shopify_location) {
            $shopify_location = new SiteSetting();
        }

        $shopify_location->business_id = $business_id;
        $shopify_location->key = 'shopify_location';
        $shopify_location->value = count($locations->locations) ? $locations->locations[0]->id : null;
        $shopify_location->save();

        flash('Fetched shopify location Successfully');
        return redirect()->back();
    }

    /**
     * Display a listing of the resource -- fullfillment_policy
     *
     * @return \Illuminate\Http\Response
     */
    public function fulfillment_policy($business_id, $default_marketplace)
    {
        // TODO: Convert to repository
        $fullfillment_policies = $this->ebayService->getPolicies($business_id, $default_marketplace, 'fulfillment_policy');
        if ($fullfillment_policies) {
            foreach ($fullfillment_policies->fulfillmentPolicies as $fulfillment) {
                $system_policy = EbayPolicy::firstOrNew([
                    'policy_id' => $fulfillment->fulfillmentPolicyId,
                ]);
                $system_policy->name = $fulfillment->name ?? '';
                $system_policy->policy_type = 'fulfillment';
                $system_policy->policy_id = $fulfillment->fulfillmentPolicyId ?? '';
                $system_policy->marketplace = $fulfillment->marketplaceId ?? '';
                $system_policy->category_types = $fulfillment->categoryTypes;

                $system_policy->handling_time_or_return_period = $fulfillment->handlingTime ?? '';
                $system_policy->ship_locations = $fulfillment->shipToLocations ?? '';
                $system_policy->ship_options = $fulfillment->shippingOptions ?? '';
                $system_policy->global_shipping = $fulfillment->globalShipping ?? '';
                $system_policy->pickup_dropOff = $fulfillment->pickupDropOff ?? '';
                $system_policy->freight_shipping = $fulfillment->freightShipping ?? '';

                $system_policy->save();
            }
        }
        return true;
    }

    /**
     * Display a listing of the resource -- payment_policy
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_policy($business_id, $default_marketplace)
    {
        // TODO: Convert to repository
        $payment_policies = $this->ebayService->getPolicies($business_id, $default_marketplace, 'payment_policy');
        if ($payment_policies) {
            foreach ($payment_policies->paymentPolicies as $payment) {
                $system_policy = EbayPolicy::firstOrNew([
                    'policy_id' => $payment->paymentPolicyId,
                ]);
                $system_policy->name = $payment->name ?? '';
                $system_policy->policy_type = 'payment';
                $system_policy->policy_id = $payment->paymentPolicyId ?? '';
                $system_policy->marketplace = $payment->marketplaceId ?? '';
                $system_policy->category_types = $payment->categoryTypes;

                $system_policy->payment_methods = $payment->paymentMethods ?? '';
                $system_policy->payment_instructions = $payment->paymentInstructions ?? '';
                $system_policy->immediate_pay = $payment->immediatePay ?? '';

                $system_policy->save();
            }
        }
        return true;
    }

    /**
     * Display a listing of the resource -- return_policy
     *
     * @return \Illuminate\Http\Response
     */
    public function return_policy($business_id, $default_marketplace)
    {
        // TODO: Convert to repository
        $return_policies = $this->ebayService->getPolicies($business_id, $default_marketplace, 'return_policy');
        if ($return_policies) {
            foreach ($return_policies->returnPolicies as $return) {
                $system_policy = EbayPolicy::firstOrNew([
                    'policy_id' => $return->returnPolicyId,
                ]);
                $system_policy->name = $return->name ?? '';
                $system_policy->policy_type = 'return';
                $system_policy->policy_id = $return->returnPolicyId ?? '';
                $system_policy->marketplace = $return->marketplaceId ?? '';
                $system_policy->category_types = $return->categoryTypes;

                $system_policy->returns_accepted = $return->returnsAccepted ?? '';
                $system_policy->handling_time_or_return_period = $return->returnPeriod ?? '';
                $system_policy->returns_method = $return->refundMethod ?? '';
                $system_policy->return_shipping_cost_payer = $return->returnShippingCostPayer ?? '';

                $system_policy->save();
            }
        }
        return true;
    }
}