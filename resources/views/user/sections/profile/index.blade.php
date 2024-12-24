@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Profile")])
@endsection 
@section('content')
<div class="row mb-20-none">
    <div class="col-xl-6 col-lg-6 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <div class="header-title">
                    <h4 class="title">{{ __("Profile Settings") }}</h4>
                </div>
                <div class="account-delate">
                    <button class="btn--base btn" data-bs-toggle="modal" data-bs-target="#delateModal">{{ __("Delate Account") }}</button>
                </div>
            </div>
            <div class="card-body profile-body-wrapper">
                <form class="card-form" method="POST" action="{{ setRoute('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")
                    <div class="profile-settings-wrapper">
                        <div class="preview-thumb profile-wallpaper">
                            <div class="avatar-preview">
                                <div class="profilePicPreview bg_img" data-background="{{ asset('public/frontend/images/element/profile-thumb.webp') }}"></div>
                            </div>
                        </div>
                        <div class="profile-thumb-content">
                            <div class="preview-thumb profile-thumb">
                                <div class="avatar-preview">
                                    <div class="profilePicPreview bg_img" data-background="{{ auth()->user()->userImage }}"></div>
                                </div>
                                <div class="avatar-edit">
                                    <input type='file' class="profilePicUpload" name="image" id="profilePicUpload2"
                                    accept=".png, .jpg, .jpeg, .webp, .svg">
                                    <label for="profilePicUpload2"><i class="las la-upload"></i></label>
                                </div>
                            </div>
                            <div class="profile-content">
                                <h6 class="username text--base">{{ auth()->user()->username }}</h6>
                                <ul class="user-info-list mt-md-2">
                                    <li><i class="las la-envelope"></i>{{ auth()->user()->email }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="profile-form-area">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => "First Name<span>*</span>",
                                    'name'          => "firstname",
                                    'placeholder'   => "Enter First Name...",
                                    'value'         => old('firstname',auth()->user()->firstname)
                                ])
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => "Last Name<span>*</span>",
                                    'name'          => "lastname",
                                    'placeholder'   => "Enter Last Name...",
                                    'value'         => old('lastname',auth()->user()->lastname)
                                ])
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>{{ __("Country") }}<span>*</span></label>
                                <select name="country" class="form--control select2-auto-tokenize country-select" data-placeholder="Select Country" data-old="{{ old('country',auth()->user()->address->country ?? "") }}"></select>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>{{ __("Phone") }}<span>*</span></label>
                                <div class="input-group">
                                    <div class="input-group-text phone-code">+{{ auth()->user()->mobile_code }}</div>
                                    <input class="phone-code" type="hidden" name="phone_code" value="{{ auth()->user()->mobile_code }}" />
                                    <input type="text" class="form--control" placeholder="Enter Phone ..." name="phone" value="{{ old('phone',auth()->user()->mobile) }}">
                                </div>
                                @error("phone")
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => "Address",
                                    'name'          => "address",
                                    'placeholder'   => "Enter Address...",
                                    'value'         => old('address',auth()->user()->address->address ?? "")
                                ])
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group"> 
                                @include('admin.components.form.input',[
                                    'label'         => "City",
                                    'name'          => "city",
                                    'placeholder'   => "Enter City...",
                                    'value'         => old('city',auth()->user()->address->city ?? "")
                                ])
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group"> 
                                @include('admin.components.form.input',[
                                    'label'         => "State",
                                    'name'          => "state",
                                    'placeholder'   => "Enter State...",
                                    'value'         => old('state',auth()->user()->address->state ?? "")
                                ])
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => "Zip Code",
                                    'name'          => "zip_code",
                                    'placeholder'   => "Enter Zip...",
                                    'value'         => old('zip_code',auth()->user()->address->zip ?? "")
                                ])
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <button type="submit" class="btn--base w-100">{{ __("Update") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __("Change Password") }}</h4>
            </div>
            <div class="card-body">
                <form class="card-form" action="{{ setRoute('user.profile.password.update') }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 form-group show_hide_password">
                            <label>{{ __("Current Password") }}<span>*</span></label>
                            <input type="password" class="form--control" name="current_password" placeholder="Enter Password...">
                            <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group show_hide_password-2">
                            <label>{{ __("New Password") }}<span>*</span></label>
                            <input type="password" class="form--control" name="password" placeholder="Enter Password...">
                            <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group show_hide_password-3">
                            <label>{{ __("Confirm Password") }}<span>*</span></label>
                            <input type="password" class="form--control" name="password_confirmation" placeholder="Enter Password...">
                            <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                        <button type="submit" class="btn--base w-100">{{ __("Change") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
<!-- Modal -->
<div class="modal fade" id="delateModal" tabindex="-1" aria-labelledby="delateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <h4 class="title">{{ __("Are you sure to delete your account") }}?</h4>
          <p>{{ __("If you do not think you will use") }} <span class="text--base">{{ $basic_settings->site_name }}</span> {{ __("again and like your account deleted. Keep in mind you will not be able to reactivate your account or retrieve any of the content or information you have added. If you would still like your account deleted, click “Delete Account”") }}.?</p>
          
        </div>
        <div class="modal-footer justify-content-between border-0">
            <button type="button" class="bg-danger" data-bs-dismiss="modal">{{ __("Close") }}</button>
            <form action="{{ route('user.delete.account') }}" method="POST">
            @csrf
            <button type="submit" class="btn--success">{{ __("Confirm") }}</button>
            </form>
        </div>
      </div>
    </div>
 </div>
@endsection

@push('script')
    <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function(){ 
            $("select[name=country]").change(function(){
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });
            countrySelect(".country-select",$(".country-select").siblings(".select2")); 
        });
    </script>
@endpush