@extends('admin.layouts.master')

@section('content')
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">General Settings</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                <li class="breadcrumb-item active">General Settings</li>
            </ol>
        </div>
    </div>
</div>

<form action="{{ route('admin.system-settings.update') }}" method="POST">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Application Currency</h5>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Currency Code</label>
                                <input type="text" name="currency[currency_code]" class="form-control"
                                    value="{{ $settings['currency']['currency_code'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Currency Symbol</label>
                                <input type="text" name="currency[currency_symbol]" class="form-control"
                                    value="{{ $settings['currency']['currency_symbol'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Free User Resource Sync Limit </h5>
                    <small class="text-muted">How many time user can sync data per day</small>
                    @csrf
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label>Product Sync Limit Per Day</label>
                                <input type="number" min="0" name="free_sync_limit_per_day[free_sync_limit_per_day.product]" class="form-control"
                                       value="{{ $settings['free_sync_limit_per_day']['free_sync_limit_per_day.product'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-sm-1">Time(s)</div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label>Order Sync Limit Per Day</label>
                                <input type="number" min="0" name="free_sync_limit_per_day[free_sync_limit_per_day.order]" class="form-control"
                                       value="{{ $settings['free_sync_limit_per_day']['free_sync_limit_per_day.order'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-sm-1">Time(s)</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
</form>

@endsection

@push('style')
@endpush

@push('script')
@endpush
