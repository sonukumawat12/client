@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('content')



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('purchase.add_purchase')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Purchases</a></li>
                    <li><span>@lang('purchase.add_purchase')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<section class="content main-content-inner">

	<div class="row">
		@isset($adsForPurchasePageArray[1])
		<div class="col-md-6 border_shadow">			
			<a href="{{$adsForPurchasePageArray[1]->link}}">
				<img src="{{asset($adsForPurchasePageArray[1]->content)}}">
			</a>
		</div>
		@endisset
		@isset($adsForPurchasePageArray[2])
		<div class="col-md-6 border_shadow">			
			<a href="{{$adsForPurchasePageArray[2]->link}}">
				<img src="{{asset($adsForPurchasePageArray[2]->content)}}">
			</a>
		</div>
		@endisset
	</div>
	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action('PurchaseController@store'), 'method' => 'post', 'id' => 'add_purchase_form',
	'files' => true ]) !!}
	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('purchase_no', __('purchase.purchase_no').':') !!}
				{!! Form::text('invoice_no', !empty($purchase_no) ? $purchase_no : 1, ['class' => 'form-control',
				'readonly'] ); !!}
			</div>
		</div>
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-user"></i>
					</span>
					{!! Form::select('contact_id', [], !empty($temp_data->contact_id)?$temp_data->contact_id:null,
					['class' => 'form-control', 'placeholder' =>
					__('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
					<span class="input-group-btn">
						<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i
								class="fa fa-plus-circle text-primary fa-lg"></i></button>
					</span>
				</div>
			</div>
		</div>
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('ref_no', __('purchase.p_invoice_no').':') !!}
				{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no:null, ['class' =>
				'form-control']); !!}
			</div>
		</div>
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					
					{!! Form::text('transaction_date',
					@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'), ['class'
					=> 'form-control',
					'required']); !!}
				</div>
			</div>
		</div>
		
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('invoice_date', __('purchase.invoice_date') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					{!! Form::date('invoice_date', date('Y-m-d'), ['class'
					=> 'form-control', 
					'required']); !!}
				</div>
			</div>
		</div>
		
		<div class="col-sm-3 @if(!empty($default_purchase_status)) hide @endif">
			<div class="form-group">
				{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
				@show_tooltip(__('tooltip.order_status'))
				{!! Form::select('status', $orderStatuses,
				!empty($temp_data->status)?$temp_data->status:$default_purchase_status, ['class' => 'form-control
				select2',
				'placeholder' => __('messages.please_select'), 'required']); !!}
			</div>
		</div>
		
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('is_vat', __('lang_v1.is_vat')) !!}
				{!! Form::select('is_vat', ['0' => __('lang_v1.no'),'1' => __('lang_v1.yes')],null, ['class' => 'form-control
				select2', 'required']); !!}
			</div>
		</div>

		<div class="clearfix"></div>

		@if(count($business_locations) == 1)
		@php
		$default_location = current(array_keys($business_locations->toArray()));
		$search_disable = false;
		@endphp
		@else
		@php $default_location = null;
		$search_disable = true;
		@endphp
		@endif
		<div class="col-sm-3">
			<div class="form-group">
			{!! Form::label('location_id', __('purchase.business_location').':*') !!}
			@show_tooltip(__('tooltip.purchase_location'))
			{!! Form::select(
				'location_id',
				$business_locations,
				isset($temp_data->location_id) ? $temp_data->location_id : ($business_locations->keys()->first() ?? $default_location),
				['class' => 'form-control select2 business_location_id', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id', 'data-default_accounts' => '']
			) !!}

			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('store_id', __('lang_v1.store_id').':*') !!}<br>
				<select name="store_id" id="store_id" class="form-control select2" required>
					<option value="">@lang('messages.please_select')</option>
				</select>
			</div>
		</div>

		<!-- Currency Exchange Rate -->
		<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
			<div class="form-group">
				{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
				@show_tooltip(__('tooltip.currency_exchange_factor'))
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-info"></i>
					</span>
					{!! Form::number('exchange_rate',
					!empty($temp_data->exchange_rate)?$temp_data->exchange_rate:$currency_details->p_exchange_rate,
					['class' => 'form-control',
					'required', 'step' => 0.001]); !!}
				</div>
				<span class="help-block text-danger">
					@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
				</span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<div class="multi-input">
					{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!}
					@show_tooltip(__('tooltip.pay_term'))
					<br />
					{!! Form::number('pay_term_number', !empty($temp_data->pay_term_number)?$temp_data->pay_term_number:
					null, ['class' => 'form-control width-40 pull-left', 'id' => 'pay_term_number_value',
					'placeholder' => __('contact.pay_term')]); !!}

					{!! Form::select('pay_term_type',
					['months' => __('lang_v1.months'),
					'days' => __('lang_v1.days')],
					!empty($temp_data->pay_term_type)?$temp_data->pay_term_type: null,
					['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' =>
					'pay_term_type']); !!}
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('document', __('purchase.attach_document') . ':') !!}
				{!! Form::file('document', ['id' => 'upload_document']); !!}
				<p class="help-block">@lang('purchase.max_file_size', ['size' =>
					(config('constants.document_size_limit') / 1000000)])</p>
			</div>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-search"></i>
					</span>
					{!! Form::text('search_products', null, ['class' => 'form-control mousetrap', 'id' =>
					'search_products', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' =>
					$search_disable]); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<button tabindex="-1" type="button" class="btn btn-link btn-modal"
					data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i
						class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
			</div>
		</div>
	</div>
	@php
	$hide_tax = '';
	if( session()->get('business.enable_inline_tax') == 0){
	$hide_tax = 'hide';
	}
	$business_id = request()->session()->get('user.business_id');
	$enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
	@endphp
	<div class="row">
		<div class="col-sm-12">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered table-th-green text-center table-striped"
					id="purchase_entry_table">
					<thead>
						<tr>
							<th style="width: 2%;" >#</th>
							<th style="width: 10%;">@lang( 'product.product_name' )</th>
							<th style="width: 8%;">@lang( 'purchase.purchase_quantity' )</th>
							<th style="width: 8%;">@lang( 'purchase.available_qty' )</th>
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
								<small>(@lang('product.inc_of_tax'))</small>
							</th>
							@if(session('business.enable_lot_number'))
							<th style="width: 6%;">
								@lang('lang_v1.lot_number')
							</th>
							@endif
							@if(session('business.enable_product_expiry'))
							<th style="width: 6%;">
								@lang('product.mfg_date') / @lang('product.exp_date')
							</th>
							@endif
							<th style="width: 2%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
						</tr>
					</thead>
					<tbody id="purchaseBody"></tbody>
				</table>
			</div>
			<hr />
			<div class="pull-right col-md-5">
				<table class="pull-right col-md-12">
					<tr class="hide">
						<th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
						<td class="col-md-5 text-left">
							<span id="total_st_before_tax" class="display_currency"></span>
							<input type="hidden" id="st_before_tax_input" value=0>
						</td>
					</tr>
					<tr>
						<th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
						<td class="col-md-5 text-left">
							<span id="total_subtotal" class="display_currency"></span>
							<!-- This is total before purchase tax-->
							<input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
						</td>
					</tr>
				</table>
			</div>

			<input type="hidden" id="row_count" value="0">
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="col-sm-12">
			<table class="table">
				<tr>
					<td class="col-md-3">
						<div class="form-group">
							{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
						</div>
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-)
						<span id="discount_calculated_amount" class="display_currency">0</span>
						{!! Form::hidden('discount_amount',
							!empty($temp_data->discount_amount)?$temp_data->discount_amount:0, ['class' => 'form-control
							input_number', 'readonly']);
							!!}
					</td>
				</tr>
				<tr hidden>
					<td>
					    <div class="form-group">
							{!! Form::label('discount_amount', __( 'purchase.purchase_tax' ) . ':') !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b> (+)
						<span id="tax_calculated_amount" class="display_currency">0</span>
						{!! Form::hidden('tax_amount',!empty($temp_data->tax_amount)?$temp_data->tax_amount: 0,
							['id' => 'tax_amount']); !!}
					</td>
				</tr>

				<tr>
					<td>
						<div class="form-group">
							{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
							{!! Form::text('shipping_details',
							!empty($temp_data->shipping_details)?$temp_data->shipping_details:null, ['class' =>
							'form-control']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style="text-align: right;">
						<div class="form-group" style="display: inline-flex; align-items: center; gap: 10px;">
							{!! Form::label('shipping_charges', '(+) ' . __('purchase.additional_shipping_charges') . ':', ['style' => 'margin-bottom: 0;']) !!}
							{!! Form::text('shipping_charges', !empty($temp_data->shipping_charges) ? $temp_data->shipping_charges : 0, ['class' => 'form-control input_number', 'style' => 'width: auto;', 'required']) !!}
						</div>
						<div class="form-group" style="display: inline-flex; align-items: center; gap: 10px;">
							{!! Form::label('price_adjustment', __('purchase.price_adjustment') . ':', ['style' => 'margin-bottom: 0; color:red;']) !!}
							{!! Form::text('price_adjustment', !empty($temp_data->price_adjustment) ? $temp_data->price_adjustment : 0, ['class' => 'form-control input_number', 'style' => 'width: auto; outline: 1px solid red; color:red;', 'required']) !!}
						</div>
					</td>					
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
						<b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency"
							data-currency_symbol='true'>0</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="form-group">
							{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
							{!!
							Form::textarea('additional_notes',!empty($temp_data->additional_notes)?$temp_data->additional_notes:
							null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</td>
				</tr>

			</table>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary unload_div hide', 'title' => __('purchase.unload_tanks')])
	<div class="box-body unload_tank">
	</div>
	@endcomponent


	@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
	<div class="box-body payment_row" data-row_id="0">
		<div id="payment_rows_div">
			@if (!empty($temp_data->payment))
			@include('sale_pos.partials.payment_row_form_expense', ['row_index' => 0, 'payment' => $temp_data->payment[0]])
			@else
			@include('sale_pos.partials.payment_row_form_expense', ['row_index' => 0])
			@endif
			<hr>
		</div>

		<div class="row">
			<div class="col-md-12">
				<button type="button" class="btn btn-primary btn-block"
					id="add-payment-row">@lang('sale.add_payment_row')</button>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
						id="payment_due">0.00</span>
				</div>

			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-12">
				<button type="button" id="submit_purchase_form"
					class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
			</div>
		</div>
	</div>
	@endcomponent

	{!! Form::close() !!}
	<input type="hidden" name="cash_account_id" id="cash_account_id" value="{{$cash_account_id}}">
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true]) 
</div>
<!-- /.content -->
@endsection

@section('javascript')
 
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
 
@include('purchase.partials.keyboard_shortcuts')
 
@if(!empty($temp_data->purchases))
@php $i = 0; @endphp
@foreach($temp_data->purchases as $purchase)
<script>

	$(document).ready(function(){
	    
	     var product_id = {{$purchase->product_id}};
		 var variation_id = {{$purchase->variation_id}};
		 var quantity = {{str_replace(',', '',$purchase->quantity)}};
		 base_url = '{{URL::to('/')}}';

		 if (product_id) {
			var row_count = {{$i}};
			$.ajax({
				method: 'POST',
				url: base_url+'/purchases/get_purchase_entry_row_temp',
				dataType: 'html',
				data: { product_id: product_id, row_count: row_count, variation_id: variation_id, quantity: quantity },
				success: function(result) {
					$(result)
						.find('.purchase_quantity')
						.each(function() {
							row = $(this).closest('tr');

							$('#purchase_entry_table tbody').append(
								update_purchase_entry_row_values(row)
							);
							update_row_price_for_exchange_rate(row);
							update_inline_profit_percentage(row);
							update_table_total();
							update_grand_total();
							update_table_sr_number();

							//Check if multipler is present then multiply it when a new row is added.
							if(__getUnitMultiplier(row) > 1){
								row.find('select.sub_unit').trigger('change');
							}
						});
					if ($(result).find('.purchase_quantity').length) {
						$('#row_count').val(
							$(result).find('.purchase_quantity').length + parseInt(row_count)
						);
					}
				},
			});
		}
	})
</script>
@php $i++; @endphp
@endforeach
@endif
<script>


$(document).ready(function(){
     $('#search_products').prop('disabled', true);

    // When the supplier list box is selected
    $('#supplier_id').on('change', function() {
      var supplierId = $(this).val();

      // Enable or disable the input based on the supplier selection
      if (supplierId !== '') {
        $('#search_products').prop('disabled', false);
      } else {
        $('#search_products').prop('disabled', true);
      }
    });

  $(document).on('click', '#search_products', function () {
        
    if ($('#search_products').length >0) {
         var selectedSupplierId = $('#supplier_id').val();
        
        // Check if the value is empty or not
        if ($.trim(selectedSupplierId).length === 0) {
          selectedSupplierId=0;
        } 
        
      
        $('#search_products')
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(
                        '/purchases/get_products',
                        { location_id: $('#location_id').val(), term: request.term ,supplier_id: $('#supplier_id').val(), module: 'purchases_addpurchases'},
                        response
                    );
                },
                minLength: 0,
                response: function (event, ui) { 
                },
                select: function (event, ui) {
                    $(this).val(null);
                    get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
            return $('<li>')
                .append('<div>' + item.text + '</div>')
                .appendTo(ul);
        };
    }
        
        
        
        
        // Get the selected supplier_id
        var selectedSupplierId = $(this).val();
        console.log(selectedSupplierId);
        // Make an AJAX request to store the selected supplier_id in the session
        $.ajax({
            url: '/purchases/',
            type: 'POST',
            data: {supplier_id: selectedSupplierId},
            success: function(response) {
                console.log(response);
            }
        });
    });
    $('.payment-amount').change(function() {
    		paid = parseFloat($(this).val());
    		var $row = $(this).closest('.row');
            var accid = parseInt($row.find('.account_id').val());
    		
            $.ajax({
                method: 'GET',
                url: '/accounting-module/check-insufficient-balance-for-accounts',
                success: function(result) {
                    var ids = result;
            
                    if(ids.includes(accid)) {
                                        
                        $.ajax({
                           method: 'GET',
                        url: '/accounting-module/get-account-balance/' + accid,
                           success: function(result) {
                            
                            if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
                                swal({
                                    title: 'Insufficient Balance',
                                    icon: "error",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                
                               $('button#submit_purchase_form').prop('disabled', true);
                                return false;
                              } else {
                                  $('button#submit_purchase_form').prop('disabled', false);
                              }
                           }
                        });
                    } else {
                      $('button#submit_purchase_form').prop('disabled', false);
                    }
    
                }
            });
    
    // @eng END 15/2 
    
    	});
    	
    $('#transaction_date_range_cheque_deposit').daterangepicker(
          dateRangeSettings,
          function (start, end) {
            $('#transaction_date_range_cheque_deposit').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
    
            get_cheques_list();
            }
        );
    
    
     
        
    $('.account_id').change(function(){
        
        var accid = parseInt($(this).val());
        var $row = $(this).closest('.row');
        var paid = parseFloat($row.find('.payment-amount').val());
        
        if($(this).val() == "cheque"){
            $row.find('.payment-amount').prop('readonly', true);
        }else{
            $row.find('.payment-amount').prop('readonly', false);
        }
        
        $.ajax({
            method: 'GET',
            url: '/accounting-module/check-insufficient-balance-for-accounts',
            success: function(result) {
                var ids = result;
                if(ids.includes(accid)) {
                   
                    $.ajax({
                       method: 'GET',
                       url: '/accounting-module/get-account-balance/' + accid,
                       success: function(result) {
                        
                        if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
                            swal({
                                title: 'Insufficient Balance',
                                icon: "error",
                                buttons: true,
                                dangerMode: true,
                            })
                            
                           $('button#submit_purchase_form').prop('disabled', true);
                            return false;
                          } else {
                              $('button#submit_purchase_form').prop('disabled', false);
                          }
                       }
                    });
                } else {
                  $('button#submit_purchase_form').prop('disabled', false);
                }
    
            }
        });
        
    });

	let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { check_store_not: check_store_not, permission : 'purchase'},
			success: function(result) {
				
				$('#store_id').empty();
				$('#store_id').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
// 		$.ajax({
// 			method: 'get',
// 			url: '/purchases/get-payment-method-by-location-id/'+$(this).val(),
// 			data: {  },
// 			success: function(result) {
// 				$('.payment_types_dropdown').empty().append(result.html);
				
// 			},
// 		});
		getInvoice();
});

$('.business_location_id').change(function(){
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { check_store_not: check_store_not, permission : 'purchase'},
			success: function(result) {
				
				$('#store_id').empty();
				$('#store_id').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
// 		$.ajax({
// 			method: 'get',
// 			url: '/purchases/get-payment-method-by-location-id/'+$(this).val(),
// 			data: {  },
// 			success: function(result) {
// 				$('.payment_types_dropdown').empty().append(result.html);
				
// 			},
// 		});
		getInvoice();


	});
function getInvoice() {
		$.ajax({
			method: 'get',
			url: '{{action("SellController@getInvoiveNo")}}',
			data: { location_id: $('#location_id').val() },
			success: function(result) {
				$('#location_id').data('default_accounts', result.default_accounts);
				
				
			},
		});
	}
$(document).ready(function(){
		setTimeout(() => {
			getInvoice();

		}, 2000);
	});
setTimeout(() => {
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/{{array_keys($business_locations->toArray())[0]}}',
			data: { check_store_not: check_store_not, permission: 'purchase'},
			success: function(result) {
				
				$('#store_id').empty();
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
				
				$('#store_id').append(`<option value="">Please Select</option>`);
			},
		});
}, 1000);

@if(auth()->user()->can('unfinished_form.purchase'))
	setInterval(function(){ 
		$.ajax({
                method: 'POST',
                url: '{{action("TempController@saveAddPurchaseTemp")}}',
                dataType: 'json',
                data: $('#add_purchase_form').serialize(),
                success: function(data) {

                },
			});
	}, 10000);
		

@if(!empty($temp_data))
	swal({
		title: "Do you want to load unsaved data?",
		icon: "info",
		buttons: {
			confirm: {
				text: "Yes",
				value: false,
				visible: true,
				className: "",
				closeModal: true
			},
			cancel: {
				text: "No",
				value: true,
				visible: true,
				className: "",
				closeModal: true,
			}
			
		},
		dangerMode: false,
	}).then((sure) => {
		if(sure){
			window.location.href = "{{action('TempController@clearData', ['type' => 'pos_create_data'])}}";
		} 
	});
@endif
@endif

@if($is_petro_enable)
	

@endif

// function get_cheques_list(payment_type = null){
//         if($('#transaction_date_range_cheque_deposit').val()){
          
           
//             start_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').startDate.format('YYYY-MM-DD');
//             end_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').endDate.format('YYYY-MM-DD');
          
//             $.ajax({
//                 method: 'get',
//                 url: '/accounting-module/cheque-list',
//                 data: { start_date, end_date,payment_type },
//                 contentType: 'html',
//                 success: function(result) {
                  
//                     $('#cheque_list_table tbody').empty().append(result);
//                 },
//             });
//         }
       
//     }
	
// 	$(document).on('change', '.payment_types_dropdown', function() {
      
//       var payment_type = $(this).val();
//       console.log("payment type value",payment_type);
//       var to_show = null;

//       var cheque_field = null;

//       var location_id = $('#location_id').val();
      
//       if(location_id == ""){
//           var location_id = $('#pmt_location_id').val();
//           if(location_id == ""){
//               toastr.error("Select location first!");
//               return false;
//           }
//       }

//       var row_id = parseInt($(this).closest('.payment_row').find('.payment_row_index').val());
      
//       var accounting_module = $(this).closest('.payment_row').find('.account_id');
      
//       var previous_acc_id = parseInt($(this).closest('.payment_row').find('.previous_account').val());
      
      
      
//       // var row_id = parseInt($(this).closest('.payment_row').data('row_id'));
      
//       accounting_module.attr('required', true);
//       accounting_module.empty();
      
//       $(this).closest('.payment_row').find('.payment_details_div').each(function() {
//           $(this).closest('.payment_row').find('.account_id').attr('required', true);
//           $(this).closest('.payment_row').find('.account_module').removeClass('hide');
          

//           if ($(this).attr('data-type') == 'cheque'  || $(this).attr('data-type')  == 'Pre_payments') {
//               cheque_field = $(this);
//           }

//           if ($(this).attr('data-type') == payment_type) {
//               to_show = $(this);
//           } else {
//               if (!$(this).hasClass('hide')) {
//                   $(this).addClass('hide');
//               }
//           }
//       });

      

//       $('.card_type_div').addClass('hide');

//       if (to_show && to_show.hasClass('hide')) {
//           to_show.removeClass('hide');
//           to_show.find('input').filter(':visible:first').focus();
//       }
      
      
//       if (payment_type.toLowerCase() == 'bank_transfer' || payment_type.toLowerCase() == 'direct_bank_deposit' || payment_type.toLowerCase() == 'bank'|| payment_type.toLowerCase() == 'cheque' | payment_type.toLowerCase() == 'Pre_payments') {
//           $(this).closest('.payment_row').find('.bank_transfer_fields').removeClass('hide');
//           $(this).closest('.payment_row').find('.add_payment_bank_details').removeClass('hide');
          
//           $(this).closest('.payment_row').find('.post_dated_cheque').removeClass('hide');
          
//           if(payment_type == 'cheque' || payment_type == 'pre payments'){
//               $(this).closest('.payment_row').find('.cheque_payment_details').removeClass('hide');
//           }else{
//               $(this).closest('.payment_row').find('.cheque_payment_details').addClass('hide');
//           }
//       }else{
//           $(this).closest('.payment_row').find('.post_dated_cheque').addClass('hide'); 
//           $(this).closest('.payment_row').find('.icheckbox_square-blue').prop('checked', false);
//           $(this).closest('.payment_row').find('.bank_transfer_fields').addClass('hide');
//           $(this).closest('.payment_row').find('.add_payment_bank_details').addClass('hide');
          
          
//       }
      
//       if (payment_type.toLowerCase() == 'cheque' || payment_type.toLowerCase() == 'Pre_payments' ){
//           $(this).closest('.payment_row').find('.cheque_payment_details_only').removeClass('hide');
//           if($('#transaction_date_range_cheque_deposit').val()){
//             console.log('pre payments');
//               get_cheques_list(payment_type); 
//               $(this).closest('.payment_row').find('.cheque_payment_details').removeClass('show');
//               $(this).closest('.payment_row').find('.post_dated_cheque').addClass('hide'); 
//               $(this).closest('.payment_row').find('.icheckbox_square-blue').prop('checked', false);
//               $(this).closest('.payment_row').find('.bank_transfer_fields').addClass('hide');
//               $(this).closest('.payment_row').find('.add_payment_bank_details').addClass('hide');
//           }
          
//       }else{
//           $(this).closest('.payment_row').find('.cheque_payment_details_only').removeClass('show');
//       }
      
      

//       $.ajax({

//               method: 'get',

//               url: '/accounting-module/get-account-group-name-dp',

//               data: { group_name: payment_type, location_id: location_id },

//               contentType: 'html',

//               success: function(result) {
//                   console.log("prepayment",result);
                 
//                  accounting_module.empty().append(result);
//                   accounting_module.attr('required', true);
//                   accounting_module.val(accounting_module.find('option:first').val());
//                   if(previous_acc_id){
//                      accounting_module.val(previous_acc_id).change();
//                   }
                  

//               },

//           });

      


//       edit_cheque_date = $(this).closest('.payment_row').find('.payment_details_div').find('#payment_edit_cheque').val();

//       if (edit_cheque_date) {

//           $(this).closest('.payment_row').find('.payment_details_div').find('.cheque_date').datepicker('setDate', edit_cheque_date);

//       } else {
//           $(this).closest('.payment_row').find('.payment_details_div').find('.cheque_date').datepicker('setDate', new Date());

//       }

//   });
</script>





@include('layouts.partials.adsense')
@endsection