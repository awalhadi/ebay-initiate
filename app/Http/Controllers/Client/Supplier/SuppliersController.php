<?php

namespace App\Http\Controllers\Client\Supplier;

use App\DataTables\SupplierDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Repositories\Supplier\SupplierRepositoryInterface;

/**
 * SuppliersController
 */
class SuppliersController extends Controller
{

    protected $supplierRepository;


    /**
     * __construct
     *
     * @param  mixed $supplierRepository
     * @return void
     */
    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Supplier'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SupplierDataTable $dataTable)
    {
        set_page_meta('Suppliers');
        return $dataTable->render('client.suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add Supplier');
        return view('client.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        $data = $request->validated();

        if ($this->supplierRepository->storeOrUpdate($data)) {
            flash('Supplier added successfully')->success();
        } else {
            flash('Supplier added fail')->error();
        }

        return redirect()->route('client.suppliers.index');
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
        $supplier = $this->supplierRepository->get($id);

        set_page_meta('Edit Supplier');
        return view('client.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->supplierRepository->storeOrUpdate($data, $id)) {
            flash('Supplier updated successfully')->success();
        } else {
            flash('Supplier updated fail')->error();
        }

        return redirect()->route('client.suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->supplierRepository->delete($id)) {
            flash('Supplier delete successfully')->success();
        } else {
            flash('Supplier delete fail')->error();
        }

        return redirect()->route('client.suppliers.index');
    }
}
