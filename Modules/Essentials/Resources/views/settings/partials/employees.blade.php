<div class="pos-tab-content active">
    <div class="row">
        {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}

        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="form-group">
                {!! Form::label('employees_starting_number',  __('essentials::lang.employees_starting_number') . ':') !!}
                {!! Form::text('employees_starting_number', !empty($settings['employees_starting_number']) ? $settings['employees_starting_number'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.employees_starting_number')]); !!}
            </div>

            <div class="form-group">
                {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger btn-block"])}}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
