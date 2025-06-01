<div class="pos-tab-content">
	<div class="row">
        {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}

        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('leave_ref_no_prefix',  __('essentials::lang.leave_ref_no_prefix') . ':') !!}
            	{!! Form::text('leave_ref_no_prefix', !empty($settings['leave_ref_no_prefix']) ? $settings['leave_ref_no_prefix'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.leave_ref_no_prefix')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('leave_instructions',  __('essentials::lang.leave_instructions') . ':') !!}
                {!! Form::textarea('leave_instructions', !empty($settings['leave_instructions']) ? $settings['leave_instructions'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.leave_instructions')]); !!}
            </div>

            <div class="form-group">
                {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger"])}}
            </div>
        </div>
        {!! Form::close() !!}
	</div>
</div>