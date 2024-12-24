@extends('user.layouts.master') 
@push('css')
    <style>
        .copy-button {
            cursor: pointer;
        }
    </style>
@endpush
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("API Key")])
@endsection 
@section('content') 
<div class="custom-card mt-10">
    <div class="dashboard-header-wrapper">
        <h4 class="title">{{ __("API Key") }}</h4>
    </div>
    @if (auth()->user()->developerApi)
    <div class="card-body">
        <div class="col-xl-12 col-lg-12 mb-3 d-md-flex align-items-center justify-content-between">
            <div class="mb-3 mb-md-0">
                @if (auth()->user()->developerApi->mode == payment_gateway_const()::ENV_PRODUCTION)
                    <span class="badge badge--success">{{ __("Production") }}</span>
                @else
                    <span class="badge badge--warning">{{ __("Sandbox") }}</span>
                @endif
            </div>
            <div class="">
                @if (auth()->user()->developerApi->mode == payment_gateway_const()::ENV_SANDBOX)
                    <button type="button" class="btn--base active-deactive-btn">{{ __("Production") }}</button>
                @else
                    <button type="button" class="btn--base active-deactive-btn">{{ __("Sandbox") }}</button>
                @endif
            </div>
        </div>
        <form class="card-form">
            <div class="row">
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>{{ __("Client Id") }}</label>
                    <div class="input-group">
                        <input type="text" class="form--control copiable" value="{{ auth()->user()->developerApi->client_id ?? "" }}" readonly>
                        <div class="input-group-text copy-button">
                            <i class="las la-copy"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>{{ __("Secret Key") }}</label>
                    <div class="input-group">
                        <input type="text" class="form--control copiable" value="{{ auth()->user()->developerApi->client_secret ?? "" }}" readonly>
                        <div class="input-group-text copy-button"><i class="las la-copy"></i></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endif
</div>
@endsection 
@push('script')
    <script>
        $(".active-deactive-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.developer.api.mode.update') }}";
            var target      = 1;
            var btnText     = $(this).text();
            var message     = `Are you sure change mode to <strong>${btnText}</strong>?`;
            openAlertModal(actionRoute,target,message,btnText,"POST");
        });
    </script>
@endpush