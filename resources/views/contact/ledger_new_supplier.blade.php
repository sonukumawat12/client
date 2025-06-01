<!-- app css -->

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
    use Modules\Petro\Entities\CustomerPayment;
    use App\TransactionPayment;
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
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('lang_v1.opening_balance') / @lang('contact.fleet_opening_balance')</td>
			<td> <span id="opening_balance">0.00</span>
			</td>
		</tr>
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>{{@num_format($ledger_details['beginning_balance'])}}
			</td>
		</tr>
	
		<tr>
			<td>@lang('contact.total_purchase')</td>
			<td>
			    <span id="total_sales"></span>
			</td>
		</tr>
		
		
		<tr>
			<td>@lang('sale.total_paid')</td>
			<td>
			    <span id="total_paid"></span>
			</td>
		</tr>

		
		<tr>
			<td><strong>@lang('lang_v1.balance_due')</strong></td>
			<td><span id="balance_due">0.00</span></td>
		</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_table_heading',
			['start_date' =>
			$start_date, 'end_date' => $end_date])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('lang_v1.system_date')</th>
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
    			<td class="row-border">B/F Balance</td>
    			<td></td><td></td><td></td><td></td><td></td><td></td>
    			<td class="row-border">
    				{{number_format($ledger_details['beginning_balance'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
    			</td>
    			<td></td>
        	</tr>
			@php $opening_balance = 0; $sales = 0; $paid = 0; $returns = 0;$returns_paid = 0; @endphp
			@foreach($ledger_transactions as $one)
			    @php $status = null;$pmt_methods = null; @endphp
			    
			    @php
			        if(!empty($transaction_amounts) && $one->amount != $transaction_amounts){
			            continue;
			        }
			    @endphp
			    
			    @php
			        if(!empty($transaction_type)){
			            if(($transaction_type == 'debit' && !in_array($one->type, ["cheque_return","property_purchase","expense","purchase","opening_balance"])) || ($transaction_type == 'credit' && in_array($one->type, ["cheque_return","property_purchase","expense","purchase","opening_balance"]))){
			                continue;
			            }
			            
			        }
			    @endphp
			
    			@if($one->type == "cheque_return")
    			    @php 
    			        $balance += $one->amount;
    			        $debit = $one->amount;
    			        $credit = "";
    			        
    			        $returns += $one->amount;
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $type = __('contact.cheque_return'); 
    			        $description = $type;
    			    @endphp
    			@elseif($one->type == "property_purchase")
    			    @php
    			        $balance += $one->amount;
    			        $debit = $one->amount;
    			        $credit = "";
    			        $sales += $one->amount;
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $type = __('contact.property_purchase');
    			        $description = $type."<br>".__('contact.invoice_no')." ".$one->invoice_no;
    			    @endphp
    			@elseif($one->type == "expense")
    			    @php 
    			        $balance += $one->amount;
    			        $debit = $one->amount;
    			        $credit = "";
    			        $sales += $one->amount;
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $type = __('contact.expenses');
    			        $description = $type;
    			        
    			    @endphp
    			@elseif($one->type == "purchase")
    			    @php 
    			        $balance += $one->amount;
    			        $debit = $one->amount;
    			        $credit = "";
    			        $sales += $one->amount;
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $type = __('contact.purchase');
    			        $description = $type."<br>".__('contact.invoice_no')." ".$one->invoice_no;
    			    @endphp
    			@elseif($one->type == "opening_balance")
    			    @php 
    			        $balance += $one->amount;
    			        $debit = $one->amount;
    			        $credit = "";
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $opening_balance += $one->amount;
    			        
    			        $type = __('contact.opening_balance');
    			        $description = $type;
    			    @endphp
    			@elseif($one->type == "purchase_return")
    			    @php 
    			        $balance -= $one->amount;
    			        $debit = "";
    			        $credit = $one->amount;
    			        $returns += $one->amount;
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        
    			        $type = __('contact.purchase_return');
    			        $description = $type."<br>".__('contact.invoice_no')." ".$one->invoice_no;
    			    @endphp
    			@elseif($one->type == "_deleted_purchase")
    			    @php 
    			        $balance -= $one->amount;
    			        $debit = "";
    			        $credit = $one->amount;
    			        $returns += $one->amount;
    			        $transaction = \App\Transaction::leftjoin('users','users.id','transactions.new_deleted_by')->where('transactions.id',$one->id)->select('users.username','transactions.invoice_no','transactions.new_deleted_at')->first();
    			        $type = "Purchase Deleted";
    			        $description = "";
    			        
    			        if(!empty($transaction)){
    			            $description = "Deleted PO No".$transaction->invoice_no." by ".$transaction->username;
    			        }
    			    @endphp
                    @elseif($one->type == "payment")
    			    @php 
    			        if(empty($one->deleted_at)){
        			        $balance -= $one->amount;
        			        $paid += $one->amount;
    			        }
    			        
    			        
    			        $debit = "";
    			        $credit = $one->amount;
    			        if ($one->payment_status != "paid" && !empty($one->airticket_no)) {
                            $debit = $one->amount;
        			        $credit = "";
                        }
    			        
    			        
    			        $status = (string) view('sell.partials.payment_status', ['payment_status' => $one->payment_status,'id' => $one->id]);
    			        


                        $type = __('contact.payment');
                        
                        if ($one->payment_status != "paid" && !empty($one->airticket_no)) {
                            $type = __('contact.sale');
                            $balance += $one->amount;
                        }

    			        $description = $type . "<br>" . __('contact.ref_no') . " " . $one->invoice_no .  $one->airticket_no;
                            			        
    			        $pmt_methods = TransactionPayment::leftjoin('accounts','transaction_payments.account_id','accounts.id')->where('transaction_payments.id',$one->payment_row)->select(['transaction_payments.*','accounts.name as account_name'])->withTrashed()->first();
    			        $payment_ref_no = !empty($pmt_methods) ? $pmt_methods->payment_ref_no : '';
    			        
    			        $txn_ids = TransactionPayment::where('payment_ref_no',$payment_ref_no)->pluck('transaction_id')->toArray();
    			        $transactions = \App\Transaction::whereIn('id',$txn_ids)->distinct('invoice_no')->pluck('invoice_no')->toArray();
    			        
    			        if(!empty($transactions)){
    			            $description .= "<br><b>".__('lang_v1.related_invoices').":</b> ".implode(', ',$transactions);
    			        }
    			        
    			        
    			     @endphp
    			@else
    			    @php $type = $one->type; $description = $type;  @endphp
    			@endif
			
			
			    <tr>
			        <td>
			            {{@format_date($one->date)}}
			        </td>
			        
			         <td>
			            {{@format_datetime($one->created_at)}}
			        </td>
			        
			        <td>
			            <!--{!! $description !!}-->
			            @if($one->type == 'payment')
			            {{"Payment Ref No: ".$one->bill_no}}
						@elseif($one->type == 'purchase')
			            {{"Supplier Invoice Number - ".$one->bill_no}}
						@else
						{{ $one->bill_no }}
						@endif
			        </td>
			        
			        <td>
			            {{ $type }}
			        </td>
			        
			        <td>
			            {{$one->location_name}}
			        </td>
			        
			        <td>
			            @if(!empty($status))
    			            {!! $status !!}
    			        @endif
			        </td>
			        
			         <td>
			            @if(!empty($credit) && $credit < 0)
			                {{abs($credit)}}
			            @endif
			            
			            {{!empty($debit) && $debit > 0 ? @num_format($debit) : ""}} 
			        </td>
			        
			        <td>
			            @if(!empty($debit) && $debit < 0)
			                {{abs($debit)}}
			            @endif
			            
			            {{!empty($credit) && $credit > 0 ? @num_format($credit) : ""}} 
			        </td>
			        
			        <td>
			           {{@num_format($balance)}} 
			        </td>
			        
			        <td>
			            @if(!empty($pmt_methods))
			                {{ ucfirst(str_replace("_"," ",$pmt_methods->method)) }}
			                
			                @if(strtolower($pmt_methods->method) == 'bank' || strtolower($pmt_methods->method) == 'bank_transfer' || strtolower($pmt_methods->method) == 'cheque' || strtolower($pmt_methods->method) == 'direct_bank_deposit')
			                    <br>
			                    @if(!empty($pmt_methods->account_name))
			                        {!! __('contact.bank_name')." ".$pmt_methods->account_name."<br>" !!}
			                    @endif
			                    
			                    @if(!empty($pmt_methods->cheque_number))
			                        {!! __('contact.cheque_number')." ".$pmt_methods->cheque_number."<br>" !!}
			                    @endif
			                    
			                    @if(!empty($pmt_methods->cheque_date))
			                        {!! __('contact.cheque_date')." ".@format_date($pmt_methods->cheque_date)."<br>" !!}
			                    @endif
			                    
			                @endif
						@else
						N/A
			            @endif
			        </td>
			    </tr>
			@endforeach

		</tbody>
	</table>
</div>

    @php
        $reports_footer = \App\System::where('key','admin_reports_footer')->first();
    @endphp
    
    @if(!empty($reports_footer))
        <style>
            #footer {
                display: none;
            }
        
            @media print {
                #footer {
                    display: block !important;
                    position: fixed;
                    bottom: -1mm;
                    width: 100%;
                    text-align: center;
                    font-size: 12px;
                    color: #333;
                }
            }
        </style>

        <div id="footer">
            {{ ($reports_footer->value) }}
        </div>
    @endif

<!-- This will be printed -->
<section class="invoice print_section" id="ledger_print">
</section>

<script>
    $("#balance_due").html("{{@num_format($balance)}}");
    $("#opening_balance").html("{{@num_format($opening_balance)}}");
    $("#total_sales").html("{{@num_format($sales)}}");
    $("#total_paid").html("{{@num_format($paid)}}");
    $("#total_returns").html("{{@num_format($returns)}}");
    $("#total_returns_paid").html("{{@num_format($returns_paid)}}");
</script>
