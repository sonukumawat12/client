<!-- app css -->
@php
	$balance_due = $ledger_details['opening_balance'] + $ledger_details['beginning_balance'] + $ledger_details['total_invoice'] + $ledger_details['returned_cheques'] + $ledger_details['returned_cheque_charges'] - $ledger_details['total_paid'];
@endphp
@if(!empty($for_pdf))
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
@endif
<style>
	.bg_color {
		background: #357ca5;
		font-size: 20px;
		color: #fff;
	}

	.bg-aqua{
	    background: #8F3A84;
	}

	.text-center {
		text-align: center;
	}

	#ledger_table th {
		background: #357ca5;
		color: #fff;
	}

	#ledger_table>tbody>tr:nth-child(2n+1)>td,
	#ledger_table>tbody>tr:nth-child(2n+1)>th {
		background-color: rgba(89, 129, 255, 0.3);
	}
</style>

@php
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 text-center @endif">
	<p class="text-center"><strong>{{$contact->business->name}}</strong><br>{{$location_details->city}}, {{$location_details->state}}<br>{!!
		$location_details->mobile !!}</p>
	<hr>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class="bg_color" style="width: 40%">@lang('lang_v1.to'):</p>
	<p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!} @if(!empty($contact->email))
		<br>@lang('business.email'): {{$contact->email}} @endif
		<br>@lang('contact.mobile'): {{$contact->mobile}}
		@if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}} @endif
	</p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.account_summary')</p>
	<hr>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('lang_v1.opening_balance')</td>
			<td>{{number_format($ledger_details['opening_balance'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>{{number_format($ledger_details['beginning_balance'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>
		@if( $contact->type == 'supplier' || $contact->type == 'both')
		<tr>
			<td>@lang('report.total_purchase')</td>
			<td>{{number_format($ledger_details['total_purchase'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>
		@endif
		@if( $contact->type == 'customer' || $contact->type == 'both')
		<tr>
			<td>@lang('lang_v1.total_sales')</td>
			<td>{{number_format($ledger_details['total_invoice'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>
		@endif
		<tr>
			<td>@lang('sale.total_paid')</td>
			<td>{{number_format($ledger_details['total_paid'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>

		<tr>
			<td>@lang('sale.returned_cheques')</td>
			<td>{{number_format($ledger_details['returned_cheques'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>

		<tr>
			<td>@lang('sale.returned_cheque_charges')</td>
			<td>{{number_format($ledger_details['returned_cheque_charges'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
			</td>
		</tr>

		<tr>
			<td><strong>@lang('lang_v1.balance_due')</strong></td>
			<td>{{number_format($balance_due,  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}</td>
		</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_table_heading',
			['start_date' =>
			$ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('lang_v1.system_created_date')</th>
				<th>@lang('lang_v1.description')</th>
				<th>@lang('lang_v1.type')</th>
				<th>@lang('sale.location')</th>
				<th>@lang('sale.payment_status')</th>
				<th>@lang('account.debit')</th>
				<th>@lang('account.credit')</th>
				<th>@lang('lang_v1.balance')</th>
				<th>@lang('lang_v1.payment_method')</th>
			</tr>
		</thead>
		<tbody>
			@php
			$balance = $ledger_details['beginning_balance'];
			@endphp
			<tr>
    			<td class="row-border">
    			    @if(!empty($ledger_details['opening_date']))
            			{{ @format_date($ledger_details['opening_date']) }}
        			@endif
        		</td>
    			<td colspan="6" class="row-border">B/F Balance</td>
    			<td class="row-border">
    				{{number_format($ledger_details['beginning_balance'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
    			</td>
    			<td colspan="3" class="row-border"></td>
    	</tr>
			@foreach($ledger_transactions as $data)
			@php
			$transaction_payment = null;
			if(!empty($data->transaction_payment_id)){
				$transaction_payment = App\TransactionPayment::where('id', $data->transaction_payment_id)->withTrashed()->first();
			}
			$amount = 0;
			if(!empty($transaction_payment)){
				if(empty($transaction_payment->transaction_id)){ // if empty then it will be parent payment
					$amount = $transaction_payment->amount;  // show parent transaction payment amount
				}else{
					$amount = $data->amount; // get the amount from contact ledger if not a payment
				}
			}else{
				$amount = $data->amount;
			}
			if ($contact->type == 'supplier'){
				if($data->acc_transaction_type == 'debit') {
					$balance = $balance - $amount;
				}
				if($data->acc_transaction_type == 'credit') {
					$balance = $balance + $amount;
				}
			}
			if ($contact->type == 'customer'){
				if($data->acc_transaction_type == 'debit') {
					$balance = $balance + $amount;
				}
				if($data->acc_transaction_type == 'credit') {
					$balance = $balance - $amount;
				}
			}
			if($data->transaction_type == 'opening_balance' && $data->final_total < 0){
				$payment_status = 'over-payment';
			}else{
				$payment_status = App\Transaction::getPaymentStatus($data);
			}
			@endphp
			<tr>
				<td class="row-border">{{@format_date($data->operation_date)}}</td>

				<td>
					@if($data->is_settlement == 1)
						<b>Settlement No: </b> {{$data->invoice_no}} <br>
						@if($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'debit' && $data->t_sub_type == 'cash_payment')
							{{$data->ref_no}} Cash Sale
						@elseif($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'credit' && $data->t_sub_type == 'cash_payment')
							{{$data->ref_no}} Cash Payment
						@elseif($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'debit' && $data->t_sub_type == 'card_payment')
							{{$data->ref_no}} Card Sale
						@elseif($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'credit' && $data->t_sub_type == 'card_payment')
							{{$data->ref_no}} Card Payment
						@elseif($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'debit' && $data->t_sub_type == 'cheque_payment')
							{{$data->ref_no}} Cheque Sale
						@elseif($data->transaction_type == 'settlement' && $data->acc_transaction_type == 'credit' && $data->t_sub_type == 'cheque_payment')
							{{$data->ref_no}} <br> <b>@lang('lang_v1.bank_name'):</b> {{$data->bank_name}} <b>@lang('lang_v1.cheque_no'):</b> {{$data->cheque_number}} <b>@lang('lang_v1.cheque_date'):</b> {{$data->cheque_date}}
						@elseif($data->transaction_type == 'sell' && $data->acc_transaction_type == 'debit' && $data->t_sub_type == 'credit_sale')
							{{$data->ref_no}} Credit Sale
							@php
								$settlement_expense = \Modules\Petro\Entities\SettlementCreditSalePayment::where('transaction_id', $data->transaction_id)->first();
							@endphp
							<br>
							@if(!empty($settlement_expense))
							<b>Order No:</b> {{ $settlement_expense->order_number}}<br>
							<b>Order Date:</b> {{ $settlement_expense->order_date}}
							<b>Reason: </b> {{ $settlement_expense->reason }}<br>
							@endif
						@endif
						@if(!empty($data->deleted_by))
							<b>@lang('lang_v1.deleted')</b>
						@endif
					@else
					    @if($data->transaction_type == 'ledger')
					    <b>{{ $data->invoice_no}}</b>
					    @endif

						@if($data->is_direct_sale)
    						@lang('lang_v1.invoice_sale'): {{$data->invoice_no}}
						@elseif($data->transaction_type == 'sell' && $data->t_sub_type == 'settlement')
    						@lang('lang_v1.settlement'): {{$data->ref_no}}
						@elseif($data->transaction_type == 'sell' && $data->type == 'debit')
    						@lang('lang_v1.pos_sale'): {{$data->invoice_no}}
						@elseif($data->transaction_type == 'sell' && $data->type == 'credit')
    						@lang('lang_v1.payment'): {{$data->invoice_no}}
						@elseif($data->transaction_type == 'sell_return')
    						@lang('lang_v1.sell_return'): {{$data->invoice_no}}
						@elseif($data->transaction_type == 'purchase')
    						<b>@lang('lang_v1.purchase'):</b> {{$data->ref_no}}<br>
    						<b>@lang('purchase.purchase_no'):</b> {{$data->invoice_no}}<br>
						@elseif($data->transaction_type == 'purchase_return')
    						@lang('lang_v1.purchase_return'): {{$data->ref_no}}
						@elseif($data->transaction_type == 'advance_payment')
    						@lang('lang_v1.advance_payment'): {{$data->ref_no}}
						@elseif($data->transaction_type == 'refund')
    						@lang('lang_v1.refund'): {{$data->ref_no}} <br> <b>@lang('lang_v1.invoice_no'): </b>{{ $data->invoice_no}}
						@elseif($data->transaction_type == 'cheque_return')
							@if($data->sub_type == 'cheque_return_charges')
							@lang('lang_v1.cheque_return_charges')<br>
							@else
							@lang('lang_v1.cheque_return')<br>
							@endif
						<b>@lang('lang_v1.invoice_no'): </b>{{ $data->invoice_no}}
						<br> <b>@lang('lang_v1.bank_name'):</b> {{$data->bank_name}} <b>@lang('lang_v1.cheque_no'):</b> {{$data->cheque_number}} <b>@lang('lang_v1.cheque_date'):</b> {{$data->cheque_date}}
						@elseif ($data->transaction_type == 'fleet_opening_balance')
                                    @php $fleet = \Modules\Fleet\Entities\Fleet::find($data->fleet_id); @endphp
                                    <b>Invoice No: </b> {{ $data->invoice_no}} <br>

                                    @if (!empty($fleet))
                                        <b> @lang('fleet::lang.vehicle_no') :</b> {{ $fleet->vehicle_number }}
                                    @endif

                        @elseif ($data->transaction_type == 'route_operation')
                                    @php $fleet = \Modules\Fleet\Entities\Fleet::find($data->fleet_id); @endphp
                                    <b>Invoice No: </b> {{ $data->invoice_no}} <br>
                                    @php $ro = \Modules\Fleet\Entities\RouteOperation::where('invoice_no',$data->invoice_no)->first();
                                        if(!empty($ro)){
                                            $route = \Modules\Fleet\Entities\Route::find($ro->route_id);
                                        }
                                    @endphp
                                    <b>@lang( 'fleet::lang.route' ) :</b>  {{ !empty($route) ? $route->route_name : "" }}
                                    @if (!empty($fleet))
                                        <br><b> @lang('fleet::lang.vehicle_no') :</b> {{ $fleet->vehicle_number }}
                                    @endif

						@endif
						@if(!empty($data->deleted_by))
							<br><b>@lang('lang_v1.deleted')</b>
						@endif

					@endif
				</td>

				<td>@if($data->transaction_type == 'purchase')
				        @lang('lang_v1.purchase')
				    @elseif($data->transaction_type	== 'opening_balance')
				        @lang('lang_v1.opening_balance')
				    @elseif($data->transaction_type == 'sell')
				        @if($data->acc_transaction_type == 'credit')
				            Payment
				        @else
				            @lang('lang_v1.sell')
				        @endif
					@elseif ($data->transaction_type == 'fleet_opening_balance')
					    @lang('fleet::lang.ob_of_to')
					@elseif ($data->transaction_type == 'route_operation')
					    @lang('fleet::lang.route_operation')
					@elseif($data->transaction_type == 'sell_return')
					    @lang('lang_v1.sell_return')
					@endif</td>

				<td>{{$data->location_name}}</td>
				<td><span class="label @payment_status($payment_status)">@lang('lang_v1.' . $payment_status) </span> </td>
				<td class="ws-nowrap">@if($data->acc_transaction_type == 'debit')
					{{number_format($amount,  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
					@endif</td>
				<td class="ws-nowrap">@if($data->acc_transaction_type == 'credit')
					{{number_format($amount,  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
					@endif</td>
				<td class="ws-nowrap">
					{{number_format($balance,  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
				</td>
				@if($data->transaction_type == 'purchase')
				@if($data->type == 'credit')
				<td>@lang('contact.credit_purchase')</td>
				@else
					@if(!empty($transaction_payment->deleted_at))
					<td>{{ucfirst($transaction_payment->method)}}</td>
					@else
					<td>
						@if(!empty($transaction_payment))
						@if($transaction_payment->method == 'bank_transfer')
						@php
							$bank_account = App\Account::find($transaction_payment->account_id);
						@endphp
						<b>@lang('lang_v1.bank_name'):</b> @if(!empty($bank_account)) {{$bank_account->name}} @endif <br>
						<b>@lang('lang_v1.cheque_number'):</b> {{$transaction_payment->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						<b>@lang('lang_v1.account_number'):</b> @if(!empty($bank_account)){{$bank_account->account_number}} @endif <br>
						@elseif($transaction_payment->method == 'cheque')
						{{ucfirst($transaction_payment->method)}} <br>
						<b>@lang('lang_v1.cheque_number'):</b> {{$data->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						@else
							{{ucfirst($transaction_payment->method)}}
						@endif
					@endif
					</td>
					@endif
				@endif
				@elseif($data->is_credit_sale == '1' && empty($data->transaction_payment_id))
				<td>@lang('contact.credit_sale')</td>
				@else
				<td>
					@if ($contact->type == 'customer')
					@if(!empty($transaction_payment))
						@if($transaction_payment->method == 'bank_transfer')
						@php
							$bank_account = App\Account::find($transaction_payment->account_id);
						@endphp
						<b>@lang('lang_v1.bank_name'):</b> @if(!empty($bank_account)) {{$bank_account->name}} @endif <br>
						<b>@lang('lang_v1.cheque_number'):</b> {{$transaction_payment->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						<b>@lang('lang_v1.account_number'):</b> @if(!empty($bank_account)){{$bank_account->account_number}} @endif <br>
						@elseif($transaction_payment->method == 'direct_bank_deposit' )
						@php
							$bank_account = App\Account::find($transaction_payment->account_id);
						@endphp
						<b>@lang('lang_v1.direct_bank_deposit')</b><br>
						<b>@lang('lang_v1.bank_name'):</b> @if(!empty($bank_account)) {{$bank_account->name}} @endif <br>
						@elseif($transaction_payment->method == 'cheque')
						{{ucfirst($transaction_payment->method)}} <br>
						<b>@lang('lang_v1.cheque_number'):</b> {{$data->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						@else
							{{ucfirst($transaction_payment->method)}}
						@endif
					@endif
					@endif
					@if ($contact->type == 'supplier')
					@if(!empty($transaction_payment))
						@if($transaction_payment->method == 'bank_transfer')
						@php
							$bank_account = App\Account::find($transaction_payment->account_id);
						@endphp
						<b>@lang('lang_v1.bank_name'):</b> @if(!empty($bank_account)) {{$bank_account->name}} @endif <br>
						<b>@lang('lang_v1.cheque_number'):</b> {{$data->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						<b>@lang('lang_v1.account_number'):</b> @if(!empty($bank_account)){{$bank_account->account_number}} @endif <br>
						@elseif($transaction_payment->method == 'direct_bank_deposit' )
						@php
							$bank_account = App\Account::find($transaction_payment->account_id);
						@endphp
						<b>@lang('lang_v1.direct_bank_deposit')</b><br>
						<b>@lang('lang_v1.bank_name'):</b> @if(!empty($bank_account)) {{$bank_account->name}} @endif <br>
						@elseif($transaction_payment->method == 'cheque')
						<b>@lang('lang_v1.cheque_number'):</b> {{$data->cheque_number}} <br>
						<b>@lang('lang_v1.cheque_date'):</b> @if(!empty($data->cheque_date)){{@format_date($data->cheque_date)}} @endif <br>
						@else
							{{ucfirst($transaction_payment->method)}}
						@endif
					@endif
					@endif
				</td>
				@endif
			</tr>
			@endforeach

		</tbody>
	</table>
</div>

<!-- This will be printed -->
<section class="invoice print_section" id="ledger_print">
</section>
