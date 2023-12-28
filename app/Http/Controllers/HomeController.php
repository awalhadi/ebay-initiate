<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\AppUserEcom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['users'] = User::where('business_id', Auth::user()->business_id)->count();
        $data['connected_ecoms'] = AppUserEcom::where('business_id', Auth::user()->business_id)->count();
        $data['products'] = Product::where('business_id', Auth::user()->business_id)->count();
        $data['orders'] = Order::where('business_id', Auth::user()->business_id)->count();

        $connected_ecoms = AppUserEcom::with('platform')->get();

        // Normal stock
        $normal_stocks = DB::table('products')
            ->select(DB::raw('count(ic_products.id) as total, ic_system_ecom_platforms.id as platform'))
            ->join('system_ecom_platforms', 'system_ecom_platforms.id', '=', 'products.ecom_platform_id')
            ->where('quantity', '>=', config('custom.product.low_stock'))
            ->where('business_id', Auth::user()->business_id)
            ->groupBy('ecom_platform_id')
            ->get()
            ->toArray();
        $temp = [];
        foreach ($normal_stocks as $stock) {
            $temp[$stock->platform] = $stock->total;
        }
        $normal_stocks = $temp;

        // Low stock
        $low_stocks = DB::table('products')
            ->select(DB::raw('count(ic_products.id) as total, ic_system_ecom_platforms.id as platform'))
            ->join('system_ecom_platforms', 'system_ecom_platforms.id', '=', 'products.ecom_platform_id')
            ->where('quantity', '>', 1)
            ->where('quantity', '<', config('custom.product.low_stock'))
            ->where('business_id', Auth::user()->business_id)
            ->groupBy('ecom_platform_id')
            ->get()
            ->toArray();

        $temp = [];
        foreach ($low_stocks as $stock) {
            $temp[$stock->platform] = $stock->total;
        }
        $low_stocks = $temp;

        // Out of stock
        $out_of_stocks = DB::table('products')
            ->select(DB::raw('count(ic_products.id) as total, ic_system_ecom_platforms.id as platform'))
            ->join('system_ecom_platforms', 'system_ecom_platforms.id', '=', 'products.ecom_platform_id')
            ->where('quantity', '<=', 0)
            ->where('business_id', Auth::user()->business_id)
            ->groupBy('ecom_platform_id')
            ->get()
            ->toArray();

        $temp = [];
        foreach ($out_of_stocks as $stock) {
            $temp[$stock->platform] = $stock->total;
        }
        $out_of_stocks = $temp;

        set_page_meta('Dashboard');
        return view('home', compact('data', 'connected_ecoms', 'low_stocks', 'out_of_stocks', 'normal_stocks'));
    }
}
