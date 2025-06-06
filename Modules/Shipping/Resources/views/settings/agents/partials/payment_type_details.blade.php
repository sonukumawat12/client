<div class="payment_details_div " data-type="card" >
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_number", __('lang_v1.card_no')) !!}
			{!! Form::text("card_number", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.card_no')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_holder_name", __('lang_v1.card_holder_name')) !!}
			{!! Form::text("card_holder_name", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.card_holder_name')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_transaction_number",__('lang_v1.card_transaction_no')) !!}
			{!! Form::text("card_transaction_number", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.card_transaction_no')]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_type", __('lang_v1.card_type')) !!}
			{!! Form::select("card_type", ['credit' => 'Credit Card', 'debit' => 'Debit Card', 'visa' => 'Visa', 'master' => 'MasterCard'], null,['class' => 'form-control select2']); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_month", __('lang_v1.month')) !!}
			<select class="form-control" id="card_month" name="card_month">
			@php for ($i=1; $i<=12; $i+=1) { @endphp
				<option value="{{$i}}"  >{{$i}}</option>
			@php } @endphp
			</select>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_year", __('lang_v1.year')) !!}
			<select class="form-control" id="card_year" name="card_year">
			@php for ($i=date('y'); $i<=date('y')+20; $i+=1) { @endphp
				<option value="{{$i}}"  >{{$i}}</option>
			@php } @endphp
			</select>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_security",__('lang_v1.security_code')) !!}
			{!! Form::text("card_security", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.security_code')]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div  add_payment_bank_details" data-type="cheque" >
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("cheque_number",__('lang_v1.cheque_no')) !!}
			{!! Form::text("cheque_number", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no'),'disabled' => false]); !!}
		</div>
	</div>
	
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("cheque_date",__('lang_v1.cheque_date')) !!}
			{!! Form::text("cheque_date", null, ['class' => 'form-control cheque_date', 'placeholder' => __('lang_v1.cheque_date'),'disabled' => false]); !!}
		</div>
	</div>
</div>

<div class="payment_details_div cheque_payment_details" >
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("bank_name",'Bank Name') !!}
			{!! Form::text("bank_name", null, ['class' => 'form-control', 'placeholder' => 'Bank Name','disabled' => false]); !!}
		</div>
	</div>
</div>

<div class="payment_details_div" data-type="custom_pay_1" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_1", __('lang_v1.transaction_no')) !!}
			{!! Form::text("transaction_no_1", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'),'disabled' => true]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div " data-type="custom_pay_2" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_2", __('lang_v1.transaction_no')) !!}
			{!! Form::text("transaction_no_2", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'),'disabled' => true]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div " data-type="custom_pay_3" >
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("transaction_no_3", __('lang_v1.transaction_no')) !!}
			{!! Form::text("transaction_no_3", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'),'disabled' => true]); !!}
		</div>
	</div>
</div>