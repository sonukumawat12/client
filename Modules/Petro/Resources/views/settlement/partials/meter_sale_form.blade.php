@php

$pump_no = null;
$pump_starting_meter = null;
$pump_closing_meter = null;
$sold_qty = null;
$meter_sale_unit_price = null;
$testing_qty = "0.00";
$meter_sale_discount_type = null;
$meter_sale_discount = "0.00";
$meter_sale_id = null;
if(!empty($meter_sale)){
    $pump_no = $meter_sale['pump_id'];
    $pump_starting_meter = number_format($meter_sale['starting_meter'], 3);
    $pump_closing_meter = number_format($meter_sale['closing_meter'], 3);
    $sold_qty = $meter_sale['qty'];
    $meter_sale_unit_price = $meter_sale['price'];
    $testing_qty = $meter_sale['testing_qty'];
    $meter_sale_discount_type = $meter_sale['discount_type'];
    $meter_sale_discount = $meter_sale['discount'];
    $meter_sale_id = $meter_sale['id'];
}
@endphp
<div class="col-md-12">
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('pump_no', __('petro::lang.pump_no').':') !!}
				{!! Form::select('pump_no', $pump_nos, $pump_no, ['class' => 'form-control meter_sale_fields check_pumper
				select2',
				'placeholder' => __('petro::lang.please_select')]); !!}
			</div>
		</div>
		<div class="col-md-2 pump_starting_meter_div">
			<div class="form-group">
				{!! Form::label('pump_starting_meter', __( 'petro::lang.pump_starting_meter' ) ) !!}
				{!! Form::text('pump_starting_meter', $pump_starting_meter, ['class' => 'form-control meter_sale_fields check_pumper
				input_number
				pump_starting_meter', 'required', 'readonly',
				'placeholder' => __(
				'petro::lang.pump_starting_meter' ) ]); !!}
			</div>
		</div>
		<div class="col-md-2 pump_closing_meter_div">
			<div class="form-group">
				{!! Form::label('pump_closing_meter', __( 'petro::lang.pump_closing_meter' ) ) !!}
				{!! Form::text('pump_closing_meter', $pump_closing_meter, ['class' => 'form-control meter_sale_fields check_pumper
				input_number
				pump_closing_meter',
				'required',
				'step' => '0.001',
				'min' => '0',
				'placeholder' => __(
				'petro::lang.pump_closing_meter' ) ]); !!}
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('sold_qty', __( 'petro::lang.sold_qty' ) ) !!}
				{!! Form::text('sold_qty', $sold_qty, ['class' => 'form-control meter_sale_fields check_pumper sold_qty
				input_number',
				'required', 'disabled',
				'placeholder' => __(
				'petro::lang.sold_qty' ) ]); !!}
				<input type="hidden" class="meter_sale_fields is_from_pumper" id="is_from_pumper" value="0">
				
				<input type="hidden" class="meter_sale_fields assignment_id" id="assignment_id" value="0">
				<input type="hidden" class="meter_sale_fields pumper_entry_id" id="pumper_entry_id" value="0">
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('unit_price', __( 'petro::lang.unit_price' ) ) !!}
				{!! Form::text('meter_sale_unit_price', $meter_sale_unit_price, ['id' => 'meter_sale_unit_price', 'class' => 'form-control
				meter_sale_fields check_pumper unit_price input_number',
				'readonly',
				'placeholder' => __(
				'petro::lang.unit_price' ) ]); !!}
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('testing_qty', __( 'petro::lang.testing_qty' ) ) !!}
				{!! Form::text('testing_qty', $testing_qty, ['class' => 'form-control check_pumper input_number
				testing_qty', 'required',
				'placeholder' => __(
				'petro::lang.testing_qty' ) ]); !!}
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('meter_sale_discount_type', __( 'petro::lang.discount_type' ) ) !!}
				{!! Form::select('meter_sale_discount_type', $discount_types, $meter_sale_discount_type, ['class' => 'form-control meter_sale_fields check_pumper
				input_number
				meter_sale_discount_type', 'required',
				'placeholder' => __(
				'petro::lang.please_select' ) ]); !!}
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				{!! Form::label('meter_sale_discount', __( 'petro::lang.discount' ) ) !!}
				{!! Form::text('meter_sale_discount', $meter_sale_discount, ['class' => 'form-control meter_sale_fields check_pumper
				input_number
				meter_sale_discount', 'required',
				'placeholder' => __(
				'petro::lang.discount' ) ]); !!}
			</div>
		</div>
		{!! Form::hidden('bulk_sale_meter', 0, ['id' => 'bulk_sale_meter']) !!}
        @if(!$meter_sale_id)
		<div class="col-md-1 pull-right">
			<button type="button" class="btn btn-primary btn_meter_sale"
				style="margin-top: 23px;">@lang('messages.add')</button>
		</div>
        @else
		<input type="hidden" name="is_edit" value="1">
		<div class="col-md-2 pull-right">
			<button type="button" class="btn btn-danger btn_meter_sale_cancel"  data-href="/petro/settlement/get-meter-sale-form/{{$meter_sale_id}}"
				style="margin-top: 23px;">@lang('messages.cancel')</button>
			<button type="button" class="btn btn-primary btn_update_meter_sale"   data-href="/petro/settlement/update-settlement-meter-sale/{{$meter_sale_id}}"
				style="margin-top: 23px;">@lang('messages.update')</button>
		</div>
        @endif
	</div>