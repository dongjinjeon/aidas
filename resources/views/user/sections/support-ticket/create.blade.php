@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Support Tickets")])
@endsection

@section('content')
    <div class="row mb-20-none">
        <div class="col-xl-12 col-lg-12 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __("Add New Ticket") }}</h4>
                </div>
                <div class="card-body">
                    <form class="card-form" action="{{ route('user.support.ticket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label for="">{{ __("Name") }}<span>*</span></label>
                                <input type="text" name="name" value="{{ auth()->user()->fullname }}" class="form--control" placeholder="{{  __("Enter Name") }}" readonly>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label for="">{{ __("Email") }}<span>*</span></label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}" class="form--control" placeholder="{{  __("Enter Email") }}" readonly>
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => __("Subject")."<span>*</span>",
                                    'name'          => "subject",
                                    'placeholder'   => __("Enter Subject"),
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.textarea',[
                                    'label'         => __("Message")."<span class='text--base'>(Optional)</span>",
                                    'name'          => "desc",
                                    'placeholder'   => __("Write Here"),
                                ])
                            </div>
                            <div class="col-xl-4 col-lg-4 form-group">
                                <label>{{ __("Attachments") }}<span class='text--base'>({{ __("Optional") }})</span></label>
                                <div class="file-holder-wrapper">
                                    <input type="file" class="file-holder" name="attachment[]" id="fileUpload" data-height="130" accept="image/*" data-max_size="20" data-file_limit="15" multiple>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <button type="submit" class="btn--base w-100">{{ __("Add New") }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

    </script>
@endpush