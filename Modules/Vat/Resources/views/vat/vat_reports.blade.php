<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'report.tax_report' )
        <small>@lang( 'report.tax_report_msg' ) 
            <button type="button" class="btn btn-primary btn-sm pull-right" id="regenerate_vat" data-toggle="modal" >@lang('superadmin::lang.regenerate_vat')</button>
        </small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="print_section">
        <h2>{{session()->get('business.name')}} - @lang( 'report.tax_report' )
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('tax_report_location_filter', __('purchase.business_location') . ':') !!}
                    {!! Form::select('tax_report_location_filter', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%']); !!}
                </div>
            </div>
            
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('tax_report_date_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'tax_report_date_filter', 'readonly']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('reference_type', __('vat::lang.reference_type') . ':') !!}
                    {!! Form::select('reference_type', ['sell' => __('vat::lang.sale'),'purchase' => __('vat::lang.purchase'), 'expense' => __('vat::lang.expense')], null, ['class' => 'form-control select2',
                    'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __('vat::lang.contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
                    'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            @endcomponent
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('report.input_tax') }} @show_tooltip(__('tooltip.input_tax'))
            @endslot
            <div class="input_tax">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('report.output_tax') }} @show_tooltip(__('tooltip.output_tax'))
            @endslot
            <div class="output_tax">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('lang_v1.expense_tax') }} @show_tooltip(__('lang_v1.expense_tax_tooltip'))
            @endslot
            <div class="expense_tax">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            @component('components.widget')
            @slot('title')
            {{ __('lang_v1.tax_overall') }} @show_tooltip(__('tooltip.tax_overall'))
            @endslot
            <h3 class="text-muted">
                {{ __('lang_v1.output_tax_minus_input_tax') }}:
                <span class="tax_diff">
                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                </span>
            </h3>
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        <div class="table-responsive">
                <table class="table table-bordered table-striped" id="taxes_details_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('vat::lang.date')</th>
                            <th>@lang('vat::lang.reference_type')</th>
                            <th>@lang('vat::lang.reference_no')</th>
                            <th>@lang('vat::lang.contact')</th>
                            <th>@lang('vat::lang.amount')</th>
                            <th>@lang('vat::lang.vat_amount')</th>
                            <th class="notexport">@lang('vat::lang.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                            <td>
                                <span class="display_currency" id="footer_total_amount" data-currency_symbol="true"></span>
                            </td>
                            <td>
                                <span class="display_currency" id="footer_vat_total"
                                    data-currency_symbol="true"></span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    </div>
    
    
    <div class="row no-print">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary print-report pull-right" aria-label="Print"><i class="fa fa-print"></i> @lang( 'messages.print' )</button>
        </div>
    </div>

    
    <div class="modal fade" id="regenerate_vat_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatController@updateVats'), 'method' => 'post', 'id' => 'regenerate_vat_form' ]) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('superadmin::lang.regenerate_vat')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('start_date', __( 'lang_v1.start_date' ) . ':*') !!}
                        {!! Form::date('start_date', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.end_date' )]); !!}
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('end_date', __( 'lang_v1.end_date' ) . ':*') !!}
                        {!! Form::date('end_date', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.end_date' )]); !!}
                    </div>
                    
                    
                    <div class="form-group">
                        {!! Form::label('transaction_type',  __('lang_v1.transaction_type') . ':') !!}
                        {!! Form::select('transaction_types[]', $reports, null, ['id'=>'transaction_type', 'multiple' ,'class' => 'form-control select2','required', 'style' => 'width:100%']); !!}
                    </div>
                    
                 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->   
    </div>

</section>
<!-- /.content -->