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
<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.account_summary')</p>

	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
	    <tr>
			<td>@lang('lang_v1.opening_balance')</td>
			<td><span id="total_ob">0.00</span></td>
		</tr>
		
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>{{@num_format($ledger_details['beginning_balance'])}}
			</td>
		</tr>

		<tr>
			<td><strong>@lang('shipping::lang.total_commission')</strong></td>
			<td><span id="total_commission">0.00</span></td>
		</tr>
		<tr>
			<td><strong>@lang('shipping::lang.total_payments')</strong></td>
			<td><span id="total_payments"></span></td>
		</tr>
		
		<tr>
			<td><strong>@lang('lang_v1.balance_due')</strong></td>
			<td><span id="balance_due"></span></td>
		</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
    <p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_table_heading',
			['start_date' =>
			$ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
</div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="rp_log_table">
        <thead>
            <tr>
                <th>@lang('shipping::lang.date')</th>
                <th>@lang('shipping::lang.description')</th>
                <th>@lang('shipping::lang.type')</th>
                <th>@lang('shipping::lang.debit')</th>
                <th>@lang('shipping::lang.credit')</th>
                <th>@lang('shipping::lang.balance')</th>
                <th>@lang('shipping::lang.payment_method')</th>
            </tr>
        </thead>
        <tbody>
            @php $balance = $ledger_details['beginning_balance']; $commission = 0; $payment = 0;$ob=0; @endphp
            @foreach($ledger_transactions as $one)
            @php 
                $parent=null;
                $recipient=null;
                $customer=null;
            @endphp
                @if($one->type == 'commission')
                    @php $balance += $one->amount; $commission += $one->amount; @endphp
                @elseif($one->type == 'partner_payment' || $one->type == 'shipping_partner_ob')
                    @php 
                        $balance -= $one->amount; 
                        
                        if($one->type == 'shipping_partner_ob'){
                            $ob += $one->amount;
                        }else{
                            $payment += $one->amount;
                        }
                    @endphp
                @endif
                
                @php
                    if(in_array($one->type,['commission','partner_payment'])&& !empty($one->parent_id)){
                        $parent = \Modules\Shipping\Entities\Shipment::find($one->parent_id);
                        $recipient = \Modules\Shipping\Entities\ShippingRecipient::find($parent->recipient_id);
                        $customer = \App\Contact::find($parent->customer_id);
                    }
                
                @endphp
                <tr>
                    <td>{{@format_date($one->date)}}</td>
                    <td>
                        @if(!empty($parent))
                            <b>@lang('shipping::lang.tracking_no')</b>: {{ $parent->tracking_no }}<br>
                            @if(!empty($customer))
                                <b>@lang('shipping::lang.customer')</b>: {{$customer->name }}<br>
                            @endif
                            @if(!empty($recipient))
                                <b>@lang('shipping::lang.recipient')</b>: {{$recipient->name }}<br>
                            @endif
                            @if(!empty($parent->delivery_time))
                                <b>@lang('shipping::lang.delivery_time')</b>: {{ @format_date($parent->delivery_time) }}<br>
                            @endif
                            
                        @endif
                    </td>
                    <td>
                        @if($one->type == 'commission')
                            {{__('shipping::lang.commission')}}
                        @elseif($one->type == 'partner_payment')
                            {{__('shipping::lang.shipping_partner_payment')}}
                        @elseif($one->type == 'shipping_partner_ob')
                            {{__('shipping::lang.opening_balance')}}
                        @endif
                    </td>
                    <td>
                        @if($one->type == 'partner_payment' || ($one->type == 'shipping_partner_ob' && $one->amount < 0))
                            {{@num_format(abs($one->amount))}}
                        @endif
                    </td>
                    <td>
                        @if($one->type == 'commission' || ($one->type == 'shipping_partner_ob' && $one->amount > 0))
                            {{@num_format(abs($one->amount))}}
                        @endif
                    </td>
                    <td>
                        {{ @num_format($balance) }}
                    </td>
                    <td>
                        @if($one->type == 'partner_payment')
                            <b>{{__('lang_v1.payment_method')}}</b>: {{ ucfirst($one->method) }} <br>
                            <b>{{__('purchase.ref_no')}}</b>: {{ $one->payment_ref_no }}
                            @if($one->method == 'cheque' || $one->method == 'bank' || $one->method == 'bank_transfer')
                                <br>
                                <b>{{__('lang_v1.cheque_date')}}</b>: {{ format_date($one->cheque_date) }} <br>
                                <b>{{__('lang_v1.cheque_number')}}</b>: {{ $one->cheque_number }}
                            @endif
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $("#total_ob").text("{{@num_format($ob)}}");
    $("#total_payments").text("{{@num_format($payment)}}");
    $("#total_commission").text("{{@num_format($commission)}}");
    $("#balance_due").text("{{@num_format($balance)}}");
</script>