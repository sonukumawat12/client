
<div class="payment_details_div @if( $payment_line['method'] !== 'card' ) {{ 'hide' }} @endif" data-type="card" >

	
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_transaction_number_$row_index",__('lang_v1.card_transaction_no')) !!}
			{!! Form::text("payment[$row_index][card_transaction_number]",$payment_line['card_transaction_number'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.card_transaction_no'), 'id' => "card_transaction_number_$row_index", !empty($edit) ? 'disabled' : '']); !!}
		</div>
	</div>
</div>

<div class="payment_details_div cheque_payment_details  @if( $payment_line['method'] !== 'cheque' ) {{ 'hide' }} @endif" data-type="cheque">
	@if(!empty($payment->cheque_date) && !empty($payment->method) && ($payment->method == 'bank_transfer' || $payment->method == 'cheque'))
	<input type="hidden" name="payment_edit_cheque" id="payment_edit_cheque" value="{{@format_date($payment->cheque_date)}}" , @if(!empty($edit)) {{ 'disabled' }} @endif > <!-- used for set cheque date to current date if empty in app.js line 1533 -->
	@endif
	
	<div class="col-md-3">
    		<div class="form-group">
    			{!! Form::label("bank_name_$row_index",__('lang_v1.bank_name')) !!}
    			{!! Form::text("payment[$row_index][bank_name]",!empty($payment->bank_name)?$payment->bank_name: '', ['class' => 'form-control bank_name', 'placeholder' => __('lang_v1.bank_name')]); !!}
    		</div>
    	</div>
	
</div>

<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_1' ) {{ 'hide' }} @endif" data-type="custom_pay_1" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_1_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_1]",!empty($payment->transaction_no_1)?$payment->transaction_no_1: $payment_line['transaction_no'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_1_$row_index", !empty($edit) ? 'disabled' : '']); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_2' ) {{ 'hide' }} @endif" data-type="custom_pay_2" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_2_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_2]", !empty($payment->transaction_no_2)?$payment->transaction_no_2:$payment_line['transaction_no'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_2_$row_index", !empty($edit) ? 'disabled' : '']); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_3' ) {{ 'hide' }} @endif" data-type="custom_pay_3" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_3_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_3]", !empty($payment->transaction_no_3)?$payment->transaction_no_3:$payment_line['transaction_no'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_3_$row_index", !empty($edit) ? 'disabled' : '']); !!}
		</div>
	</div>
</div>

<script>
       
</script>
