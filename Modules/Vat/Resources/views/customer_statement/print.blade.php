<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div id="report_div">
                @php
                    $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
                    $report_name = \Modules\Vat\Entities\VatSetting::where('business_id',request()->session()->get('user.business_id'))->where('status',1)->first()->tax_report_name ?? 'vat';
                    
                    $invoice2_settings = \Modules\Vat\Entities\CustomerStatementFontSetting::where('business_id',request()->session()->get('user.business_id'))->first()->settings ?? json_encode(array());
                    $invoice2_settings = (object) json_decode($invoice2_settings);
                
                @endphp
                <style>
                
                  .row {
                    page-break-inside: avoid;
                  }
                
                  .zero-padding{
                      padding-bottom:0px !important;
                      padding-top: 0px important;
                  }
                  
                  .text-bold{
                      font-weight: bold;
                  }
                  
                  .bordered{
                      border: 1px solid black;
                  }
                  
                  .separator {
                        border: 1px dotted black;
                    }
                    
                    .pad-50{
                        padding: 20px;
                    }
                    
                    .border-none{
                        border-top: 1px solid white;
                        border-bottom: 1px solid white;
                        border-left: 1px solid white;
                    }
                    
                    .border-none-bottom{
                        border-top: 1px solid white;
                        border-left: 1px solid white;
                    }
                    
                    .border-bottom-only{
                        border-top: 1px solid white;
                        border-bottom: 1px solid black;
                        border-left: 1px solid white;
                    }
                    
                    .uppercase{
                        text-transform: uppercase;
                    }
                    
                    .text-center{
                        text-align: center;
                    }
                    
                    .header_size{
                        font-size: @if(!empty($invoice2_settings->header_size)) {{$invoice2_settings->header_size}}px !important; @else 18px !important; @endif
                    }
                    .company_size{
                        font-size: @if(!empty($invoice2_settings->company_size)) {{$invoice2_settings->company_size}}px !important; @else 18px !important; @endif
                    }
                    .address_size{
                        font-size: @if(!empty($invoice2_settings->address_size)) {{$invoice2_settings->address_size}}px !important; @else 18px !important; @endif
                    }
                    .left_header_size{
                        font-size: @if(!empty($invoice2_settings->left_header_size)) {{$invoice2_settings->left_header_size}}px !important; @else 18px !important; @endif
                    }
                    .right_header_size{
                        font-size: @if(!empty($invoice2_settings->right_header_size)) {{$invoice2_settings->right_header_size}}px !important; @else 18px !important; @endif
                    }
                    .system_footer_size{
                        font-size: @if(!empty($invoice2_settings->system_footer_size)) {{$invoice2_settings->system_footer_size}}px !important; @else 18px !important; @endif
                    }
                    
                    
                    
                    .thead_size{
                        font-size: @if(!empty($invoice2_settings->thead_size)) {{$invoice2_settings->thead_size}}px !important; @else 18px !important; @endif
                    }
                    .tbody_size{
                        font-size: @if(!empty($invoice2_settings->tbody_size)) {{$invoice2_settings->tbody_size}}px !important; @else 18px !important; @endif
                    }
                    .sub_size{
                        font-size: @if(!empty($invoice2_settings->sub_size)) {{$invoice2_settings->sub_size}}px !important; @else 18px !important; @endif
                    }
                    .footer_size{
                        font-size: @if(!empty($invoice2_settings->footer_size)) {{$invoice2_settings->footer_size}}px !important; @else 18px !important; @endif
                    } 
    
                </style>
                
                 @php
                    $currency_precision = !empty($business_details->currency_precision) ?
                    $business_details->currency_precision : 2;
                    @endphp
                    
                    @if(!empty($logo) && $logo->alignment == "Left")
                        <table style="width: 100%">
                            <tr>
                                @if(!empty($logo) && !empty($logo->logo))
                                <td width="10%" class="border-none">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="75" width="75">
                                </td>
                                @endif
                                <td  class="text-center border-none" width="90%">
                                    <p class="text-center uppercase">
                                        <strong style="text-transform: uppercase;">
                                        
                                        <span class="header_size">@lang('vat::lang.'.$report_name) @lang('vat::lang.statement')</span>
                                        @if($logo->business_name == 1)
                                            <br><span class="company_size">{{$contact->business->name}}</span><br>
                                        @endif
                                        </strong>
                                        
                                        <span class="address_size">
                                            @if($logo->business_address == 1)
                                                {{$location_details->city}},
                                                {{$location_details->state}}<br>
                                            @endif
                                            
                                            @if($logo->mobile_no == 1)
                                                {!! $location_details->mobile !!}
                                            @endif  
                                        </span>
                                    </p>
                                </td>
                            </tr>
                            
                        </table>
                            
                    @elseif(!empty($logo) && $logo->alignment == "Right")
                        <table style="width: 100%">
                        <tr>
                            
                            <td  class="text-center border-none" width="90%">
                                <p class="text-center uppercase">
                                    <strong style="text-transform: uppercase;">
                                        <span class="header_size">@lang('vat::lang.'.$report_name) @lang('vat::lang.statement')</span>
                                    @if($logo->business_name == 1)
                                        <span class="company_size"><br>{{$contact->business->name}}</span><br>
                                    @endif
                                    </strong>
                                    
                                    <span class="address_size">
                                    @if($logo->business_address == 1)
                                        {{$location_details->city}},
                                        {{$location_details->state}}<br>
                                    @endif
                                    
                                    @if($logo->mobile_no == 1)
                                        {!! $location_details->mobile !!}
                                    @endif  
                                    </span>
                                </p>
                            </td>
                            
                            @if(!empty($logo) && !empty($logo->logo))
                            <td width="10%" class="border-none">
                                <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                            		height="75" width="75">
                            </td>
                            @endif
                        </tr>
                        
                    </table>
                    @else
                        <table style="width: 100%" class="border-none">
                        @if(!empty($logo) && !empty($logo->logo))
                            <tr>
                                <td class="text-center border-none" width="100%">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="75" width="75">
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td  class="text-center border-none" width="100%">
                                <p class="text-center uppercase">
                                    <strong style="text-transform: uppercase;">
                                        <span class="header_size">@lang('vat::lang.'.$report_name) @lang('vat::lang.statement')</span>
                                    @if(!empty($logo) && $logo->business_name == 1)
                                        <span class="company_size"><br>{{$contact->business->name}}<br></span>
                                    @endif
                                    </strong>
                                    
                                    <span class="address_size">
                                    @if(!empty($logo) && $logo->business_address == 1)
                                        {{$location_details->city}},
                                        {{$location_details->state}}<br>
                                    @endif
                                    
                                    @if(!empty($logo) && $logo->mobile_no == 1)
                                        {!! $location_details->mobile !!}
                                    @endif    
                                    </span>
                                </p>
                            </td>
                        </tr>
                        
                    </table>
                    @endif
                    
                <table style="width: 100%" class="border-none">
                    <tr>
                        <td style="width: 34% !important;" class="border-none">
                            <h5 class="left_header_size">@lang('vat::lang.customer'): {{$contact->name}}</h5>
                            <h5 class="left_header_size">@lang('vat::lang.address'): {{$contact->address}}</h5>
                            <h5 class="left_header_size">@lang('vat::lang.your_vat'): {{$contact->vat_number}}</h5>
                        </td>
                        <td style="width: 25% !important;" class="border-none"></td>
                        <td style="width: 25% !important;" class="border-none">
                            <h5 class="right_header_size ">@lang('vat::lang.vat_registration_no'):</h5>
                            <h5 class="right_header_size uppercase">@lang('vat::lang.invoice') #:</h5>
                            <h5 class="right_header_size uppercase">@lang('vat::lang.invoice_date'):</h5>
                        </td>
                        <td style="width: 16% !important;" class="border-none">
                            <h5 class="right_header_size">{{ $business_details->tax_number_1 }}</h5>
                            <h5 class="right_header_size">{{$statement->statement_no}}</h5>
                            <h5 class="right_header_size">{{ @format_date($statement->print_date) }}</h5>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 34% !important;" class="border-none">
                            <h5 class="left_header_size">@lang('vat::lang.payment_method'): Credit</h5>
                        </td>
                        <td style="width: 25% !important;" class="border-none"></td>
                        <td style="width: 25% !important;" class="border-none"></td>
                        <td style="width: 16% !important;" class="border-none"></td>
                    </tr>
                    
                    <tr>
                        <td style="width: 34% !important;" class="border-none">
                            <h5 class="left_header_size">@lang('vat::lang.date_period'): {{@format_date($start_date)}} @lang('contact.to') {{@format_date($end_date)}}</h5>
                        </td>
                        <td style="width: 25% !important;" class="border-none"></td>
                        <td style="width: 25% !important;" class="border-none"></td>
                        <td style="width: 16% !important;" class="border-none"></td>
                    </tr>
                </table>
                
                <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border-top: 2px solid black;" >
                         <thead>
                                <tr>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('contact.date')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.po_no')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.our_reference')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.vehicle_no')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.qty')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 25% !important" colspan="2">@lang('vat::lang.product')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.unit_price')</th>
                                    <th class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 9.375% !important">@lang('vat::lang.amount')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; $i = 0;$pre_tax = 0;$tax_total = 0; @endphp
                                @foreach ($statement_details as $item)
                                    @php 
                                        $total += $item->invoice_amount;
                                        $i++; 
                                        //$pre_tax +=  ($item->qty*$item->unit_price_before_tax);
                                        //$tax_total += ($item->qty * ($item->unit_price - $item->unit_price_before_tax));
                                        
                                        $transaction = \App\Transaction::find($item->transaction_id);
                                        $vehicle = "";
                                        if(!empty($transaction)){
                                            $credit_sale = \Modules\Petro\Entities\SettlementCreditSalePayment::find($transaction->credit_sale_id);
                                            if(!empty($credit_sale)){
                                                $vehicle = $credit_sale->customer_reference;
                                            }
                                        }
                                        
                                    @endphp
                                <tr>
                                    <td class="tbody_size text-left bordered zero-padding">{{@format_date(!empty($item->tdate)? $item->tdate : $item->date)}}</td>
                                    <td class="tbody_size text-center bordered zero-padding">{{$item->order_no}}</td>
                                    <td class="tbody_size text-center bordered zero-padding">{{$item->invoice_no}}</td>
                                    <td class="tbody_size text-left bordered zero-padding">{{!empty($item->vehicle_number) ? $item->vehicle_number : $vehicle}}</td>
                                    <td class="tbody_size text-center bordered zero-padding">{{@format_quantity($item->qty)}}</td>
                                    <td class="tbody_size bordered zero-padding" style=" word-break: break-all;" colspan="2">{{$item->product}}</td>
                                    <td class="tbody_size text-right bordered zero-padding">{{@num_format($item->unit_price)}}</td>
                                    <td class="tbody_size text-right bordered zero-padding">{{@num_format($item->invoice_amount)}}</td>
                                </tr>
                                @endforeach
                                
                                @php
                                    $tax_rate = \App\TaxRate::where('business_id',request()->session()->get('business.id'))->first()->amount ?? 0;
                                    $pre_tax = $total / (1+ ($tax_rate/100));
                                    $tax_total = ($tax_rate/100) * $pre_tax;
                                    $grand_total = $tax_total + $pre_tax;
                                 @endphp
                                <tr>
                                    <td class="border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="border-bottom-only" colspan="3" style="">&nbsp;</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-right zero-padding border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($total)}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-right zero-padding border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.tax_base_value')</td>
                                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($pre_tax)}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-right zero-padding border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.vat') {{$tax_rate}}%</td>
                                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($tax_total)}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-right zero-padding border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.price_adjustment')</td>
                                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($statement->price_adjustment)}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-right zero-padding border-none" colspan="6" style="">&nbsp;</td>
                                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($grand_total+$statement->price_adjustment)}}</td>
                                </tr>
                            </tbody>
                             
                        </table>
                
               
                <table style="width: 100%" class="border-none">
                    @if(!empty($logo) && !empty($logo->statement_note) && $logo->text_position == 'above')
                        <tr>
                            <td colspan="3" class="text-center border-none">
                                <p class="system_footer_size">{{$logo->statement_note}}</p>
                            </td>
                        </tr>
                    @endif
                    
                    <tr>
                       <td class="text-center border-none  pad-50" style="width: 33.33% !important">
                            <hr class="separator">
                		    <h5 class="footer_size">@lang('vat::lang.prepared_by')</h5>
                        </td>
                        
                        <td class="text-center border-none  pad-50" style="width: 33.33% !important">
                            <hr class="separator">
                		    <h5 class="footer_size">@lang('vat::lang.checked_by')</h5>
                        </td>
                        
                        <td class="text-center border-none  pad-50" style="width: 33.33% !important">
                            <hr class="separator">
                		    <h5 class="footer_size">@lang('vat::lang.customer_signature')</h5>
                        </td>
                    </tr>
                    
                    @if(!empty($logo) && !empty($logo->statement_note) && $logo->text_position == 'below')
                        <tr>
                            <td colspan="3" class="text-center border-none">
                                <p class="system_footer_size">{{$logo->statement_note}}</p>
                            </td>
                        </tr>
                    @endif
                   
                </table>
                
            </div>
        </div>
    </div>
   

</section>