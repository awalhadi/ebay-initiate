<?php

namespace App\Http\Controllers\Client\Dashboard;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\AppUserEcom;
use App\Models\OrderDetail;
use App\Models\ArrivingItem;
use App\Repositories\ArrivingItem\ArrivingItemRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ArrivingItemData;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\AppUserEcom\AppUserEcomRepositoryInterface;

/**
 * DashbordController
 */
class DashboardController extends Controller
{
    protected $userRepository;
    protected $appUserEcomRepository;
    protected $productRepository;
    protected $orderRepository;
    protected $arrivingItemRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AppUserEcomRepositoryInterface $appUserEcomRepository,
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository,
        ArrivingItemRepositoryInterface $arrivingItemRepository
    ) {
        $this->userRepository = $userRepository;
        $this->appUserEcomRepository = $appUserEcomRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->arrivingItemRepository = $arrivingItemRepository;
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // Count data for widgets
        $data['users'] = $this->userRepository->ownUsersCount();
        $data['connected_ecoms'] = $this->appUserEcomRepository->connectedEcomCount();
        $data['products'] = $this->productRepository->ownProductsCount();
        $data['orders'] = $this->orderRepository->ownOrdersCount();
        // User connected ecom
        $connected_ecoms = $this->appUserEcomRepository->connectedEcom(['platform']);

        // Normal stock
        $normal_stocks = $this->productRepository->platfromAndLevelWiseStockCount();
        // Low stock
        $low_stocks = $this->productRepository->platfromAndLevelWiseStockCount('low');
        // Out of stock
        $out_of_stocks = $this->productRepository->platfromAndLevelWiseStockCount('out');
        // Sales by year
        $sales_year = $this->orderRepository->salesByYear();
        // Most sale items count
        $most_sale_count = $this->orderRepository->mostSaleCount();
        // Today total sale
        $today_total_sale = $this->orderRepository->todayTotalSale();
        // Total items to ship
        $total_items_to_ship = $this->orderRepository->totalItemsToShip();
        // Most sale item
        $most_sale_items = $this->productRepository->getProductBySkus(array_keys($most_sale_count));
        // Recently delivered
        $recently_delivered_items = $this->arrivingItemRepository->recentlyDeliverdItem();



        set_page_meta('Dashboard');
        return view('home', compact(
            'data',
            'connected_ecoms',
            'low_stocks',
            'out_of_stocks',
            'normal_stocks',
            'sales_year',
            'most_sale_count',
            'most_sale_items',
            'recently_delivered_items',
            'today_total_sale',
            'total_items_to_ship'
        ));
    }
}
