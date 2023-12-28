<?php

namespace App\Http\Controllers\Client\Subscription;

use PDF;
use Exception;
use App\Events\SubscriptionSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubscriptionRequest;
use App\Services\AmazonPay\AmazonPayService;
use App\DataTables\SubscriptionHistoryDataTable;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Billing\BillingRepositoryInterface;
use App\Repositories\SubPack\SubPackRepositoryInterface;
use App\Repositories\UserSubscription\UserSubscriptionRepositoryInterface;

/**
 * SubscriptionsController
 */
class SubscriptionsController extends Controller
{

    protected $subPackRepository;
    protected $userSubscriptionRepository;
    protected $userRepository;
    protected $billingRepository;
    protected $amazonPayService;

    /**
     * __construct
     *
     * @param  mixed $subPackRepository
     * @return void
     */
    public function __construct(
        SubPackRepositoryInterface $subPackRepository,
        UserSubscriptionRepositoryInterface $userSubscriptionRepository,
        UserRepositoryInterface $userRepository,
        BillingRepositoryInterface $billingRepository,
        AmazonPayService $amazonPayService
    ) {
        $this->subPackRepository = $subPackRepository;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
        $this->userRepository = $userRepository;
        $this->billingRepository = $billingRepository;
        $this->amazonPayService = $amazonPayService;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Subscription_Plan'));
    }

    /**
     * packageList
     *
     * @return void
     */
    public function packageList()
    {
        $packages = $this->subPackRepository->getActiveData();
        $current_subs = $this->userSubscriptionRepository->getLastSub(['package_info']);

        set_page_meta('Subscription Plan');
        return view('client.subscriptions.packages.index', compact('packages', 'current_subs'));
    }

    /**
     * checkout
     *
     * @param  mixed $packageId
     * @return void
     */
    public function checkout($packageId)
    {
        // return amazonPaySecret();
        //  return $_SERVER["DOCUMENT_ROOT"] . '/amazon/private.pem';
        // Amazon test
        // $amazonpay_config = array(
        //     'public_key_id' => 'AHQFEVJWJAA5IVOODKT2TXUZ',
        //     'private_key'   => amazonPaySecret(),
        //     'region'        => 'US',
        //     'sandbox'       => true
        // );

        // $client = new \Amazon\Pay\API\Client($amazonpay_config);
        // $payload = '{"storeId":"amzn1.application-oa2-client.4aff6253e3ef4c4097e9f300766cf2bf","webCheckoutDetails":{"checkoutReviewReturnUrl":"http://127.0.0.1:8000/"},"chargePermissionType":"Recurring","recurringMetadata":{"frequency":{ unit":"Month","value":"1"},"amount":{"amount":"30","currencyCode":"USD"}}}';
        // $signature = $client->generateButtonSignature($payload);

        // $headers = array('x-amz-pay-Idempotency-Key' => uniqid());
        // $requestResult = [
        //     'error' => 1,
        //     'msg' => 'Error. Can not create checkout session.',
        //     'signature' => null,
        //     'payload' => null
        // ];

        // $client = new \Amazon\Pay\API\Client($amazonpay_config);

        //$payload2 = '{"storeId":"amzn1.application-oa2-client.4aff6253e3ef4c4097e9f300766cf2bf","webCheckoutDetails":{"checkoutReviewReturnUrl":"http://127.0.0.1:8000/"},"chargePermissionType":"Recurring","recurringMetadata":{"frequency":{ unit":"Month","value":"1"},"amount":{"amount":"30","currencyCode":"USD"}}}';

        // $payload = array(
        //     "webCheckoutDetails" => array(
        //         "checkoutReviewReturnUrl" => "https://127.0.0.1:8000/amazon-pay/checkout-review",
        //         "checkoutResultReturnUrl" => "https://www.example.com/result"
        //     ),
        //     "storeId" => "amzn1.application-oa2-client.4aff6253e3ef4c4097e9f300766cf2bf",
        //     "chargePermissionType" => "Recurring",
        //     "recurringMetadata" => [
        //         "frequency" => [
        //             "unit" => "Month",
        //             "value" => "1"
        //         ],
        //         "amount" => [
        //             "amount" => "30",
        //             "currencyCode" => "USD"
        //         ]
        //     ],
        // );



        //  $resultCheckOut = $client->createCheckoutSession($payload, $headers);
        //  $checkoutSession = json_decode($resultCheckOut['response']);
        // $signature = $client->generateButtonSignature($payload);

        // if ($resultCheckOut['status'] !== 201) {
        //     return json_encode($requestResult, true);
        // } else {
        //     $requestResult = [
        //         'error' => 0,
        //         'msg' => null,
        //         'signature' => $signature,
        //         'payload' => $payload
        //     ];
        //     // return json_encode($requestResult);
        // }


        // $checkoutSession = $payload;
        // $payload = json_encode($payload);

        $package = $this->subPackRepository->get($packageId);



        try {
            $data = [
                'package_id' => $package->id,
                'package_type' => $package->package_type,
                'package_price' => $package->package_price,
            ];
            $signature_data = $this->amazonPayService->signature($data);
            if ($signature_data) return $signature_data;
            throw new Exception('File not found');
        } catch (\Throwable $th) {
            $signature_data = [
                'payload' => '',
                'signature' => ''
            ];
        }


        return view('client.subscriptions.packages.checkout', compact('package', 'signature_data'));
    }

    /**
     * pay
     *
     * @param  mixed $request
     * @param  mixed $packageId
     * @return void
     */
    public function pay(SubscriptionRequest $request, $packageId)
    {

        // TODO: verify user subscription
        $package = $this->subPackRepository->get($packageId);

        $data = [
            'package_type' => $package->package_type,
            'package_id' => $package->id,
            'package_price' => $package->package_price,
            'package' => $package,
            'pay_by' => $request->pay_by,
            'paypal_sub_id' => $request->paypal_sub_id,
            'stripe_price_id' => $package->stripe_price_id,
            'stripe_product_id' => $package->stripe_product_id,
            'card_number' => $request->card_number,
            'exp_month' => $request->exp_month,
            'exp_year' => $request->exp_year,
            'cvc_code' => $request->cvc_code,
        ];

        if ($subscription_data = $this->userSubscriptionRepository->storeOrUpdate($data)) {

            try {
                $data = $subscription_data;
                $user = $this->userRepository->getAppUsers(Auth::user()->business_id);
                $billing = $this->billingRepository->getBillingAddress(null, ['country', 'state', 'city']);
                event(new SubscriptionSuccess($data, $user, $billing));
            } catch (\Throwable $th) {
                throw $th;
            }

            flash('Subscription successfull')->success();
        } else {
            flash('Invalid card info. Subscription fail')->error();
        }

        return redirect()->route('client.subscription.packageList');
    }

    /**
     * history
     *
     * @return void
     */
    public function history(SubscriptionHistoryDataTable $dataTable)
    {
        set_page_meta('Subscription History');
        return $dataTable->render('client.subscriptions.packages.history');
    }

    public function invoice($id)
    {
        $data = $this->userSubscriptionRepository->get($id);
        $user = $this->userRepository->getAppUsers(Auth::user()->business_id);
        $billing = $this->billingRepository->getBillingAddress(null, ['country', 'state', 'city']);

        set_page_meta('Billing Invoice');
        return view('client.subscriptions.packages.invoice', compact('data', 'user', 'billing'));
    }

    public function invoiceDownload($id)
    {
        $data = $this->userSubscriptionRepository->get($id);
        $user = $this->userRepository->getAppUsers(Auth::user()->business_id);
        $billing = $this->billingRepository->getBillingAddress(null, ['country', 'state', 'city']);

        // return view('client.subscriptions.packages.pdf.invoice', compact('data', 'user', 'billing'));

        $pdf = PDF::loadView('client.subscriptions.packages.pdf.invoice', ['data' => $data, 'user' => $user, 'billing' => $billing]);
        return $pdf->download('Package-invoice-' . $data->id . '-' . Auth::user()->business_id . '.pdf');
    }
}
