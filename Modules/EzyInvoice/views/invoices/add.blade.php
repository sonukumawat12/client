@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="modal-dialog" role="document" style="width: 100%;">
      {!! Form::open(['url' => action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@store'), 'method' => 'post', 'id' =>'settlement_form' ]) !!}
      <div class="d-flex" style="justify-content: center;">
         <h4 class="modal-title pull-left" style="padding-right: 50px" >@lang( 'ezyinvoice::lang.settlement_no' ):  {{$settlement_no}} </h4>
         <h4 class="modal-title pull-right">@lang( 'ezyinvoice::lang.date' ):  {{!empty($settlement) ? $settlement->transaction_date : now()->format('Y-m-d') }}</h4>
         <input type="hidden" name="settlement_no" value="{{$settlement_no}}" id="settlement_no" >
      </div>
    
      <div class="modal-body">
         <div class="col-md-12">
            <div class="row">
               <div class="col-md-2 text-center">
                  <b>@lang('ezyinvoice::lang.pump_operator')</b> <br>
                  {{isset($pump_operator->name) ? $pump_operator->name : ''}}
               </div>
            </div>
            <br><br>
            <div class="d-flex" style="justify-content: end;">
               <div class="text-center text-red">
                    <button type="button" id="settlement_save_btn" style="margin-right: 20px;"
                        class="btn btn-primary">@lang('messages.save')</button>
                    @if(!empty($settlement))
                    <button data-href="{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@preview', [$settlement->id])}}" class="btn-modal btn btn-success pull-right" id="payment_review_btn" data-container=".preview_settlement"> @lang("ezyinvoice::lang.preview")</button>
                    @endif
               </div>
            </div>
         </div>
         
         <input type="hidden" name="total_balance" id="total_balance"
            value="{{!empty($total_balance)? $total_balance : 0 }}">
         <input type="hidden" name="total_amount" id="total_amount"
            value="{{!empty($total_amount)? $total_amount : 0 }}">
         <input type="hidden" name="total_paid" id="total_paid" value="{{!empty($total_paid)? $total_paid : 0 }}">
         <br><br>
         <div class="clearfix"></div>
         <div style="margin-top: 20px;">
           <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('credit_sale_customer_id', __('ezyinvoice::lang.customer').':') !!}
                                {!! Form::select('credit_sale_customer_id', $customers, null, ['class' => 'form-control select2',
                                                    'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
            
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('order_number', __( 'ezyinvoice::lang.order_number' ) ) !!}
                                {!! Form::text('order_number', null, ['class' => 'form-control credit_sale_fields
                                            order_number',
                                            'placeholder' => __(
                                            'ezyinvoice::lang.order_number' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('order_date', __( 'ezyinvoice::lang.order_date' ) ) !!}
                                {!! Form::text('order_date', null, ['class' => 'form-control
                                order_date',
                                'placeholder' => __(
                                'ezyinvoice::lang.order_date' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('customer_reference_one_time', __( 'ezyinvoice::lang.customer_reference_one_time' ) ) !!}
                                {!! Form::text('customer_reference', null, ['class' => 'form-control
                                customer_reference_one_time', 'id' => 'customer_reference_one_time',
                                'placeholder' => __(
                                'ezyinvoice::lang.customer_reference_one_time' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('credit_sale_product_id', __('ezyinvoice::lang.credit_sale_product').':') !!}
                                {!! Form::select('credit_sale_product_id', $products, null, ['class' => 'form-control select2',
                                'style' => 'width: 100%;',
                                'placeholder' => __(
                                'ezyinvoice::lang.please_select' )]); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('unit_price', __( 'ezyinvoice::lang.unit_price' ) ) !!}
                                {!! Form::text('unit_price', null, ['class' => 'form-control input_number
                                unit_price', 'readonly',
                                'placeholder' => __(
                                'ezyinvoice::lang.unit_price' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('credit_sale_qty', __( 'ezyinvoice::lang.credit_sale_qty' ) ) !!}
                                {!! Form::text('credit_sale_qty', null, ['class' => 'form-control credit_sale_fields input_number
                                credit_sale_qty',
                                'placeholder' => __(
                                'ezyinvoice::lang.credit_sale_qty' ), 'disabled' => true ]); !!}
                                <input type="hidden" name="credit_sale_qty_hidden" value="0" id="credit_sale_qty_hidden" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('credit_sale_amount', __( 'ezyinvoice::lang.amount' ) ) !!}
                                {!! Form::text('credit_sale_amount', null, ['class' => 'form-control credit_sale_fields cust_input_number
                                credit_sale_amount', 'required', 'disabled' => true,
                                'placeholder' => __(
                                'ezyinvoice::lang.amount' ) ]); !!}
                                <input type="hidden" name="credit_sale_amount_hidden" value="0" id="credit_sale_amount_hidden" >
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('customer_reference', __( 'ezyinvoice::lang.customer_reference' ) ) !!}
                                <div class="input-group">
                                    {!! Form::select('customer_reference', [], null, ['class' => 'form-control credit_sale_fields select2
                                    customer_reference', 'required', 'id' => 'customer_reference', 'style' => 'width: 100%',
                                    'placeholder' => __(
                                    'ezyinvoice::lang.please_select' ) ]); !!}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn quick_add_customer_reference
                                            btn-default
                                            bg-white btn-flat btn-modal"
                                            data-href="{{action('CustomerReferenceController@create', ['quick_add' => true])}}"
                                            title="@lang('lang_v1.add_customer_reference')" data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label("credit_note", __('lang_v1.payment_note') . ':') !!}
                              {!! Form::textarea("credit_note", null, ['class' => 'form-control cash_fields', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-2 pull-right">
                            <button type="button" class="btn btn-primary pull-right credit_sale_add"
                                style="margin-top: 23px;">@lang('messages.add')</button>
                        </div>
                        <div class="clearfix"></div>
            
                        <div class="col-md-4 text-red" style="font-size: 18px; font-weight: bold; ">
                            @lang('ezyinvoice::lang.current_outstanding'): <span class="current_outstanding"></span></div>
                        <div class="col-md-4 text-red" style="font-size: 18px; font-weight: bold; ">
                            @lang('ezyinvoice::lang.credit_limit'): <span class="credit_limit"></span></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="credit_sale_table">
                        <thead>
                            <tr>
                                <th>@lang('ezyinvoice::lang.cusotmer_name' )</th>
                                <th>@lang('ezyinvoice::lang.outstanding' )</th>
                                <th>@lang('ezyinvoice::lang.limit' )</th>
                                <th>@lang('ezyinvoice::lang.order_no' )</th>
                                <th>@lang('ezyinvoice::lang.order_date' )</th>
                                <th>@lang('ezyinvoice::lang.customer_reference' )</th>
                                <th>@lang('ezyinvoice::lang.product' )</th>
                                <th>@lang('ezyinvoice::lang.unit_price' )</th>
                                <th>@lang('ezyinvoice::lang.qty' )</th>
                                <th>@lang('ezyinvoice::lang.amount' )</th>
                                <th>@lang('lang_v1.note') </th>
                                <th>@lang('ezyinvoice::lang.action' )</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($settlement_credit_sale_payments as $credit_sale_payment)
                            <tr>
                                <td>{{$credit_sale_payment->customer_name}}</td>
                                <td>{{@num_format($credit_sale_payment->outstanding)}}</td>
                                <td>{{@num_format($credit_sale_payment->credit_limit)}}</td>
                                <td>{{$credit_sale_payment->order_number}}</td>
                                <td>{{$credit_sale_payment->order_date}}</td>
                                <td>{{$credit_sale_payment->customer_reference}}</td>
                                <td>{{$credit_sale_payment->product_name}}</td>
                                <td>{{@num_format($credit_sale_payment->price)}}</td>
                                <td>{{@num_format($credit_sale_payment->qty)}}</td>
                                <td class="credit_sale_amount">{{@num_format($credit_sale_payment->amount)}}
                                </td>
                                <td>{{$credit_sale_payment->note}}</td>
                                <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment"
                                        data-href="/ezy-invoice/invoices/payment/delete-credit-sale-payment/{{$credit_sale_payment->id}}"><i
                                            class="fa fa-times"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
            
                        <tfoot>
                            <tr>
                                <td colspan="9" style="text-align: right; font-weight: bold;">@lang('ezyinvoice::lang.total') :</td>
                                @if(!empty($settlement_credit_sale_payments))
                                <td style="text-align: left; font-weight: bold;" class="credit_sale_total">
                                    {{@num_format( $settlement_credit_sale_payments->sum('amount'))}}</td>
                                @endif
                            </tr>
                             @if(!empty($settlement_credit_sale_payments))
                            <input type="hidden" value="{{ $settlement_credit_sale_payments->sum('amount')}}" name="credit_sale_total" id="credit_sale_total">
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
    
         </div>
    
         <div class="clearfix"></div>
         {!! Form::close() !!}
    </div>
        <div class="modal fade preview_settlement" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</div>

   
