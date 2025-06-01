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

@if(in_array($contact_type,['supplier','']))
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.supplier_summary')</p>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>
			    <span id="supplier_bf"></span>
			</td>
		</tr>
	
		<tr>
			<td>@lang('lang_v1.total_paid')</td>
			<td>
			    <span id="supplier_total_in"></span>
			</td>
		</tr>
		
		
		<tr>
			<td>@lang('lang_v1.total_credit_purchase')</td>
			<td>
			    <span id="supplier_total_out"></span>
			</td>
		</tr>
	
		<tr>
			<td><strong>@lang('lang_v1.balance')</strong></td>
			<td><span id="supplier_balance">0.00</span></td>
		</tr>
	</table>
</div>
@endif

@if(in_array($contact_type,['customer','']))
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.customer_summary')</p>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>
			    <span id="customer_bf"></span>
			</td>
		</tr>
	
		<tr>
			<td>@lang('lang_v1.total_received')</td>
			<td>
			    <span id="customer_total_in"></span>
			</td>
		</tr>
		
		
		<tr>
			<td>@lang('lang_v1.total_credit_sale')</td>
			<td>
			    <span id="customer_total_out"></span>
			</td>
		</tr>
	
		<tr>
			<td><strong>@lang('lang_v1.balance')</strong></td>
			<td><span id="customer_balance">0.00</span></td>
		</tr>
	</table>
</div>
@endif
<hr>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_summary_heading',
			['start_date' =>
			$start_date, 'end_date' => $end_date])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('lang_v1.contact_type')</th>
				<th>@lang('lang_v1.contact')</th>
				<th>@lang('lang_v1.beginning_balance')</th>
				<th>@lang('account.debit')</th>
				<th>@lang('account.credit')</th>
				<th>@lang('lang_v1.balance')</th>
			</tr>
		</thead>
		<tbody>
		   @php
		   
		    $customer_total_in = 0;
		    $supplier_total_in = 0;
            $customer_total_out = 0;
            $supplier_total_out = 0;
            $customer_balance = 0;
            $supplier_balance = 0;
            $supplier_bf = 0;
            $customer_bf = 0;
		   
		   @endphp
			@foreach($ledger_details as $one)
			
			@php
			    if($one['contact_type'] == 'customer'){
			        $customer_total_in += $one['total_in'];
        		    $customer_total_out += $one['total_out'];
                    $customer_balance += $one['balance'];
                    $customer_bf += $one['bf_balance'];
			    }
			    
			    if($one['contact_type'] == 'supplier'){
			        $supplier_total_in += $one['total_in'];
        		    $supplier_total_out += $one['total_out'];
                    $supplier_balance += $one['balance'];
                    $supplier_bf += $one['bf_balance'];
			    }
			@endphp
		        <tr>
		            <td>{{@format_date($one['date']) }}</td>
		            <td>{{ ucfirst($one['contact_type']) }}</td>
		            <td>{{ $one['contact_name'] }}</td>
		            <td>{{ @num_format($one['bf_balance']) }}</td>
		            <td>{{ @num_format($one['total_in']) }}</td>
		            <td>{{ @num_format($one['total_out']) }}</td>
		            <td>{{ @num_format($one['balance']) }}</td>
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
    $("#customer_total_in").html("{{@num_format($customer_total_in)}}");
    $("#supplier_total_in").html("{{@num_format($supplier_total_in)}}");
    $("#customer_total_out").html("{{@num_format($customer_total_out)}}");
    $("#supplier_total_out").html("{{@num_format($supplier_total_out)}}");
    $("#customer_balance").html("{{@num_format($customer_balance)}}");
    $("#supplier_balance").html("{{@num_format($supplier_balance)}}");
    $("#supplier_bf").html("{{@num_format($supplier_bf)}}");
    $("#customer_bf").html("{{@num_format($customer_bf)}}");
</script>
