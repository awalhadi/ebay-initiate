<?php

namespace App\Http\Controllers\Client\Warehouse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\WarehouseDataTable;
use App\Http\Requests\WarehouseRequest;
use App\Services\EcomPlatforms\EbayService;
use App\Services\EcomPlatforms\EcomPlatformService;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class WarehousesController extends Controller
{
    protected $warehouseRepository;
    protected $systemEcomPlatformRepository;
    protected $ecomPlatformService;
    protected $ebayService;

    /**
     * __construct
     *
     * @param  mixed $warehouseRepository
     * @return void
     */
    public function __construct(
        EcomPlatformService $ecomPlatformService,
        EbayService $ebayService,
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository,
        WarehouseRepositoryInterface $warehouseRepository
    ) {
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;
        $this->ebayService = $ebayService;
        $this->ecomPlatformService = $ecomPlatformService;
        $this->warehouseRepository = $warehouseRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Warehouse'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WarehouseDataTable $dataTable)
    {
        set_page_meta('Warehouses');
        return $dataTable->render('client.warehouses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $is_connected_to_ebay = in_array($this->systemEcomPlatformRepository->model::PLATFORM_EBAY, $this->ecomPlatformService->userConnectedEcoms(null, ['platform'])->pluck('platform.slug')->toArray());
        $ebay_countries = $this->warehouseRepository->model::COUNTRIES;

        set_page_meta('Add Warehouse');
        return view('client.warehouses.create', compact('is_connected_to_ebay', 'ebay_countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WarehouseRequest $request)
    {
        $data = $request->all();

        $warehouse = $this->warehouseRepository->storeOrUpdate($data);

        if ($warehouse) {
            if ($warehouse->is_create_on_ebay) {
                // Create warehouse on eBay
                $response = $this->ebayService->createOrUpdateWarehouse($warehouse);
            }
            flash('Warehouse added successfully')->success();
        } else {
            flash('Warehouse added fail')->error();
        }

        return redirect()->route('client.warehouses.index');
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
        // dd($this->ebayService->getWarehouses());

        $warehouse = $this->warehouseRepository->get($id);
        $is_connected_to_ebay = in_array($this->systemEcomPlatformRepository->model::PLATFORM_EBAY, $this->ecomPlatformService->userConnectedEcoms(null, ['platform'])->pluck('platform.slug')->toArray());
        $ebay_countries = $this->warehouseRepository->model::COUNTRIES;

        set_page_meta('Edit Warehouses');
        return view('client.warehouses.edit', compact('warehouse', 'is_connected_to_ebay', 'ebay_countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WarehouseRequest $request, $id)
    {
        $data = $request->all();

        $warehouse = $this->warehouseRepository->get($id);

        $previous_is_create_on_ebay = $warehouse->is_create_on_ebay;

        $this->warehouseRepository->storeOrUpdate($data, $id);

        $warehouse = $this->warehouseRepository->get($id);

        if ($warehouse) {
            if (!$previous_is_create_on_ebay && $warehouse->is_create_on_ebay) {
                // Create warehouse on eBay
                $response = $this->ebayService->createOrUpdateWarehouse($warehouse);
            } elseif ($previous_is_create_on_ebay && !$warehouse->is_create_on_ebay) {
                // Delete warehouse on EBay
                $response = $this->ebayService->deleteWarehouse($warehouse);
            }
            flash('Warehouse updated successfully')->success();
        } else {
            flash('Warehouse updated fail')->error();
        }

        return redirect()->route('client.warehouses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->warehouseRepository->delete($id)) {
            flash('Warehouse delete successfully')->success();
        } else {
            flash('Warehouse delete fail')->error();
        }

        return redirect()->route('client.warehouses.index');
    }
}
