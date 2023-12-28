<?php

namespace App\Http\Controllers\Client\Shipping;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ShippingDataTable;
use App\Http\Requests\ShippingRequest;
use Illuminate\Http\Client\HttpClientException;
use App\Services\Shipping\ShippingProviderService;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\Shipping\ShippingRepositoryInterface;

class ShippingController extends Controller
{
    protected $countryRepository;
    protected $shippingRepository;
    protected $orderRepository;
    protected $shippingProviderService;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        ShippingRepositoryInterface $shippingRepository,
        OrderRepositoryInterface $orderRepository,
        ShippingProviderService $shippingProviderService
    ) {
        $this->countryRepository = $countryRepository;
        $this->shippingRepository = $shippingRepository;
        $this->orderRepository = $orderRepository;
        $this->shippingProviderService = $shippingProviderService;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Custom_Shipping'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ShippingDataTable $dataTable)
    {
        set_page_meta('Sipping Manage');
        return $dataTable->render('client.shippings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($order_id)
    {
        $order =  $this->orderRepository->shippedOrderDetails($order_id, ['details', 'platform'])['order'];
        if (!$order) abort(404);

        $countries = $this->countryRepository->get();
        $boxes = $this->shippingRepository->getBoxes();
        $categories = $this->shippingRepository->getCategories();

        //TODO: currently only for easyship
        $connection = $this->shippingProviderService->getConnection('easyship');

        if (!$connection){
            flash('Please connect a shipping provider')->warning();
            return back();
        }


        set_page_meta('Create Shipping');
        return view('client.shippings.create', compact('countries', 'boxes', 'order', 'categories', 'connection'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShippingRequest $request)
    {
        try {
            // Process Data
            $data = [
                'order_id' => $request->order_id,
                'shipping_data' => $request->except(['_token', 'order_id'])
            ];

            // Store shipping info
            if ($shipping = $this->shippingRepository->storeOrUpdate($data)) {

                return redirect()->route('client.shippings.get_rates', $shipping->id);
            }
        } catch (HttpClientException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            $data = json_decode($responseBodyAsString);

            flash($data->errors[0])->error()->important();
            return redirect()->back();
        } catch (Exception $ex) {
            flash('Something went wrong')->error();
            return redirect()->route('client.shippings.index');
        }
    }

    public function getRates($id)
    {
        $shipping = $this->shippingRepository->get($id);
        if (!$shipping) abort(404);

        // Shipping ID
        $shipping_id = $shipping->id;

        // Send to rates page
        $couriers = $this->shippingRepository->getRates($shipping->shipping_data);

        set_page_meta('Select Shipping Courier');
        return view('client.shippings.select_curier', compact('couriers', 'shipping_id'));
    }

    public function processShipping($shipping_id, $courier_id, $courier_name)
    {
        // Add courier info
        $courier_data = [
            'allow_courier_fallback' => false,
            'apply_shipping_rules' => true,
            'selected_courier_id' => $courier_id,
            'courier_name' => $courier_name
        ];

        if ($this->shippingRepository->processShipping($shipping_id, $courier_data)) {
            flash('Shipping added successfully')->success();
        } else {
            flash('Shipping added fail')->error();
        }

        return redirect()->route('client.shippings.index');
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

        $shipping = $this->shippingRepository->get($id);
        if (!$shipping) abort(404);

        $order =  $this->orderRepository->shippedOrderDetails($shipping->order_id, ['details', 'platform'])['order'];
        if (!$order) abort(404);

        $countries = $this->countryRepository->get();
        $boxes = $this->shippingRepository->getBoxes();
        $categories = $this->shippingRepository->getCategories();

        //TODO: currently only for easyship
        $connection = $this->shippingProviderService->getConnection('easyship');


        set_page_meta('Edit Shipping');
        return view('client.shippings.edit', compact('shipping', 'countries', 'boxes', 'order', 'categories', 'connection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ShippingRequest $request, $id)
    {
        try {
            // Process Data
            $data = [
                'shipping_data' => $request->except(['_token', '_method', 'order_id'])
            ];

            // Update shipping info
            if ($this->shippingRepository->storeOrUpdate($data, $id)) {

                return redirect()->route('client.shippings.get_rates', $id);
            }
        } catch (HttpClientException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            $data = json_decode($responseBodyAsString);

            flash($data->errors[0])->error()->important();
            return redirect()->back();
        } catch (Exception $ex) {
            flash('Something went wrong')->error();
            return redirect()->route('client.shippings.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
