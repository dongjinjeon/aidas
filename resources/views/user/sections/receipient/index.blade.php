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
<div class="add-recipient-btn text-end pb-3">
    <a href="{{ setRoute("user.receipient.create") }}" class="btn--base">+ {{ __("Add New Recipient") }} </a>
 </div>
 @forelse ($receipients as $item)
 <div class="dashboard-list-item-wrapper">
    <div class="dashboard-list-item receive d-flex justify-content-between">
        <div class="dashboard-list-left">
            <div class="dashboard-list-user-wrapper">
                <div class="dashboard-list-user-profile">
                    <img src="{{ $item->receiver->userImage }}">
                </div>
                <div class="dashboard-list-user-content">
                    <h5 class="title">{{ $item->fullName }}</h5>
                </div>
            </div>
        </div>
        <div class="dashboard-list-button">
            <a href="{{ setRoute('user.receipient.edit',$item->id) }}" class="btn btn--base edit-modal-button recipient-btn"><i class="las la-pencil-alt"></i></a>
         <button type="button" class="btn--danger delete-recipient delate-btn" data-id="{{ $item->id }}" data-name="{{ $item->fullname }}" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="las la-trash-alt"></i></button>
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
@endsection
@push('script')
<script>
     $(".delate-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.receipient.delete') }}";
            var target      = $(this).data('id');
            var btnText = "Delete";
            var name = $(this).data('name');
            var message     = `Are you sure to delete <strong>${name}</strong>?`;
            openAlertModal(actionRoute,target,message,btnText,"DELETE");
        });
</script>
@endpush