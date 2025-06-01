<?php $readonly = $active == 1 ? $purchase_zero == 1 ? '' : 'readonly' : ''; ?>

@foreach( $variations as $variation)
<tr class="product_row">
    <td class=" @if($purchase_pos) hide @endif"><span class="sr_number"></span></td>
    <td>
        {{ $product->name }} ({{$variation->sub_sku}})
        @if( $product->type == 'variable' )
        <br />
        (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
        @endif
    </td>
    <td style="line-height: 2px !important;">
        {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}
        {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id , ['class' =>
        'hidden_variation_id']); !!}

        @php
        $check_decimal = 'false';
        if($product->unit->allow_decimal == 0){
        $check_decimal = 'true';
        }
        $currency_precision = config('constants.currency_precision', 2);
        $quantity_precision = config('constants.quantity_precision', 2);
        @endphp
        <div class="input-group input-number">
            
            {!! Form::text('purchases[' . $row_count . '][quantity]', number_format(!empty($temp_qty)?$temp_qty:1,
            $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class'
            =>
            "form-control purchase_quantity p_qty_$product->id input_number mousetrap", 'required', 'data-rule-abs_digit' =>
            $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed') , 'id' =>
            'product_id'.$product->id]); !!}
            
        </div>
        <br>
        @if(!empty($sub_units))
        <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
            @foreach($sub_units as $key => $value)
            <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                {{$value['actual_name']}}
            </option>
            @endforeach
        </select>
        @else
        {{ $product->unit->actual_name }}
        @endif
        
        @php
            $business_id = request()->session()->get('user.business_id');
            $enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
        @endphp
        
        @if ($enable_free_qty)
            <br>
            <input type="number" name="purchases[{{$row_count}}][free_qty]"
                class="free_qty form-control" placeholder="@lang( 'purchase.free_qty' )" value="">
        @endif
        
        <br>

        <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
        <input type="hidden" class="base_unit_selling_price" value="{{$variation->default_sell_price}}">
        <input type="hidden" class="is_fuel_category" name="is_fuel_category" value="{{$is_fuel_category}}">
        <input type="hidden" class="product_id" name="product_id" value="{{$product->id}}">

        <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">

    </td>
    
 
    <td class=" @if($purchase_pos) hide @endif">
        {!! Form::text('purchases[' . $row_count . '][pp_without_discount]',
        rtrim(rtrim($variation->default_purchase_price, '0'), '.'), ['class' => 'form-control input-sm
         input_number', 'required', $readonly]); !!}
    </td>
    <td class="">
        {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm
        inline_discounts input_number', 'required']); !!}
    </td>
    <td class="@if($purchase_pos) hide @endif">
        {!! Form::text('purchases[' . $row_count . '][purchase_price]', $variation->default_purchase_price, ['class' =>
        'form-control input-sm purchase_unit_cost ', 'required', $readonly]); !!}
    </td>
    <td class="{{$hide_tax}}">
        <span class="row_subtotal_before_tax display_currency">0</span>
        <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
    </td>
    <td  style="line-height: 2px !important;" class="{{$hide_tax}}">
        <div class="form-group">
            <select name="purchases[{{ $row_count }}][purchase_line_tax_id]"
                class="form-control select2  purchase_line_tax_id" placeholder="'Please Select'">
                <option value="" data-tax_amount="0" @if( $hide_tax=='hide' ) selected @endif>@lang('lang_v1.none')
                </option>
                @foreach($taxes as $tax)
                <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $product->tax == $tax->id &&
                    $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                @endforeach
            </select>
        </div>
        <br>
        <div class="form-group">
            {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']);
            !!}
            <span class="input-group-addon purchase_product_unit_tax_text">
                0.00</span>
        </div>
    </td>
    <td class="{{$hide_tax}} @if($purchase_pos) hide @endif">
        @php
        $dpp_inc_tax = $variation->dpp_inc_tax;
        if($hide_tax == 'hide'){
        $dpp_inc_tax = $variation->default_purchase_price;
        }

        @endphp
        {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control
        input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
    </td>
    <td>
        <input type="text" class="row_subtotal_after_tax_hidden form-control" value=0>
    </td>
    <td
        class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif  @if($purchase_pos) hide @endif">
        {!! Form::text('purchases[' . $row_count . '][profit_percent]', @num_format($variation->profit_percent), ['class' =>
        'form-control input-sm input_number profit_percent', 'required']); !!}
    </td>
    <td class=" @if($purchase_pos) hide @endif">
        @if(session('business.enable_editing_product_from_purchase'))
        {!! Form::text('purchases[' . $row_count . '][default_sell_price]',
        @num_format($variation->default_sell_price), ['class' => 'form-control input-sm input_number default_sell_price',
        'required', $readonly]); !!}
        @else
        {{ @num_format($variation->default_sell_price)}}
        @endif
    </td>
   
    

    <td><i class="fa fa-times remove_purchase_entry_row text-danger" data-row_count="{{ $row_count }}" title="Remove"
            style="cursor:pointer;"></i></td>
    <?php $row_count++ ;?>
</tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">