@extends('layouts.app')
@section('title', __('expense.edit_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('expense.edit_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action('ExpenseController@update', [$expense->id]), 'method' => 'PUT', 'id' => 'add_expense_form', 'files' => true ]) !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location').':*') !!}
            {!! Form::select('location_id', $business_locations, $expense->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
            {!! Form::select('expense_category_id', $expense_categories, $expense->expense_category_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('ref_no', __('purchase.ref_no').':*') !!}
            {!! Form::text('ref_no', $expense->ref_no, ['class' => 'form-control', 'required']); !!}
          </div>
        </div>
        <div class="col-sm-4">
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
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('expense_for', __('expense.expense_for').':') !!} @show_tooltip(__('tooltip.expense_for'))
            {!! Form::select('expense_for', $employees, $expense->expense_for, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('fleet_id', __('fleet::lang.fleet').':') !!}
            {!! Form::select('fleet_id', $fleets, $expense->fleet_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
    <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!}
            <select name="contact_id" class="form-control select2 select2_contact_id" placeholder="{{ __('messages.please_select') }}">
                @if ($expense->contact)
                    <option value="{{ $expense->contact->id }}">{{ $expense->contact->name . " - " . $expense->contact->supplier_business_name . "(" . $expense->contact->contact_id . ")" }}</option>
                @else
                    <option value="">{{ __('messages.please_select') }}</option>
                @endif
            </select>
        </div>
    </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                {!! Form::file('document', ['id' => 'upload_document']); !!}
                <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p>
            </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
                {!! Form::textarea('additional_notes', $expense->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
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
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
						{!! Form::select('expense_account', $expense_accounts, $expense->expense_account, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'),'required']) !!}
						
					</div>
				</div>
      </div>
    </div>
  </div> <!--box end-->
     <!--box end-->
     @include('expense.recur_expense_form_part')
     <div class="box box-solid">
      <div class="box-header">
              <h3 class="box-title">@lang('lang_v1.payment')</h3>
          </div>
      <div class="box-body">
        <div class="row">
            
            
            <div class="col-md-12 payment_row" data-row_id="0">
				<div id="payment_rows_div">
        		@if(!empty($expense->payment_lines) && $expense->payment_lines->count() > 0)
        			@include('sale_pos.partials.payment_row_form_expense', ['row_index' => 0, 'payment' => $expense->payment_lines[0],'edit' => 1])
        			@else
        			@include('sale_pos.partials.payment_row_form_expense', ['row_index' => 0, 'payment' => $expense->payment_lines,'edit' => 1])
        			@endif
        			<hr>
        		</div>
			</div>
        </div>
        <div class="col-sm-12">
          {!! Form::hidden('is_print',0, ['id'=>'print_and_save']) !!}
          <button id="submitBtn" type="submit" class="btn btn-primary pull-right m-8">@lang('messages.update')</button> <!-- @eng 15/2 -->
          <button id="printBtnSave" type="submit" class="btn btn-success pull-right m-8">@lang('messages.save_and_print')</button>
          
        </div>
      </div>
    </div>
    <!--box end-->
    <input type="hidden" value="{{$cash_account_id}}" id="cash_account_id" /> <!-- @eng 15/2 -->
{!! Form::close() !!}
</section>
@endsection

@section('javascript')
   <script>
      $(document).ready(function(){
        // $('#amount_0').val(null); // @eng 15/2
        $('#amount_0').trigger('change');
        $('.payment_types_dropdown').trigger('change');
      });
      $('#method_0').prop('disabled', false);
      
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
	
  $(document).on('click','#printBtnSave',function () {
		$('#print_and_save').val(1);
	});
	$(document).on('click','#submitBtn',function () {
		$('#print_and_save').val(0);
	});
      $('#amount_0').change(function(){
        total = parseFloat($('#final_total').val());
        paid = parseFloat($('#amount_0').val());
        due = total - paid;
        if(due > 0){
          $('.controller_account_div').removeClass('hide')
        }else{
          $('.controller_account_div').addClass('hide')
        }
        $('#payment_due').text(__currency_trans_from_en(due, false, false));

        var account_balance = parseFloat($('#account_id option:selected').data('account_balance'));
        
        // @eng START 15/2
        
        // if($('#account_id option:selected').data('check_insufficient_balance')){
        //   if(paid > account_balance){
        //     Insufficient_balance_swal();
        //   }
        // }
        if(paid == null) return false;
        $.ajax({
            method: 'GET',
            url: '/accounting-module/check-insufficient-balance-for-accounts',
            success: function(result) {
                var ids = result;
                console.log(result, $('#account_0').val());
                // console.log( $('#account_id').find(":selected"), $('#account_id').find(":selected").data(), ' is id supposedly BUT the following should be:', $('#cash_account_id').val());
                if(ids.includes(parseInt($('#account_0').val()))) {
                                    
                    $.ajax({
                       method: 'GET',
                    //   url: '/accounting-module/get-account-balance/' + $('#cash_account_id').val(),
                    url: '/accounting-module/get-account-balance/' + parseInt($('#account_0').val()),
                       success: function(result) {
                        
                        if(parseFloat(paid) > parseFloat(result.balance)  || result.balance == null){
                            swal({
                                title: 'Insufficient Balance',
                                icon: "error",
                                buttons: true,
                                dangerMode: true,
                            })
                            
                           $('button#submitBtn').prop('disabled', true);
                            return false;
                          } else {
                              $('button#submitBtn').prop('disabled', false);
                          }
                       }
                    });
                } else {
                  $('button#submitBtn').prop('disabled', false);
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
    
    
    // @eng START 15/2    
    $('#account_0').change(function(){
        var $row = $(this).closest('.row');
        if($row.find('#method_0').val() == "cheque"){
            $row.find('.payment-amount').prop('readonly', true);
        }else{
            $row.find('.payment-amount').prop('readonly', false);
        }
        
        if(paid == null) return false;
        $.ajax({
            method: 'GET',
            url: '/accounting-module/check-insufficient-balance-for-accounts',
            success: function(result) {
                var ids = result;
                
                if(ids.includes(parseInt($('#account_0').val()))) {
                    
                                    
                    $.ajax({
                       method: 'GET',
                       url: '/accounting-module/get-account-balance/' + parseInt($('#account_0').val()),
                       success: function(result) {
                          console.log(parseFloat(paid) , parseFloat(result.balance));
                        if(parseFloat(paid) > parseFloat(result.balance)  || result.balance == null){
                            swal({
                                title: 'Insufficient Balance',
                                icon: "error",
                                buttons: true,
                                dangerMode: true,
                            })
                            
                           $('button#submitBtn').prop('disabled', true);
                            return false;
                          } else {
                              $('button#submitBtn').prop('disabled', false);
                          }
                       }
                    });
                } else {
                  $('button#submitBtn').prop('disabled', false);
                }

            }
        });
        
    });
    // @eng END 15/2
    
      $('#expense_category_id').change(function(){
			$.ajax({
				method: 'get',
				url: '/get-expense-account-category-id/'+ $(this).val(),
				data: {  },
				success: function(result) {
					$('#expense_account').empty().append(
						`<option value="${result.expense_account_id}" selected>${result.name}</option>`
					);
				
				},
			});
    })
    // @eng START 15/2    
    // $('#method_0').change(function(){
    //   if($(this).val() == 'bank_transfer' || $(this).val() == 'direct_bank_deposit'){
    //     $('.account_list').removeClass('hide');
    //   }else{
    //     $('.account_list').addClass('hide');
    //   }
    // })
    // @eng END 15/2

   </script>
  <script>
    $(document).ready(function() {
      $('.select2_contact_id').select2({
        ajax: {
          url: '/get-contacts',
          dataType: 'json',
          delay: 1000, // Wait 1000ms after the user stops typing
          data: function(params) {
            return {
              search: params.term,
              page: params.page || 1,
            };
          },
          processResults: function(data) {
            if (!data || !data.results) {
              console.error('Invalid response:', data);
              toastr.error("Error Searching Contacts");
              return { results: [] };
            }
            return {
              results: data.results,
              pagination: {
                more: data.pagination.more,
              },
            };
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            toastr.error("Error Searching Contacts");
          },
        },
        placeholder: '{{ __('messages.please_select') }}',
        minimumInputLength: 1, // Search after typing 1 character
      });
    });
  </script>
@endsection