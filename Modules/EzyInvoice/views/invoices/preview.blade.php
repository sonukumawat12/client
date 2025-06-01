<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" id="close_invoice_preview" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{$settlement->settlement_no}}</h4>
        </div>

        <div class="modal-body">
            @php
            $business_id = session()->get('user.business_id');
            $business_details = App\Business::find($business_id);
            $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
            : 2;
            @endphp
            <div class="row">
                <div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: -10px; font-size: 18px;">
                    @lang('ezyinvoice::lang.payment_details')
                </div>
                <div class="">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                
                                <tr>
                                    <th colspan="6" class="text-red">@lang('ezyinvoice::lang.credit_sales' )</th>
                                </tr>
                                <tr>
                                    <th>@lang('ezyinvoice::lang.customer')</th>
                                    <th>@lang('ezyinvoice::lang.order_number')</th>
                                    <th>@lang('ezyinvoice::lang.order_date')</th>
                                    <th>@lang('ezyinvoice::lang.product')</th>
                                    <th>@lang('ezyinvoice::lang.qty')</th>
                                    <th>@lang('ezyinvoice::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->credit_sale_payments as $credit_sale)
                                <tr>
                                    <td>
                                        @php
                                        $credit_sale_customer = \App\Contact::findOrFail($credit_sale->customer_id);
                                        $credit_sale_product = \App\Product::findOrFail($credit_sale->product_id);
                                        @endphp
                                        {{!empty($credit_sale_customer) ? $credit_sale_customer->name : ''}}
                                    </td>
                                    <td>
                                        {{$credit_sale->order_number}}
                                    </td>
                                    <td>
                                        {{$credit_sale->order_date}}
                                    </td>
                                    <td>
                                        {{!empty($credit_sale_product) ? $credit_sale_product->name : ''}}
                                    </td>
                                    <td>
                                        {{$credit_sale->qty}}
                                    </td>
                                    <td>{{number_format($credit_sale->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2">
                                        <button data-href="{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@productPreview', [$settlement->id])}}" type="button" class="btn-modal btn btn-primary pull-left credit_sale_product_detail" data-container=".preview_settlement" id="product_preview_btn">Credit Sales Product details</button>
                                    </th>
                                    <th colspan="3" class="text-right">
                                        @lang('ezyinvoice::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->credit_sale_payments()->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" id="close_invoice_preview_button">@lang( 'messages.close' )</button>
        </div>

    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->