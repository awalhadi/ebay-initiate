<?php

namespace App\Http\Controllers\Client\CategoriesSkus;

use App\Http\Controllers\Controller;
use App\Repositories\CategorySku\CategorySkuRepositoryInterface;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoriesSkusController extends Controller
{
    public $categorySkuRepository;
    public $categoryRepository;

    public function __construct(
        CategorySkuRepositoryInterface $categorySkuRepository,
        ProductCategoryRepositoryInterface $categoryRepository
    ) {
        $this->categorySkuRepository = $categorySkuRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function categorySkus($category_id)
    {
        $category = $this->categoryRepository->get($category_id);
        $c_skus = $this->categorySkuRepository->getByCategory($category_id, ['platform', 'sku']);

        set_page_meta('Assigned SKU');
        return view('client.category_skus.list', compact('category', 'c_skus'));
    }


    public function assign(Request $request, $category_id)
    {
        $this->validate($request, [
            'selected_skus' => 'required|array'
        ]);

        foreach ($request->selected_skus as $item) {
            $data = [
                'category_id' => $category_id,
                'sku_id' => $item['id'],
                'ecom_platform_id' => $item['ecom_platform_id']
            ];

            $already_assign = $this->categorySkuRepository->getByCategorySku($data['category_id'], $data['sku_id']);
            if ($already_assign) continue;

            $this->categorySkuRepository->storeOrUpdate($data);
        }

        flash('Skus assign to category successfull')->success();

        return response()->json(['success' => true]);
    }

    public function detach(Request $request)
    {
        $this->validate($request, [
            'skus' => 'required|array'
        ]);
        $this->categorySkuRepository->deleteByIds($request->skus);

        flash('Skus detach from category successfull')->success();
        return redirect()->back();
    }
}
