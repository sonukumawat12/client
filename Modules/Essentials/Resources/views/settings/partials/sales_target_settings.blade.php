<div class="pos-tab-content">
    <div class="row">
        {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}

        <div class="col-xs-6">
            <div class="checkbox">
                
                <label>
                    {!! Form::checkbox('calculate_sales_target_commission_without_tax', 1, !empty($settings['calculate_sales_target_commission_without_tax']) ? 1 : 0, ['class' => 'input-icheck'] ); !!} @lang('essentials::lang.calculate_sales_target_commission_without_tax')
                </label>
                @show_tooltip(__('essentials::lang.calculate_sales_target_commission_without_tax_help'))
                <input type="hidden" name="calculate_sales_target_commission_without_tax_one" value="1">
            </div>
            <div class="form-group">
                {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger btn-block"])}}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>