<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('credit_sale_customer_id', __('petro::lang.customer').':') !!}
                    {!! Form::select('credit_sale_customer_id', $customers, null, ['class' => 'form-control select2',
                    'style' => 'width: 100%;']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_number', __( 'petro::lang.order_number' ) ) !!}
                    {!! Form::text('order_number', null, ['class' => 'form-control credit_sale_fields
                    order_number',
                    'placeholder' => __(
                    'petro::lang.order_number' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_date', __( 'petro::lang.order_date' ) ) !!}
                    {!! Form::text('order_date', null, ['class' => 'form-control
                    order_date',
                    'placeholder' => __(
                    'petro::lang.order_date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_reference', __( 'petro::lang.select_customer_vehicle_no' ) ) !!}
                   {!! Form::select('customer_reference', [], null, ['class' => 'form-control credit_sale_fields select2
                        customer_reference', 'required', 'id' => 'customer_reference', 'style' => 'width: 100%',
                        'placeholder' => __(
                        'petro::lang.please_select' ) ]); !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('credit_sale_product_id', __('petro::lang.credit_sale_product').':') !!}
                    {!! Form::select('credit_sale_product_id', $products, null, ['class' => 'form-control select2',
                    'style' => 'width: 100%;',
                    'placeholder' => __(
                    'petro::lang.please_select' )]); !!}
                </div>
                <input type="hidden" id="manual_discount" value="{{auth()->user()->can('manual_discount') ? 1 : 0}}">

            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit_price', __( 'petro::lang.unit_price' ) ) !!}
                    {!! Form::text('unit_price', null, ['class' => 'form-control input_number
                    unit_price', 'readonly',
                    'placeholder' => __(
                    'petro::lang.unit_price' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit_discount', __( 'petro::lang.unit_discount' ) ) !!}
                    {!! Form::text('unit_discount', null, ['class' => 'form-control input_number
                    unit_discount', 'disabled' => true,
                    'placeholder' => __(
                    'petro::lang.unit_discount' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('credit_sale_qty', __( 'petro::lang.credit_sale_qty' ) ) !!}
                    {!! Form::text('credit_sale_qty', null, ['class' => 'form-control credit_sale_fields input_number
                    credit_sale_qty',
                    'placeholder' => __(
                    'petro::lang.credit_sale_qty' ), 'disabled' => true ]); !!}
                    <input type="hidden" name="credit_sale_qty_hidden" value="0" id="credit_sale_qty_hidden" >
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('credit_total_amount', __( 'petro::lang.amount' ).__( 'petro::lang.before_discount_cr' ) ) !!} 
                    
                    {!! Form::text('credit_total_amount', null, ['id' => 'credit_total_amount', 'class' => 'form-control credit_sale_fields cust_input_number
                    credit_total_amount', 'required', 'disabled' => true,
                    'placeholder' => __(
                    'petro::lang.credit_total_amount' ) ]); !!}
                </div>
            </div>
            
            
            <div class="col-md-3  hidden">
                <div class="form-group">
                    {!! Form::text('credit_sale_amount', null, ['id' => 'credit_sale_amount','class' => 'form-control credit_sale_fields cust_input_number
                    credit_sale_amount', 'required', 'disabled' => true,
                    'placeholder' => __(
                    'petro::lang.amount' ) ]); !!}
                    <input type="hidden" name="credit_sale_amount_hidden" value="0" id="credit_sale_amount_hidden" >
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('credit_discount_amount', __( 'petro::lang.credit_discount_amount' ) ) !!}
                    {!! Form::text('credit_discount_amount', null, ['class' => 'form-control credit_sale_fields cust_input_number
                    credit_discount_amount', 'required', 'disabled' => true,
                    'placeholder' => __(
                    'petro::lang.credit_discount_amount' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_reference_one_time', __( 'petro::lang.enter_customer_vehicle_no' ) ) !!}
                    {!! Form::text('customer_reference', null, ['class' => 'form-control
                    customer_reference_one_time', 'id' => 'customer_reference_one_time',
                    'placeholder' => __(
                    'petro::lang.enter_customer_vehicle_no' ) ]); !!}
                </div>
            </div>
            
            
            <div class="col-md-4">
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
                @lang('petro::lang.current_outstanding'): <span class="current_outstanding"></span></div>
            <div class="col-md-4 text-red" style="font-size: 18px; font-weight: bold; ">
                @lang('petro::lang.credit_limit'): <span class="credit_limit"></span></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="credit_sale_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.outstanding' )</th>
                    <th>@lang('petro::lang.limit' )</th>
                    <th>@lang('petro::lang.order_no' )</th>
                    <th>@lang('petro::lang.order_date' )</th>
                    <th>@lang('petro::lang.customer_reference' )</th>
                    <th>@lang('petro::lang.product' )</th>
                    <th>@lang('petro::lang.unit_price' )</th>
                    <th>@lang('petro::lang.qty' )</th>
                    <th>@lang('petro::lang.sub_total' )</th>
                    <th>@lang('petro::lang.discount_total' )</th>
                    <th>@lang('petro::lang.total' )</th>
                    <th>@lang('lang_v1.note') </th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="9" style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="credit_sale_total">
                        0.00</td>
                    <td style="text-align: left; font-weight: bold;" class="credit_tb_discount_total">
                        0.00</td>
                    <td style="text-align: left; font-weight: bold;" class="credit_tbl_amount_total">
                        0.00</td>
                </tr>
                <input type="hidden" value="0" name="credit_sale_total" id="credit_sale_total">
            </tfoot>
        </table>
    </div>
    
     <div class="col-md-3 pull-right">
        <button type="button" class="btn btn-danger pull-right credit_sale_finalize_print" style="margin-left: 10px; margin-top: 23px;">Print & Save</button>
        <button type="button" class="btn btn-danger pull-right credit_sale_finalize"
            style="margin-left: 10px; margin-top: 23px;">@lang('petro::lang.finalize')</button>
    </div>
            
            
</div>
