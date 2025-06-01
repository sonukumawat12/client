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
    use Modules\Vat\Entities\VatPayment;
@endphp

<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('vat::lang.vat_summary')</p>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('vat::lang.vat_opening_balance')</td>
			<td> <span id="opening_balance">0.00</span></td>
		</tr>
		<tr>
			<td>@lang('vat::lang.vat_bf_balance')</td>
			<td> <span id="vat_bf_balance">{{@num_format($ledger_details['beginning_balance'])}}</span></td>
		</tr>
		
		<tr>
			<td>@lang('vat::lang.vat_input')</td>
			<td> <span id="vat_input">0.00</span></td>
		</tr>
		
		<tr>
			<td>@lang('vat::lang.vat_output')</td>
			<td> <span id="vat_output">0.00</span></td>
		</tr>
		
		<tr>
			<td>@lang('vat::lang.vat_penalty')</td>
			<td> <span id="vat_penalty">0.00</span></td>
		</tr>
		
		<tr>
			<td>@lang('vat::lang.vat_payment')</td>
			<td> <span id="vat_payment">0.00</span></td>
		</tr>
		
		<tr>
			<td>@lang('vat::lang.vat_payable_balance')</td>
			<td> <span id="vat_payable_balance">0.00</span></td>
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
				<th>@lang('lang_v1.description')</th>
				<th>@lang('vat::lang.vat_type')</th>
				<th>@lang('vat::lang.cheque_no')</th>
				<th>@lang('vat::lang.vat_input')</th>
				<th>@lang('vat::lang.vat_output')</th>
				<th>@lang('vat::lang.balance')</th>
			</tr>
		</thead>
		<tbody>
		    @php
			$balance = $ledger_details['beginning_balance'];
			@endphp
			<tr>
    			<td class="row-border">B/F Balance</td>
    			<td></td><td></td><td></td><td></td>
    			<td class="row-border">
    				{{number_format($ledger_details['beginning_balance'],  $currency_precision, session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}
    			</td>
    			<td ></td>
        	</tr>
        	
        	@php $input_tax = 0; $output_tax = 0;$total_paid = 0; $total_penalty = 0; $opening_balance = 0; @endphp
        	@foreach($ledger_transactions as $one)
        	    @php
        	        $details = "";
        	        if($one->type == 'sell' || $one->type == 'vat_penalty'){
        	            $input_amount = "";
        	            $output_amount = $one->amount;
        	            $balance += $one->amount;
        	            
        	            if($one->type == 'vat_penalty'){
        	                $total_penalty += $one->amount;
        	            }else{
        	                $output_tax += $one->amount;
        	            }
        	            
        	        } else if($one->type == 'input_ob' || $one->type == 'output_ob'){
        	            
        	            if($one->type == 'input_ob'){
        	                $input_amount = $one->amount;
        	                $output_amount = "";
        	                
        	                $opening_balance -= $one->amount;
        	                $balance -= $one->amount;
        	            }else{
        	                $input_amount = "";
        	                $output_amount = $one->amount;
        	                
        	                $opening_balance += $one->amount;
        	                $balance += $one->amount;
        	            }
        	            
        	        }
        	        else if($one->type == 'vat_payment'){
        	            $input_amount = "";
        	            $output_amount = $one->amount;
        	            $balance -= $one->amount;
        	            $total_paid += $one->amount;
        	            
        	            
        	            $vat_payment = VatPayment::find($one->id);
                        if(!empty($vat_payment)){
                            $details .= '<b>' . __('vat::lang.form_no')."</b> ". $vat_payment->form_no . '<br>' ;
                            if(!empty($vat_payment->cheque_date)){
                                $details .= '<b>' . __('vat::lang.cheque_date')."</b> ". $vat_payment->cheque_date . '<br>' ;
                            }
                            
                            if(!empty($vat_payment->cheque_number)){
                                $details .= '<b>' . __('vat::lang.cheque_number')."</b> ". $vat_payment->cheque_number . '<br>' ;
                            }
                            
                            if(!empty($vat_payment->recipient_name)){
                                $details .= '<b>' . __('vat::lang.recipient_name')."</b> ". $vat_payment->recipient_name . '<br>' ;
                            }
                        }
        	            
        	            
        	        }else if($one->type == 'purchase' || $one->type == 'expense'){
        	            $output_amount = "";
        	            $input_amount = $one->amount;
        	            $input_tax += $one->amount;
        	            $balance -= $one->amount;
        	        }
    
        	    @endphp
        	    <tr class="@if($one->type == 'vat_penalty') text-danger @endif">
        	        <td>{{@format_date($one->date)}}</td>
        	        <td>
        	            @if(in_array($one->type,['output_ob','input_ob']))
        	                @lang('vat::lang.vat_opening_balance')<br>
        	            @else
        	                {{ucfirst(str_replace("_"," ",$one->type))}}<br>
        	            @endif
        	            
        	            {!! $details !!}
        	            {{ $one->transaction_note }}</td>
        	        <td>
        	            @if(in_array($one->type,['output_ob','input_ob']))
        	                @lang('vat::lang.vat_opening_balance')<br>
        	            @else
        	                {{ucfirst(str_replace("_"," ",$one->type))}}<br>
        	            @endif
        	        </td>
        	        <td></td>
        	        <td>
        	            @if(!empty($input_amount))
        	                {{@num_format($input_amount)}}
        	            @endif
        	       </td>
        	        <td>
        	            @if(!empty($output_amount))
        	                {{@num_format($output_amount)}}
        	            @endif
        	       </td>
        	        <td>{{@num_format($balance)}}</td>
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
    $("#vat_input").html("{{@num_format($input_tax)}}");
    $("#vat_output").html("{{@num_format($output_tax)}}");
    $("#vat_payment").html("{{@num_format($total_paid)}}");
    $("#vat_penalty").html("{{@num_format($total_penalty)}}");
    $("#vat_payable_balance").html("{{@num_format($balance)}}");
    $("#opening_balance").html("{{@num_format($opening_balance)}}");
</script>
