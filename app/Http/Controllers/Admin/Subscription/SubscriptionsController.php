<?php

namespace App\Http\Controllers\Admin\Subscription;

use PDF;
use Illuminate\Http\Request;
use App\Events\SubscriptionSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserRepositoryInterface;
use App\DataTables\Admin\SubscriptionInvoiceDataTable;
use App\Repositories\Billing\BillingRepositoryInterface;
use App\Repositories\UserSubscription\UserSubscriptionRepositoryInterface;

class SubscriptionsController extends Controller
{
    protected $userSubscriptionRepository;
    protected $userRepository;
    protected $billingRepository;

    /**
     * __construct
     *
     * @param  mixed $subPackRepository
     * @return void
     */
    public function __construct(
        UserSubscriptionRepositoryInterface $userSubscriptionRepository,
        UserRepositoryInterface $userRepository,
        BillingRepositoryInterface $billingRepository
    ) {
        $this->userSubscriptionRepository = $userSubscriptionRepository;
        $this->userRepository = $userRepository;
        $this->billingRepository = $billingRepository;
    }

    public function invoices(SubscriptionInvoiceDataTable $dataTable)
    {
        set_page_meta('Subscription Invoices');
        return $dataTable->render('admin.subscription_invoices.index');
    }

    public function invoiceDetails($id)
    {
        $data = $this->userSubscriptionRepository->get($id);
        $user = $this->userRepository->getAppUsers($data->business_id);
        $billing = $this->billingRepository->getBillingAddress($data->business_id, ['country', 'state', 'city']);

        set_page_meta('Subscription Invoice Details');
        return view('admin.subscription_invoices.show', compact('data', 'user', 'billing'));
    }

    public function invoiceDownload($id)
    {
        $data = $this->userSubscriptionRepository->get($id);
        $user = $this->userRepository->getAppUsers($data->business_id);
        $billing = $this->billingRepository->getBillingAddress($data->business_id, ['country', 'state', 'city']);

        // return view('admin.subscription_invoices.pdf.invoice', compact('data', 'user', 'billing'));

        $pdf = PDF::loadView('admin.subscription_invoices.pdf.invoice', ['data' => $data, 'user' => $user, 'billing' => $billing]);
        return $pdf->download('Package-invoice-' . $data->id . '-' . $data->business_id . '.pdf');
    }

    public function invoiceSend($id)
    {
        $data = $this->userSubscriptionRepository->get($id);
        $user = $this->userRepository->getAppUsers($data->business_id);
        $billing = $this->billingRepository->getBillingAddress($data->business_id, ['country', 'state', 'city']);
        event(new SubscriptionSuccess($data, $user, $billing));

        flash('Invoice send successfully')->success();
        return redirect()->route('admin.subscriptions.invoices');
    }
}
