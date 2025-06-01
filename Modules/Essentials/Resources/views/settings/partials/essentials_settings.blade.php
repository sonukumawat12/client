<div class="pos-tab-content">
    <div class="row">
        {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('essentials_todos_prefix',  __('essentials::lang.essentials_todos_prefix') . ':') !!}
                {!! Form::text('essentials_todos_prefix', !empty($settings['essentials_todos_prefix']) ? $settings['essentials_todos_prefix'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.essentials_todos_prefix')]); !!}
            </div>

            <div class="form-group">
                {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger btn-block"])}}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>