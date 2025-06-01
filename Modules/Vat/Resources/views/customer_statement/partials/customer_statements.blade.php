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
            
            <div class="col-md-2">
                {!! Form::label('customer_statement_customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('customer_statement_customer_id', $customers, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('customer_statement_date_range', date('m/d/Y') . ' - ' .
                    date('m/d/Y') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'customer_statement_date_range', 'readonly']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                {!! Form::label('logo', __('lang_v1.customer_statement_logos'). ':', []) !!}
                {!! Form::select('logo', $logos, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.please_select'), 'style' => 'width: 100%;']) !!}
            </div>
            
            <div class="col-md-2">
                {!! Form::label('price_adjustment', __('vat::lang.price_adjustment'). ':', []) !!}
                {!! Form::text('price_adjustment', 0, ['class' => 'form-control',
                'placeholder' => __('vat::lang.price_adjustment'), 'style' => 'width: 100%;']) !!}
            </div>
            
            <div class="col-md-2">
                {!! Form::label('customer_statement_reference', __('lang_v1.reference'). ':', []) !!}
                {!! Form::select('customer_statement_reference', $reference, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
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
                        <div class="col-md-12">
                            <h4 class="">@lang('lang_v1.statement'): @lang('report.from') <span
                                    class="from_date"></span>
                                @lang('report.to') <span class="to_date"></span> </h4>
                                <input type="hidden" name="statement_no" id="statement_no" value="{{$statement_no}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{-- <div class="table-responsive"> --}}
                            <table class="table table-bordered table-striped" id="customer_statement_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="notexport" style="width: 40px;">@lang('lang_v1.action')</th>
                                        <th>@lang('contact.date')</th>
                                        <th>@lang('contact.customer')</th>
                                        <th>@lang('vat::lang.po_no')</th>
                                        <th>@lang('vat::lang.our_reference')</th>
                                        <th>@lang('vat::lang.vehicle_no')</th>
                                        <th>@lang('vat::lang.qty')</th>
                                        <th>@lang('vat::lang.product')</th>
                                        <th>@lang('vat::lang.unit_price')</th>
                                        <th>@lang('vat::lang.amount')</th>

                                    </tr>
                                </thead>

                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
                
                <div id="print_footer_div" class="print_footer_div">
                
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<input type="hidden" name="due_total" id="due_total" value="0">