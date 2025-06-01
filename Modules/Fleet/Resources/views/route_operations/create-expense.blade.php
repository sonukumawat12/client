
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content" style="padding: 10px!important;">
      
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'fleet::lang.add_expense' )</h4>
    </div>
      
	{!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteOperationController@storeExpense'), 'method' => 'post', 'id' => 'add_expense_form', 'files'
	=> true ]) !!}
	
	<input type="hidden" name="ro_id" value="{{$ro->id}}">
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
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations,
						!empty($temp_data->location_id)?$temp_data->location_id: $default_location, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
						<div class="input-group">
							{!! Form::select('expense_category_id', $expense_categories,
							!empty($temp_data->expense_category_id)?$temp_data->expense_category_id: null, ['class' =>
							'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
							<span class="input-group-btn">
								<button type="button" class="btn
                                btn-default
                                bg-white btn-flat btn-modal" data-href="{{action('ExpenseCategoryController@create', ['quick_add' => true])}}" title="@lang('lang_v1.add_expense_category')" data-container=".expense_category_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no: $ref_no, ['class' =>
						'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date',
							@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'),
							['class' => 'form-control',  'required', 'id' => 'expense_transaction_date']);
							!!}
						</div>
					</div>
				</div>
			
				<div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('fleet_id', __('fleet::lang.fleet').':') !!}
                        {!! Form::select('fleet_id', $fleets, $ro->fleet_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'disabled' => 'disabled']) !!}
                    </div>
                </div>
                
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('trip_no', __('fleet::lang.trip_no').':') !!}
						{!! Form::text('trip_no', $ro->invoice_no, ['class' => 'form-control', 'placeholder' => __('fleet::lang.fleet'), 'readonly' => 'readonly']) !!}
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('driver', __('fleet::lang.driver').':') !!}
						{!! Form::text('driver', $driver, ['class' => 'form-control', 'placeholder' => __('fleet::lang.fleet'), 'readonly' => 'readonly']) !!}
					</div>
				</div>

				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!}
						{!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
						'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>

				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
						{!! Form::text('final_total', !empty($temp_data->final_total)?$temp_data->final_total:null,
						['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']);
						!!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
						{!! Form::select('expense_account', $expense_accounts, $expense_account_id, ['class' =>
						'form-control select2', 'placeholder' => __('lang_v1.please_select'),'style' => 'width: 100%']) !!}

					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('payee', 'Select Payee' . ':*') !!}
						{!! Form::text('payee', !empty($payee_name->name)?$payee_name->name: 'Payee Not Selected', ['class' =>
						'form-control', 'readonly']); !!}
					</div>
				</div>

			</div>
		</div>
	</div>
	@include('fleet::route_operations.recur_expense_form_part')
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
	
	<div class="modal-footer">
      <button id="submitBtn" type="submit" class="btn btn-primary">@lang('messages.save')</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
	
	{!! Form::close() !!}

	<div class="modal fade expense_category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	</div>

  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->


<script>
    $('#expense_transaction_date').datepicker('setDate', new Date());

	jQuery.validator.addMethod("greaterThanZero", function(value, element) {
		return (parseFloat(value) > 0);
	});
	$.validator.messages.greaterThanZero = 'Zero Values not accepted. Please correct';
	jQuery.validator.addClassRules("payment-amount", {
		required: true,
		greaterThanZero: true
	});

	$('form#add_expense_form').validate({
		rules: {

		},
		messages: {

		},
	});




	$(document).ready(function() {
		$('#location_id').trigger('change');
		$('.payment_types_dropdown').trigger('change');
	})

	$(".expense_category_modal").on('hide.bs.modal', function() {
		$.ajax({
			method: 'get',
			url: '/expense-categories/get-drop-down',
			data: {},
			contentType: 'html',
			success: function(result) {
				$('#expense_category_id').empty().append(result)
			},
		});
	});


	@if(auth()-> user()-> can('unfinished_form.expense'))
	setInterval(function() {
		$.ajax({
			method: 'POST',
			url: '{{action("TempController@saveAddExpenseTemp")}}',
			dataType: 'json',
			data: $('#add_expense_form').serialize(),
			success: function(data) {},
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
		if (sure) {
			window.location.href = "{{action('TempController@clearData', ['type' => 'add_expense_data'])}}";
		}
	});
	@endif
	@endif

	@if($account_module)
	$('#expense_account').select2();
	@endif


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
	
	$('#amount_0').change(function() {
		total = parseFloat($('#final_total').val());
		paid = parseFloat($('#amount_0').val());
		due = total - paid;
		if (due > 0) {
			$('.controller_account_div').removeClass('hide')
		} else {
			$('.controller_account_div').addClass('hide')
		}
		$('#payment_due').text(__currency_trans_from_en(due, false, false));


        if(paid == null) return false;
        
        $.ajax({
            method: 'GET',
            url: '/accounting-module/check-insufficient-balance-for-accounts',
            success: function(result) {
                var ids = result;
                if(ids.includes(parseInt($('#account_0').val()))) {
                                     
                    $.ajax({
                       method: 'GET',
                    //   url: '/accounting-module/get-account-balance/' + $('#cash_account_id').val(),
                    url: '/accounting-module/get-account-balance/' + parseInt($('#account_0').val()),
                       success: function(result) {
                        
                        if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
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
    
    function get_cheques_list(){
        if($('#transaction_date_range_cheque_deposit').val()){
            start_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').startDate.format('YYYY-MM-DD');
            end_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $.ajax({
                method: 'get',
                url: '{{action("AccountController@getChequeList")}}',
                data: { start_date, end_date },
                contentType: 'html',
                success: function(result) {
                    $('#cheque_list_table tbody').empty().append(result);
                },
            });
        }
       
    }
    
    

	$('#expense_category_id').change(function() {
		$.ajax({
			method: 'get',
			url: '/get-expense-account-category-id/' + $(this).val(),
			data: {},
			success: function(result) {
				$('#expense_account').empty().append(
					`<option value="${result.expense_account_id}" selected>${result.name}</option>`
				);
				$('#payee').val(result.payee_name);

			},
		});
	})
	
	    // @eng START 15/2    
    $('#account_0').change(function(){
        var $row = $(this).closest('.row');
        if($row.find('#method_0').val() == "cheque"){
            $row.find('.payment-amount').prop('readonly', true);
        }else{
            $row.find('.payment-amount').prop('readonly', false);
        }
        
        total = parseFloat($('#final_total').val());
		paid = parseFloat($('#amount_0').val());
		due = total - paid;
		if (due > 0) {
			$('.controller_account_div').removeClass('hide')
		} else {
			$('.controller_account_div').addClass('hide')
		}
		$('#payment_due').text(__currency_trans_from_en(due, false, false));

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
                        
                        if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
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
</script>
