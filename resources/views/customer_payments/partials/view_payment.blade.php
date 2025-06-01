<div class="row">
                <div class="col-sm-6">
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
                    
                </div>
                <div class="col-sm-6">
                    <b>@lang('lang_v1.location')</b> : {{ $location }}
                    <br>
                    
                    <b>@lang('customer_payments.payment_ref_no')</b> : {{ $parent_payment->payment_ref_no }}
                    <br>
                    <b>@lang('messages.date'):</b> {{ @format_date($parent_payment->paid_on) }}
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
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
                    </br>
                        
                        <div class="row">
                            <div class="col-sm-4 text-right"><b>@lang('sale.bill_no')</b></div>
                            <div class="col-sm-4 text-right"><b>@lang('lang_v1.interest')</b></div>
                            <div class="col-sm-4 text-right"><b>@lang('sale.amount')</b></div>
                        </div>
                        
                        
                        @foreach($child_payments as $payment_line)
                            <div class="row">
                                <div class="col-sm-4 text-right">{{!empty($payment_line->invoice_no) ? $payment_line->invoice_no : $payment_line->payment_ref_no }}</div>
                                <div class="col-sm-4 text-right">{{number_format((0), $company->currency_precision)}}</div>
                                <div class="col-sm-4 text-right">{{number_format($payment_line->amount, $company->currency_precision)}}</div>
                            </div>
                            
                        @endforeach
                        
                        <div class="row">
                            <div class="col-sm-4 text-right text-danger">{{ $parent_payment->payment_ref_no }}</div>
                            <div class="col-sm-4 text-right text-danger">{{number_format((0), $company->currency_precision)}}</div>
                            <div class="col-sm-4 text-right text-danger">{{ number_format($final_total,$company->currency_precision) }}</div>
                        </div>
                        
                    
                </div>
                <div class="col-md-6">
                    <b>@lang('sale.payment_note')</b> : {{ $parent_payment->payment_note }}
                    <br>
                </div>
            </div>