<!-- Content Header (Page header) -->

<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            
            
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
                    {!! Form::label('contact_id', __('vat::lang.contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
                    'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        <div class="table-responsive">
                <table class="table table-bordered table-striped" id="taxes_details_table" width="100%">
                    <thead>
                        <tr style="background-color: #3B84FF;color: #ffffff;">
                            <th>@lang('vat::lang.serial_no')</th>
                            <th>@lang('vat::lang.invoice_date')</th>
                            <th>@lang('vat::lang.tax_invoice_no')</th>
                            <th>@lang('vat::lang.purchaser_tin')</th>
                            <th>@lang('vat::lang.name_of_purchaser')</th>
                            <th>@lang('vat::lang.description')</th>
                            <th>@lang('vat::lang.value_of_supply')</th>
                            <th>@lang('vat::lang.vat_amount')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="8">.</td>
                        </tr>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                            <td>
                                <span class="display_currency" id="footer_total_amount" data-currency_symbol="true"></span>
                            </td>
                            <td>
                                <span class="display_currency" id="footer_vat_total"
                                    data-currency_symbol="true"></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    </div>
    
  

</section>
<!-- /.content -->