<?php

namespace App\Http\Controllers\Client\Payment;

use App\Http\Controllers\Controller;
use App\Repositories\SubPack\SubPackRepositoryInterface;
use App\Repositories\UserSubscription\UserSubscriptionRepositoryInterface;
use App\Services\AmazonPay\AmazonPayService;
use Illuminate\Http\Request;

class AmazonPayController extends Controller
{
    protected $amazonPayService;
    protected $subPackRepository;
    protected $userSubscriptionRepository;

    public function __construct(
        AmazonPayService $amazonPayService,
        SubPackRepositoryInterface $subPackRepository,
        UserSubscriptionRepositoryInterface $userSubscriptionRepository
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->subPackRepository = $subPackRepository;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
    }

    public function checkoutReview(Request $request)
    {
        $csid = $request->amazonCheckoutSessionId;

        $checkout_session = json_decode($this->amazonPayService->getCheckoutSession($csid));
        $package_id = $checkout_session->merchantMetadata->customInformation;
        $package = $this->subPackRepository->get($package_id);

        $data = [
            'package_id' => $package->id,
            'package_type' => $package->package_type,
            'package_price' => $package->package_price,
        ];

        $result = $this->amazonPayService->updateCheckoutSession($csid, $data);
        $redirect = json_decode($result)->webCheckoutDetails->amazonPayRedirectUrl;

        return redirect($redirect);

        // return view('client.amazon_pay.review', compact('csid'));
    }

    public function checkoutResult(Request $request)
    {
        $csid = $request->amazonCheckoutSessionId;

        if(!$csid) abort(404);

        $checkout_session = json_decode($this->amazonPayService->getCheckoutSession($csid));
        $package_id = $checkout_session->merchantMetadata->customInformation;
        $package = $this->subPackRepository->get($package_id);
        // $package = $this->subPackRepository->get(9);

        $data = [
            'package_id' => $package->id,
            'package_type' => $package->package_type,
            'package_price' => $package->package_price,
        ];

        $result = $this->amazonPayService->completeCheckoutSession($csid, $data);
        $cp_id = json_decode($result)->chargePermissionId;

        $result = $this->amazonPayService->charge($cp_id, $data);
        // $result = $this->amazonPayService->charge('C01-7029200-4337775', $data);


        $result_data = json_decode($result);
        $c_id = $result_data->chargeId;


        $data = [
            'package_type' => $package->package_type,
            'package_id' => $package->id,
            'package_price' => $package->package_price,
            'package' => $package,
            'pay_by' => 'amazon_pay',
            'charge_permission_id' => $cp_id,
            'charge_id' => $c_id
        ];

        if ($this->userSubscriptionRepository->storeOrUpdate($data)) {
            flash('Subscription successful')->success();
        } else {
            flash('Invalid card info. Subscription fail')->error();
        }

        return redirect()->route('client.subscription.packageList');
    }
}
