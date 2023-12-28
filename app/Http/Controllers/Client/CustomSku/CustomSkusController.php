<?php

namespace App\Http\Controllers\Client\CustomSku;

use App\DataTables\CustomSkuDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomSkuRequest;
use App\Repositories\CustomSku\CustomSkuRepositoryInterface;
use Illuminate\Http\Request;

class CustomSkusController extends Controller
{
    protected $customSkuRepository;

    public function __construct(CustomSkuRepositoryInterface $customSkuRepository)
    {
        $this->customSkuRepository = $customSkuRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Custom_SKU'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CustomSkuDataTable $dataTable)
    {
        set_page_meta('Custom SKU');
        return $dataTable->render('client.custom_skus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add Custom SKU');
        return view('client.custom_skus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomSkuRequest $request)
    {
        $data = $request->validated();

        if ($this->customSkuRepository->storeOrUpdate($data)) {
            flash('Custom sku added successfully')->success();
        } else {
            flash('Custom sku added fail')->error();
        }

        return redirect()->route('client.custom-skus.index');
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
        $custom_sku = $this->customSkuRepository->get($id);

        set_page_meta('Edit Custom SKU');
        return view('client.custom_skus.edit', compact('custom_sku'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomSkuRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->customSkuRepository->storeOrUpdate($data, $id)) {
            flash('Custom sku updated successfully')->success();
        } else {
            flash('Custom sku updated fail')->error();
        }

        return redirect()->route('client.custom-skus.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->customSkuRepository->delete($id)) {
            flash('Custom sku delete successfully')->success();
        } else {
            flash('Custom sku delete fail')->error();
        }

        return redirect()->route('client.custom-skus.index');
    }
}
