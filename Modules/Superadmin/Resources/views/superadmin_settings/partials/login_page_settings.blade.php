<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active bg-light-green">
                            <a href="#login" data-toggle="tab">
                                <i class="fa fa-book"></i> <strong>@lang('superadmin::lang.login')</strong>
                            </a>
                        </li>
                        <li class="bg-woocommerce">
                            <a href="#banners" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.banners') </strong>
                            </a>
                        </li>
                        <li class="bg-orange">
                            <a href="#gifts" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.give_away_gifts') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="login">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_member_register_btn_login_page', 1,
                                                    !empty($settings["enable_member_register_btn_login_page"]) ?
                                                    (int)$settings["enable_member_register_btn_login_page"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!}
                                                    {{ __( 'superadmin::lang.enable_member_register_btn_login_page' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_patient_register_btn_login_page', 1,
                                                    !empty($settings["enable_patient_register_btn_login_page"]) ?
                                                    (int)$settings["enable_patient_register_btn_login_page"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!}
                                                    {{ __( 'superadmin::lang.enable_patient_register_btn_login_page' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_visitor_register_btn_login_page', 1,
                                                    !empty($settings["enable_visitor_register_btn_login_page"]) ?
                                                    (int)$settings["enable_visitor_register_btn_login_page"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!}
                                                    {{ __( 'superadmin::lang.enable_visitor_register_btn_login_page' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_register_btn_login_page', 1,
                                                    !empty($settings["enable_register_btn_login_page"]) ?
                                                    (int)$settings["enable_register_btn_login_page"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!}
                                                    {{ __( 'superadmin::lang.enable_register_btn_login_page' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_agent_register_btn_login_page', 1,
                                                    !empty($settings["enable_agent_register_btn_login_page"]) ?
                                                    (int)$settings["enable_agent_register_btn_login_page"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!}
                                                    {{ __( 'superadmin::lang.enable_agent_register_btn_login_page' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_customer_login', 1, !empty($settings["enable_customer_login"]) ?
                                                    (int)$settings["enable_customer_login"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_customer_login' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_agent_login', 1, !empty($settings["enable_agent_login"]) ?
                                                    (int)$settings["enable_agent_login"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_agent_login' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('enable_employee_login', 1, !empty($settings["enable_employee_login"]) ?
                                                    (int)$settings["enable_employee_login"] : 0 ,
                                                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_employee_login' ) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane " id="banners">
                            @include('superadmin::superadmin_settings.partials.banners')
                        </div>
                        <div class="tab-pane " id="gifts">
                            @include('superadmin::superadmin_settings.give_away_gifts.index')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>