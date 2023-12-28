<?php

namespace App\Http\Controllers\Client\EcomPlatform;

use App\Events\EcomConnected;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EcomApiConnectionEbay;
use App\Models\EcomApiConnectionEtsy;
use App\Models\EcomApiConnectionShopify;
use App\Models\EcomApiConnectionWalmart;
use App\Services\EcomPlatforms\EbayService;
use App\Services\EcomPlatforms\EtsyService;
use App\Services\EcomPlatforms\ShopifyService;
use App\Services\EcomPlatforms\WalmartService;
use App\Services\EcomPlatforms\EcomOrderService;
use App\Services\EcomPlatforms\EcomProductService;
use App\Services\EcomPlatforms\EcomPlatformService;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class EcomPlatformsController extends Controller
{
    protected $systemEcomPlatformRepository;
    protected $ecomPlatformService;
    protected $ecomProductService;
    protected $ecomOrderService;
    protected $ebayService;
    protected $etsyService;
    protected $shopifyService;
    protected $walmartService;

    public function __construct(
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository,
        EcomPlatformService $ecomPlatformService,
        EbayService $ebayService,
        EtsyService $etsyService,
        ShopifyService $shopifyService,
        EcomProductService $ecomProductService,
        EcomOrderService $ecomOrderService,
        WalmartService $walmartService
    ) {
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;
        $this->ecomPlatformService = $ecomPlatformService;
        $this->ebayService = $ebayService;
        $this->etsyService = $etsyService;
        $this->shopifyService = $shopifyService;
        $this->ecomProductService = $ecomProductService;
        $this->ecomOrderService = $ecomOrderService;
        $this->walmartService = $walmartService;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Ecom_Connection'));
    }

    public function index()
    {
        // Get all system platforms
        $platforms = json_encode($this->systemEcomPlatformRepository->get());

        // Already connected ecom
        $connected_platforms = json_encode($this->ecomPlatformService->userConnectedEcoms());

        set_page_meta('E-com Connection');
        return view('client.ecom_platform_manage.platforms.index', compact('platforms', 'connected_platforms'));
    }

    public function connect(Request $request)
    {
        // Get platform slug from request
        $platform = $this->systemEcomPlatformRepository->getEcomPlatformBySlug($request->platform);

        // If no platform found redirect back
        if (!$platform) return $this->noPlatformFound();

        // Check platform already connected or not
        if ($this->ecomPlatformService->userConnectedEcom($platform->id)) {
            flash('This platform already connected.')->warning();
            return redirect()->route('client.ecom-platforms.index');
        }

        // Send to platform connection page
        try {
            if ($platform->slug) {
                if ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_EBAY) {
                    return redirect($this->ebayService->getConnectionUrl());
                } else if ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_ETSY) {
                    return redirect($this->etsyService->getConnectionUrl());
                } else {
                    set_page_meta('E-com Connection');
                    return view('client.ecom_platform_manage.platform_connections.' . $platform->slug, compact('platform'));
                }
            } else {
                return redirect()->back();
            }
        } catch (Exception $ex) {
            throw $ex;
            flash('Under construction!!')->warning();
            return redirect()->route('client.ecom-platforms.index');
        }
    }

    public function connectStore(Request $request)
    {
        // Validate platform data
        $this->validate($request, ['platform' => 'required']);

        // Get platform slug from request
        $platform = $this->systemEcomPlatformRepository->getEcomPlatformBySlug($request->platform);

        // If no platform found redirect back
        if (!$platform) return $this->noPlatformFound();


        // Connection status
        $is_valid_connection = false;

        // Select platform

        if ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_EBAY) {
            // EBAY
            $this->validate($request, EcomApiConnectionEbay::VALIDATION_RULES);
            $data = [
                'platform_id' => $platform->id,
                'code' => $request->code,
            ];
            $is_valid_connection = $this->ebayService->initialConnectionCheck($data);
        } elseif ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_ETSY) {
            // ETSY
            $this->validate($request, EcomApiConnectionEtsy::VALIDATION_RULES);
            $data = [
                'platform_id' => $platform->id,
                'oauth_token' => $request->oauth_token,
                'oauth_verifier' => $request->oauth_verifier,
            ];
            $is_valid_connection = $this->etsyService->initialConnectionCheck($data);
        } elseif ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_SHOPIFY) {
            // SHOPIFY
            $this->validate($request, EcomApiConnectionShopify::VALIDATION_RULES);
            $data = [
                'platform_id' => $platform->id,
                'shop_url' => $request->shop_url,
                'api_key' => $request->api_key,
                'password' => $request->password,
            ];
            $is_valid_connection = $this->shopifyService->initialConnectionCheck($data);
        } elseif ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_WALMART) {
            // WALMART
            $this->validate($request, EcomApiConnectionWalmart::VALIDATION_RULES);
            $data = [
                'platform_id' => $platform->id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
            ];
            $is_valid_connection = $this->walmartService->initialConnectionCheck($data);
        }

        // Check connection credentials is correct
        if (!$is_valid_connection) {
            // Add flag for support link
            $request->request->add(['connection_fail' => true]);
            flash('Sorry, we could not integrate your ecommerce account.')->error();
            return back()->withInput();
        }

        // Track user connected ecom list
        if ($is_valid_connection) {
            $this->ecomPlatformService->storeAppUserEcomConnection($platform->id);
            // Sync products with DB
            $this->ecomProductService->syncProductWithDB([$platform->slug]);
            // Sync orders with DB
            $this->ecomOrderService->syncOrderWithDB([$platform->slug]);

            // Send the success email
            try {
                // TODO: send email to multiple user
                event(new EcomConnected(['platform'=> $platform->slug], Auth::user()));
            }catch (Exception $ex){

            }
        }

        flash('Ecom integrated successfully.')->success();
        return redirect()->route('client.ecom-platforms.index');
    }


    public function disconnect(Request $request)
    {
        // Get platform slug from request
        $platform = $this->systemEcomPlatformRepository->getEcomPlatformBySlug($request->platform);

        // If no platform found redirect back
        if (!$platform) return $this->noPlatformFound();

        // Select platform and disconnect
        if ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_EBAY) {
            // EBAY
            $this->ebayService->disconnectApiConnection();
        } elseif ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_ETSY) {
            // ETSY
            $this->etsyService->disconnectApiConnection();
        } elseif ($platform->slug == $this->systemEcomPlatformRepository->model::PLATFORM_SHOPIFY) {
            // SHOPIFY
            $this->shopifyService->disconnectApiConnection();
        }

        // Delete platform from user connected ecom list
        $this->ecomPlatformService->deleteAppUserEcomConnection($platform->id);

        flash('Ecom platform disconnected successfully.')->success();
        return response()->json([
            'success' => true
        ]);
    }


    public function noPlatformFound(): \Illuminate\Http\RedirectResponse
    {
        flash('Please provide a valid platform!')->error();
        return redirect()->route('client.ecom-platforms.index');
    }
}
