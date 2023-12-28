<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Events\NewUserRegister;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CommonNotification;
use App\Events\ShippingProviderConnected;
use Illuminate\Support\Facades\Notification;
use App\Services\EcomPlatforms\WalmartService;
use App\Services\EcomPlatformApis\EbayApiService;
use App\Services\ShippingApis\EasyshipApiService;
use App\Services\EcomPlatformApis\ShopifyApiService;
use App\Services\EcomPlatformApis\WalmartApiService;
use App\Repositories\UserSubscription\UserSubscriptionRepositoryInterface;

class TestController extends Controller
{
    protected $ebayApiService;
    protected $shopifyApiService;
    protected $walmartApiService;
    protected $userSubscriptionRepository;
    protected $easyshipApiService;

    public function __construct(
        EbayApiService                      $ebayApiService,
        ShopifyApiService                   $shopifyApiService,
        WalmartService                      $walmartService,
        WalmartApiService                   $walmartApiService,
        UserSubscriptionRepositoryInterface $userSubscriptionRepository,
        EasyshipApiService $easyshipApiService
    ) {
        $this->ebayApiService = $ebayApiService;
        $this->shopifyApiService = $shopifyApiService;
        $this->walmartService = $walmartService;
        $this->walmartApiService = $walmartApiService;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
        $this->easyshipApiService = $easyshipApiService;
    }


    public function easyshipTest()
    {
        return  $this->easyshipApiService->getShipment('ESSG1905565');
    }


    function amazonPay()
    {
        return file_get_contents('\storage\amazon_pay\private.pem');
    }

    public function shopifyTest()
    {
        return $this->shopifyApiService->doTest();
        // $start_date = Carbon::now()->subDays(2)->format('Y-m-d');
        // $end_date = Carbon::now()->format('Y-m-d');

        // return $this->last2daysData($start_date, $end_date);

        //        $url = $this->shopifyApiService->getOrderFetchInitialUrl();
        //        return $this->shopifyApiService->getOrdersForJob($url, Auth::user()->business_id);
        //        return  $this->shopifyApiService->getProductList();
    }

    public function last2daysData($start_date, $end_date)
    {
        $data = Order::whereDate('order_creation_date', '>=', $start_date)
            ->whereDate('order_creation_date', '<=', $end_date)
            ->get();

        $first_order = Order::orderBy('order_creation_date', 'ASC')->first();

        if (!count($data)) {
            $start_date = Carbon::parse($start_date)->subDays(2)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->subDays(2)->format('Y-m-d');

            if ($start_date < $first_order->order_creation_date) {
                return $data;
            } else {
                return $this->last2daysData($start_date, $end_date);
            }
        }
        return $data;
    }

    public function walmartProducts()
    {
        // dd($this->walmartService->getOrder('6814799328397'));
        // return ;
        return $this->walmartService->getOrders();
    }

    public function renewSubs()
    {
        return $this->userSubscriptionRepository->renewSubs();
    }

    public function notiCheck()
    {
        Notification::send(Auth::user(), new CommonNotification([
            'title' => 'Test title',
            'message' => 'Test message',
        ]));
        // Notification::send(Auth::user(), new UserRegister(Auth::user()));


        // event(new NewUserRegister(Auth::user()));
    }

    public function ebay()
    {
        return $this->ebayApiService->getDeal();
    }

    protected function dotest()
    {
        event(new ShippingProviderConnected(['provider'=>'Easyship'], Auth::user()));
        return 'dkddkdkddkdkdkdkdkdkdkdkdkdkdkdkdkdkdkdkdk';
    }
}
