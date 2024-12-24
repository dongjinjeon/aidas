@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __('Select Recipient')])
@endsection  
@section('content')
<div class="add-recipient-btn text-end pb-3">
    <a href="{{ setRoute("user.receipient.create",['token' => $identifier]) }}" class="btn--base">+ {{ __("Add New Recipient") }} </a>
 </div>
 <form action="{{ setRoute('user.send.money.recipient.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="identifier" value="{{ $identifier }}"> 
    <input type="hidden" name="recipient" value="">
    @forelse ($receipients as $key => $item)
    <div class="dashboard-list-item-wrapper">
       <div class="dashboard-list-item receive d-flex justify-content-between">
           <div class="dashboard-list-left">
               <div class="dashboard-list-user-wrapper">
                   <div class="dashboard-list-user-profile">
                       <img src="{{  $item->receiver->userImage  }}">
                   </div>
                   <div class="dashboard-list-user-content">
                       <h5 class="title">{{ $item->fullName }}</h5>
                   </div>
               </div>
           </div>
            <div class="dashboard-list-button">
                <button type="button" class="btn btn--base select-btn" data-id="{{ $item->receiver->id }}">select</button>
            </div>
       </div>
       <div class="preview-list-wrapper">
           <div class="preview-list-item">
               <div class="preview-list-left">
                   <div class="preview-list-user-wrapper">
                       <div class="preview-list-user-icon">
                           <i class="las la-user"></i>
                       </div>
                       <div class="preview-list-user-content">
                           <span>{{ __("Name") }}</span>
                       </div>
                   </div>
               </div>
               <div class="preview-list-right">
                   <span>{{ $item->fullName }}</span>
                   <span>{{ $item->receiver->name }}</span>
               </div>
           </div>
           <div class="preview-list-item">
               <div class="preview-list-left">
                   <div class="preview-list-user-wrapper">
                       <div class="preview-list-user-icon">
                           <i class="las la-globe"></i>
                       </div>
                       <div class="preview-list-user-content">
                           <span>{{ __("Country") }}</span>
                       </div>
                   </div>
               </div>
               <div class="preview-list-right">
                   <span>{{ $item->country }}</span>
               </div>
           </div>
           <div class="preview-list-item">
               <div class="preview-list-left">
                   <div class="preview-list-user-wrapper">
                       <div class="preview-list-user-icon">
                           <i class="lab la-centercode"></i>
                       </div>
                       <div class="preview-list-user-content">
                           <span>{{ __("Zip Code") }}</span>
                       </div>
                   </div>
               </div>
               <div class="preview-list-right">
                   <span class="text--danger">{{ $item->zip_code }}</span>
               </div>
           </div>
           <div class="preview-list-item">
               <div class="preview-list-left">
                   <div class="preview-list-user-wrapper">
                       <div class="preview-list-user-icon">
                           <i class="las la-envelope"></i>
                       </div>
                       <div class="preview-list-user-content">
                           <span>{{ __("Email") }}</span>
                       </div>
                   </div>
               </div>
               <div class="preview-list-right">
                   <span>{{ $item->email }}</span>
               </div>
           </div>
       </div>
   </div>  
   @empty
   <div class="alert alert-primary" style="margin-top: 37.5px; text-align:center">{{ __('No recipient found!') }}</div>
   @endforelse
    <div class="money-tranasfer-btn text-center">
       <button class="btn--base w-100"> {{ __("Next") }} </button>
    </div>
 </form>
@endsection
@push('script')
    <script>
        $(".dashboard-list-item-wrapper .select-btn").click(function(){
            $(".dashboard-list-item-wrapper").removeClass("selected");
            $(this).parents(".dashboard-list-item-wrapper").toggleClass("selected");
            // Get the data-id attribute value
            var dataId = $(this).data("id"); 
            // Update the value of the input field with the data-id value
            $("input[name='recipient']").val(dataId);
        });
    </script>
@endpush