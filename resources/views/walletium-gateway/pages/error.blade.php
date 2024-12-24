@extends('walletium-gateway.layouts.master')

@push('css')
    
@endpush

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div class="row">
        <div class="col-4 ptb-80 mx-auto text-center">
            <section class="account-section payment">
                <div class="right">
                    <div class="account-area">
                        <div class="account-header text-center">
                            @if ($data['logo'] && $data['logo'] != "")
                                <a class="site-logo" href="javascript:void(0)"><img src="{{ $data['logo'] }}" alt="logo"></a>
                            @endif
                            <h4 class="title mt-4">{{ $data['title'] ?? "" }}</h4>
                            <p>{{ $data['subtitle'] ?? "" }}</p>
                        </div>
                        <div class="return-btn text-center">
                            <a href="{{ $data['link'] ?? "javascript:void(0)" }}" class="btn--base">{{ __($data['button_text'] ?? "") }}</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection 