@php

use App\Account;

$business_id = request()->session()->get('user.business_id');

$cpc_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'CPC')

            ->pluck('accounts.name', 'accounts.id');

$cash_account_id = Account::getAccountByAccountName('Cash')->id;



if(!isset($payment)){

$payment = json_encode([]);

}

if(!isset($payment_line)){

    $payment_line = [

            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',

            'is_return' => 0, 'transaction_no' => '', 'account_id' => '', 'related_account_id' => '', 'post_dated_cheque' => '','update_post_dated_cheque' => '', 'payment_id' => ''

        ];

}

@endphp



<style>

.icheckbox_square-blue {

    margin-right: 10px !important;

}

</style>



<div class="row payment-row">

	<input type="hidden" class="payment_row_index" value="{{ $row_index }}">
	@if(isset($view_page) || isset($edit_page))
		<input type="hidden" name="payment[{{$row_index}}][payment_id]" value="{{ $data[0]->t_id ?? null }}">
	@else
		<input type="hidden" name="payment[{{$row_index}}][payment_id]" value="{{ $payment->id ?? null }}">
	@endif
	@php

	$col_class = 'col-md-3';

	if(!empty($accounts)){

	$col_class = 'col-md-3';

	}

	@endphp

	<div class="{{$col_class}}">

		<div class="form-group">

			{!! Form::label("amount_$row_index" ,__('sale.amount') . ':*') !!}

			<div class="input-group">

				<span class="input-group-addon">

					<i class="fa fa-money"></i>

				</span>
				@if(isset($view_page) || isset($edit_page))
				{!! Form::text("payment[$row_index][amount]", @num_format(!empty($data[0]->total_paid)?str_replace(',', ''

				,$data[0]->total_paid):0 ?? null), ['class' => 'form-control payment-amount input_number',

				'required', 'id' => "amount_$row_index",'placeholder' => __('sale.amount'), isset($view_page) ? 'disabled' : '']); !!}
				@else
				{!! Form::text("payment[$row_index][amount]", @num_format(!empty($payment->amount)?str_replace(',', ''

				,$payment->amount):$payment_line['amount'] ?? null), ['class' => 'form-control payment-amount input_number',

				'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount'), !empty($edit) ? '' : '']); !!}
				@endif

			</div>

		</div>

	</div>

	<div class="{{$col_class}}">

		<div class="form-group">

			{!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}

			<div class="input-group">

				<span class="input-group-addon">

					<i class="fa fa-money"></i>

				</span>
				@if(isset($view_page) || isset($edit_page))
					{!! Form::select("payment[$row_index][method]", $payment_types, !empty($data[0]->t_method) ? $data[0]->t_method : null, ['class' => 'form-control

					payment_types_dropdown select2', 'required', 'id' => "method_$row_index", 'style' => 'width:100%;',

					'placeholder' => __('messages.please_select')]); !!}
				@else
					{!! Form::select("payment[$row_index][method]", $payment_types, !empty($payment->method) ? $payment->method : null, ['class' => 'form-control

					payment_types_dropdown select2', 'required', 'id' => "method_$row_index", 'style' => 'width:100%;',

					'placeholder' => __('messages.please_select')]); !!}
				@endif
			</div>

		</div>

	</div>

	<div class="{{$col_class}} account_module">

		<div class="form-group">

			{!! Form::label("account_$row_index" , __('lang_v1.payment_account') . ':') !!}

			<div class="input-group">

				<span class="input-group-addon">

					<i class="fa fa-money"></i>

				</span>

				
				@if(isset($view_page) || isset($edit_page))
					<input type="hidden" id="previous_acc_id_{{$row_index}}" class="previous_account" value="@if(!empty($data[0]->related_account_id)) {{$data[0]->related_account_id}} @elseif(!empty($data[0]->t_account_id)) {{$data[0]->t_account_id}} @endif">
					{!! Form::select("payment[$row_index][account_id]", [], !empty($data[0]->related_account_id) ? $data[0]->related_account_id : (!empty($data[0]->t_account_id)? $data[0]->t_account_id : null) , ['class' =>

					'form-control account_id

					select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_$row_index", 'style' =>

					'width:100%;']); !!}
				@else
					<input type="hidden" id="previous_acc_id_{{$row_index}}" class="previous_account" value="@if(!empty($payment->related_account_id)) {{$payment->related_account_id}} @elseif(!empty($payment->account_id)) {{$payment->account_id}} @endif">
				

					{!! Form::select("payment[$row_index][account_id]", [], !empty($payment->related_account_id)? $payment->related_account_id : (!empty($payment->account_id)? $payment->account_id : null) , ['class' =>

					'form-control account_id

					select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_$row_index", 'style' =>

					'width:100%;']); !!}
				@endif

			</div>

		</div>

	</div>

	
	@php
                    
        $business_id = request()
            ->session()
            ->get('user.business_id');
        
        $pacakge_details = [];
            
        $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
        if (!empty($subscription)) {
            $pacakge_details = $subscription->package_details;
        }
    
    @endphp
    
    

	{{-- post dated cheque check box --}}
	
	@if(!empty($pacakge_details['show_post_dated_cheque']))
	   <div class="clearfix"></div>
    	<div class="col-md-6 hide post_dated_cheque">
    
    		<br>
    
    		<label>
    			@if(isset($view_page) || isset($edit_page))
    				 {!! Form::checkbox("payment[$row_index][post_dated_cheque]", 1, !empty($data[0]->t_post_dated_cheque) == 1, ['class' => 'icheckbox_square-blue', 'id' => "post_dated_cheque_$row_index",]); !!} @lang('lang_v1.post_dated_cheque')?
    			@else
    				 {!! Form::checkbox("payment[$row_index][post_dated_cheque]", 1, !empty($payment->post_dated_cheque) == 1, ['class' => 'icheckbox_square-blue', 'id' => "post_dated_cheque_$row_index"]); !!} @lang('lang_v1.post_dated_cheque')?
    			@endif
    	</div>
    	
    	@if(!empty($pacakge_details['update_post_dated_cheque']))
    	<div class="col-md-6 hide post_dated_cheque">
    
    		<br>
    
    		<label>
    			@if(isset($view_page) || isset($edit_page))
    				 {!! Form::checkbox("payment[$row_index][update_post_dated_cheque]", 1, !empty($data[0]->t_update_post_dated_cheque) == 1, ['class' => 'icheckbox_square-blue', 'id' => "update_post_dated_cheque_$row_index",]); !!} @lang('account.update_post_dated_cheque')?
    			@else
    				 {!! Form::checkbox("payment[$row_index][update_post_dated_cheque]", 1, !empty($payment->update_post_dated_cheque) == 1, ['class' => 'icheckbox_square-blue', 'id' => "update_post_dated_cheque_$row_index"]); !!} @lang('account.update_post_dated_cheque')?
    			@endif
    	</div>
    	@endif
    	<div class="clearfix"></div>
    @endif

	

	<div class="col-md-6 hide bank_transfer_fields">

	    <div class="col-md-6">

    		<div class="form-group">
    			{!! Form::label("cheque_number",__('lang_v1.cheque_no')) !!}

    			@if(isset($view_page) || isset($edit_page))
	    			{!! Form::text("payment[$row_index][cheque_number]",!empty($data[0]->t_cheque_number)?$data[0]->t_cheque_number: $payment_line['cheque_number'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no'), 'id' => "cheque_number",]); !!}
	    		@else
	    			{!! Form::text("payment[$row_index][cheque_number]",!empty($payment->cheque_number)?$payment->cheque_number: $payment_line['cheque_number'] ?? null, ['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no'), 'id' => "cheque_number"]); !!}
	    		@endif

    		</div>

    	</div>

    	<div class="col-md-6">

    		<div class="form-group">

    			{!! Form::label("cheque_date_$row_index",__('lang_v1.cheque_date')) !!}
    			
    			@if(isset($view_page) || isset($edit_page))
    				{!! Form::date("payment[$row_index][cheque_date]",!empty($data[0]->t_cheque_date)?$data[0]->t_cheque_date: '', ['class' => 'form-control cheque_date', 'placeholder' => __('lang_v1.cheque_date'),]); !!}
    			@else
    				{!! Form::date("payment[$row_index][cheque_date]",!empty($payment->cheque_date)?$payment->cheque_date: '', ['class' => 'form-control cheque_date', 'placeholder' => __('lang_v1.cheque_date')]); !!}
    			@endif
    		</div>

    	</div>

	</div>

	

	@include('sale_pos.partials.payment_type_details', ['payment' => $payment,'edit' => !empty($edit) ? 'true' : ''])

	<div class="{{ $col_class }}">

		<div class="form-group">

			{!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}

			@if(isset($view_page) || isset($edit_page))
				{!! Form::textarea("payment[$row_index][note]", !empty($data[0]->t_note)?$data[0]->t_note:'' ?? null,

				['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
			@else
				{!! Form::textarea("payment[$row_index][note]", !empty($payment->note)?$payment->note:$payment_line['note'] ?? null,

				['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
			@endif
		</div>

	</div>

</div>



<script>



</script>