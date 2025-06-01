@extends('layouts.app')
@section('title', __('expense.edit_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('expense.edit_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatExpenseController@update', [$expense->id]), 'method' => 'PUT', 'id' => 'add_expense_form', 'files' => true ]) !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location').':*') !!}
            {!! Form::select('location_id', $business_locations, $expense->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
            {!! Form::select('expense_category_id', $expense_categories, $expense->expense_category_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ref_no', __('purchase.ref_no').':*') !!}
            {!! Form::text('ref_no', $expense->ref_no, ['class' => 'form-control','readonly', 'required']); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('transaction_date', @format_datetime($expense->transaction_date), ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
						{!! Form::select('contact_id', $contacts, $expense->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
       
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('tax_id', $taxes['tax_rates'], $expense->tax_id, ['class' => 'form-control'], $taxes['attributes']); !!}

            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
            value="0">
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
    		<div class="form-group">
    			{!! Form::label('is_vat', __('lang_v1.is_vat')) !!}
    			{!! Form::select('is_vat', ['0' => __('lang_v1.no'),'1' => __('lang_v1.yes')],$expense->is_vat, ['class' => 'form-control
    			select2', 'required']); !!}
    		</div>
    	</div>
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
            <!-- @eng 15/2 START -->
            <!--{!! Form::text('final_total', rtrim(rtrim($expense->final_total, '0'), '.'), ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']) !!}-->
            {!! Form::text('final_total', rtrim(rtrim($expense->final_total, '0'), '.'), ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required', 'id'=>'final_total']) !!}
            <!-- @eng 15/2 END -->
          </div>
        </div>
			
      </div>
    </div>
  </div> <!--box end-->
     
     <div class="box box-solid">
      <div class="box-header">
              <h3 class="box-title">@lang('lang_v1.payment')</h3>
          </div>
      <div class="box-body">
        <div class="row">
            
            
            <div class="col-md-12 payment_row" data-row_id="0">
				<div id="payment_rows_div">
        		@if(!empty($expense->payment_lines) && $expense->payment_lines->count() > 0)
        			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'payment' => $expense->payment_lines[0]])
        			@else
        			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'payment' => $expense->payment_lines])
        			@endif
        			<hr>
        		</div>
			</div>
        </div>
        <div class="col-sm-12">
          <button type="submit" id="submitBtn" class="btn btn-primary pull-right">@lang('messages.update')</button> <!-- @eng 15/2 -->
        </div>
      </div>
    </div>
{!! Form::close() !!}
</section>
@endsection

@section('javascript')
   <script>
      $(document).ready(function(){
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
    	
    	$(".payment_types_dropdown").trigger('change');
    	
      });
   </script>
@endsection