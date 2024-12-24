@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __('Sending Money Purpose')])
@endsection 
@section('content')
<div class="row mb-50">
    <div class="col-xl-12 col-lg-12 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __("Sending Money Purpose") }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ setRoute('user.send.money.confirm.submit') }}" method="POST" class="card-form">
                    @csrf
                    <input type="hidden" name="identifier" value="{{ $identifier }}">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 form-group">
                            <label>{{ __("Sending Purpose") }} <span>*</span></label>
                            <select class="nice-select" name="sending_purpose">
                                <option value="" selected disabled>{{ __("Select Purpose") }}</option>
                                @foreach ($sending_purpose ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-xl-6 col-lg-6 form-group">
                            <label>{{ __("Source Of Fund") }} <span>*</span></label>
                            <select class="nice-select" name="source_of_fund">
                                <option value="" selected disabled>{{ __("Select Source") }}</option>
                                @foreach ($source_of_found ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label>{{ __("Remarks") }} <span>({{ __("Optional") }})</span></label>
                            <textarea class="form--control" name="remarks" placeholder="Write Here..."></textarea>
                        </div>
                    </div>
                    <div class="conform-btn pt-2">
                        <button type="submit" class="btn--base w-100">{{ __("Confirm") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection