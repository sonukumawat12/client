@php

	$product_name = $product->product_name . '<br />' . $product->sub_sku ;

	if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}

@endphp


<tr class="product_row" data-row_index="{{$row_count}}">

	<td>
		@if($edit_price || $edit_discount )

		<div data-toggle="tooltip" data-placement="bottom" title="@lang('lang_v1.pos_edit_product_price_help')">

			@if(isset($price_later) && $price_later == 1)

			<span class="text-info cursor-pointer">

				{!! $product_name !!}

				&nbsp;<i class="fa fa-info-circle"></i>

			</span>

			@else

			<span class="text-link text-info cursor-pointer row_edit_product_price_btn" data-toggle="modal"

				data-target="#row_edit_product_price_modal_{{$row_count}}" disabled>

				{!! $product_name !!}

				&nbsp;<i class="fa fa-info-circle"></i>
			</span>

			@endif

		</div>

		@else

		{!! $product_name !!}

		@endif

		<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">

		<input type="hidden" class="product_type" name="products[{{$row_count}}][product_type]"

			value="{{$product->product_type}}">

		<div data-toggle="tooltip" data-placement="bottom" title="@lang('lang_v1.add_description')">

			<i class="fa fa-commenting cursor-pointer text-primary add-pos-row-description" data-toggle="modal"

				data-target="#row_description_modal_{{$row_count}}"></i>

		</div>



		@php

		$hide_tax = 'hide';

		if(session()->get('business.enable_inline_tax') == 1){

		$hide_tax = '';

		}



		$tax_id = $product->tax_id;

		$item_tax = !empty($product->item_tax) ? $product->item_tax : 0;

		$unit_price_inc_tax = $product->sell_price_inc_tax;

		if($hide_tax == 'hide'){

		$tax_id = null;

		$unit_price_inc_tax = $product->default_sell_price;

		}

		@endphp



		<div class="modal fade row_edit_product_price_model in" id="row_edit_product_price_modal_{{$row_count}}"

			tabindex="-1" role="dialog">

			@include('sale_pos.partials.row_edit_product_price_modal')

		</div>



		<!-- Description modal start -->

		@if($toggle_popup == 0)

		<div class="modal fade row_description_modal in" id="row_description_modal_{{$row_count}}" tabindex="-1"

			role="dialog">

			<div class="modal-dialog" role="document">

				<div class="modal-content">

					<div class="modal-header">

						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

								aria-hidden="true">&times;</span></button>

						<h4 class="modal-title" id="myModalLabel">{{$product->product_name}} - {{$product->sub_sku}}

						</h4>

					</div>

					<div class="modal-body">

						<div class="form-group">

							<label>@lang('lang_v1.description')</label>

							@php

							$sell_line_note = '';

							if(!empty($product->sell_line_note)){

							$sell_line_note = $product->sell_line_note;

							}

							@endphp

							<textarea class="form-control" name="products[{{$row_count}}][sell_line_note]"

								rows="3">{{$sell_line_note}}</textarea>

							<p class="help-block">@lang('lang_v1.sell_line_description_help')</p>

						</div>

					</div>

					<div class="modal-footer">

						<button type="button" class="btn btn-default"

							data-dismiss="modal">@lang('messages.close')</button>

					</div>

				</div>

			</div>

		</div>

		@endif

		<!-- Description modal end -->

		@if(in_array('modifiers' , $enabled_modules))

		<div class="modifiers_html">

			@if(!empty($product->product_ms))

			@include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count'

			=> $loop->index, 'product_ms' => $product->product_ms ) )

			@endif

		</div>

		@endif



		@php
		
		if(empty($product->current_stock)){
		    $product->current_stock = $product->qty_available;
		}

		$max_qty_rule = $product->current_stock;
		
		$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=>

		$product->formatted_qty_available, 'unit' => $product->unit ]);

		@endphp



		@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)

		@php

		$lot_enabled = session()->get('business.enable_lot_number');

		$exp_enabled = session()->get('business.enable_product_expiry');

		$lot_no_line_id = '';

		if(!empty($product->lot_no_line_id)){

		$lot_no_line_id = $product->lot_no_line_id;

		}

		@endphp

		@if(!empty($product->lot_numbers))

		<select class="form-control lot_number input-sm" name="products[{{$row_count}}][lot_no_line_id]"

			@if(!empty($product->transaction_sell_lines_id)) disabled @endif>

			<option value="">@lang('lang_v1.lot_n_expiry')</option>

			@foreach($product->lot_numbers as $lot_number)

			@php

			$selected = "";

			if($lot_number->purchase_line_id == $lot_no_line_id){

			$selected = "selected";



			$max_qty_rule = $lot_number->qty_available;

			$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' =>

			$product->unit ]);

			}



			$expiry_text = '';

			if($exp_enabled == 1 && !empty($lot_number->exp_date)){

			if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){

			$expiry_text = '(' . __('report.expired') . ')';

			}

			}



			//preselected lot number if product searched by lot number

			if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {

			$selected = "selected";



			$max_qty_rule = $lot_number->qty_available;

			$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' =>

			$product->unit ]);

			}

			@endphp

			<option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}"

				data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])"

				{{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}}

				@endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 &&

				!empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif

				{{$expiry_text}}</option>

			@endforeach

		</select>

		@endif

		@endif



	</td>
	<td>

		{{-- If edit then transaction sell lines will be present --}}

		@if(!empty($product->transaction_sell_lines_id))

		<input type="hidden" name="products[{{$row_count}}][transaction_sell_lines_id]" class="form-control"

			value="{{$product->transaction_sell_lines_id}}">

		@endif



		<input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id"

			value="{{$product->product_id}}">



		<input type="hidden" value="{{$product->variation_id}}" name="products[{{$row_count}}][variation_id]"

			class="row_variation_id">



		<input type="hidden" value="{{$product->enable_stock}}" name="products[{{$row_count}}][enable_stock]">



		@if(empty($product->quantity_ordered))

		@php

		$product->quantity_ordered = 1;

		@endphp

		@endif

		@isset($temp_qty)

		@if(!empty($temp_qty))

		@php

		$product->quantity_ordered = $temp_qty;

		@endphp



		@endif



		@endisset



		@php

		$multiplier = 1;

		$allow_decimal = true;

		if($product->unit_allow_decimal != 1) {

		$allow_decimal = false;

		}

		@endphp

		@foreach($sub_units as $key => $value)

		@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)

		@php

		$multiplier = $value['multiplier'];

		$max_qty_rule = $max_qty_rule / $multiplier;

		$unit_name = $value['name'];

		$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' =>

		$unit_name ]);



		if(!empty($product->lot_no_line_id)){

		$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name ]);

		}



		if($value['allow_decimal']) {

		$allow_decimal = true;

		}

		@endphp

		@endif

		@endforeach

		<input type="text"
    class="form-control pos_quantity input_number mousetrap input_quantity"
    name="products[{{$row_count}}][quantity]"
    value="{{ @format_quantity($product->quantity_ordered) }}"
    data-min="0.01"
    step="0.01"
    inputmode="decimal"
    pattern="^\d*\.?\d*$"
    data-decimal="1"
    data-allow-overselling="@if(empty($pos_settings['allow_overselling']))false@else true @endif"
    data-rule-required="true"
    data-msg-required="@lang('validation.custom-messages.this_field_is_required')"

    @if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order))
        data-rule-max-value="{{$max_qty_rule}}"
        data-qty_available="{{$product->current_stock}}"
        data-msg-max-value="{{$max_qty_msg}}"
        data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit ])"
    @endif
>




		<input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">

		@if(count($sub_units) > 0)

		<br>

		<select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">

			@foreach($sub_units as $key => $value)

			<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}"

				data-unit_price="@if(array_key_exists($key,$default_multiple_unit_price)){{$default_multiple_unit_price[$key]}}@endif"

				data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}"

				@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>

				{{$value['name']}}

			</option>

			@endforeach

		</select>

		@else

		{{$product->unit}}

		@endif



		<input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]"

			value="{{$multiplier}}">



		<input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}">



		{{-- Hidden fields for combo products --}}

		@if($product->product_type == 'combo')



		@foreach($product->combo_products as $k => $combo_product)



		@if(isset($action) && $action == 'edit')

		@php

		$combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;



		$qty_total = $combo_product['quantity'];

		@endphp

		@else

		@php

		$qty_total = $combo_product['qty_required'];

		@endphp

		@endif



		<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][product_id]"

			value="{{$combo_product['product_id']}}">



		<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][variation_id]"

			value="{{$combo_product['variation_id']}}">



		<input type="hidden" class="combo_product_qty" name="products[{{$row_count}}][combo][{{$k}}][quantity]"

			data-unit_quantity="{{$combo_product['qty_required']}}" value="{{$qty_total}}">



		@if(isset($action) && $action == 'edit')

		<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]"

			value="{{$combo_product['id']}}">

		@endif



		@endforeach

		@endif

	</td>
	
	@if(!empty($pos_settings['inline_service_staff']))
	<td>

		<div class="form-group">

			<div class="input-group">

				{!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters,

				!empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' =>

				'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'),

				'required' => (!empty($pos_settings['is_service_staff_required']) &&

				$pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}

			</div>

		</div>

	</td>
	@endif

	<td class="{{$hide_tax}}">
        <input type="hidden" value="{{@number_format($unit_price_inc_tax)}}" class="pos_unit_price_inc_tax" />
		<input type="text" name="products[{{$row_count}}][unit_price_inc_tax]"
			   class="form-control input_number"
			   value="{{@num_format($unit_price_inc_tax)}}"
			   readonly
			   @if(!empty($pos_settings['enable_msp']))
			   data-rule-min-value="{{$unit_price_inc_tax}}"
			   data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}"
			   @endif>
	</td>



	@if(isset($is_sales_page) && $is_sales_page == '1')

	<td>

		<input type="text" name="products[{{$row_count}}][default_sell_price]" @if(isset($price_later) && $price_later=='1' ) disabled @endif

			class="products_default_sell_price_{{$row_count}} default_sell_price form-control input_number mousetrap"

			value="{{@num_format(!empty($product->default_sell_price) ? $product->default_sell_price : $product->default_purchase_price)}}">

	</td>

	@endif


	<td>

		{!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' =>

		__('lang_v1.percentage')], 'fixed', ['class' => 'form-control  row_discount_type_table','onchange'=>'updateDiscount(this.name,this.value)', 'disabled' => isset($price_later) && $price_later ==

		1 ? true : false]); !!}

	</td>

	<td>

		{!! Form::text("products[$row_count][line_discount_amount]", 0, ['class' => 'form-control input_number

		row_discount_amount_table','oninput'=>'updateDiscount(this.name,this.value)', 'disabled' => isset($price_later) && $price_later == 1 ? true : false]); !!}

	</td>

	@if (isset($pos_settings['hide_purchase_price']) && ($pos_settings['hide_purchase_price'] !='1'))
	<td class="price_later_td  @if(isset($price_later) && $price_later != 1) hide @endif">

		<input type="text" name="products[{{$row_count}}][last_purchased_price]"

			class="products_last_purchased_price_{{$row_count}} last_purchased_price form-control input_number mousetrap"
			@if(empty($pos_settings['is_pos_subtotal_editable'])) readonly @endif
			value="{{@num_format(!empty($product->last_purchased_price) ? $product->last_purchased_price : $product->dpp_inc_tax)}}">

	</td>
	@endif



	@php

	$enable_line_discount = !empty($pos_settings['enable_line_discount'])? 1: 0;

	@endphp

	@if($enable_line_discount)

	@if(isset($is_sales_page) && $is_sales_page != '1')

	<td class="text-center">

		<input type="text" name="products[{{$row_count}}][line_discount_val]" class="form-control line_discount_val"

			value="">

		<input type="hidden" name="products[{{$row_count}}][line_discount]" class="form-control pos_line_discount"

			value="">

		<div class="clearfix"></div>

		@if($product->weight_excess_loss_applicable)

		{!! Form::label('weight_loss', __('lang_v1.weight_loss')) !!}

		{!! Form::text('products['.$row_count.'][weight_loss]', !empty($product->weight_loss) ?

		@num_format($product->weight_loss) : null,

		['class' => 'form-control', 'id'=>'weight_loss']); !!}

		@endif

	</td>

	@endif

	@else
	
	@if($product->weight_excess_loss_applicable)

	<td>

		{!! Form::label('weight_loss', __('lang_v1.weight_loss')) !!}

		{!! Form::text('products['.$row_count.'][weight_loss]', !empty($product->weight_loss) ?

		@num_format($product->weight_loss) : null,

		['class' => 'form-control', 'id'=>'weight_loss']); !!}

	</td>
	
	@endif

	@endif



	@if(isset($is_sales_page) && $is_sales_page == '1')

	<td>

		<input type="text" name="products[{{$row_count}}][tax]" @if(isset($price_later) && $price_later=='1' ) disabled @endif

			class="products_tax_{{$row_count}} tax form-control input_number mousetrap"

			value="{{@num_format(!empty($product->tax) ? $product->tax : 0)}}">

	</td>

	<td>

		<input type="text" name="products[{{$row_count}}][sell_price_inc_tax]" @if(isset($price_later) && $price_later=='1' ) disabled @endif

			class="products_sell_price_inc_tax_{{$row_count}} sell_price_inc_tax form-control input_number mousetrap"

			value="{{@num_format(!empty($product->sell_price_inc_tax) ? $product->sell_price_inc_tax : $product->default_purchase_price)}}">

	</td>

	@endif

    
    <!--sub total-->

	<td class="text-center v-center">
	    @php
    $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
@endphp

<!-- Subtotal field that updates dynamically after discount -->
<input type="{{$subtotal_type}}"
       class="form-control pos_line_total input_number"
       id="subtotal_{{$row_count}}"
       name="products[{{$row_count}}][subtotal]"
       value="{{@num_format($product->quantity_ordered * $unit_price_inc_tax )}}"
       readonly>

<!-- Displayed subtotal text -->
<span class="display_currency pos_line_total_text"
      id="subtotal_text_{{$row_count}}"
      data-currency_symbol="true">
    {{@num_format($product->quantity_ordered * $unit_price_inc_tax)}}
</span>

<script>
    function updateSubtotal(row) {
		let discountType = document.querySelector(`select[name="products[${row}][line_discount_type]"]`).value;
		let discountValue = parseFloat(document.querySelector(`input[name="products[${row}][line_discount_amount]"]`).value.replace(/[^0-9.-]+/g, "")) || 0;
		let unitPrice = parseFloat(document.querySelector(`input[name="products[${row}][unit_price_inc_tax]"]`).value.replace(/[^0-9.-]+/g, ""));
		let quantity = parseFloat(document.querySelector(`input[name="products[${row}][quantity]"]`).value.replace(/[^0-9.-]+/g, "")) || 1;

		let subtotal = unitPrice * quantity;

		if (discountType === "percentage") {
			subtotal -= (subtotal * discountValue / 100);
		} else if (discountType === "fixed") {
			subtotal -= discountValue;
		}

		document.getElementById(`subtotal_${row}`).value = subtotal.toFixed(2);
		document.getElementById(`subtotal_text_${row}`).innerText = subtotal.toFixed(2);
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".row_discount_type, .row_discount_amount, .pos_quantity, .pos_unit_price_inc_tax").forEach(element => {
            element.addEventListener("input", function () {
                let row = this.name.match(/\d+/)[0];
                updateSubtotal(row);
            });
        });
    });

	function updateDiscount(elmName,value){
		let elm =  document.querySelector(`.modal-body [name='${elmName}']`);

		elm.value=value;		
		let row = elmName.match(/\d+/)[0];
		jQuery(elm).trigger('change');
	}
</script>

	</td>

	<td class="text-center">

		<i class="fa fa-close text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>

	</td>


	
</tr>

