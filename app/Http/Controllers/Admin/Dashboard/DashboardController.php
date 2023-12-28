<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * DashbordController
 */
class DashboardController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // Count data for widgets
        $data['saas_users'] = User::where('user_type', User::USER_TYPE_APP_USER)->count();
        $data['clients_users'] = User::where('user_type', User::USER_TYPE_CLIENTS_USER)->count();
        $data['paid_users'] = User::where('subscription_type', User::SUBSCRIPTION_PAID)->where('user_type', User::USER_TYPE_APP_USER)->count();
        $data['free_users'] = User::where('subscription_type', User::SUBSCRIPTION_FREE)->where('user_type', User::USER_TYPE_APP_USER)->count();
        $data['total_packages'] = SubscriptionPackage::count();
        $data['total_products'] = Product::count();
        $data['total_orders'] = Order::count();
        $data['total_issues'] = Issue::count();

        set_page_meta('Dashboard');
        return view('admin.dashboard.index', compact('data'));
    }
}
