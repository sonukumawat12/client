@php
    use App\ReportConfiguration;
    use Carbon\Carbon;
    $business_id = request()->session()->get('user.business_id');
    $customer_statement = ReportConfiguration::where('business_id',$business_id)->where('name','customer_statement_report')->first();
    $customer_statement_report = !empty($customer_statement) ? json_decode($customer_statement->configurations,true) : [];
    $colspan = 0;
    $paid_customer_statement = \App\TransactionPayment::where('linked_customer_statement',$id)->count();
    
    $pacakge_details = [];
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }
    
@endphp

<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                    <style>
                        @media print {

                            .dt-buttons,
                            .dataTables_length,
                            .dataTables_filter,
                            .dataTables_info,
                            .dataTables_paginate {
                                display: none;
                            }

                            .customer_details_div {
                                display: none;
                            }
                        }
                    </style>
                    <div class="col-md-12">
                        <style>
                            .bg_color {
                                background: #357ca5;
                                font-size: 20px;
                                color: #fff;
                            }

                            .text-center {
                                text-align: center;
                            }

                            #customer_detail_table th {
                                background: #357ca5;
                                color: #fff;
                            }

                            #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
                            #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
                                background-color: rgba(89, 129, 255, 0.3);
                            }
                        </style>
                        
                        @if($paid_customer_statement > 0)
                            @if(auth()->user()->can('contact.delete_customer_statement') && (!empty($pacakge_details['contact.delete_customer_statement']) || !array_key_exists('contact.delete_customer_statement',$pacakge_details)))
                                <a data-href="{{action('CustomerStatementController@destroyPayments', [$id])}}" class="delete_customer_statement btn btn-danger pull-right"><i class="fa fa-trash"></i>{{ __("lang_v1.delete_payments") }}</a>
                            @endif
                        @endif

                        @php
                        $currency_precision = !empty($business_details->currency_precision) ?
                        $business_details->currency_precision : 2;
                        @endphp
                        
                        @if(!empty($logo) && $logo->alignment == "Left")
                            <div class="row">
                                @if(!empty($logo) && !empty($logo->logo))
                                <div class="col-md-1">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="100" width="100">
                                </div>
                                @endif
                                <div class="col-md-11 col-sm-11 @if(!empty($for_pdf)) text-center @endif">
                                    <p class="text-center">
                                        <strong>{{$contact->business->name}}</strong><br>{{$location_details->city}},
                                        {{$location_details->state}}<br>{!!
                                        $location_details->mobile !!}</p>
                                    <hr>
                                </div>
                            </div>
                        @elseif(!empty($logo) && $logo->alignment == "Right")
                            <div class="row">
                                
                                <div class="col-md-11 col-sm-11 @if(!empty($for_pdf)) text-center @endif">
                                    <p class="text-center">
                                        <strong>{{$contact->business->name}}</strong><br>{{$location_details->city}},
                                        {{$location_details->state}}<br>{!!
                                        $location_details->mobile !!}</p>
                                    <hr>
                                </div>
                                @if(!empty($logo) && !empty($logo->logo))
                                <div class="col-md-1">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="100" width="100">
                                </div>
                                @endif
                            </div>
                        @else
                        
                            <div class="row">
                               
                               @if(!empty($logo) && !empty($logo->logo)) 
                                <div class="col-md-12">
                                    <img src="{{url($logo->logo)}}" class="img img-responsive center-block"
                                		height="100" width="100">
                                </div>
                                @endif
                                
                                <div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) text-center @endif">
                                    <p class="text-center">
                                        <strong>{{$contact->business->name}}</strong><br>{{$location_details->city}},
                                        {{$location_details->state}}<br>{!!
                                        $location_details->mobile !!}</p>
                                    <hr>
                                </div>
                                
                            </div>
                        
                        @endif
                        
                        
                        <div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
                            <h4 class="modal-title" id="modalTitle"><b>@lang('lang_v1.invoice_no'):</b>
                                {{ $statement->statement_no }}
                            </h4>
                            <p class="bg_color" style="width: 40%; margin-top: 5px;">@lang('lang_v1.to'):</p>
                            <p><strong>{{$contact->name}}</strong>
                            @if(!empty($contact->contact_address))
                            <br> {!! $contact->contact_address !!}
                            @endif
                                @if(!empty($contact->email))
                                <br>@lang('business.email'): {{$contact->email}} @endif
                                <br>@lang('contact.mobile'): {{$contact->mobile}}
                                @if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}}
                                @endif
                            </p>
                        </div>
                       
                    </div>

                    <div class="row" style="margin-top: 20px;">
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
                                    
                                    @if(!empty($customer_statement_report['route']))
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
                                    
                                    
                                        @if( !empty($customer_statement_report['route']))
                                            <td>{{$item->route_name}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['vehicle']))
                                            <td>{{$item->vehicle_number}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_reference']))
                                            <td style="width: calc(75% / {{ $colspan }});">{{$item->customer_reference}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['customer_po']))
                                            <td style="width: calc(75% / {{ $colspan }});">{{$item->order_no}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['voucher_date']))
                                            <td style="width: calc(75% / {{ $colspan }});">{{ \Carbon\Carbon::parse($item->order_date)->format('Y-m-d') }}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['product']))
                                            <td>{{$item->product}}</td>
                                        @endif
                                        
                                        @if(empty($customer_statement_report) || !empty($customer_statement_report['qty']))
                                            <td style="width: calc(120% / {{ $colspan }});">{{@format_quantity($item->qty)}}</td>
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
                			                
                			                @if(strtolower($pmt_methods->method) == 'bank' || strtolower($pmt_methods->method) == 'bank_transfer')
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
                                    <th colspan="{{empty($customer_statement_report) ? 11 : $colspan-2}}">@lang('contact.total')</th>
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
                    
                    <table width="100%" style="margin-top: 30px; ">
                        <tr>
                            <th class="width-50">
                                <strong>@lang('contact.signature') :...............................................</strong>
                            </th>
                            <th  class="width-50">
                                @lang('contact.total'): {{@num_format($total)}}</strong>
                            </th>
                        </tr>
                    </table>
                    
                    @if(!empty($logo) && !empty($logo->statement_note) && $logo->text_position == 'below')
                        <div class="col-xs-12 text-center">
                            <p>{{$logo->statement_note}}</p>
                        </div>
                    @endif
                

                </div>
            </div>
        </div>
    </div>
</div>