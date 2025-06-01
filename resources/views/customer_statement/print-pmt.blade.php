@php
    use App\ReportConfiguration;
    use Carbon\Carbon;
    $business_id = request()->session()->get('user.business_id');
    $customer_statement = ReportConfiguration::where('business_id',$business_id)->where('name','customer_statement_report')->first();
    $customer_statement_report = !empty($customer_statement) ? json_decode($customer_statement->configurations,true) : [];
    $colspan = 0;
@endphp

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div id="report_div">
                <div id="print_header_div">
                    <style>
                        @media print {
                            #report_print_div {-webkit-print-color-adjust: exact;}
                        }
                        .bg_color {
                            background: #8F3A84 !important;
                            font-size: 20px;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }

                        .text-center {
                            text-align: center;
                        }

                        #customer_detail_table th {
                            background: #8F3A84 !important;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }
                        
                        #customer_statement_table th {
                            background: #8F3A84 !important;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }

                        #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
                        #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
                            background-color: #F3BDEB !important;
                            print-color-adjust: exact;
                        }
                        .uppercase {
                          text-transform: uppercase;
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
                                <td width="10%">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="75" width="75">
                                </td>
                                @endif
                                <td  class="text-center" width="90%">
                                    <p class="text-center uppercase">
                                        <strong>@lang('contact.customer_statement')<br>
                                        @if($logo->business_name == 1)
                                            {{$contact->business->name}}
                                        @endif
                                        </strong><br>
                                        @if($logo->business_address == 1)
                                            {{$location_details->city}},
                                            {{$location_details->state}}
                                        @endif
                                        <br>
                                        @if($logo->mobile_no == 1)
                                            {!! $location_details->mobile !!}
                                        @endif    
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center" colspan="2">
                                    <p class="text-center" style="color: #8F3A84 !important;print-color-adjust: exact;">
                                        <strong>@lang('contact.date_range_from') {{date('d M Y',strtotime($start_date))}} @lang('contact.to') {{date('d M Y',strtotime($end_date))}}</strong></p>
                                </td>
                            </tr>
                        </table>
                            
                        @elseif(!empty($logo) && $logo->alignment == "Right")
                            <table style="width: 100%">
                            <tr>
                                
                                <td  class="text-center" width="90%">
                                    <p class="text-center uppercase">
                                        <strong>@lang('contact.customer_statement')<<br>
                                        @if($logo->business_name == 1)
                                            {{$contact->business->name}}
                                        @endif
                                        </strong><br>
                                        @if($logo->business_address == 1)
                                            {{$location_details->city}},
                                            {{$location_details->state}}
                                        @endif
                                        <br>
                                        @if($logo->mobile_no == 1)
                                            {!! $location_details->mobile !!}
                                        @endif    
                                    </p>
                                </td>
                                
                                @if(!empty($logo) && !empty($logo->logo))
                                <td width="10%">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="75" width="75">
                                </td>
                                @endif
                            </tr>
                            <tr>
                                <td  class="text-center" colspan="2">
                                    <p class="text-center" style="color: #8F3A84 !important;print-color-adjust: exact;">
                                        <strong>@lang('contact.date_range_from') {{date('d M Y',strtotime($start_date))}} @lang('contact.to') {{date('d M Y',strtotime($end_date))}}</strong></p>
                                </td>
                            </tr>
                        </table>
                        @else
                            <table style="width: 100%">
                            @if(!empty($logo) && !empty($logo->logo))
                            <tr>
                                <td class="text-center" width="100%">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="75" width="75">
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td  class="text-center" width="100%">
                                    <p class="text-center uppercase">
                                        <strong>@lang('contact.customer_statement')<br>
                                        @if($logo->business_name == 1)
                                            {{$contact->business->name}}
                                        @endif
                                        </strong><br>
                                        @if($logo->business_address == 1)
                                            {{$location_details->city}},
                                            {{$location_details->state}}
                                        @endif
                                        <br>
                                        @if($logo->mobile_no == 1)
                                            {!! $location_details->mobile !!}
                                        @endif    
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center" colspan="2">
                                    <p class="text-center" style="color: #8F3A84 !important;print-color-adjust: exact;">
                                        <strong>@lang('contact.date_range_from') {{date('d M Y',strtotime($start_date))}} @lang('contact.to') {{date('d M Y',strtotime($end_date))}}</strong></p>
                                </td>
                            </tr>
                        </table>
                        @endif
                        
                        
                    
                    
                    <table style="width: 100%">
                        <tr>
                            
                            <td>
                                <div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif" style="float: left">
                                    <h4 class="modal-title" id="modalTitle"><b>@lang('lang_v1.invoice_no'):</b>
                                        {{ $statement->statement_no }}
                                    </h4>
                                    <p class="bg_color" style="width: 40%; margin-top: 5px;">Customer:</p>
                                    <p><strong>{{$contact->name}}</strong>
                                    <!-- <br>  -->
                                        @if($logo->contact_no == 1)
                                            {!! $contact->contact_address !!}
                                        @endif
                                        @if($logo->email == 1)
                                            @if(!empty($contact->email))
                                                <br>@lang('business.email'): {{$contact->email}} 
                                            @endif
                                        @endif
                                        
                                        @if($logo->mobile_no == 1)
                                            <br>@lang('contact.mobile'): {{$contact->mobile}}
                                        @endif
                                        
                                        @if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}}
                                        @endif
                                        <br>
                                        <strong>@lang('contact.printed_on'): </strong>{{date('d M Y H:m')}}
                                    </p>
                                </div>
                            </td>
                            
                            <td style="text-align: right; font-size: 20px; color: #FF0000 !important;print-color-adjust: exact;">
                                @if($reprint_no > 0) @lang('contact.copy') - {{$reprint_no}} @endif
                            </td>
                            
                        </tr>
                    </table>
                </div>
                
                <div class="row" style="margin-top: 0x;">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped" id="customer_statement_table">
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
                                            <td class="customer-reference">{{$item->customer_reference}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_po']))
                                            <td style="width: calc(55% / {{ $colspan }});">{{$item->order_no}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['voucher_date']))
                                            <td style="width: calc(50% / {{ $colspan }});">{{ \Carbon\Carbon::parse($item->order_date)->format('Y-m-d') }}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['product']))
                                            <td>{{$item->product}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['qty']))
                                            <td style="width: calc(110% / {{ $colspan }});">{{@format_quantity($item->qty)}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['unit_price']))
                                            <td>{{@num_format($item->unit_price)}}</td>
                                        @endif
                                    @else
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['date']))
                                            <td>{{@format_date($item->date)}}</td>
                                        @endif
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['location']))
                                            <td>{{$item->location}}</td>
                                        @endif
                                       <td colspan="{{($colspan - 4)}}">
                                        {{__('contact.payment')}} -
                                        @if(!empty($pmt_methods))
                			                {{ucfirst($pmt_methods->method)}}
                			                
                			                @if(strtolower($pmt_methods->method) == 'bank' || strtolower($pmt_methods->method) == 'bank_transfer' )
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
                                            @if( strtolower($pmt_methods->method) == 'cheque')
                			                    @if(!empty($pmt_methods->cheque_number))
                			                        {!! __('contact.cheque_number')." ".$pmt_methods->cheque_number."" !!}
                			                    @endif
                			                @endif
                			            @endif
                			            @if($item->type == 'customer_payment')
                			                {{__('contact.ref_no')." ".$item->invoice_no;}}
                			            @endif
                                        
                                       </td>
                                        
                                    @endif
                                    
                                    
                                    
                                    @if(empty($customer_statement_report) || !empty($customer_statement_report['invoice_amount']))
                                        <td style="width: calc(90% / {{ $colspan }});">{{@num_format($amount)}}</td>
                                    @endif
                                    
                                    @if(empty($customer_statement_report) || !empty($customer_statement_report['due_amount']))
                                        <td style="width: calc(90% / {{ $colspan }});">{{@num_format($due)}}</td>
                                    @endif
                                
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="{{empty($customer_statement_report) ? 11 : $colspan-2}}"></th>
                                    <th>@lang('contact.balance')</th>
                                    <th>{{@num_format($due)}}</th>
                                </tr>
                            </tbody>
                             {{-- <tfoot>
                                <tr>
                                    <th colspan="{{empty($customer_statement_report) ? 11 : $colspan-2}}"></th>
                                    <th>{{@num_format($total)}}</th>
                                    <th>{{@num_format($due)}}</th>
                                </tr>
                            </tfoot>
                            --}}
                        </table>
                    </div>
                </div>
                
                <hr>
                
                @if(!empty($logo) && !empty($logo->statement_note) && $logo->text_position == 'above')
                    <div class="col-xs-12 text-center">
                        <p>{{$logo->statement_note}}</p>
                    </div>
                @endif
                
                 <div class="row" style="height: 100px !important;"></div>
                
                <table width="100%">
                    <tr>
                        <th class="width-50">
                            <strong>@lang('contact.signature') :...............................................</strong>
                        </th>
                        <th  class="width-50">
                            @lang('contact.total'): {{@num_format($total)}}</strong>
                        </th>
                    </tr>
                </table>
                
                
                 <div class="row" style="height: 100px !important;"></div>
                
                @if(!empty($logo) && !empty($logo->statement_note) && $logo->text_position == 'below')
                    <div class="col-xs-12 text-center">
                        <p>{{$logo->statement_note}}</p>
                    </div>
                @endif
                
                
               
                
            </div>
        </div>
    </div>
    
    @php
        $reports_footer = \App\System::where('key','admin_reports_footer')->first();
    @endphp
    
    @if(!empty($reports_footer))
        <style>
            #footer {
                display: none;
                margin-top: 50px !important;
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

</section>