<?php

namespace App\Http\Controllers\Client\MergeSku;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MergeSku\MergeSkuRepositoryInterface;
use App\Repositories\CustomSku\CustomSkuRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class MergeSkusController extends Controller
{

    protected $ecomPlatformRepository;
    protected $customSkuRepository;
    protected $mergeSkuRepository;

    public function __construct(
        SystemEcomPlatformRepositoryInterface $ecomPlatformRepository,
        CustomSkuRepositoryInterface $customSkuRepository,
        MergeSkuRepositoryInterface $mergeSkuRepository
    ) {
        $this->ecomPlatformRepository = $ecomPlatformRepository;
        $this->customSkuRepository = $customSkuRepository;
        $this->mergeSkuRepository = $mergeSkuRepository;


        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Merge_SKU'));
    }

    public function mergeSkuList($custom_sku_id)
    {
        $custom_sku = $this->customSkuRepository->get($custom_sku_id);
        $m_skus = $this->mergeSkuRepository->getByCustomSku($custom_sku_id, ['platform', 'sku']);

        set_page_meta('Merged SKU');
        return view('client.merge_skus.list', compact('custom_sku', 'm_skus'));
    }

    public function mergeSku()
    {
        $platforms = $this->ecomPlatformRepository->get();
        $custom_skus = $this->customSkuRepository->get();

        set_page_meta('Merge SKU');
        return view('client.merge_skus.index', compact('platforms', 'custom_skus'));
    }

    public function assign(Request $request, $custom_sku_id)
    {
        $this->validate($request, [
            'selected_skus' => 'required|array'
        ]);
        foreach ($request->selected_skus as $item) {
            $data = [
                'custom_sku_id' => $custom_sku_id,
                'sku_id' => $item['id'],
                'ecom_platform_id' => $item['ecom_platform_id']
            ];

            $already_assign = $this->mergeSkuRepository->getByCustomSkuSku($data['custom_sku_id'], $data['sku_id']);
            if ($already_assign) continue;

            $this->mergeSkuRepository->storeOrUpdate($data);
        }

        flash('Sku merge successfull')->success();

        return response()->json(['success' => true]);
    }


    public function detach(Request $request)
    {
        $this->validate($request, [
            'skus' => 'required|array'
        ]);
        $this->mergeSkuRepository->deleteByIds($request->skus);

        flash('Skus detach from merged successfull')->success();
        return redirect()->back();
    }
}
