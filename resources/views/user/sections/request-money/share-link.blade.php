@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection 
@section('content')
<div class="row justify-content-center mb-20-none">
    <div class="col-xl-6 col-lg-6 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __("Share Request Money") }}</h4>
            </div>
            <div class="card-body">
                <div class="card-form">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label>{{ __("Copy your request link") }}</label>
                            <div class="input-group">
                                <input type="text" class="form--control" value="{{ $requestMoneyData->link }}" id="copyInput" readonly>
                                <button class="input-group-text" onclick="copyText()">Copy</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                        <a href="{{ setRoute('user.request.money.index') }}" class="btn--base w-100">Done</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection
@push('script')
<script>
    function copyText() {
        // Get the input element
        var copyInput = document.getElementById("copyInput");

        // Select the text in the input
        copyInput.select();
        copyInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text to the clipboard
        document.execCommand("copy");

        // Display a message 
        throwMessage('success',["Copied"]);
    }
</script>
@endpush