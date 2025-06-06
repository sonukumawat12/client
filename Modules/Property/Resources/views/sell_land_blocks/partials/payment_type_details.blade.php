@php
	$business_id = request()->session()->get('business.id');
	$card_types = [];
	$card_group = App\AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
	if (!empty($card_group)) {
		$card_types = App\Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
	}

@endphp
<div class="payment_details_div @if( $payment_line['method'] !== 'card' ) {{ 'hide' }} @endif" data-type="card" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_number_$row_index", __('lang_v1.card_no')) !!}
			{!! Form::text("payment[$row_index][card_number]", $payment_line['card_number'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.card_no'), 'id' => "card_number_$row_index"]); !!}
		</div>
	</div>
	
	
	 <div class="col-md-3">
        <div class="form-group">
            {!! Form::label("account_id" , 'Card Account:') !!}
            <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-money"></i>
            </span>
                {!! Form::select("account_id", $card_group_accounts, null , ['class' =>
                'form-control
                select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' =>
                'width:100%;']); !!}
            </div>
        </div>
    </div>
	
	
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_holder_name_$row_index", __('lang_v1.card_holder_name')) !!}
			{!! Form::text("payment[$row_index][card_holder_name]", $payment_line['card_holder_name'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.card_holder_name'), 'id' => "card_holder_name_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_transaction_number_$row_index",__('lang_v1.card_transaction_no')) !!}
			{!! Form::text("payment[$row_index][card_transaction_number]",$payment_line['card_transaction_number'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.card_transaction_no'), 'id' => "card_transaction_number_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("card_type_$row_index", __('lang_v1.card_type')) !!}
			{!! Form::select("payment[$row_index][card_type]",  $card_types , $payment_line['card_type'],['class' => 'form-control reset', 'id' => "card_type_$row_index", 'placeholder' => __('lang_v1.please_select') ]); !!}
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			{!! Form::label("card_month_$row_index", __('lang_v1.month')) !!}
			{!! Form::text("payment[$row_index][card_month]",$payment_line['card_month'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.month'),
			'id' => "card_month_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			{!! Form::label("card_year_$row_index", __('lang_v1.year')) !!}
			{!! Form::text("payment[$row_index][card_year]",$payment_line['card_year'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.year'), 'id' => "card_year_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			{!! Form::label("card_security_$row_index",__('lang_v1.security_code')) !!}
			{!! Form::text("payment[$row_index][card_security]", $payment_line['card_security'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.security_code'), 'id' => "card_security_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'cheque' ) {{ 'hide' }} @endif" data-type="cheque" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("cheque_number_$row_index",__('lang_v1.cheque_no')) !!}
			{!! Form::text("payment[$row_index][cheque_number]",!empty($payment->cheque_number)?$payment->cheque_number: $payment_line['cheque_number'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.cheque_no'), 'id' => "cheque_number_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("cheque_date_$row_index",__('lang_v1.cheque_date')) !!}
			{!! Form::text("payment[$row_index][cheque_date]",!empty($payment->cheque_date)?$payment->cheque_date: $payment_line['cheque_date'], ['class' => 'form-control cheque_date', 'placeholder' => __('lang_v1.cheque_date'), 'id' => "cheque_date_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("bank_name_$row_index",__('lang_v1.bank_name')) !!}
			{!! Form::text("payment[$row_index][bank_name]",!empty($payment->bank_name)?$payment->bank_name: $payment_line['bank_name'], ['class' => 'form-control reset bank_name', 'placeholder' => __('lang_v1.bank_name'), 'id' => "bank_name_$row_index"]); !!}
		</div>
	</div>
</div>
{{-- <div class="payment_details_div @if( $payment_line['method'] !== 'bank_transfer' ) {{ 'hide' }} @endif" data-type="bank_transfer" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("bank_account_number_$row_index",__('lang_v1.bank_account_number')) !!}
			{!! Form::select('payment[$row_index][bank_account_number]', $bank_group_accounts, null, ['class' => 'form-control reset', 'sytle'=> 'width: 100%', 'placeholder' => __('lang_v1.please_select'), 'id' => "bank_account_number_$row_index"]) !!}
			{!! Form::text( "payment[$row_index][bank_account_number]", !empty($payment->bank_account_number)?$payment->bank_account_number:$payment_line['bank_account_number'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.bank_account_number'), 'id' => "bank_account_number_$row_index"]); !!} 
		</div>
	</div>
</div>--}}
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_1' ) {{ 'hide' }} @endif" data-type="custom_pay_1" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("transaction_no_1_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_1]",!empty($payment->transaction_no_1)?$payment->transaction_no_1: $payment_line['transaction_no'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_1_$row_index"]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_2' ) {{ 'hide' }} @endif" data-type="custom_pay_2" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("transaction_no_2_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_2]", !empty($payment->transaction_no_2)?$payment->transaction_no_2:$payment_line['transaction_no'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_2_$row_index"]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_3' ) {{ 'hide' }} @endif" data-type="custom_pay_3" >
	<div class="col-md-3">
		<div class="form-group">
			{!! Form::label("transaction_no_3_$row_index", __('lang_v1.transaction_no')) !!}
			{!! Form::text("payment[$row_index][transaction_no_3]", !empty($payment->transaction_no_3)?$payment->transaction_no_3:$payment_line['transaction_no'], ['class' => 'form-control reset', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_3_$row_index"]); !!}
		</div>
	</div>
</div>
