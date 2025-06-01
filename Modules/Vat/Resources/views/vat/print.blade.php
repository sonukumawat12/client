
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div id="report_div">
                <div id="print_header_div">
                    <style>
                        @media print {
                            #report_print_div {-webkit-print-color-adjust: exact;}
                        }
                        
                        .text-center {
                            text-align: center;
                        }

                        
                        .uppercase {
                          text-transform: uppercase;
                        }
                    </style>
                    @php
                    $currency_precision = !empty($business_details->currency_precision) ?
                    $business_details->currency_precision : 2;
                    @endphp
                    
                   
                    <table style="width: 100%">
                        
                        <tr>
                            <td  class="text-center" width="100%">
                                <p class="text-center uppercase">
                                    <strong>@lang( 'report.tax_report' )<br>
                                        {{$location_details->name}}
                                    </strong><br>
                                        {{$location_details->city}},
                                        {{$location_details->state}}
                                    <br>
                                    {!! $location_details->mobile !!}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td  class="text-center" colspan="2">
                                <p class="text-center">
                                    <strong>@lang('contact.date_range_from') {{date('d M Y',strtotime($start_date))}} @lang('contact.to') {{date('d M Y',strtotime($end_date))}}</strong></p>
                            </td>
                        </tr>
                    </table>
                       
                    
                    <table style="width: 100%">
                        <tr>
                            
                            <td>
                                <strong>{{ __('report.input_tax') }}</strong> <span>{{@num_format($input_tax)}}</span>
                            </td>
                            
                            <td>
                                <strong>{{ __('report.output_tax') }}</strong> <span>{{@num_format($output_tax)}}</span>
                            </td>
                            
                            <td>
                                <strong>{{ __('lang_v1.expense_tax') }}</strong> <span>{{@num_format($expense_tax)}}</span>
                            </td>
                            
                        </tr>
                    </table>
                </div>
                
                <div class="row" style="margin-top: 0x;">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped" id="customer_statement_table">
                            <thead>
                                <tr>
                                    <th>@lang('vat::lang.date')</th>
                                    <th>@lang('vat::lang.reference_type')</th>
                                    <th>@lang('vat::lang.reference_no')</th>
                                    <th>@lang('vat::lang.contact')</th>
                                    <th>@lang('vat::lang.amount')</th>
                                    <th>@lang('vat::lang.vat_amount')</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @if(!empty($expenses))
                                    @php $total = 0; $total_tax = 0; @endphp
                                    @foreach($expenses as $one)
                                        @php 
                                            $total += $one->final_total;
                                            $total_tax += $one->tax_amount;
                                        @endphp
                                        <tr>
                                            <td>{{ @format_date($one->transaction_date) }}</td>
                                            <td>{{ $one->type }}</td>
                                            <td>{{ $one->invoice_no }}</td>
                                            <td>{{ $one->contact_name }}</td>
                                            <td>{{ @num_format($one->final_total) }}</td>
                                            <td>{{ @num_format($one->tax_amount) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>

                             <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td>
                                        <span class="display_currency" id="footer_total_amount" data-currency_symbol="true">{{@num_format($total)}}</span>
                                    </td>
                                    <td>
                                        <span class="display_currency" id="footer_vat_total"
                                            data-currency_symbol="true">{{@num_format($total_tax)}}</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                    
                        </table>
                    </div>
                </div>
                
                
               
                
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