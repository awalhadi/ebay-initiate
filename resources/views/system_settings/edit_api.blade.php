{{-- @extends('layouts.master') --}}

{{-- @section('content') --}}
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">API Settings</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                <li class="breadcrumb-item active">API Settings</li>
            </ol>
        </div>
    </div>
</div>

<form action="{{ route('admin.system-settings.update') }}" method="POST">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Paypal</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Base URL</label>
                                <input type="text" name="paypal[paypal.baseUrl]" class="form-control"
                                    value="{{ $settings['paypal']['paypal.baseUrl'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Client ID</label>
                                <input type="text" name="paypal[paypal.clientId]" class="form-control"
                                    value="{{ $settings['paypal']['paypal.clientId'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Client Secret</label>
                                <input type="text" name="paypal[paypal.secret]" class="form-control"
                                    value="{{ $settings['paypal']['paypal.secret'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Product ID</label>
                                <input type="text" name="paypal[paypal.productId]" class="form-control"
                                    value="{{ $settings['paypal']['paypal.productId'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Stripe</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Public Key</label>
                                <input type="text" name="stripe[stripe.public_key]" class="form-control"
                                    value="{{ $settings['stripe']['stripe.public_key'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="text" name="stripe[stripe.secret_key]" class="form-control"
                                    value="{{ $settings['stripe']['stripe.secret_key'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Amazon Pay</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Public Key ID</label>
                                <input type="text" name="amazon_pay[amazon_pay.public_key_id]" class="form-control"
                                    value="{{ $settings['amazon_pay']['amazon_pay.public_key_id'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Private Key (.pem) File Path</label>
                                <input type="text" name="amazon_pay[amazon_pay.private_key]" class="form-control"
                                    value="{{ $settings['amazon_pay']['amazon_pay.private_key'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Store ID</label>
                                <input type="text" name="amazon_pay[amazon_pay.store_id]" class="form-control"
                                    value="{{ $settings['amazon_pay']['amazon_pay.store_id'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Marchant ID</label>
                                <input type="text" name="amazon_pay[amazon_pay.merchant_id]" class="form-control"
                                    value="{{ $settings['amazon_pay']['amazon_pay.merchant_id'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Region</label>

                                <select name="amazon_pay[amazon_pay.region]" class="form-control">
                                    @foreach (amazonPayRegions() as $item)
                                        <option value="{{ $item }}" @if($item == $settings['amazon_pay']['amazon_pay.region']) selected @endif>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Sandbox</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="status_yes" value="true"
                                        name="amazon_pay[amazon_pay.sandbox]" class="custom-control-input"
                                        @if(isset($settings['amazon_pay']['amazon_pay.sandbox']) &&
                                        $settings['amazon_pay']['amazon_pay.sandbox']=='true' ) checked="" @endif>
                                    <label class="custom-control-label" for="status_yes">True</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="status_no" value="false"
                                        name="amazon_pay[amazon_pay.sandbox]" class="custom-control-input"
                                        @if(isset($settings['amazon_pay']['amazon_pay.sandbox']) &&
                                        $settings['amazon_pay']['amazon_pay.sandbox']=='false' ) checked="" @endif>
                                    <label class="custom-control-label" for="status_no">False</label>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Ebay</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>ENV</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="ebay_env" value="sandbox"
                                        name="ebay[ecom.ebay.env]" class="custom-control-input"
                                        @if(isset($settings['ebay']['ecom.ebay.env']) &&
                                        $settings['ebay']['ecom.ebay.env']=='sandbox' ) checked="" @endif>
                                    <label class="custom-control-label" for="ebay_env">Sandbox</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="evay_env_p" value="production"
                                        name="ebay[ecom.ebay.env]" class="custom-control-input"
                                        @if(isset($settings['ebay']['ecom.ebay.env']) &&
                                        $settings['ebay']['ecom.ebay.env']=='production' ) checked="" @endif>
                                    <label class="custom-control-label" for="evay_env_p">Production</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label>Marketplace</label>

                                <select class="form-control" name="ebay[ebay.marketplace]">
                                    @foreach (ebayMarketplaces() as $item)
                                        <option value="{{ $item }}" @if($item == $settings['ebay']['ebay.marketplace']) selected @endif>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label>URL</label>
                                   <input type="text" name="ebay[ecom.ebay.url]" class="form-control"
                                    value="{{ $settings['ebay']['ecom.ebay.url'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Auth URL</label>
                                   <input type="text" name="ebay[ecom.ebay.auth_url]" class="form-control"
                                    value="{{ $settings['ebay']['ecom.ebay.auth_url'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Client ID</label>
                                   <input type="text" name="ebay[ecom.ebay.client_id]" class="form-control"
                                    value="{{ $settings['ebay']['ecom.ebay.client_id'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Client Secret</label>
                                   <input type="text" name="ebay[ecom.ebay.client_secret]" class="form-control"
                                    value="{{ $settings['ebay']['ecom.ebay.client_secret'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Redirect URI</label>
                                   <input type="text" name="ebay[ecom.ebay.redirect_uri]" class="form-control"
                                    value="{{ $settings['ebay']['ecom.ebay.redirect_uri'] ?? '' }}">
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Etsy</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" name="etsy[ecom.etsy_url]" class="form-control"
                                    value="{{ $settings['etsy']['ecom.etsy_url'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Shopify</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>API Version</label>
                                <input type="text" name="shopify[shopify.api_version]" class="form-control"
                                    value="{{ $settings['shopify']['shopify.api_version'] ?? '' }}">
                                    <small class="text-muted">If you change the version. Some functions may not work.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Walmart</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" name="walmart[ecom.walmart_url]" class="form-control"
                                    value="{{ $settings['walmart']['ecom.walmart_url'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Service Name</label>
                                <input type="text" name="walmart[ecom.walmart_service_name]" class="form-control"
                                    value="{{ $settings['walmart']['ecom.walmart_service_name'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
</form>

{{-- @endsection

@push('style')
@endpush

@push('script')
@endpush --}}