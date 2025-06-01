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
	<p class="text-center"><strong>{{$pump_operator->business->name}}</strong><br>{{$location_details->city}},
		{{$location_details->state}}<br>{!!
		$location_details->mobile !!}</p>
	<hr>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class="bg_color" style="width: 40%">@lang('lang_v1.to'):</p>
	<p><strong>{{$pump_operator->name}}</strong><br> {!! $pump_operator->contact_address !!}
		@if(!empty($pump_operator->email))
		<br>@lang('business.email'): {{$pump_operator->email}} @endif
		<br>@lang('contact.mobile'): {{$pump_operator->mobile}}

	</p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.account_summary')</p>
	<hr>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('lang_v1.opening_balance')</td>
			<td><span id="total_opening_balance"></span></td>
		</tr>
		
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>{{@num_format($ledger_details['beginning_balance'])}}
			</td>
		</tr>

		<tr>
			<td><strong>@lang('petro::lang.total_short')</strong></td>
			<td><span id="total_short">0.00</span></td>
		</tr>
		<tr>
			<td><strong>@lang('petro::lang.total_excess')</strong></td>
			<td><span id="total_excess">0.00</span></td>
		</tr>
		<tr>
			<td><strong>@lang('petro::lang.total_commission')</strong></td>
			<td><span id="total_commission">0.00</span></td>
		</tr>
		
			<tr>
			<td><strong>@lang('petro::lang.shortage_recovered')</strong></td>
			<td><span id="shortage_recovered">0.00</span></td>
		</tr>
		<tr>
			<td><strong>@lang('petro::lang.excess_paid')</strong></td>
			<td><span id="excess_paid">0.00</span></td>
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
			$ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('purchase.ref_no')</th>
				<th>@lang('lang_v1.type')</th>
				<th>@lang('sale.location')</th>
				<th>@lang('account.debit')</th>
				<th>@lang('account.credit')</th>
				<th>@lang('lang_v1.balance')</th>
				<th>@lang('lang_v1.payment_method')</th>
			</tr>
		</thead>
		<tbody>
			@php
    			$balance = $ledger_details['beginning_balance'];
    			$total_short = 0;
    			$total_excess = 0;
    			$total_recovered = 0;
    			$total_commission = 0;
    			$total_opening_balance = 0;
    			$shortage_recovered = 0;
    			$excess_paid = 0;
			@endphp
			@foreach($ledger_transactions as $data)
			
			@if($data->amount > 0)
			
        			@if($data->type == 'shortage' || $data->type == 'excess_paid' || $data->type == 'commission')
        				@php $balance += $data->amount; @endphp
        			@endif
        			
        			@if($data->type == 'shortage_recovered' || $data->type == 'excess')
        				@php $balance -= $data->amount; @endphp
        			@endif
        			
        			@if($data->type == 'shortage')
        		        @php $total_short += $data->amount;  @endphp
        			@elseif($data->type == 'excess')
        			    @php $total_excess += $data->amount @endphp
        			@elseif($data->type == 'excess_paid')
        			    @php $excess_paid += $data->amount @endphp
        			@elseif($data->type == 'shortage_recovered')
        			    @php $total_recovered += $data->amount @endphp
        			@elseif($data->type == 'commission')
        			    @php $total_commission += $data->amount @endphp
        			@elseif($data->type == 'opening_balance')
        			    @php 
        			        if($data->sub_type == 'excess'){
        			            $total_opening_balance -= $data->amount;
        			            $balance -= $data->amount;
        			        }else{
        			            $total_opening_balance += $data->amount;
        			            $balance += $data->amount;
        			        }
        			        
        			    @endphp
        			@endif
        			
        			<tr>
        				<td class="row-border">{{@format_date($data->date)}}</td>
        				<td>
        					@if($data->type == 'shortage' || $data->type == 'excess' || $data->type == 'commission')
            					<b>@lang('petro::lang.settlement_no'):</b> {{$data->ref_no}}
        					@endif
        					
        					@if($data->type == 'shortage_recovered' || $data->type == 'excess_paid')
            					<b>@lang('petro::lang.payment_ref_no'):</b> {{$data->ref_no}}
        					@endif
        					
        					@if($data->type == 'opening_balance')
            					<b>@lang('lang_v1.opening_balance'):</b>
        					@endif
        					
        				</td>
        				<td>
        				    @if($data->type == 'shortage')
        				        @lang('petro::lang.shortage')
        					@elseif($data->type == 'excess')
        					    @lang('petro::lang.excess')
            				@elseif($data->type == 'excess_paid')
            				    @lang('petro::lang.excess_paid')
            				@elseif($data->type == 'shortage_recovered')
            				    @lang('petro::lang.shortage_recovered')
            				@elseif($data->type == 'commission')
            				    @lang('petro::lang.commission')
            				@elseif($data->type == 'opening_balance')
            				    @lang('lang_v1.opening_balance')
        					@endif
        				
        				</td>
        				<td>{{$data->location_name}}</td>
        				<td class="ws-nowrap">
        					@if($data->type == 'shortage' || $data->type == 'excess_paid' || $data->type == 'commission' || ($data->type == 'opening_balance' && $data->sub_type == 'shortage'))
        						{{@num_format($data->amount)}}
        					@endif
        				</td>
        				<td class="ws-nowrap">
        					@if($data->type == 'excess' || $data->type == 'shortage_recovered'|| ($data->type == 'opening_balance' && $data->sub_type == 'excess'))
        						{{@num_format($data->amount)}}
        					@endif</td>
        				<td class="ws-nowrap">
        					{{@num_format($balance)}}
        				</td>
        				<td>
        				    @if($data->type == 'shortage_recovered' || $data->type == 'excess_paid')
            					{{ucfirst($data->method)}}
        					@endif
        				    
        				</td>
        			</tr>
        		@endif
			@endforeach

		</tbody>
	</table>
</div>

<!-- This will be printed -->
<section class="invoice print_section" id="ledger_print">
</section>

<script>
    $("#total_short").text("{{@num_format($total_short)}}");
    $("#total_excess").text("{{@num_format($total_excess)}}");
    
    $("#excess_paid").text("{{@num_format($excess_paid)}}");
    $("#shortage_recovered").text("{{@num_format($total_recovered)}}");
    
    $("#total_commission").text("{{@num_format($total_commission)}}");
    $("#balance_due").text("{{@num_format($balance)}}");
    $("#total_opening_balance").text("{{@num_format($total_opening_balance)}}");
</script>