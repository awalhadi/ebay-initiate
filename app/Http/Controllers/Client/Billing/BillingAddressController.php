<?php

namespace App\Http\Controllers\Client\Billing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BillingAddressRequest;
use App\Repositories\Billing\BillingRepositoryInterface;
use App\Repositories\Country\CountryRepositoryInterface;

/**
 * BillingAddressController
 */
class BillingAddressController extends Controller
{
    protected $countryRepository;
    protected $billingRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        CountryRepositoryInterface $countryRepository,
        BillingRepositoryInterface $billingRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->billingRepository = $billingRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Billing') . '|' .  saasPermission('Billing_Address'));
    }

    /**
     * edit
     *
     * @return void
     */
    public function edit()
    {
        $countries = $this->countryRepository->get();
        $billing_address = $this->billingRepository->getBillingAddress();

        set_page_meta('Billing Address');
        return view('client.billings.address.edit', compact('countries', 'billing_address'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(BillingAddressRequest $request)
    {
        $data = $request->validated();

        if ($this->billingRepository->updateBillingAddress($data)) {
            flash('Billing address updated successfully')->success();
        } else {
            flash('Billing address updated fail')->error();
        }

        return back();
    }

    public function billingHistory()
    {
    }
}
