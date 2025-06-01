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
    use App\Transaction;
@endphp

<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_table_heading',
			['start_date' =>
			$start_date, 'end_date' => $end_date])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('lang_v1.description')</th>
				<th>@lang('lang_v1.type')</th>
				<th>@lang('sale.location')</th>
				<th>@lang('account.credit')</th>
				<th>@lang('lang_v1.payment_method')</th>
			</tr>
		</thead>
		<tbody>
			@php
			$balance = $ledger_details['beginning_balance'];
			@endphp
			
			@php $opening_balance = 0; $sales = 0; $paid = 0; $returns = 0;$returns_paid = 0;$loans = 0; $cheque_returns = 0; $cheque_return_charges = 0; $interest_charged = 0; @endphp
			@foreach($ledger_transactions as $one)
				@if(empty($one->deleted_at))
			    	@if($one->type == "payment" || $one->type == "customer_payment")
    			    @php 
    			        $status = null;$pmt_methods = null; $notes = null;
    			         $transaction = Transaction::find($one->id);
    			    @endphp
    			
        			@if($one->type == "payment")
        			    @php 
        			        if(empty($one->deleted_at)){
            			        $balance -= $one->amount;
            			        $paid += $one->amount;
        			        }
        			        
        			        
        			        $debit = "";
        			        $credit = $one->amount;
        			        
        			        
        			        $type = __('contact.payment');  
        			        $description = $type."<br>".__('contact.ref_no')." ".$one->invoice_no;
        			        
        			        $pmt_methods = TransactionPayment::leftjoin('accounts','transaction_payments.account_id','accounts.id')->where('transaction_payments.id',$one->payment_row)->select(['transaction_payments.*','accounts.name as account_name'])->first();
        			        
        			     @endphp
        			@elseif($one->type == "customer_payment")
        			    @php 
        			        if(empty($one->deleted_at)){
            			        $balance -= $one->amount;
            			        $paid += $one->amount;
        			        }
        			        
        			        
        			        $debit = "";
        			        $credit = $one->amount;
        			        
        			        
        			        $type = __('contact.customer_payments');   
        			        $description = $type."<br>".__('contact.ref_no')." ".$one->invoice_no;
        			    @endphp
        			    
        			@else
        			    @php $type = $one->type; $description = $type;  @endphp
        			@endif
    			
    			
    			    <tr @if(!empty($one->deleted_at)) class="deleted-row" @endif>
    			        <td>
    			            {{@format_date($one->date)}}
    			        </td>
    			        
    			        <td>
    			            @php
    			                $user = null;
    			                if(!empty($one->deleted_at)){
    			                    if(!empty($one->deleted_by)){
    			                        $user = \App\User::find($one->deleted_by);
    			                    }else{
    			                        $user = \App\User::first();
    			                    }
    			                    
    			                }
    			            
    			            @endphp
    			            
    			            {!! $description !!}
    			            <br>
    			            @if (!empty($one->airticket_no))
                             {{ "Airticket No: " . $one->airticket_no }}
                            @endif
                            <br>
    			            @if(!empty($transaction) && $transaction->discount_amount > 0)
    			                <b>@lang('petro::lang.discount'):</b> {{@num_format($transaction->discount_amount)}} <br>
    			            @endif
    			            
    			            @if(!empty($transaction) && !empty($transaction->ref_no) && $one->type != "payment"  )
    			                <b>@lang('lang_v1.ref_no'):</b> {{$transaction->ref_no }} <br>
    			            @endif
    			            
    			            @if(!empty($transaction) && !empty($transaction->order_no))
    			                <b>@lang('lang_v1.order_no'):</b> {{$transaction->order_no }} <br>
    			            @endif
    			            
    			            
    			            @if(!empty($user) && !empty($one->deleted_at))
    			                <b>@lang('lang_v1.deleted_by'):</b>{{$user->username}} <br>
    			            @endif
    			            
    			            {!! $notes !!}
    			            
    			        </td>
    			        
    			        <td>
    			            {{ $type }}
    			        </td>
    			        
    			        <td>
    			            {{$one->location_name}}
    			        </td>
    			        
    			        
    			        <td>
    			            {{!empty($credit) ? @num_format($credit) : ""}} 
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
    			            @endif
    			        </td>
    			    </tr>
    			 @endif
				@endif
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
