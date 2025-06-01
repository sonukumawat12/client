@php
    use App\ReportConfiguration;
    $business_id = request()->session()->get('user.business_id');
    $customer_statement = ReportConfiguration::where('business_id',$business_id)->where('name','customer_statement_report')->first();
    $customer_statement_report = !empty($customer_statement) ? json_decode($customer_statement->configurations,true) : [];
    $colspan = 0;
@endphp

@php
$currency_precision = !empty($business_details->currency_precision) ?
$business_details->currency_precision : 2;
@endphp

<table>
    <tr>
        
        <td>
            {{$contact->name}}
        </td>
        
        
    </tr>
</table>
<table>
    <thead>
        <tr>
            @if(empty($customer_statement_report) || !empty($customer_statement_report['date']))
                @php $colspan++ @endphp
                <th>@lang('contact.date')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['location']))
                @php $colspan++ @endphp
                <th>@lang('contact.location')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_no']))
                @php $colspan++ @endphp
                <th>@lang('contact.invoice_no')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['route']))
                @php $colspan++ @endphp
                <th>@lang('contact.route')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['vehicle']))
                @php $colspan++ @endphp
                <th>@lang('contact.vehicle')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_reference']))
                @php $colspan++ @endphp
                <th>@lang('contact.customer_reference')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_po']))
                @php $colspan++ @endphp
                <th>@lang('lang_v1.customer_po_no')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['voucher_date']))
                @php $colspan++ @endphp
                <th>@lang('contact.voucher_order_date')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['product']))
                @php $colspan++ @endphp
                <th>@lang('contact.product')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['qty']))
                @php $colspan++ @endphp
                <th>@lang('contact.qty')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['unit_price']))
                @php $colspan++ @endphp
                <th>@lang('contact.unit_price')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_amount']))
                @php $colspan++ @endphp
                <th>@lang('contact.invoice_amount')</th>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['due_amount']))
                @php $colspan++ @endphp
                <th>@lang('contact.due_amount')</th>
            @endif
            

        </tr>
    </thead>

     <tbody>
        <tr>
            <td colspan="{{($colspan - 1)}}">@lang('lang_v1.beginning_balance')</td>
            <td>{{@num_format($ledger_details['beginning_balance'])}}
            </td>
        </tr>
        
        @php
            use Modules\Petro\Entities\CustomerPayment;
            use App\TransactionPayment;
            use App\Transaction;
        @endphp
        @php $total = 0;$due = $ledger_details['beginning_balance']; @endphp
        @foreach ($statement_details as $item)
            @php 
                if($item->type == 'payment' || $item->type == 'customer_payment'){
                    $amount = -$item->invoice_amount;
                    
                    if($item->type == 'payment'){
                        $pmt_methods = TransactionPayment::leftjoin('accounts','transaction_payments.account_id','accounts.id')->where('transaction_payments.id',$item->statement_id)->select(['transaction_payments.*','accounts.name as account_name'])->first();
                    }
                }else{
                    $amount = $item->invoice_amount;
                }
                
                $total += $amount; 
                $due += $amount;
            @endphp
        <tr>
            @if($item->type == 'transaction')
                @if(empty($customer_statement_report) || !empty($customer_statement_report['date']))
                    <td>{{@format_date($item->date)}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['location']))
                    <td>{{$item->location}}</td>
                @endif
                
                
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_no']))
                    <td >{{$item->invoice_no}}</td>
                @endif
            
            
                @if(empty($customer_statement_report) || !empty($customer_statement_report['route']))
                    <td>{{$item->route_name}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['vehicle']))
                    <td>{{$item->vehicle_number}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_reference']))
                    <td>{{$item->customer_reference}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_po']))
                    <td>{{$item->order_no}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['voucher_date']))
                    <td>{{$item->order_date}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['product']))
                    <td>{{$item->product}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['qty']))
                    <td>{{@format_quantity($item->qty)}}</td>
                @endif
                
                @if(empty($customer_statement_report) || !empty($customer_statement_report['unit_price']))
                    <td>{{@num_format($item->unit_price)}}</td>
                @endif
            @else
                @if(empty($customer_statement_report) || !empty($customer_statement_report['date']))
                    <td>{{@format_date($item->date)}}</td>
                @endif
                
               <td colspan="{{($colspan - 3)}}">
                {{__('contact.payment')}}<br>
                @if(!empty($pmt_methods))
	                {{ucfirst($pmt_methods->method)}}
	                
	                @if(strtolower($pmt_methods->method) == 'bank' || strtolower($pmt_methods->method) == 'bank_transfer' || strtolower($pmt_methods->method) == 'cheque')
	                    <br>
	                    @if(!empty($pmt_methods->account_name))
	                        {!! __('contact.bank_name')." ".$pmt_methods->account_name."<br>" !!}
	                    @endif
	                    
	                    @if(!empty($pmt_methods->cheque_number))
	                        {!! __('contact.cheque_number')." ".$pmt_methods->cheque_number."<br>" !!}
	                    @endif
	                    
	                    @if(!empty($pmt_methods->cheque_date))
	                        {!! __('contact.cheque_date')." ".@format_date($pmt_methods->cheque_date."<br>") !!}
	                    @endif
	                    
	                @endif
	            @endif
	            @if($item->type == 'customer_payment')
	                {{__('contact.ref_no')." ".$item->invoice_no;}}
	            @endif
                
               </td>
                
            @endif
            
            
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_amount']))
                <td>{{@num_format($amount)}}</td>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['due_amount']))
                <td>{{@num_format($due)}}</td>
            @endif
        
        </tr>
        @endforeach
    </tbody>
     
</table>