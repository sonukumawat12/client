
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

<style>
.header-section {
  line-height: 20px !important;
}
@page { size: A4 potrait; margin: 5px; } body { margin: 5px; padding: 15px; }
@media print {
  

  .row {
    page-break-inside: avoid;
  }

  body {
    margin: 0;
    padding: 20;
  }
  
  
  
}
</style>

<!-- Your existing HTML content here -->


<!-- Your existing HTML content here -->

<div class="A5" style="width: 100% !important;">
    <table width="100%">
        <tr>
            <td>
                @if(!empty($parent_payment->contact))
                        <b>{{ __('customer.customer') }}:</b>
                        <br> {{ $parent_payment->contact->name }}
                        <br>
                    @endif
                    {{ __('business.address') }}:
                    <br>
                   
                        @if(!empty($parent_payment->contact))
                            @if($parent_payment->contact->landmark)
                                {{ $parent_payment->contact->landmark }},
                            @endif
                            {{ $parent_payment->contact->city }}
                            @if($parent_payment->contact->state)
                                {{ ',' . $parent_payment->contact->state }}
                                <br>
                            @endif
                            @if($parent_payment->contact->country)
                                {{ $parent_payment->contact->country }}
                                <br>
                            @endif
                            @if($parent_payment->contact->mobile)
                                {{__('contact.mobile')}}: {{ $parent_payment->contact->mobile }}
                            @endif
                            @if($parent_payment->contact->alternate_number)
                                <br> {{__('contact.alternate_contact_number')}}: {{ $parent_payment->contact->alternate_number }}
                            @endif
                            @if($parent_payment->contact->landline)
                                <br> {{__('contact.landline')}}: {{ $parent_payment->contact->landline }}
                            @endif
                        @endif
            </td>
            
            <td>
                <b>@lang('customer_payments.payment_ref_no')</b> : {{ $parent_payment->payment_ref_no }}
                    <br>
                    <b>@lang('messages.date'):</b> {{ @format_date($parent_payment->paid_on) }}
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <th colspan="3">
                @php
                        $final_total = $parent_payment->amount;
                        
                    @endphp
                    <b>@lang('sale.amount')</b> : {{ number_format($final_total,$company->currency_precision) }}
                    <br>
                    <b>@lang('sale.payment_method')</b> : @php $paid_in_types = ['customer_page' => 'Customer Page', 'all_sale_page' => 'All Sale Page', 'settlement' => 'Settlement']; @endphp
                    {{$parent_payment->method}}
                    <br>
                    @if($parent_payment->method == "cheque")
                        <b>@lang('sale.cheque_number')</b> : {{ $parent_payment->cheque_number }}
                        <br>
                        <b>@lang('sale.bank_name')</b> : {{ $parent_payment->bank_name }}
                        <br>
                        <b>@lang('sale.cheque_date')</b> : {{ $parent_payment->created_at->format('m/d/Y') }}
                        <br>
                    @endif
            </th>
        </tr>
        <tr>
            <th>
               @lang('sale.bill_no') 
            </th>
            <th>
                @lang('lang_v1.interest')
            </th>
            <th>
                @lang('sale.amount')
            </th>
        </tr>
        @foreach($child_payments as $payment_line)
            <tr>
                <td>
                    {{!empty($payment_line->invoice_no) ? $payment_line->invoice_no : $payment_line->payment_ref_no }}
                </td>
                <td>
                    {{number_format((0), $company->currency_precision)}}
                </td>
                <td>
                    {{number_format($payment_line->amount, $company->currency_precision)}}
                </td>
            </tr>
                        
        @endforeach
        <tr>
            <th colspan="3">
                <b>@lang('sale.payment_note')</b> : {{ $parent_payment->payment_note }}
            </th>
        </tr>
    </table>
        
</div>

