@php
$hide_tax = '';
if( session()->get('business.enable_inline_tax') == 0){
$hide_tax = 'hide';
}
$currency_precision = config('constants.currency_precision', 2);
$quantity_precision = config('constants.quantity_precision', 2);
$business_id = request()->session()->get('user.business_id');
$enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
@endphp
<style>
    .current_stock_td {
        display: none;
    }
</style>
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-th-green text-center table-striped"
        id="purchase_entry_table">
        <thead>
            <tr>
				<th style="width: 2%;" >#</th>
				<th style="width: 10%;">@lang( 'product.product_name' )</th>
				<th style="width: 8%;">@lang( 'purchase.purchase_quantity' )</th>
				<th style="width: 8%;">@lang( 'lang_v1.unit_cost_before_discount' )</th>
				<th style="width: 8%;">@lang( 'lang_v1.discount_percent' )</th>
				<th style="width: 8%;">@lang( 'purchase.unit_cost_before_tax' )</th>
				<th class="{{$hide_tax}}"  style="width: 8%;">@lang( 'purchase.subtotal_before_tax' )</th>
				<th class="{{$hide_tax}}"  style="width: 8%;">@lang( 'purchase.product_tax' )</th>
				<th class="{{$hide_tax}}"  style="width: 8%;">@lang( 'purchase.net_cost' )</th>
				<th  style="width: 8%;">@lang( 'purchase.line_total' )</th>
				<th style="width: 8%;" class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
					@lang( 'lang_v1.profit_margin' )
				</th>
				<th style="width: 6%;">
					@lang( 'purchase.unit_selling_price' )
					<small>(@lang('product.exc_of_tax'))</small>
				</th>
				<th style="width: 2%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
			</tr>
        </thead>
        <tbody>
            <?php $row_count = 0; ?>
            @foreach($products as $purchase_line)
            <tr>
                <td><span class="sr_number"></span></td>
                <td>
                    {{ $purchase_line->name }}
                    
                </td>

                <td style="line-height: 2px !important;">
                    {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id, ['class' => 'hidden_product_id'] ); !!}
                    
                    {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]', $purchase_line->id, ['class' => 'hidden_purchase_id']); !!}


                    {!! Form::text('purchases[' . $loop->index . '][quantity]',
                    @num_format($purchase_line->purchase_qty),
                    ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'id' => 'product_id'.$purchase_line->product_id]); !!}

                    <input type="hidden" class="base_unit_cost"
                        value="{{$purchase_line->net_cost}}">
                        <br>
                        <input type="hidden" class="base_unit_selling_price"
                            value="{{$purchase_line->unit_selling_price}}">
                        
                        <br>
                         @if ($enable_free_qty)
                        
                            {!! Form::text('purchases[' . $loop->index . '][free_qty]',
                            number_format($purchase_line->free_qty, $quantity_precision, $currency_details->decimal_separator,
                            $currency_details->thousand_separator),
                            ['class' => 'form-control input-sm purchase_bonus_qty input_number mousetrap', 'required']); !!}
                        
                        @endif
                </td>
                
               

                <td>
                    {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]',
                    $purchase_line->unit_before_discount, ['class' => 'form-control input-sm
                    purchase_unit_cost_without_discount input_number', 'required']); !!}
                </td>
                <td>
                    {!! Form::text('purchases[' . $loop->index . '][discount_percent]',
                    number_format($purchase_line->discount, $currency_precision,
                    $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' =>
                    'form-control input-sm inline_discounts input_number', 'required']); !!} <b>%</b>
                </td>
                <td>
                    {!! Form::text('purchases[' . $loop->index . '][purchase_price]',
                    $purchase_line->unit_cost, ['class' => 'form-control input-sm
                    purchase_unit_cost input_number', 'required']); !!}
                    {!! Form::hidden('purchases[' . $loop->index . '][purchase_price]',
                    $purchase_line->unit_cost, ['class' => 'pp_exc_tax', 'required']); !!}
                </td>
                <td class="{{$hide_tax}}">
                    <span class="row_subtotal_before_tax">
                        {{number_format($purchase_line->purchase_qty * $purchase_line->unit_cost, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    </span>
                    <input type="hidden" class="row_subtotal_before_tax_hidden"
                        value="{{($purchase_line->purchase_qty * $purchase_line->unit_cost)}}">
                </td>

                <td style="line-height: 2px !important;" class="{{$hide_tax}}">
                    <div class="form-group">
                        <select name="purchases[{{ $loop->index }}][purchase_line_tax_id]"
                            class="form-control purchase_line_tax_id" placeholder="'Please Select'">
                            <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                                selected @endif >@lang('lang_v1.none')</option>
                            @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->
                                tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <span class="input-group-addon purchase_product_unit_tax_text">
                            {{number_format($purchase_line->tax_amount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                        </span>
                        {!! Form::hidden('purchases[' . $loop->index . '][item_tax]',
                        number_format($purchase_line->tax_amount, $currency_precision,
                        $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' =>
                        'purchase_product_unit_tax']); !!}
                    </div>
                </td>
                <td class="{{$hide_tax}}">
                    {!! Form::text('purchases[' . $loop->index . '][purchase_price_inc_tax]',
                    $purchase_line->net_cost, ['class' =>
                    'form-control input-sm purchase_unit_cost_after_tax input_number',  'required']); !!}
                </td>
                <td>
                    <span class="row_subtotal_after_tax">
                        {{number_format($purchase_line->line_total, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    </span>
                    <input type="hidden" class="row_subtotal_after_tax_hidden"
                        value="{{number_format($purchase_line->line_total, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
                </td>

                <td class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
                    @php
                    $pp = $purchase_line->net_cost;
                    $sp = $purchase_line->unit_selling_price;
                    
                    if($pp == 0){
                    $profit_percent = 100;
                    } else {
                    $profit_percent = (($sp - $pp) * 100 / $pp);
                    }
                    @endphp

                    {!! Form::text('purchases[' . $loop->index . '][profit_percent]',
                    number_format($profit_percent, $currency_precision, $currency_details->decimal_separator,
                    $currency_details->thousand_separator),
                    ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
                </td>

                <td>
                    @if(session('business.enable_editing_product_from_purchase'))
                    {!! Form::text('purchases[' . $loop->index . '][default_sell_price]', number_format($sp,
                    $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                    ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
                    @else
                    {{number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    @endif

                </td>
                

                <td></td>
                <input type="hidden" name="is_fuel_category"
                    value="0">
            </tr>
            <?php $row_count = $loop->index + 1 ; ?>
            @endforeach
        </tbody>
    </table>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">