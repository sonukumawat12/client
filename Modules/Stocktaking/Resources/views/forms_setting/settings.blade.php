
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('Stocktaking::lang.Stocktaking_forms_setting')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    
    <hr>

    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['action' => '\Modules\Stocktaking\Http\Controllers\FormsSettingController@store', 'method' =>
            'post', 'id' =>
            'form_setting']) !!}
            @component('components.widget', ['class' => 'box-primary'])
            
          
            <div class="row">
                 <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('F22_form_sn', __('Stocktaking::lang.form_starting_number') . ':') !!}
                        {!! Form::text('F22_form_sn', !empty($settings->F22_form_sn) ? $settings->F22_form_sn : null,
                            ['class' => 'form-control', 'id' => 'F22_form_sn']) !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('F22_form_tdate', __('Stocktaking::lang.transaction_date') . ':') !!}
                        {!! Form::text('F22_form_tdate', !empty($settings->F22_form_tdate) ? date('m/d/Y',
                            strtotime($settings->F22_form_tdate)):
                            null, ['class' => 'form-control', 'id' => 'F22_form_tdate',
                            'readonly']) !!}
                    </div>
                </div>
            
                <div class="col-md-3">
                    <div class="form-group">
                        @lang('Stocktaking::lang.previous_opening_balance_zero_frequency')
                        
                        <button type="button" class="btn btn-primary btn-modal" id="form_f22_modal"
                            data-href="{{action('\Modules\Stocktaking\Http\Controllers\FormsSettingController@getFormF22Setting')}}"
                            data-container=".form_f22_modal"> @lang('Stocktaking::lang.click_to_new') </button>
                        
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('current_stock_aa_onstocktaking', __('Stocktaking::lang.current_stock_aa_onstocktaking') . ':') !!}
                        {!! Form::select('current_stock_aa_onstocktaking', ['1' => 'Yes', '0' => 'No'], !empty($settings) ? $settings->current_stock_aa_onstocktaking : null, ['class' => 'form-control',
                            'placeholder' => __('Stocktaking::lang.please_select')]) !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('stock_adjustment_loss_account', __('Stocktaking::lang.stock_adjustment_loss_account') . ':') !!}
                        {!! Form::select('stock_adjustment_loss_account', $expense_accounts, !empty($settings) ? $settings->stock_adjustment_loss_account : null, ['class' => 'form-control select2',
                            'placeholder' => __('Stocktaking::lang.please_select'),'width' => '100%']) !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('stock_adjustment_gain_account', __('Stocktaking::lang.stock_adjustment_gain_account') . ':') !!}
                        {!! Form::select('stock_adjustment_gain_account', $income_accounts, !empty($settings) ? $settings->stock_adjustment_gain_account : null, ['class' => 'form-control select2',
                            'placeholder' => __('Stocktaking::lang.please_select'),'width' => '100%']) !!}
                    </div>
                </div>
                
                
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btn-sm pull-right" style="margin-left: 10px;"
                        type="submit">@lang('Stocktaking::lang.save')</button>
                </div>

            </div>
            @endcomponent
            {!! Form::close() !!}
        </div>
    </div>
  
</section>
<!-- /.content -->