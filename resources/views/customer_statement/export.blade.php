@php
    use App\ReportConfiguration;
    $business_id = request()->session()->get('user.business_id');
    $customer_statement = ReportConfiguration::where('business_id',$business_id)->where('name','customer_statement_report')->first();
    $customer_statement_report = !empty($customer_statement) ? json_decode($customer_statement->configurations,true) : [];
    $colspan = 0;
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
        @php $total = 0; @endphp
        @foreach ($statement_details as $item)
            @php $total += $item->invoice_amount; @endphp
        <tr>
            @if(empty($customer_statement_report) || !empty($customer_statement_report['date']))
                <td>{{@format_date($item->date)}}</td>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['location']))
                <td>{{$item->location}}</td>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_no']))
                <td>{{$item->invoice_no}}</td>
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
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_amount']))
                <td>{{@num_format($item->invoice_amount)}}</td>
            @endif
            
            @if(empty($customer_statement_report) || !empty($customer_statement_report['due_amount']))
                <td>{{@num_format($item->due_amount)}}</td>
            @endif
        
        </tr>
        @endforeach
    </tbody>
     <tfoot>
        <tr>
            <th colspan="{{empty($customer_statement_report) ? 11 : $colspan-2}}">@lang('contact.total')</th>
            <th>{{@num_format($total)}}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
