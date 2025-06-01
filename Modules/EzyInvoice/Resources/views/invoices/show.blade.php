<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{!empty($settlement) ? $settlement->settlement_no : ""}}</h4>
        </div>

        <div class="modal-body">

            @php
            $business_id = session()->get('user.business_id');
            $business_details = App\Business::find($business_id);
            $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
            : 2;
            @endphp


            <style>
                .settlement_print_div table. {
                    border: 1px solid #222;
                    margin-top: 10px;
                    margin-bottom: 0px;
                }

                .settlement_print_div table.table-bordered>thead>tr>th {
                    border: 1px solid #222;
                }

                .settlement_print_div table.table-bordered>tbody>tr>td {
                    border: 1px solid #222;
                    font-size: 13px;
                }

                @media print {

                    .no-print,
                    .no-print * {
                        display: none !important;
                    }
                }
            </style>
            <div class="row">
                <div class="col-md-12 settlement_print_div">
                    <div class="col-xs-12 text-center">
                        <p style="font-size: 22px;" class="text-center"><strong>{{!empty($business) ? $business->name : ""}}</strong></p>
                        <p style="font-size: 16px;">@lang('ezyinvoice::lang.pump_operator_sale_report')</p>
                    </div>
                    

                    <div class="clearfix"></div>
                    <br>
                    <div class="col-xs-12 text-center"
                        style="font-weight: bold; maring-bottom: -10px; font-size: 18px;">
                        @lang('ezyinvoice::lang.credit_sales')
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('ezyinvoice::lang.cusotmer_name' )</th>
                                        <th>@lang('ezyinvoice::lang.current_outstanding_before_sale' )</th>
                                        <th>@lang('ezyinvoice::lang.voucher_no' )</th>
                                        <th>@lang('ezyinvoice::lang.product_name' )</th>
                                        <th>@lang('ezyinvoice::lang.qty' )</th>
                                        <th>@lang('ezyinvoice::lang.unit_rate' )</th>
                                        <th>@lang('ezyinvoice::lang.total' )</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $credit_sale_total = $settlement->credit_sale_payments->sum('amount');
                                    @endphp
                                    @if(!empty($settlement->credit_sale_payments ))
                                    @foreach ($settlement->credit_sale_payments as $credit_sale_payment)
                                    @php
                                    $customer_name = App\Contact::where('id',
                                    $credit_sale_payment->customer_id)->first();
                                    $product = App\Product::where('id', $credit_sale_payment->product_id)->first();
                                    @endphp
                                    <tr>
                                        <td>{{!empty($customer_name) ? $customer_name->name : ''}}</td>
                                        <td>{{@num_format($credit_sale_payment->outstanding)}}</td>
                                        <td>{{$credit_sale_payment->order_number}}</td>
                                        <td>{{!empty($product)? $product->name : '' }}</td>
                                        <td>{{@num_format($credit_sale_payment->qty)}}</td>
                                        <td>{{@num_format($credit_sale_payment->price)}}</td>
                                        <td class="credit_sale_amount text-right">
                                            {{@num_format($credit_sale_payment->price * $credit_sale_payment->qty)}}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="5" style="text-align: right;"><b>@lang('ezyinvoice::lang.sub_total')</b>
                                        </td>
                                        <td class="text-right">{{@num_format($credit_sale_total)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->