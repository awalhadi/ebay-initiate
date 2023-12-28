<?php

namespace App\Http\Controllers\Client\ShippingProvider;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\ShippingProviderConnected;
use App\Models\ShippingApiConnectionEasyship;
use App\Services\Shipping\ShipstationService;
use App\Models\ShippingApiConnectionShipstation;
use App\Services\Shipping\ShippingProviderService;
use App\Repositories\SystemShippingProvider\SystemShippingProviderRepositoryInterface;
use App\Services\Shipping\EasyshipService;

class ShippingProvidersController extends Controller
{
    protected $shippingProviderRepository;
    protected $shipstationService;
    protected $easyshipService;
    protected $shippingProviderService;

    public function __construct(
        SystemShippingProviderRepositoryInterface $shippingProviderRepository,
        ShipstationService $shipstationService,
        EasyshipService $easyshipService,
        ShippingProviderService $shippingProviderService
    ) {
        $this->shippingProviderRepository = $shippingProviderRepository;
        $this->shipstationService = $shipstationService;
        $this->easyshipService = $easyshipService;
        $this->shippingProviderService = $shippingProviderService;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Shipping_Providers'));
    }
    public function index()
    {
        // Get all system providers
        $providers = json_encode($this->shippingProviderRepository->get());

        // Already connected ecom
        $connected_providers = json_encode($this->shippingProviderService->userConnectedShippings());

        set_page_meta('Shipping Provider Connection');
        return view('client.shipping_providers_manage.providers.index', compact('providers', 'connected_providers'));
    }

    public function connect(Request $request)
    {
        // Get provider slug from request
        $provider = $this->shippingProviderRepository->getShippingProviderBySlug($request->provider);

        // If no provider found redirect back
        if (!$provider) return $this->noProviderFound();

        // Check provider already connected or not
        if ($this->shippingProviderService->userConnectedShipping($provider->id)) {
            flash('This provider already connected.')->warning();
            return redirect()->route('client.shipping-providers.index');
        }

        // Send to provider connection page
        try {
            if ($provider->slug) {
                set_page_meta('Shipping Provider Connection');
                return view('client.shipping_providers_manage.provider_connections.' . $provider->slug, compact('provider'));
            } else {
                return redirect()->back();
            }
        } catch (Exception $ex) {
            flash('Under construction!!')->warning();
            return redirect()->route('client.shipping-providers.index');
        }
    }

    public function connectStore(Request $request)
    {
        // Validate provider data
        $this->validate($request, ['provider' => 'required']);

        // Get provider slug from request
        $provider = $this->shippingProviderRepository->getShippingProviderBySlug($request->provider);

        // If no provider found redirect back
        if (!$provider) return $this->noPlatformFound();


        // Connection status
        $is_valid_connection = false;

        // Select provider
        if ($provider->slug == $this->shippingProviderRepository->model::SHIPPING_PROVIDER_SHIPSTATION) {
            // SHIPSTATION
            $this->validate($request, ShippingApiConnectionShipstation::VALIDATION_RULES);
            $data = [
                'provider_id' => $provider->id,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
            ];
            $is_valid_connection = $this->shipstationService->initialConnectionCheck($data);
        } elseif ($provider->slug == $this->shippingProviderRepository->model::SHIPPING_PROVIDER_EASYSHIP) {
            // EASYSHIP
            $this->validate($request, ShippingApiConnectionEasyship::VALIDATION_RULES);
            $data = [
                'provider_id' => $provider->id,
                'access_token' => $request->access_token,
                'origin_address' => $request->origin_address,
                'sender_address' => $request->sender_address,
                'return_address' => $request->return_address,
            ];
            $is_valid_connection = $this->easyshipService->initialConnectionCheck($data);
        }

        // Check connection credentials is correct
        if (!$is_valid_connection) {
            // Add flag for support link
            $request->request->add(['connection_fail' => true]);
            flash('Sorry, we could not integrate your shipping account.')->error();
            return back()->withInput();
        }

        // Track user connected ecom list
        if ($is_valid_connection) {
            $this->shippingProviderService->storeAppUserShippingConnection($provider->id);

            // Send shipping connected success email
            try {
                // TODO: send to multiple user
                event(new ShippingProviderConnected(['provider'=>$provider->name], Auth::user()));
            }catch (\Exception $ex){

            }
        }

        flash('Shipping provider integrated successfully.')->success();
        return redirect()->route('client.shipping-providers.index');
    }

    public function edit($provider_slug)
    {
        // Get provider slug from request
        $provider = $this->shippingProviderRepository->getShippingProviderBySlug($provider_slug);

        // If no provider found redirect back
        if (!$provider) return $this->noProviderFound();

        // Send to provider connection page
        try {
            if ($this->shippingProviderService->userConnectedShipping($provider->id)) {
                $connection = $this->shippingProviderService->getConnection($provider_slug);

                set_page_meta('Shipping Provider Edit');
                return view('client.shipping_providers_manage.provider_edit.' . $provider->slug, compact('provider', 'connection'));
            } else {
                flash('Please connect your provider first!')->warning();
                return redirect()->back();
            }
        } catch (Exception $ex) {
            flash('Under construction!!')->warning();
            return redirect()->route('client.shipping-providers.index');
        }
    }

    public function update(Request $request)
    {
        // Validate provider data
        $this->validate($request, ['provider' => 'required']);

        // Get provider slug from request
        $provider = $this->shippingProviderRepository->getShippingProviderBySlug($request->provider);

        // If no provider found redirect back
        if (!$provider) return $this->noPlatformFound();

        // Check user connection
        $connect = $this->shippingProviderService->userConnectedShipping($provider->id);
        if (!$connect) return $this->noPlatformFound();

        // Update status
        $is_update_connection = false;

        // Select provider
        if ($provider->slug == $this->shippingProviderRepository->model::SHIPPING_PROVIDER_EASYSHIP) {
            // EASYSHIP
            $this->validate($request, ShippingApiConnectionEasyship::VALIDATION_RULES);
            $data = [
                'provider_id' => $provider->id,
                'access_token' => $request->access_token,
                'origin_address' => $request->origin_address,
                'sender_address' => $request->sender_address,
                'return_address' => $request->return_address,
            ];
            $is_update_connection = $this->easyshipService->updateConnection($data);
        }

        // Check connection credentials is correct
        if (!$is_update_connection) {
            // Add flag for support link
            $request->request->add(['connection_fail' => true]);
            flash('Sorry, we could not update your shipping account.')->error();
            return back()->withInput();
        }

        flash('Shipping provider info update successfully.')->success();
        return redirect()->route('client.shipping-providers.index');
    }


    public function disconnect(Request $request)
    {
        // Get provider slug from request
        $provider = $this->shippingProviderRepository->getShippingProviderBySlug($request->provider);

        // If no provider found redirect back
        if (!$provider) return $this->noPlatformFound();

        // Select provider and disconnect
        if ($provider->slug == $this->shippingProviderRepository->model::SHIPPING_PROVIDER_SHIPSTATION) {
            // SHIPSTATION
            $this->shipstationService->disconnectApiConnection();
        } elseif ($provider->slug == $this->shippingProviderRepository->model::SHIPPING_PROVIDER_EASYSHIP) {
            // EASYSHIP
            $this->easyshipService->disconnectApiConnection();
        }

        // Delete provider from user connected list
        $this->shippingProviderService->deleteAppUserShippingConnection($provider->id);

        flash('Shipping provider disconnected successfully.')->success();
        return response()->json([
            'success' => true
        ]);
    }

    public function noProviderFound(): \Illuminate\Http\RedirectResponse
    {
        flash('Please provide a valid shipping provider!')->error();
        return redirect()->route('client.shipping-providers.index');
    }
}
