@extends('layouts.app')
@section('title', __('expense.add_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('expense.add_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
     <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="active">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@create')}}" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_expenses')</strong>
                        </a>
                    </li>
                  
                    <li class="">
                        <a  href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@index')}}"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_expenses')</strong>
                        </a>
                    </li>

                </ul>
                </div>
            </div>
        </div>
	{!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatExpenseController@store'), 'method' => 'post', 'id' => 'add_expense_form', 'files'
	=> true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">

				@if(count($business_locations) == 1)
				@php
				$default_location = current(array_keys($business_locations->toArray()))
				@endphp
				@else
				@php $default_location = null; @endphp
				@endif
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations,
						!empty($temp_data->location_id)?$temp_data->location_id: $default_location, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
						{!! Form::select('expense_category_id', $expense_categories,
							!empty($temp_data->expense_category_id)?$temp_data->expense_category_id: null, ['class' =>
							'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no: $ref_no, ['class' =>
						'form-control','readonly']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date',
							@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'),
							['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']);
							!!}
						</div>
					</div>
				</div>
			
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!}
						{!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
						'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>

		        <div class="col-md-3">
					<div class="form-group">
						{!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-info"></i>
							</span>
							{!! Form::select('tax_id', $taxes['tax_rates'],
							!empty($temp_data->tax_id)?$temp_data->tax_id:null, ['class' => 'form-control'],
							$taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="{{!empty($temp_data->tax_calculation_amount)?$temp_data->tax_calculation_amount:0}}">
						</div>
					</div>
				</div>
				<div class="col-sm-3">
            		<div class="form-group">
            			{!! Form::label('is_vat', __('lang_v1.is_vat')) !!}
            			{!! Form::select('is_vat', ['0' => __('lang_v1.no'),'1' => __('lang_v1.yes')],null, ['class' => 'form-control
            			select2', 'required']); !!}
            		</div>
            	</div>
        
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
						{!! Form::text('final_total', !empty($temp_data->final_total)?$temp_data->final_total:null,
						['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']);
						!!}
					</div>
				</div>
			
			</div>
		</div>
	</div>
		<!--box end-->
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">@lang('sale.add_payment')</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12 payment_row" data-row_id="0">
					<div id="payment_rows_div">
            			@if (!empty($temp_data->payment))
            			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'payment' => $temp_data->payment[0]])
            			@else
            			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
            			@endif
            			<hr>
            		</div>
				</div>
			</div>
		</div>
	</div>
	<!--box end-->
	<div class="col-sm-12">
		<!--<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>--> <!-- @eng 15/2 -->
		<button id="submitBtn" type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button> <!-- @eng 15/2 -->
	</div>
	{!! Form::close() !!}

	<div class="modal fade expense_category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	</div>
</section>
@endsection

@section('javascript')
<script>
    $('#final_total').change(function() {
		$('#amount_0').val($('#final_total').val());
		total = parseFloat($('#final_total').val());
		paid = parseFloat($('#amount_0').val());
		due = total - paid;
		if (due > 0) {
			$('.controller_account_div').removeClass('hide')
		} else {
			$('.controller_account_div').addClass('hide')
		}
		$('#payment_due').text(__currency_trans_from_en(due, false, false));
		$('#amount_0').trigger('change');
	});
</script>
@endsection