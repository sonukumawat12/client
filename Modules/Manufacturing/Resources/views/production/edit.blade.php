@extends('layouts.app')
@section('title', __('manufacturing::lang.production'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('manufacturing::lang.production') </h1>
</section>

<!-- Main content -->
<section class="content">

	{!! Form::open(['url' => action('\Modules\Manufacturing\Http\Controllers\ProductionController@update',
	[$production_purchase->id]), 'method' => 'put', 'id' => 'production_form', 'files' => true ]) !!}
	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
				{!! Form::text('ref_no', $production_purchase->ref_no, ['class' => 'form-control']); !!}
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('transaction_date', __('manufacturing::lang.mfg_date') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					{!! Form::text('transaction_date', @format_datetime($production_purchase->transaction_date),
					['class' => 'form-control', 'readonly', 'required']); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('location_id', __('purchase.business_location').':*') !!}
				@show_tooltip(__('tooltip.purchase_location'))
				{!! Form::select('location_id', $business_locations, $production_purchase->location_id, ['class' =>
				'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
			</div>
		</div>
		@php
		$purchase_line = $production_purchase->purchase_lines[0];
		@endphp
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('variation_id_shown', __('sale.product').':*') !!}
				{!! Form::select('variation_id_shown', $recipe_dropdown, $purchase_line->variation_id, ['class' =>
				'form-control', 'placeholder' => __('messages.please_select'), 'required', 'disabled']); !!}
				{!! Form::hidden('variation_id', $purchase_line->variation_id, ['id' => 'variation_id']); !!}
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('recipe_quantity', __('lang_v1.quantity').':*') !!}
				<div class="@if(!empty($sub_units)) input_inline @else input-group @endif" id="recipe_quantity_input">
					{!! Form::text('quantity', @num_format($quantity), ['class' => 'form-control input_number', 'id' =>
					'recipe_quantity', 'required', 'data-rule-notEmpty' => 'true', 'data-rule-notEqualToWastedQuantity'
					=> 'true']); !!}
					<span class="@if(empty($sub_units)) input-group-addon @endif" id="unit_html">
						@if(!empty($sub_units))
						<select name="sub_unit_id" class="form-control" id="sub_unit_id">
							@foreach($sub_units as $key => $value)
							<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}"
								data-unit_name="{{$value['name']}}" @if($key==$sub_unit_id) selected @endif>{{$value['name']}}</option>
							@endforeach
						</select>
						@else
						{{ $unit_name }}
						@endif
					</span>
				</div>
			</div>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary', 'title' => __('manufacturing::lang.ingredients')])
	<div class="row">
		<div class="col-md-12">
			<div id="enter_ingredients_table">
				@include('manufacturing::recipe.ingredients_for_production')
			</div>
		</div>
	</div>
	@if(count($costs)>0)
            <div class="row">
				<div class="col-md-12">
				  <table class="table table-striped table-th-green">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.type')</th>
								<th>@lang('manufacturing::lang.name')</th>
								<th>@lang('manufacturing::lang.fixed_percentage')</th>
								<th>@lang('manufacturing::lang.value')</th>
								<th>@lang('manufacturing::lang.total')</th>
							</tr>
						</thead>
						<tbody>
						@foreach($costs as $cost)
						<tr>
							<td>{{ucfirst($cost->type)}}</td>
							<td>{{ucfirst($cost->name)}}</td>
							<td>{{ucfirst($cost->cost_type)}}</td>
							<td>{{$cost->cost_value}}</td>
							<td><span class="display_currency" data-currency_symbol="true">{{$cost->cost_total}}</td>
						</tr>
						@endforeach
						</tbody>
						
					</table>
				</div>
			</div>
            @endif
			<div class="row">
      			<div class="col-md-6 py-3">
      				<!-- <strong>@lang('manufacturing::lang.wastage'):</strong>
      				{{$recipe->waste_percent ?? 0}} % <br> -->
      				<strong>@lang('manufacturing::lang.byproducts_available'):</strong>
      				@if(!empty($recipe->by_product_available)){{ucfirst($recipe->by_product_available)}} @endif
      			</div>
				@if($recipe->by_product_available=='yes')
      			<div class="col-md-12">
				  <table class="table table-striped table-th-green">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.by_products')</th>
								<th>@lang('lang_v1.quantity')</th>
							</tr>
						</thead>
						<tbody>
						@foreach($rec_by_products as $prod)
						<tr>
							<td>{{$prod->name}}</td>
							<td>{{$prod->output_qty}} {{$prod->unit}}</td>
						</tr>
						@endforeach
						</tbody>
						
					</table>
				</div>
				@endif
      		</div>
	<div class="row">
		@if(request()->session()->get('business.enable_lot_number') == 1)
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('lot_number', __('lang_v1.lot_number').':') !!}
				{!! Form::text('lot_number', $purchase_line->lot_number, ['class' => 'form-control']); !!}
			</div>
		</div>
		@endif
		@if(session('business.enable_product_expiry'))
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('exp_date', __('product.exp_date').':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					{!! Form::text('exp_date', !empty($purchase_line->exp_date) ? @format_date($purchase_line->exp_date)
					: null, ['class' => 'form-control', 'readonly']); !!}
				</div>
			</div>
		</div>
		@endif
		<div class="col-md-3">
			<div class="form-group">
				{!! Form::label('mfg_wasted_units', __('manufacturing::lang.waste_units').':') !!}
				@show_tooltip(__('manufacturing::lang.wastage_tooltip'))
				<div class="input-group">
					{!! Form::text('mfg_wasted_units', @num_format($production_purchase->mfg_wasted_units), ['class' =>
					'form-control input_number']); !!}
					<span class="input-group-addon" id="wasted_units_text">{{$unit_name}}</span>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				{!! Form::label('production_cost', __('manufacturing::lang.production_cost').':') !!}
				@show_tooltip(__('manufacturing::lang.production_cost_tooltip'))
				<div class="input-group">
					{!! Form::text('production_cost', @num_format($production_purchase->mfg_production_cost), ['class'
					=> 'form-control input_number']); !!}
					<span class="input-group-addon"><i class="fa fa-percent"></i></span>
				</div>
			</div>
		</div>
	</div>

	<!-- By-Product Start -->
	<div class="row">
		<div class="col-md-12">
			@include('manufacturing::production.by_product_row', ['ingredient' => (object) $by_products])
		</div>
	</div>

	<!-- By-Product End -->


	<div class="row">
		<div class="col-md-3 col-md-offset-9">
			{!! Form::hidden('final_total', @num_format($production_purchase->final_total), ['id' => 'final_total']);
			!!}
			<strong>
				{{__('manufacturing::lang.total_production_cost')}}:
			</strong>
			<span id="total_production_cost" class="display_currency"
				data-currency_symbol="true">{{$total_production_cost}}</span><br>
			<strong>
				{{__('manufacturing::lang.total_cost')}}:
			</strong>
			<span id="final_total_text" class="display_currency"
				data-currency_symbol="true">{{ $production_purchase->final_total }}</span>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-9">
			<div class="form-group">
				<br>
				<div class="checkbox">
					<label>
						{!! Form::checkbox('finalize', 1, false, ['class' => 'input-icheck', 'id' => 'finalize']); !!}
						@lang('manufacturing::lang.finalize')
					</label> @show_tooltip(__('manufacturing::lang.finalize_tooltip'))
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn btn-primary pull-right">@lang('messages.submit')</button>
		</div>
	</div>
	@endcomponent

	{!! Form::close() !!}
</section>
@endsection

@section('javascript')
@include('manufacturing::production.production_script')

<script type="text/javascript">
	$(document).ready( function () {
			calculateRecipeTotal();
		});
</script>
@endsection