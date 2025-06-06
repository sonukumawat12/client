@extends('layouts.app')

@section('title', __('sale.add_sale'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header page-title-area" style="padding-top: 0px;">
	<div class="row">
		<div class="col-md-6">
			<h3>@lang('sale.add_sale')</h3>

		</div>
		<div class="col-md-6">
			<br>
			<div class="col-md-6 text-red" style="font-size: 18px; font-weight: bold">
				@lang('lang_v1.customer'): <span class="customer_name"></span>
			</div>
			<div class="col-md-6 text-red" style="font-size: 18px; font-weight: bold">
				@lang('lang_v1.due_amount'): <span class="customer_due_amount"></span>
			</div>
		</div>
	</div>
</section>
<!-- Main content -->
<section class="content main-content-inner no-print">
    
	@if(!empty($pos_settings['allow_overselling']))
	<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
	<input type="hidden" id="reward_point_enabled">
	@endif
	
	<input type="hidden" id="module" value="sales_addsales">

	@if(!is_null($default_location))
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-map-marker"></i>
					</span>
					{!! Form::select('select_location_id', $business_locations, null, ['class' => 'form-control
					input-sm',
					'placeholder' => __('lang_v1.select_location'),
					'id' => 'location_id',
					'required', 'autofocus'], $bl_attributes); !!}
					<span class="input-group-addon">
						@show_tooltip(__('tooltip.sale_location'))
					</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-map-marker"></i>
					</span>
					{!! Form::select('select_store_id', $stores, null, ['class' => 'select_store_id form-control
					input-sm',
					'placeholder' => __('Select Store'),
					'id' => 'select_store_id',
					'required']); !!}
					<span class="input-group-addon">
						@show_tooltip(__('Store from where you want to sell'))
					</span>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<h4 class="invoice_no" style="margin: 0; width: 150px;">Invoice No:</h4>
		</div>
	</div>
	@endif
	<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	<input type="hidden" id="service_addition_method" value="{{$business_details->service_addition_method}}">
	{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_sell_form' ]) !!}
	<div class="row">
		<input type="hidden" name="store_id" id="store_id" value="">
		<div class="col-md-12 col-sm-12">
			@component('components.widget', ['class' => 'box-primary'])
			<input type="hidden" name="price_later" id="price_later" value="0">
			{!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type' =>
			isset($bl_attributes[$default_location]['data-receipt_printer_type']) ?
			$bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}

			@if(!empty($price_groups))
			@if(count($price_groups) > 1)
			<div class="col-sm-4">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-money"></i>
						</span>
						@php
						reset($price_groups);
						@endphp
						{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
						{!! Form::select('price_group', $price_groups,
						!empty($temp_data->price_group)?$temp_data->price_group:null, ['class' => 'form-control
						select2', 'id' =>
						'price_group']); !!}
						<span class="input-group-addon">
							@show_tooltip(__('lang_v1.price_group_help_text'))
						</span>
					</div>
				</div>
			</div>

			@else
			@php
			reset($price_groups);
			@endphp
			{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
			@endif
			@endif

			{!! Form::hidden('default_price_group',
			!empty($temp_data->default_price_group)?$temp_data->default_price_group:null, ['id' =>
			'default_price_group']) !!}

			@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-external-link text-primary service_modal_btn"></i>
						</span>
						{!! Form::select('types_of_service_id', $types_of_service, null, ['class' => 'form-control',
						'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder' =>
						__('lang_v1.select_types_of_service')]); !!}

						{!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group'])
						!!}

						<span class="input-group-addon">
							@show_tooltip(__('lang_v1.types_of_service_help'))
						</span>
					</div>
					<small>
						<p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'): <span></span></p>
					</small>
				</div>
			</div>
			<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog"
				aria-labelledby="gridSystemModalLabel"></div>
			@endif

			@if(in_array('subscription', $enabled_modules))
			<div class="col-md-4 pull-right col-sm-6">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('is_recurring', 1, false, ['class' => 'input-icheck', 'id' =>
						'is_recurring']); !!} @lang('lang_v1.subscribe')?
					</label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal"
						class="btn btn-link"><i
							class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
				</div>
			</div>
			@endif
			<div class="clearfix"></div>
			<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
				<div class="form-group">
					{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						<input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id']}}">
						<input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name']}}">
						{!! Form::select('contact_id',
						[], !empty($temp_data->contact_id)?$temp_data->contact_id:null, ['class' => 'form-control
						mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter
						Customer name / phone', 'required']); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat add_new_customer"
								data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<div class="multi-input">
						{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!}
						@show_tooltip(__('tooltip.pay_term'))
						<br />
						{!! Form::number('pay_term_number',
						!empty($temp_data->pay_term_number)?$temp_data->pay_term_number :
						$walk_in_customer['pay_term_number'], ['class' =>
						'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

						{!! Form::select('pay_term_type',
						['months' => __('lang_v1.months'),
						'days' => __('lang_v1.days')],
						!empty($temp_data->pay_term_type)?$temp_data->pay_term_type :
						$walk_in_customer['pay_term_type'],
						['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]);
						!!}
					</div>
				</div>
			</div>

			@if(!empty($commission_agent))
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
					{!! Form::select('commission_agent',
					$commission_agent, null, ['class' => 'form-control select2']); !!}
				</div>
			</div>
			@endif
			<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
				<div class="form-group">
					{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('transaction_date',
						!empty($temp_data->transaction_date)?$temp_data->transaction_date : $default_datetime, ['class'
						=> 'form-control', 'readonly',
						'required']); !!}
					</div>
				</div>
			</div>
			<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
				<div class="form-group">
					{!! Form::label('status', __('sale.status') . ':*') !!}
					{!! Form::select('status', $status_array,!empty($temp_data->status)?$temp_data->status : null,
					['class' =>
					'form-control select2', 'placeholder' =>
					__('messages.please_select'), 'required']); !!}
				</div>
			</div>
			<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif need_to_reserve hide">
				<div class="form-group">
					{!! Form::label('need_to_reserve', __('lang_v1.need_to_reserve') . ':') !!}
					{!! Form::select('need_to_reserve', ['yes' => 'Yes', 'no' => 'No'], null, ['class' =>
					'form-control select2', 'placeholder' =>
					__('messages.please_select'), 'style' => 'width: 100%;']); !!}
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
					{!! Form::select('invoice_scheme_id', $invoice_schemes,
					!empty($temp_data->invoice_scheme_id)?$temp_data->invoice_scheme_id : $default_invoice_schemes->id,
					['class' =>
					'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<!-- Call restaurant module if defined -->
			@if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
			<span id="restaurant_module_span">
				<div class="col-md-3"></div>
			</span>
			@endif
			@endcomponent

			@component('components.widget', ['class' => 'box-primary'])
			<div class="col-sm-10 col-sm-offset-1">
				@if(!empty($pos_settings['price_later_sales']))
				<div class="row">
					<a href="#" style="margin-top: 2px; background: rgb(202, 132, 2); color: #fff; margin-right: 50px;"
						type="button" class="btn btn-price-later btn-flat btn-sm pull-right">
						@lang('lang_v1.price_later')
					</a>
				</div>
				<br>
				<div class="clearfix"></div>
				@endif
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal"
								data-target="#configure_search_modal"
								title="{{__('lang_v1.configure_product_search')}}"><i
									class="fa fa-barcode"></i></button>
						</div>
						{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' =>
						'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
						'disabled' => is_null($default_location)? true : false,
						'autofocus' => is_null($default_location)? false : true,
						]); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product"
								data-href="{{action('ProductController@quickAdd')}}"
								data-container=".quick_add_product_modal"><i
									class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
			</div>

			<div class="row col-sm-12 pos_product_div" style="min-height: 0">

				<input type="hidden" name="sell_price_tax" id="sell_price_tax"
					value="{{!empty($temp_data->sell_price_tax)?$temp_data->sell_price_tax :$business_details->sell_price_tax}}">

				<!-- Keeps count of product rows -->
				<input type="hidden" id="is_sales_page" value="1">
				<input type="hidden" id="product_row_count" value="0">
				@php
				$hide_tax = '';
				if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
				}
				@endphp
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
						<thead>
							<tr>
								<th class="text-center">
									@lang('sale.product')
								</th>
								<th class="text-center">
									@lang('sale.qty')
								</th>
								@if(!empty($pos_settings['inline_service_staff']))
								<th class="text-center">
									@lang('restaurant.service_staff')
								</th>
								@endif
								
								<th class="text-center {{$hide_tax}}">
									@lang('sale.unit_price_inc_tax')
								</th>
								
								@if(isset($is_sales_page) && $is_sales_page == '1')
    								<th>@lang('sale.unit_price')</th>
    								<th>@lang('sale.discount_type')</th>
    								<th>@lang('sale.discount')</th>
								@endif
				
								<th class="price_later_td @if(isset($price_later) && $price_later != 1) hide @endif">@lang('lang_v1.purchase_price')</th>
								@if(isset($is_sales_page) && $is_sales_page == '1')
								<th>@lang('sale.tax')</th>
								<th class="text-center {{$hide_tax}}">
									@lang('sale.price_inc_tax')
								</th>
								@endif
								
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody id="saleBody">
							{{-- @if(!empty($temp_data->products))
							@foreach ($temp_data->products as $key => $product)
								@include('sale_pos.product_row', ['row_count' => $key, 'temp_product' => $product])
							@endforeach
							@endif --}}
						</tbody>
					</table>
				</div>
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped">
						<tr>
							<td>
								<div class="pull-right">
									<b>@lang('sale.item'):</b>
									<span class="total_quantity">0</span>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>@lang('sale.total'): </b>
									<span class="price_total">0</span>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			@endcomponent

			@component('components.widget', ['class' => 'box-primary'])
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' =>
						__('lang_v1.percentage')], !empty($temp_data->discount_type)?$temp_data->discount_type
						:'percentage' , ['class' => 'form-control','placeholder' =>
						__('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::text('discount_amount',
						@num_format(!empty($temp_data->discount_amount)?str_replace(',', ''
						,$temp_data->discount_amount) :$business_details->default_sales_discount),
						['class' => 'form-control input_number', 'data-default' =>
						$business_details->default_sales_discount]); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4"><br>
				<b>@lang( 'sale.discount_amount' ):</b>(-)
				<span class="display_currency" id="total_discount">0</span>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
				<input type="hidden" name="rp_redeemed" id="rp_redeemed"
					value="{{!empty($temp_data->rp_redeemed)?$temp_data->rp_redeemed :0}}">
				<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount"
					value="{{!empty($temp_data->rp_redeemed_amount)?$temp_data->rp_redeemed_amount :0}}">
				<div class="col-md-12">
					<h4>{{session('business.rp_name')}}</h4>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-gift"></i>
							</span>
							{!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input',
							'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0,
							'data-max_points' => 0, 'data-min_order_total' =>
							session('business.min_order_total_for_redeem') ]); !!}
							<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
				</div>
				<div class="col-md-4">
					<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span>
					</p>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::select('tax_rate_id', $taxes['tax_rates'],
						!empty($temp_data->tax_rate_id)?$temp_data->tax_rate_id :$business_details->default_sales_tax,
						['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=>
						$business_details->default_sales_tax], $taxes['attributes']); !!}

						<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
							value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(!empty($temp_data->tax_calculation_amount)?str_replace(',', '' ,$temp_data->tax_calculation_amount) :optional($transaction->tax)->amount)}} @endif"
							data-default="{{$business_details->tax_calculation_amount}}">
					</div>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-4">
				<b>@lang( 'sale.order_tax' ):</b>(+)
				<span class="display_currency" id="order_tax">0</span>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('shipping_details', __('sale.shipping_details')) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!!
						Form::textarea('shipping_details',!empty($temp_data->shipping_details)?$temp_data->shipping_details
						:null, ['class' => 'form-control','placeholder' =>
						__('sale.shipping_details') ,'rows' => '1', 'cols'=>'30']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-map-marker"></i>
						</span>
						{!!
						Form::textarea('shipping_address',!empty($temp_data->shipping_address)?$temp_data->shipping_address
						:null, ['class' => 'form-control','placeholder' =>
						__('lang_v1.shipping_address') ,'rows' => '1', 'cols'=>'30']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!!Form::text('shipping_charges',@num_format(!empty($temp_data->shipping_charges)?str_replace(',',
						'' ,$temp_data->shipping_charges) :0.00),['class'=>'form-control
						input_number','placeholder'=> __('sale.shipping_charges')]);!!}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
					{!! Form::select('shipping_status',$shipping_statuses,
					!empty($temp_data->shipping_status)?$temp_data->shipping_status :null, ['class' =>
					'form-control','placeholder' => __('messages.please_select')]); !!}
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
					{!! Form::text('delivered_to', !empty($temp_data->delivered_to)?$temp_data->delivered_to :null,
					['class' => 'form-control','placeholder' =>
					__('lang_v1.delivered_to')]); !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 col-md-offset-8">
				<div><b>@lang('sale.total_payable'): </b>
					<input type="hidden" name="final_total" id="final_total_input"
						value="{{!empty($temp_data->final_total)?$temp_data->final_total : 0}}">
					<span id="total_payable">{{!empty($temp_data->final_total)?$temp_data->final_total :0}}</span>
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					{!! Form::label('sell_note',__('sale.sell_note')) !!}
					{!! Form::textarea('sale_note',!empty($temp_data->sale_note)?$temp_data->sale_note : null, ['class'
					=> 'form-control', 'rows' => 3]); !!}
				</div>
			</div>
			<input type="hidden" name="is_direct_sale"
				value="{{!empty($temp_data->is_direct_sale)?$temp_data->is_direct_sale :1}}">
			@endcomponent

		</div>
	</div>
	@can('sell.payments')
	@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
	<div class="box-body payment_row" data-row_id="0">
		<div id="payment_rows_div">
		@if (!empty($temp_data->payment))
		@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'payment' => $temp_data->payment[0]])
		@else
		@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
		@endif
		<hr>
		<div class="row hide" id="credit_sale_field">
			<div class="col-md-3">
				{!! Form::label('order_no',__('sale.order_no')) !!}
				{!! Form::text('order_no', null, ['class'
				=> 'form-control']); !!}
			</div>
			<div class="col-md-3">
				{!! Form::label('order_date',__('sale.order_date')) !!}
				{!! Form::text('order_date', null, ['class'
				=> 'form-control']); !!}
			</div>
			<div class="col-md-3">
				{!! Form::label('customer_ref',__('sale.customer_ref')) !!}
				{!! Form::select('customer_ref', [], null, ['class'
				=> 'form-control select', 'id' => 'customer_ref']); !!}
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span>
				</div>
			</div>
		</div>
	    </div>
	</div>
	@endcomponent
	@endcan

	<div class="row">
		<div class="col-sm-12">
			<button type="button" class="btn btn-danger pull-left btn-flat"
				id="request_approval">@lang('lang_v1.request_approval')</button>

			<input type="hidden" name="submit_type" id="submit_type">
			<button type="button" name="save" value="save" id="submit-sell"
				class="btn btn-primary pull-right btn-flat submit-sell">@lang('messages.save')</button>
			<button type="button" value="save_and_print" style="margin-right: 10px;" id="save-and-print"
				class="btn btn-primary pull-right btn-flat submit-sell">@lang('messages.save_and_print')</button>
		</div>
	</div>

	@if(empty($pos_settings['disable_recurring_invoice']))
	@include('sale_pos.partials.recurring_invoice_modal')
	@endif
	<input type="hidden" name="is_duplicate" value="0" id="is_duplicate">
	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

@include('sale_pos.partials.configure_search_modal')

@stop
@php
$business_id = request()->session()->get('user.business_id');
$subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
$enable_duplicate_invoice = 0;
if (!empty($subscription)) {
$package = DB::table('packages')->where('id', $subscription->package_id)->select('enable_duplicate_invoice')->first();
$enable_duplicate_invoice = $package->enable_duplicate_invoice;

}
@endphp
@section('javascript')
<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('plugins/mousetrap/mousetrap.min.js?v=' . $asset_v) }}"></script>
<script>
	$('#request_approval').click(function(){
		let customer_id = $('#customer_id').val();

		$.ajax({
			method: 'get',
			url: '/customer-limit-approval/send-reuqest-for-approval/'+customer_id,
			data: {  },
			success: function(result) {
				if(result.success === 1){
					toastr.success(result.msg)
				}
			},
		});
	})

	$('#status').change(function(){
		if($(this).val() == 'order'){
			$('.need_to_reserve').removeClass('hide');
		}else{
			$('.need_to_reserve').addClass('hide');
		}
	})


	@if($enable_duplicate_invoice )
	@if(!empty($shortcuts["pos"]["duplicate_invoice"]) && ($pos_settings['disable_duplicate_invoice'] == 0))
		Mousetrap.bind('{{$shortcuts["pos"]["duplicate_invoice"]}}', function(e) {
			e.preventDefault();
			if(parseInt($('#is_duplicate').val()) == 1){
				$('#is_duplicate').val('0');
			}else{
				$('#is_duplicate').val('1');
			}
			$('#is_duplicate').trigger('change');
		});
	@endif
	@endif
	
	$('#order_date').datepicker().datepicker("setDate", new Date());
	$('#customer_id').change(function(){
		$.ajax({
			method: 'get',
			contentType: 'html',
			url: '/get-customer-reference/'+$(this).val(),
			data: { },
			success: function(result) {
				$('#customer_ref').empty().append(result);
			},
		});

		$.ajax({
			method: 'get',
			url: '/pos/get_customer_details',
			data: { customer_id : $(this).val() },
			success: function(result) {
				$('.customer_name').text(result.customer_name);
				$('.customer_due_amount').text(result.due_amount);
				if(result.sol_with_approval === 1){
					$('#request_approval').removeClass('hide');
				}else{
					$('#request_approval').addClass('hide');
				}
			},
		});
	});
	$('#method_0').change(function(){
		if($(this).val() == 'credit_sale'){
			$('#credit_sale_field').removeClass('hide');
		}else{
			$('#credit_sale_field').addClass('hide');
		}
	});
</script>
<!-- Call restaurant module if defined -->
@if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff'
,$enabled_modules))
<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
@endif

<script>
	setInterval(function(){ 
		$.ajax({
                method: 'POST',
                url: '{{action("TempController@saveAddSaleTemp")}}',
                dataType: 'json',
                data: $('#add_sell_form').serialize(),
                success: function(data) {
                   console.log(data);
				   
                },
			});
	}, 10000);


		var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        var is_direct_sell = false;
        if (
            $('input[name="is_direct_sale"]').length > 0 &&
            $('input[name="is_direct_sale"]').val() == 1
        ) {
            is_direct_sell = true;
        }

        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = parseInt($('#price_group').val());
        }

        //If default price group present
        if ($('#default_price_group').length > 0 && 
            !price_group) {
            price_group = $('#default_price_group').val();
        }

        //If types of service selected give more priority
        if ($('#types_of_service_price_group').length > 0 && 
            $('#types_of_service_price_group').val()) {
            price_group = $('#types_of_service_price_group').val();
		}
		

		 @if(!empty($temp_data->products))
		 @php $i = -1; @endphp
		 @foreach($temp_data->products as $product)
		 base_url = '{{URL::to('/')}}';
		 qty = parseInt({{$product->quantity}});
		 $.ajax({
            method: 'GET',
            url: base_url+'/sells/pos/get_product_row_temp/{{$product->variation_id}}/' + location_id+ '/'+qty,
            data: {
                product_row: {{$i}},
                customer_id: customer_id,
                is_direct_sell: is_direct_sell,
                price_group: price_group,
                purchase_line_id: null
            },
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $('table#pos_table tbody')
                        .append(result.html_content)
						.find('input.pos_quantity');
					//increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
                    var this_row = $('table#pos_table tbody')
                        .find('tr')
                        .last();
                    pos_each_row(this_row);

                    //For initial discount if present
                    var line_total = __read_number(this_row.find('input.pos_line_total'));
                    this_row.find('span.pos_line_total_text').text(line_total);

                    pos_total_row();

                    //Check if multipler is present then multiply it when a new row is added.
                    if(__getUnitMultiplier(this_row) > 1){
                        this_row.find('select.sub_unit').trigger('change');
                    }

                    if (result.enable_sr_no == '1') {
                        var new_row = $('table#pos_table tbody')
                            .find('tr')
                            .last();
                        new_row.find('.add-pos-row-description').trigger('click');
                    }

                    round_row_to_iraqi_dinnar(this_row);
                    __currency_convert_recursively(this_row);

                    $('input#search_product')
                        .focus()
                        .select();

                    //Used in restaurant module
                    if (result.html_modifier) {
                        $('table#pos_table tbody')
                            .find('tr')
                            .last()
                            .find('td:first')
                            .append(result.html_modifier);
                    }

                    //scroll bottom of items list
                    $(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);
                } else {
                    toastr.error(result.msg);
                    $('input#search_product')
                        .focus()
                        .select();
                }
				}
				

		});
		@php $i++; @endphp
		@endforeach
		 @endif

$('#is_duplicate').change(function(){
	getInvoice();
});

function getInvoice() {
	$.ajax({
		method: 'get',
		url: '{{action("SellController@getInvoiveNo")}}',
		data: { location_id: $('#location_id').val() },
		success: function(result) {
			if(parseInt($('#is_duplicate').val()) == 1){
				$('.invoice_no').text('Invoice No: '+result.duplicate_invoice_no);
			}else{
				$('.invoice_no').text('Invoice No: '+result.orignal_invoice_no);
			}
			
			$('.payment_types_dropdown').val('cash');
			$('.payment_types_dropdown').trigger('change');
		},
	});
}

$(document).on('change', '.payment_types_dropdown', function(e) {
    var payment_type = $(this).val();
   
	if(payment_type == 'direct_bank_deposit' || payment_type == 'bank_transfer'){
		$('.account_module').removeClass('hide');
	}else{
		$('.account_module').addClass('hide');
	}
});

$(document).on('change', '#select_store_id', function(e) {
	var store_id = $("select#select_store_id option").filter(":selected").val();
	$("#store_id").val(store_id);
	if ($('input#location_id').val()) {
        $('input#search_product').prop('disabled', false).focus();
    } else {
        $('input#search_product').prop('disabled', true);
    }
});
</script>
@endsection