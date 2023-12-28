<?php

namespace App\Http\Controllers\Client\BulkSku;

use Illuminate\Http\Request;
use App\Imports\BulkSkuImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class BulkSkuUploadsController extends Controller
{
    protected $ecomPlatformRepository, $productRepository;

    public function __construct(
        SystemEcomPlatformRepositoryInterface $ecomPlatformRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->ecomPlatformRepository = $ecomPlatformRepository;
        $this->productRepository = $productRepository;


        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Bulk_SKU_Upload'));
    }

    public function upload()
    {
        $platforms = $this->ecomPlatformRepository->get();

        set_page_meta('Bulk SKU Upload');
        return view('client.bluk_skus.upload', compact('platforms'));
    }

    public function uploadSubmit(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        Excel::import(new BulkSkuImport($this->ecomPlatformRepository, $this->productRepository), $request->file);

        flash('Bulk SKU uploaded successfully')->success();
        return redirect()->back();
    }
}
