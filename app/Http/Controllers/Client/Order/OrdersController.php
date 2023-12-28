<?php

namespace App\Http\Controllers\Client\Order;

use PDF;
use Illuminate\Http\Request;
use App\DataTables\OrderDataTable;
use App\Http\Controllers\Controller;
use App\DataTables\OrderShippedDataTable;
use App\DataTables\PendingShippngDataTable;
use App\Services\EcomPlatforms\EbayService;
use App\Services\EcomPlatforms\EcomOrderService;
use App\Services\EcomPlatforms\EcomPlatformService;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\SystemEcomPlatform\SystemEcomPlatformRepositoryInterface;

class OrdersController extends Controller
{
    protected $ecomPlatformService;
    protected $ebayService;
    protected $ecomOrderService;
    protected $systemEcomPlatformRepository;
    protected $orderRepository;

    public function __construct(
        EbayService $ebayService,
        EcomPlatformService $ecomPlatformService,
        EcomOrderService $ecomOrderService,
        SystemEcomPlatformRepositoryInterface $systemEcomPlatformRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->ecomPlatformService = $ecomPlatformService;
        $this->ebayService = $ebayService;
        $this->ecomOrderService = $ecomOrderService;
        $this->systemEcomPlatformRepository = $systemEcomPlatformRepository;
        $this->orderRepository = $orderRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Order') . '|' . saasPermission('Ecom_Order') . '|' .  saasPermission('Shipped_Orders') . '|' .  saasPermission('Pending_Shipping'));
    }

    public function pendingToShippedList(PendingShippngDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Pending_Shipping'));

        set_page_meta('Pending Shippng List');
        return $dataTable->render('client.orders.pending_shipping_list');
    }

    public function pendingToShipped()
    {
        $total_pending = $this->orderRepository->totalPendingToSipped();
        $platform_total_pending = $this->orderRepository->platformWisePendingToShipped();
        $connected_platforms = $this->ecomPlatformService->userConnectedEcoms(null, ['platform']);

        set_page_meta('Pending Shipping Orders');
        return view('client.orders.pending_shipping', compact('total_pending', 'platform_total_pending', 'connected_platforms'));
    }

    public function shippedOrders(OrderShippedDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Shipped_Orders'));

        set_page_meta('Shipped Orders');
        return $dataTable->render('client.orders.shipped');
    }

    public function shippedOrderDetails($order_id)
    {
        $details =  $this->orderRepository->shippedOrderDetails($order_id, ['details', 'platform']);
        $data = $details['order'];
        $item_details = $details['item_details'];

        set_page_meta('Order Details');
        return view('client.orders.shipped_details', compact('data', 'item_details'));
    }

    public function index(OrderDataTable $dataTable)
    {
        // Check saas user permission
        checkPermission(saasPermission('Ecom_Order'));

        $platforms = $this->ecomPlatformService->userConnectedEcoms()->pluck('ecom_platform_id')->toArray();
        $connected_platforms = $this->systemEcomPlatformRepository->platformsWhereUserConnected($platforms);

        set_page_meta('Orders');
        return $dataTable->render('client.orders.index', compact('connected_platforms'));
    }


    public function sync(Request $request, $platform)
    {
        $this->ecomOrderService->syncOrderWithDB([$platform]);

        flash("Order is fetching from $platform.")->success();
        return redirect()->route('client.orders.index');
    }

    public function show($id)
    {
        $data = $this->orderRepository->get($id, ['details', 'platform']);

        set_page_meta('Order Details');
        return view('client.orders.show', compact('data'));
    }

    public function invoiceDownload($id)
    {
        $data = $this->orderRepository->get($id, ['details', 'platform']);

        //    return view('client.orders.pdf.invoice', compact('data'));
        $pdf = PDF::loadView('client.orders.pdf.invoice', ['data' => $data]);
        return $pdf->download('Order-invoice-' . $data->id . '-' . $data->order_reference_id . '.pdf');
    }
}
