<?php

namespace App\Http\Controllers\Client\ArrivingItem;

use App\DataTables\ArrivingItemDataTable;
use App\DataTables\DeliveredItemDataTable;
use App\Http\Requests\ArrivingItemRequest;
use App\Repositories\ArrivingItem\ArrivingItemRepositoryInterface;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ArrivingItem;
use Illuminate\Http\Request;
use App\Models\ArrivingItemData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Supplier\SupplierRepositoryInterface;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;

class ArrivingItemsController extends Controller
{
    protected $supplerRepository;
    protected $warehouseRepository;
    protected $arrivingItemRepository;

    public function __construct(
        SupplierRepositoryInterface $supplerRepository,
        WarehouseRepositoryInterface $warehouseRepository,
        ArrivingItemRepositoryInterface $arrivingItemRepository
    ) {
        $this->supplerRepository = $supplerRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->arrivingItemRepository = $arrivingItemRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Arriving_Item'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArrivingItemDataTable $dataTable)
    {
        set_page_meta('Arriving Item List');
        return $dataTable->render('client.arriving_items.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = $this->supplerRepository->getActiveData();
        $warehouses = $this->warehouseRepository->getActiveData();

        set_page_meta('Add Arriving Item');
        return view('client.arriving_items.create', compact('suppliers', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArrivingItemRequest $request)
    {
        $data = $data = $request->validated();

        if ($this->arrivingItemRepository->storeOrUpdate($data)){
            flash('Item added successfully')->success();
            return response()->json(['success' => true]);
        }else{
            flash('Item add failed')->success();
            return response()->status(422)->json(['success' => false]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = ArrivingItem::with(['items', 'supplier', 'warehouse'])->findOrFail($id);

        set_page_meta('Show Item');
        return view('client.arriving_items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = ArrivingItem::with('items')->findOrFail($id);

        $suppliers = $this->supplerRepository->getActiveData();
        $warehouses = $this->warehouseRepository->getActiveData();

        set_page_meta('Edit Item');
        return view('client.arriving_items.edit', compact('item', 'suppliers', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ArrivingItemRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->arrivingItemRepository->storeOrUpdate($data, $id)){
            flash('Item updated successfully')->success();
            return response()->json(['success' => true]);
        }else{
            flash('Item update failed')->success();
            return response()->status(422)->json(['success' => false]);
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
        $item = ArrivingItem::findOrFail($id);

        if ($item->delete()) {
            flash('Product category delete successfully')->success();
        } else {
            flash('Product category delete fail')->error();
        }

        return redirect()->route('client.arriving-items.index');
    }

    public function deliveredItem(DeliveredItemDataTable $dataTable)
    {
        set_page_meta('Delivered Item List');
        return $dataTable->render('client.arriving_items.delivered_items');
    }
}
