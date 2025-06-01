<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('APP_NAME', __('superadmin::lang.app_name') . ':') !!}
            	{!! Form::text('APP_NAME', $default_values['APP_NAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.app_name')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('APP_TITLE', __('superadmin::lang.app_title') . ':') !!}
            	{!! Form::text('APP_TITLE', $default_values['APP_TITLE'], ['class' => 'form-control','placeholder' => __('superadmin::lang.app_title')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('APP_LOCALE', __('superadmin::lang.app_default_language') . ':') !!}
            	{!! Form::select('APP_LOCALE', $languages, $default_values['APP_LOCALE'], ['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                    {!! Form::checkbox('superadmin_enable_register_tc', 1,!empty($settings["superadmin_enable_register_tc"]), 
                    [ 'class' => 'input-icheck']); !!}
                    @lang('superadmin::lang.enable_register_tc')
                    </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-8">
                <div class="form-group">
                	{!! Form::label('app_footer', __('superadmin::lang.app_footer') . ':') !!}
                	{!! Form::text('app_footer', $settings['app_footer'], ['class' => 'form-control','placeholder' => __('superadmin::lang.app_title')]); !!}
                </div>
            </div>
        
        <div class="clearfix"></div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('superadmin_register_tc', __('superadmin::lang.register_tc') .":") !!}

                {!! Form::textarea('superadmin_register_tc', !empty($settings['superadmin_register_tc']) ? $settings['superadmin_register_tc'] : '', ['class' => 'form-control', 'rows' => 5, 'cols' => 200]) !!}
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('footer_top_margin', __('superadmin::lang.footer_top_margin') . ':') !!}
            	{!! Form::text('footer_top_margin', $settings['footer_top_margin'], ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('admin_invoice_footer', __('superadmin::lang.admin_invoice_footer') .":") !!}

                {!! Form::textarea('admin_invoice_footer', !empty($settings['admin_invoice_footer']) ? $settings['admin_invoice_footer'] : '', ['class' => 'form-control', 'rows' => 5, 'cols' => 200]) !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('admin_reports_footer', __('superadmin::lang.admin_reports_footer') .":") !!}

                {!! Form::textarea('admin_reports_footer', !empty($settings['admin_reports_footer']) ? $settings['admin_reports_footer'] : '', ['class' => 'form-control', 'rows' => 5, 'cols' => 200]) !!}
            </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('main_page_refresh_interval_minute', __('superadmin::lang.main_page_refresh_interval_minute') .":") !!}

                {!! Form::text('main_page_refresh_interval_minute', !empty($settings['main_page_refresh_interval_minute']) ? $settings['main_page_refresh_interval_minute'] : '', ['class' => 'form-control']) !!}
            </div>
        </div>

    </div>
</div>