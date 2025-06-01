@php
    use App\ReportConfiguration;
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
                            <p class="bg_color" style="width: 40%; margin-top: 20px;">@lang('lang_v1.to'):</p>
                            <p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!}
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