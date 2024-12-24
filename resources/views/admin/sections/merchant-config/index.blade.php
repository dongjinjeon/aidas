@extends('admin.layouts.master')

@push('css')
<style>
    .fileholder {
        min-height: 374px !important;
    }

    .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,.fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view{
        height: 330px !important;
    }
</style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Merchant Config")])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __($page_title) }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.merchant.config.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        @include('admin.components.form.input-file',[
                            'label'             => "Image:",
                            'name'              => "image",
                            'class'             => "file-holder",
                            'old_files_path'    => files_asset_path("merchant-config"),
                            'old_files'         => $merchant_config->image ?? "",
                        ])
                    </div>

                    <div class="col-xl-8 col-lg-8">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input',[
                                'label'         => "Payment Gateway Name",
                                'label_after'   => "*",
                                'name'          => "name",
                                'value'         => old("name",$merchant_config->name ?? ""),
                            ])
                        </div>
    
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input',[
                                'label'         => "Version",
                                'label_after'   => "*",
                                'name'          => "version",
                                'value'         => old("version",$merchant_config->version ?? ""),
                            ])
                        </div>


                        <div class="col-xl-3 col-lg-3 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => 'Email Verification',
                                'label_after'   => "*",
                                'name'          => 'email_verify',
                                'value'         => old('email_verify',$merchant_config->email_verify ?? 0),
                                'options'       => ['Active' => 1,'Deactive' => 0],
                            ])
                        </div>


                    </div>

                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn',[
                            'class'         => "w-100 btn-loading",
                            'text'          => "Submit",
                            'permission'    => "admin.setup.sections.section.update"
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

@endpush