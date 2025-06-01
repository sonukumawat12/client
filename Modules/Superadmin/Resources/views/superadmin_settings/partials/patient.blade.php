<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active bg-light-green">
                            <a href="#health" data-toggle="tab">
                                <i class="fa fa-book"></i> <strong>@lang('superadmin::lang.patient')</strong>
                            </a>
                        </li>
                        <li class="bg-woocommerce">
                            <a href="#hospital" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                @lang('superadmin::lang.hospital')</strong>
                            </a>
                        </li>
                        <li class="bg-orange">
                            <a href="#pharmacy" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.pharmacy') </strong>
                            </a>
                        </li>
                        <li class="bg-purple">
                            <a href="#laboratory" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.laboratory') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="health">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            {!! Form::label('patient_prefix', __('superadmin::lang.patient_prefix') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </span>
                                                {!! Form::text('patient_prefix', $settings["patient_prefix"], ['class' =>
                                                'form-control','placeholder' => __('superadmin::lang.patient_prefix')]); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            {!! Form::label('patient_code_start_from', __('superadmin::lang.patient_code_start_from') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </span>
                                                {!! Form::text('patient_code_start_from', $settings["patient_code_start_from"], ['class' =>
                                                'form-control','placeholder' => __('superadmin::lang.patient_code_start_from')]); !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            {!! Form::label('upload_image_width', __('superadmin::lang.upload_image_width') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </span>
                                                {!! Form::text('upload_image_width', $settings["upload_image_width"], ['class' =>
                                                'form-control','placeholder' => __('superadmin::lang.upload_image_width')]); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            {!! Form::label('upload_image_height', __('superadmin::lang.upload_image_height') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </span>
                                                {!! Form::text('upload_image_height', $settings["upload_image_height"], ['class' =>
                                                'form-control','placeholder' => __('superadmin::lang.upload_image_height')]); !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane " id="hospital">
                            @include('superadmin::superadmin_settings.partials.hospital')
                        </div>
                        <div class="tab-pane " id="pharmacy">
                            @include('superadmin::superadmin_settings.partials.pharmacy')
                        </div>
                        <div class="tab-pane " id="laboratory">
                            @include('superadmin::superadmin_settings.partials.laboratory')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>