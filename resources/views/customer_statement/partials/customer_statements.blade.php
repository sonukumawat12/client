<!-- Main content -->
<style>
        /* #print_header_div{
            display: none !important;
        } */
</style>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            
            <div class="col-md-2" id="location_filter">
                <div class="form-group">
                    {!! Form::label('customer_statement_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('customer_statement_location_id', $business_locations, null, ['class' =>
                    'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                {!! Form::label('customer_statement_customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('customer_statement_customer_id', $customers, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('customer_statement_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'customer_statement_date_range', 'readonly']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                {!! Form::label('logo', __('lang_v1.customer_statement_logos'). ':', []) !!}
                {!! Form::select('logo', $logos, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_statement_reference', __('lang_v1.reference'). ':', []) !!}
                    {!! Form::select('customer_statement_reference', $reference, null, ['class' => 'form-control select2',
                    'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
                </div>
            </div>
            
            

            <div class="box-tools" style="margin-top: 25px">
                <button class="btn btn-primary print_report pull-right" onclick="saveDiv()"> &nbsp;
                    <i class="fa fa-save"></i> @lang('messages.save')</button>
            </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            
            <div id="report_div">
                <style>
                    @media print {
                        .dt-buttons, .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate{
                            display: none;
                        }
                        #print_header_div{
                            display: inline !important;
                        }
                        .customer_details_div{
                            visibility: hidden;
                        }
                        .notexport {
                            display: none
                        }
                    }
                </style>
                <div id="print_header_div" class="print_header_div">
                
                </div>
                <div class="row">
                    <div class="col-md-12 text-center text-red" style="text-align: center">
                        <div class="col-md-4">
                            <h4 class="">@lang('lang_v1.statement'): @lang('report.from') <span
                                    class="from_date"></span>
                                @lang('report.to') <span class="to_date"></span> </h4>
                        </div>
                    
                        <div class="col-md-4 customer_details_div">
                            <h4 class="">
                                 <span class="customer_name"></span>
                            </h4>
                        </div>
                        <div class="col-md-4 customer_details_div">
                            <h4 class="">
                                <span class="statement_no"></span>
                                <input type="hidden" name="statement_no" id="statement_no" value="{{$statement_no+1}}">
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{-- <div class="table-responsive"> --}}
                            <table class="table table-bordered table-striped" id="customer_statement_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="notexport">@lang('lang_v1.action')</th>
                                        <th>@lang('contact.date')</th>
                                        <th>@lang('lang_v1.customer_po_no')</th>
                                        <!--<th>@lang('contact.location')</th>-->
                                        <th>@lang('contact.invoice_no')</th>
                                        <th>@lang('contact.route_name')</th>
                                        <th>@lang('contact.vehicle_number')</th>
                                        <th>@lang('contact.voucher_order_date')</th>
                                        <th>@lang('contact.product')</th>
                                        <th>@lang('contact.qty')</th>
                                        <th>@lang('contact.unit_price')</th>
                                        <th>@lang('contact.invoice_amount')</th>
                                        <th>@lang('contact.due_amount')</th>

                                    </tr>
                                </thead>
                                
                                <tfoot>
                                    <tr class="bg-gray font-17 text-center footer-total">
                                        <td colspan="10"></td>
                                        <td><strong>@lang('sale.total'):</strong></td>
                                        <td><span class="display_currency" id="footer_total" data-currency_symbol="true"></span></td>
                                        <td><span class="display_currency" id="footer_due" data-currency_symbol="true"></span></td>
                                    </tr>
                                </tfoot>

                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<input type="hidden" name="due_total" id="due_total" value="0">