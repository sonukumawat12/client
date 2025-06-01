<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('16a_location_id', __('purchase.business_location') . ':') !!}

                        {!! Form::select('16a_location_id', $business_locations, $business_locations->keys()->first(), [
                            'id' => '16a_location_id',
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}


                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('form_16a_date', __('report.date') . ':') !!}
                       
                            {{-- Single text input that triggers the dropdown --}}
                            {!! Form::text('form_16a_date', @format_date(date('Y-m-d')), [
                                'class' => 'form-control dropdown-toggle input_number customer_transaction_date',
                                'id' => 'form_16a_date',
                              
                                'readonly',
                                'required',
                            ]) !!}

                       
                    </div>

                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('type', __('mpcs::lang.F16a_from_no') . ':') !!}
                        {!! Form::text('F16a_from_no', $F16a_from_no, ['class' => 'form-control', 'readonly', 'id' => 'F16a_from_no']) !!}
                    </div>
                </div>
                {!! Form::hidden('formId', $form_number, ['class' => 'form-control', 'readonly', 'id' => 'form_id']) !!}
                <button type="button" class="btn btn-primary" style="margin-top: 20px;" id="print_form_16a_btn">
                    <i class="fa fa-print"></i> Print
                </button>
            @endcomponent
        </div>
    </div>

    <div class="row" id="printarea">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                @php
                    $totPurchasePreValue = optional($settings)->total_purchase_price_with_vat ?? '0.00';
                    $totSalePreValue = optional($settings)->total_sale_price_with_vat ?? '0.00';
                @endphp
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 text-red" style="margin-top: 14px;">
                            <b>@lang('petro::lang.date'): <span class="from_date"></span></b>
                        </div>
                        <div class="col-md-5">
                            <div class="text-center">
                                <h5 style="font-weight: bold;">{{ request()->session()->get('business.name') }} <br>
                                    <span class="f16a_location_name">@lang('petro::lang.all')</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center pull-left">
                                <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.16A_form')
                                    @lang('mpcs::lang.form_no') : <span id="form_no1">{{ $F16a_from_no }}</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="form_16a_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th colspan="2" class="text-center" style="width: 150px;">@lang('mpcs::lang.purchase_price_with_vat')
                                        </th>
                                        <th colspan="2" class="text-center" style="width: 150px;">@lang('mpcs::lang.sale_price_with_vat')
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>@lang('mpcs::lang.purchase_order_no')</th>
                                        <th>@lang('mpcs::lang.product')</th>
                                        <th>@lang('mpcs::lang.location')</th>
                                        <th>@lang('mpcs::lang.received_qty')</th>
                                        <th>@lang('mpcs::lang.unit')</th>
                                        <th>@lang('mpcs::lang.total')</th>
                                        <th>@lang('mpcs::lang.unit')</th>
                                        <th>@lang('mpcs::lang.total')</th>
                                        <th>@lang('mpcs::lang.p_invoice_no')</th>
                                        <th>@lang('mpcs::lang.stock_book_no')</th>

                                    </tr>
                                </thead>
                                <tfoot class="bg-gray">
                                    <tr>
                                        <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.total_this_page')</td>
                                        <td class="text-red text-bold" id="footer_F16A_total_purchase_price"></td>
                                        <td>&nbsp;</td>
                                        <td class="text-red text-bold" colspan="3" id="footer_F16A_total_sale_price">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-red text-bold" colspan="5">Total previous Date
                                        </td>
                                        <td class="text-red text-bold" id="pre_F16A_total_purchase_price">
                                            </td>
                                        <td>&nbsp;</td>
                                        <td class="text-red text-bold" colspan="3" id="pre_F16A_total_sale_price">
                                            </td>
                                    </tr>
                                    <tr>
                                        <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.grand_total')</td>
                                        <td class="text-red text-bold" id="grand_F16A_total_purchase_price"></td>
                                        <td>&nbsp;</td>
                                        <td class="text-red text-bold" colspan="3" id="grand_F16A_total_sale_price">
                                        </td>
                                    </tr>
                                    
                                    <input type="hidden" name="total_this_p_prev" id="total_this_p_prev" value="{{ $totPurchasePreValue }}">
                                    <input type="hidden" name="total_this_s_prev" id="total_this_s_prev" value="{{ $totSalePreValue }}">

                                    <input type="hidden" name="total_this_p" id="total_this_p" value="0">
                                    <input type="hidden" name="total_this_s" id="total_this_s" value="0">
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
