<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EbayService;

class CategoryController extends Controller
{
    protected $ebayService;

    public function __construct()
    {
        $this->ebayService = new EbayService;
    }


    public function getDefaultCategory()
    {
      $defaultCategoryTreeId = $this->ebayService->getDefaultCategoryTreeId();
      $data = [
        'status' => 'success',
        'data' => $defaultCategoryTreeId
      ];

      return response()->json($data);
    }


    public function getCategoryTreeById()
    {
      $categoryTree = $this->ebayService->getCategoryTree();
      $data = [
        'status' => 'success',
        'data' => $categoryTree
      ];

      return response()->json($data);
    }
}
