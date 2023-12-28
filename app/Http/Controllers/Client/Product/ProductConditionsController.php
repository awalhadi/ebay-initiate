<?php

namespace App\Http\Controllers\Client\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ProductConditionDataTable;
use App\Http\Requests\ProductConditionRequest;
use App\Repositories\ProductCondition\ProductConditionRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class ProductConditionsController extends Controller
{
    protected $productConditionRepository;
    protected $systemEcomPlatformRepository;


    /**
     * __construct
     *
     * @param  mixed $productConditionRepository
     * @return void
     */
    public function __construct(
        ProductConditionRepositoryInterface $productConditionRepository,
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository
    ) {
        $this->productConditionRepository = $productConditionRepository;
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Product_Condition'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductConditionDataTable $dataTable)
    {
        set_page_meta('Product Conidtions');
        return $dataTable->render('client.product_conditions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all system platforms
        $platforms = $this->systemEcomPlatformRepository->get();

        set_page_meta('Add Product Conidtion');
        return view('client.product_conditions.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProductConditionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductConditionRequest $request)
    {
        $data = $request->validated();

        if ($this->productConditionRepository->storeOrUpdate($data)) {
            flash('Product condition added successfully')->success();
        } else {
            flash('Product condition added fail')->error();
        }

        return redirect()->route('client.product-conditions.index');
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
        // Get all system platforms
        $platforms = $this->systemEcomPlatformRepository->get();
        $product_condition = $this->productConditionRepository->get($id);

        set_page_meta('Edit Product Conidtion');
        return view('client.product_conditions.edit', compact('platforms', 'product_condition'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductConditionRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->productConditionRepository->storeOrUpdate($data, $id)) {
            flash('Product condition updated successfully')->success();
        } else {
            flash('Product condition updated fail')->error();
        }

        return redirect()->route('client.product-conditions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->productConditionRepository->delete($id)) {
            flash('Product condition delete successfully')->success();
        } else {
            flash('Product condition delete fail')->error();
        }

        return redirect()->route('client.product-conditions.index');
    }
}
