
@php 
    $url_credit = action('AccountController@show', [$allaccounts['credit'],'is_iframe' => 1]);
    $url_cash = action('AccountController@show', [$allaccounts['cash'],'is_iframe' => 1]);
    $url_cheque = action('AccountController@show', [$allaccounts['cheque'],'is_iframe' => 1]);
    
    $url_payable = action('AccountController@show', [$allaccounts['payable'],'is_iframe' => 1]);
    
    
@endphp

<style>
    #daily_report_header_table{
        display: none;
    }
    @media print {
        .footer-space {
          height: 100px;
        }
        .header-space {
            height: 200px;
        }
        .page-header {
            max-width: 750px;
        }
        #daily_report_header_table{
            display: block !important;
            width:100% !important;
            text-align: center !important;
        }
        
        .width-50 {
            width: 50% !important; /* Ensure each column takes 50% width */
            float: left; /* Float left to ensure side-by-side display */
        }
        .f-left {
            float: left; /* Float left for the first column */
        }
        
        .background-box{
            background: #800080;
            color: #fff;
        }

    }
    .allchecked{
        padding: 5px;
        cursor: pointer;
        margin: 20px;
    }
    .okbutton{
        width: 75px;
        padding: 5px;
        color:#fff;
        margin-top: 5px;
        cursor: pointer;
    }
    
    .unOkbutton{
        width: 75px;
        padding: 5px;
        color:#fff;
        margin-top: 5px;
        cursor: pointer;
    }
    
    .bg-danger{
        background-color:#F90707 !important ;
    }
    
    .bg-success {
        background-color:#1D6103 !important ;
    }
</style>

<!-- header on every print-page -@eng (14/2) -->
<table class="table table-bordered table-striped page-header" id="daily_report_table" style="margin-top: 0px; important;"> 
    <tbody>
       
    
        @if ($day_diff == 0)
            <tr>
                <td class="text-right" style="padding: 0px !important;">@lang('report.shift'): {{ $work_shift }}</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- footer on every print page -@eng (14/2) -->
<div class="page-footer">
    <small>
    	{{ config('app.name', 'ultimatePOS') }} - V{{config('author.app_version')}} | @lang('report.copyright') &copy; {{ date('Y') }} @lang('report.all_rights_reserved').
    </small>   <span class="page-number"></span>             
</div>

<table> <!-- Page Table: for control over print layout -@eng (14/2) -->
    <tbody>
        <tr>
            <td>
                <section class="content">
                
                    <div id="daily_report_div">
                        
                        <div id="daily_report_header_table">
                            @if (!empty($location_details))
                                <h4>{{ $location_details->name }} <br>
                        
                                        {{ $location_details->city }}
                        
                                    </h4>
                            @else
                                <h4>{{ strtoupper(request()->session()->get('business.name')) }}</h4>
                                <h4>@lang('report.date_range'): @lang('report.from') {{ $print_s_date }}
                                            @lang('report.to') {{ $print_e_date }}</h4>
                                
                            @endif
                        </div>
                        
                
                        <div class="table-responsive default">
                
                            <div class="page-body">
                                @if(!empty($reviewed))
                                   <button class="btn btn-success no-print pull-right reviewButton">@lang('report.reviewed')</button>
                                @else 
                                   <button class="btn btn-danger no-print pull-right reviewButton">@lang('report.review')</button>
                                @endif
                                
                                
                                <table class="table table-bordered table-striped @if(empty($activate_sales_section)) no-print @endif" id="sale_table">
                
                                    <thead>
                
                                    <tr>
                
                                        <th class="text-left"><span
                                                    style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;" class="background-box">@lang('report.sale')</span>
                                        </th>
                                        <th colspan="2" class="text-left">
                                            @if ($petro_module)
                                                <span
                                                    style="background: #09D0F4; padding: 5px 10px 5px 10px; color: #fff;"
                                                     data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('sales_details')"
                                                    >@lang('report.meter_sales')</span>
                                            @endif
                                        </th>
                                        <th colspan="2" class="text-left"><span
                                                    style="background: #E382D6; padding: 5px 10px 5px 10px; color: #fff;"
                                                     data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('items_sold')"
                                                    >@lang('report.items_sold')</span>
                                        </th>
                
                                    </tr>
                
                                    <tr>
                
                                        <th>@lang('report.product')</th>
                
                                        <th>@lang('report.qty')</th>
                
                                        <th>@lang('report.discount_given')</th>
                
                
                
                                        <th colspan="2">@lang('report.amount')</th>
                
                                    </tr>
                
                                    </thead>
                
                                    <tbody style="background-color: #FFF0D9">
                
                                    @php
                
                                        $total_sales_amount = 0;
                
                                    @endphp
                
                                    <!-- regular sales -->
                                    <!--modified by iftekhar-->
                                    @foreach ($sales ?? [] as $sale)
                                        <tr>
                
                                            <td>{{ $sale->category_name }}</td>
                
                                            <td class="text-right">{{ @format_quantity($sale->qty) }}</td>
                
                                            <td class="text-right">{{ @num_format($sale->dicount_given) }}</td>
                
                                            <td colspan="2" class="text-right">{{ @num_format($sale->total_amount) }}</td>
                
                                        </tr>
                
                                        @php
                
                                            $total_sales_amount += $sale->total_amount;
                
                                        @endphp
                                    @endforeach
                                    <!-- petro sales -->
                                    <!--modified by iftekhar-->
                                    @foreach ($petro_sales ?? [] as $petro_sale) 
                                        <tr>
                
                                            <td>{{ $petro_sale->sub_category_name }}</td>
                
                                            <td class="text-right">{{ @format_quantity($petro_sale->qty) }}</td>
                
                                            <td class="text-right">{{ @num_format($petro_sale->dicount_given) }}</td>
                
                
                
                                            <td colspan="2" class="text-right">{{ @num_format($petro_sale->total_amount) }}</td>
                
                                        </tr>
                
                                        @php
                
                                            $total_sales_amount += $petro_sale->total_amount;
                
                                        @endphp
                                    @endforeach
                                
                
                                    <!-- Modified By iftekhar -->
                                    <!-- line : 142 to 156 for showing total discount amount -->
                                       
                
                                    <tr>
                
                                        <th colspan="3">@lang('report.total_discount')</th>
                
                
                
                                        <th class="text-right">{{ $total_discount ? '-' . @num_format($total_discount) : 0  }}</th>
                
                                    </tr>
                
                                    <tr>
                
                                        <th colspan="2">@lang('report.total_sale_amount')
                
                                        <td class="text-right">{{ @num_format($dicount_given) }}</td>
                
                
                
                                        <th class="text-right">{{ @num_format($total_sales_amount) }}</th>
                
                                    </tr>
                
                
                
                                    </tbody>
                
                                </table>
                                
                                <div class="row">
                                    
                                    <!-- @eng START 12/2 --> <!-- @eng START 14/2 -->
                                        <style>
                                            #sales_by_cashier_op_table th, td {
                                                /* padding: 5px; */ /*@eng 14/2 */
                                                /* padding: 8px;*/ /*@eng 14/2 */
                                                word-wrap: break-word !important;
                                                line-height: 16px !important;
                                                max-width: fit-content !important;
                                            }
                                        </style>
                                        <!-- @eng END 12/2 --><!-- @eng END 14/2 -->
                                        <!-- @eng test start --><!-- @eng START 14/2 -->
                                        <div class="col-md-12 @if(empty($activate_sales_by_cashier)) no-print @endif">
                                            <!-- @eng START 12/2 -->
                                            <div style="margin-bottom: 5px; margin-top: 5px;">
                                                <span style="font-weight:bold; background: #800080; padding: 5px 10px 5px 10px; color: #fff;">
                                                    @lang('report.sales_by_cashier_operator')
                                                </span>
                                            </div>
                                            <!-- @eng END 12/2 -->
                                            <table id="sales_by_cashier_op_table" style="font-size:12px; background-color: #D9E1F2" class="table table-bordered table-striped"> <!-- @eng 12/2 -->
                                                <thead>
                                                    
                                                    <!--<th>--> <!--@eng START 12/2-->
                                                         <!--<span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Sales by Cashier / Operator </span>-->
                                                    <!--</th>--><!-- @eng END 12/2 -->
                                                    
                                                    @if($pump_operator_sales->count() > 0)
                                                    <th>@lang('report.pump_operator')</th>
                                                    <th style="width: 30%;">@lang('report.settlement_no')</th>
                                                    @else
                                                    <th>@lang('report.cashiers')</th>
                                                    <th>@lang('report.invoice_no')</th>
                                                    @endif
                                                    <th>@lang('report.cash')</th>
                                                    <th>@lang('report.cheque')</th>
                                                    <th>@lang('report.card')</th>
                                                    <th>@lang('report.credit_sale')</th>
                                                    <th>@lang('report.loans')</th>
                                                    <th>@lang('report.owners_drawings')</th>
                                                    <th>@lang('report.short')</th>
                                                    <th>@lang('report.excess')</th>
                                                    <th>@lang('report.expense')</th>
                                                    <th>@lang('report.total_sale')</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total_row['cash'] = 0;
                                                        $total_row['cheque'] = 0;
                                                        $total_row['card'] = 0;
                                                        $total_row['credit_sale'] = 0;
                                                        $grandtotal = 0;
                                                        $total_row['shortage'] = 0;
                                                        $total_row['excess'] = 0;
                                                        $total_row['expense'] = 0;
                                                        $total_row['loan_total'] = 0;
                                                        $total_row['drawings_total'] = 0;
                                                    @endphp
                                                    @php
                                                    
                                                        $total_row['cash'] = $pump_operator_sales->sum('cash_total') + $pump_operator_sales->sum('deposit_total') + $cashiers->sum('cash_total');
                                                        $total_row['cheque'] = $pump_operator_sales->sum('cheque_total') +
                                                        $cashiers->sum('cheque_total');
                                                        $total_row['card'] = $pump_operator_sales->sum('card_total') +
                                                        $cashiers->sum('card_total');
                                                        $total_row['credit_sale'] = $pump_operator_sales->sum('credit_sale_total') +
                                                        $cashiers->sum('credit_sale_total');
                                                        $total_row['shortage'] = $pump_operator_sales->sum('shortage_amount');
                                                        $total_row['excess'] = $pump_operator_sales->sum('excess_amount') ;
                                                        $total_row['expense'] = $pump_operator_sales->sum('expense_amount') +
                                                        $cashiers->sum('expense_amount');
                                                        
                                                        $total_row['loan_total'] = $pump_operator_sales->sum('loan_total') + $pump_operator_sales->sum('customer_loan_total');
                                                        $total_row['drawings_total'] = $pump_operator_sales->sum('drawing_total');
                                                        
                                                    @endphp                                    
                                                    @foreach ($pump_operator_sales as $pump_operator_sale)
                                                    <tr>
                                                        <!--<td></td>--> <!-- @eng 12/2 -->
                                                        <td>{{ $pump_operator_sale->pump_operator_name}}</td>
                                                    <!--@if($day_diff <= 5) <td>{{ $pump_operator_sale->settlement_nos}}</td>-->
                                                    <!--    @else-->
                                                    <!--    <td></td>-->
                                                    <!--    @endif-->
                                                        <td>{{ $pump_operator_sale->settlement_nos}}</td> <!-- @eng 7/2 15:42 --><!-- @eng 14/2 -->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->cash_total + $pump_operator_sale->deposit_total)}}</td> <!--done-->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->cheque_total)}}</td><!--done-->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->card_total)}}</td> <!--done-->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->credit_sale_total)}}</td> <!--done-->
                                                        
                                                        <td class="text-right">{{@num_format($pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total)}}</td><!--done-->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->drawing_total)}}</td><!--done-->
                                                        
                                                        <td class="text-right">{{@num_format($pump_operator_sale->shortage_amount)}}</td> <!--done-->
                                                        <td class="text-right">{{@num_format(abs($pump_operator_sale->excess_amount))}}</td> <!--done-->
                                                        <td class="text-right">{{@num_format($pump_operator_sale->expense_amount)}}</td> <!--done-->
                                                        
                                                        <td class="text-right">{{@num_format(
                                                                                            $pump_operator_sale->expense_amount - abs($pump_operator_sale->excess_amount)
                                                                                            + $pump_operator_sale->shortage_amount + $pump_operator_sale->credit_sale_total
                                                                                            + $pump_operator_sale->card_total + $pump_operator_sale->cheque_total
                                                                                            + $pump_operator_sale->cash_total + $pump_operator_sale->deposit_total 
                                                                                            + $pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total
                                                                                            +$pump_operator_sale->drawing_total
                                                                                    )}}
                                                        
                                                    </tr> 
                                                    @endforeach  
                                                    
                                                    @foreach ($cashiers as $cashier)
                                                    @php
                                                    $cid = $cashier->cashier_id;
                                                    $grandTotal = array_column(array_filter($cashiers_total_sales, function ($item) use ($cid) {
                                                                        return $item['cashier_id'] == $cid;
                                                                    }), 'grand_total');
                                                    
                                                    $grandtotal += !empty($grandTotal) ? $grandTotal[0] : 0; @endphp
                                                    <tr>
                                                        <td>{{ $cashier->cashier_name}}</td>
                                                   
                                                   
                                                        <td>{{ $cashier->settlement_nos}}</td> <!-- @eng 7/2 15:42 --><!-- @eng 14/2 -->
                                                        <td class="text-right">{{@num_format($cashier->cash_total)}}</td>
                                                        <td class="text-right">{{@num_format($cashier->cheque_total)}}</td>
                                                        <td class="text-right">{{@num_format($cashier->card_total)}}</td>
                                                        <td class="text-right">{{@num_format($cashier->credit_sale_total)}}</td>
                                                        
                                                        <td class="text-right">{{@num_format(0)}}</td>
                                                        <td class="text-right">{{@num_format(0)}}</td>
                                                        
                                                        <td class="text-right">{{@num_format($cashier->shortage_amount)}}</td>
                                                        <td class="text-right">{{@num_format(abs($cashier->excess_amount))}}</td>
                                                        <td class="text-right">{{@num_format($cashier->expense_total)}}</td>
                                                        
                                                        <td class="text-right">{{@num_format(
                                                                                    $cashier->cash_total + $cashier->cheque_total 
                                                                                    + $cashier->card_total + $cashier->credit_sale_total 
                                                                                    + $cashier->shortage_amount - abs($cashier->excess_amount) 
                                                                                    + $cashier->expense_total
                                                                                )}}
                                                        </td>
                                                    </tr>
                                                    @endforeach  
                                                    <tr class="text-red">
                                                        @if($pump_operator_sales->count() > 0) <!-- @eng START 14/2 -->
                                                            <td colspan="2"><b>@lang('lang_v1.total')</b></td><!-- @eng 12/2 -->
                                                            <!--<td colspan="3"><b>@lang('lang_v1.total')</b></td>--> <!-- @eng 12/2 -->
                                                        @else
                                                            <td colspan="2"><b>@lang('lang_v1.total')</b></td>
                                                        @endif <!-- @eng END 14/2-->
                                                        <td class="text-right"><b>{{@num_format($total_row['cash'])}}</b></td>
                                                        <td class="text-right"><b>{{@num_format($total_row['cheque'])}}</b></td>
                                                        <td class="text-right"><b>{{@num_format($total_row['card'])}}</b></td>
                                                        <td class="text-right"><b>{{@num_format($total_row['credit_sale'])}}</b></td>
                                                        
                                                        
                                                        <td class="text-right"><b>{{@num_format($total_row['loan_total'])}}</b></td>
                                                        <td class="text-right"><b>{{@num_format($total_row['drawings_total'])}}</b></td>
                                                        
                                                        
                                                        <td class="text-right"><b>{{@num_format($total_row['shortage'])}}</b></td>
                                                        <td class="text-right"><b>{{@num_format(abs($total_row['excess']))}}</b></td>
                                                        <td class="text-right"><b>{{@num_format($total_row['expense'])}}</b></td>
                                                        <td class="text-right">
                                                            {{@num_format(
                                                                    $total_row['cash'] + $total_row['cheque'] + $total_row['card']
                                                                    + $total_row['credit_sale'] + $total_row['shortage'] - abs($total_row['excess'])
                                                                    + $total_row['expense'] + $total_row['loan_total']+ $total_row['drawings_total']
                                                            )}}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>                     
                                        <!-- @eng test end --><!-- @eng END 14/2 -->
                
                                    <div class="col-md-6 width-50 f-left @if(empty($activate_add)) no-print @endif">
                
                                        <table class="table table-bordered table-striped" id="daily_report_table">
                
                                            <thead>
                
                                            <tr>
                
                                                <th colspan="5" class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.add')</span></th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody style="background-color: #F4DDFF">
                
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('outstanding_details')">
                
                                                <td class="heading_td">@lang('report.received_payment_for_outstanding')</td>
                
                                                <td class="text-right">{{ @num_format($total_received_outstanding_ra) }}</td>
                
                                            </tr>
                
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('deposit_details')">
                
                                                <td class="heading_td">@lang('report.received_customer_payment_advance')</td>
                
                                                <td class="text-right">{{ @num_format($deposit_by_customer) }}</td>
                
                                            </tr>
                
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('withdrawal_details')">
                
                                                <td class="heading_td">@lang('report.withdraw_cash_from_banks')</td>
                
                                                <td class="text-right">{{ @num_format($withdrawal_cash) }}</td>
                
                                            </tr>
                                            @php
                                                $total_shortage = $shortage_recover['cash']+$shortage_recover['cheque']+$shortage_recover['card']+$shortage_recover['credit_sale'];
                                                $total_purchase_return = $cash_purchase_returns+$card_purchase_returns+$bank_purchase_returns+$cheque_purchase_returns;
                                                $totalIncome = $total_received_outstanding_ra+$deposit_by_customer +$withdrawal_cash + $total_shortage + $total_purchase_return;
                                            @endphp
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('shortage_details')">
                
                                                <td class="heading_td" >@lang('report.shortage_recovered')</td>
                
                                                <td class="text-right">{{ @num_format($total_shortage) }}</td>
                
                                            </tr>
                                            
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('purchase_return_details')">
                
                                                <td class="heading_td">@lang('report.purchase_returned')</td>
                
                                                <td class="text-right"><b>{{ @num_format($total_purchase_return) }}</b>
                
                                            </tr>
                                            
                
                                            <!--@if ($petro_module)-->
                                            <!--    <tr>-->
                
                                            <!--        <td class="heading_td">Excess Payments Total</td>-->
                
                                            <!--        <td colspan="2">{{ @num_format($excess_total) }}</td>-->
                
                                            <!--    </tr> -->
                                            <!--@endif-->
                
                                            <tr>
                
                                                <th class="heading_td">@lang('report.total_in_add_section')</th>
                
                                                <th class="text-right">{{ @num_format($totalIncome) }}</th>
                
                                            </tr>
                
                                            <tr>
                
                                                <td>&nbsp; </td>
                
                                                <td>&nbsp; </td>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                
                                    
                
                                    <div class="col-md-6 width-50 @if(empty($activate_less)) no-print @endif">
                
                                        <table class="table table-bordered table-striped" id="daily_report_table">
                
                                            <thead>
                
                                            <tr>
                
                                                <th colspan="5" class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.less')</span></th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody style="background-color: #E2EFDA">
                
                                            @if ($petro_module)
                                                <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view more detail!" style="cursor: pointer" onClick="viewDetails('expense_details')">
                
                                                    <td class="heading_td">@lang('report.expenses_in_sales')</td>
                
                                                    <td colspan="2" class="text-right" >{{ @num_format($expense_in_settlement) }}</td>
                
                                                </tr>
                                            @endif
                                            
                                            @php
                                            
                                                $totalExCom = $excess_commission['cash']+$excess_commission['cheque']+$excess_commission['card']+$excess_commission['credit_sale'];
                                                $totaSaleReturnPaid = $cash_sell_returns+$card_sell_returns+$bank_sell_returns+$cheque_sell_returns;
                                                $totalDirectExpenses = $direct_cash_expenses+$cheque_expenses+$bank_expenses+$card_expenses;
                                                $totalPurchase = abs($purchase_details->sum('amount'));
                                            @endphp
                                            
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('excess_commission_details')">
                
                                                <td class="heading_td">@lang('report.excess_and_commission_paid')</td>
            
                                                <td colspan="2" class="text-right">{{ @num_format($totalExCom) }}</td>
            
                                            </tr>
                                            
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('sell_return_details')">
                
                                                <td class="heading_td">@lang('report.sales_returned')</td>
            
                                                <td colspan="2" class="text-right">{{ @num_format($totaSaleReturnPaid) }}</td>
            
                                            </tr>
                                            
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('direct_expense_details')">
                
                                                <td class="heading_td">@lang('report.direct_expenses')</td>
            
                                                <td colspan="2" class="text-right">{{ @num_format($totalDirectExpenses) }}</td>
            
                                            </tr>
                                            
                                            <tr data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('purchase_details')">
                
                                                <td class="heading_td">@lang('report.purchases')</td>
            
                                                <td colspan="2" class="text-right"><b>{{ @num_format($totalPurchase) }}</b>
                                                </td>
            
                                            </tr>
                
                
                
                                            <tr>
                
                                                <th class="heading_td">@lang('report.total_in_less_section')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($total_out+$totalExCom+$totaSaleReturnPaid+$totalDirectExpenses+$totalPurchase) }}</th>
                
                                            </tr>
                
                
                
                
                
                                            <tr>
                
                                                <th class="heading_td">@lang('report.difference_add_less')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($totalIncome - ($total_out+$totalExCom+$totaSaleReturnPaid+$totalDirectExpenses+$totalPurchase)) }}</th>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                                    
                                </div>
                                <div class="row" style="">
                                    <div class="col-md-6 width-50 f-left @if(empty($activate_sales_return)) no-print @endif">
                
                                        <table class="table table-bordered table-striped">
                
                                            <thead>
                
                                            <tr>
                
                                                <th colspan="5" class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.sales_return')</span></th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody>
                
            
                                            <tr>
                
                                                <th class="heading_td">@lang('report.returns_amount')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($sellreturns) }}</th>
                
                                            </tr>
                
                
                                            <tr>
                
                                                <th class="heading_td">@lang('report.payments_pending')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($sellreturns-$sellreturns_payment) }}</th>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                                    <div class="col-md-6 width-50 @if(empty($activate_purchase_return)) no-print @endif">
                
                                        <table class="table table-bordered table-striped">
                
                                            <thead>
                
                                            <tr>
                
                                                <th colspan="5" class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.purchases_returns')</span></th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody>
                
            
                                            <tr>
                
                                                <th class="heading_td">@lang('report.purchases_amount')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($purchasereturns) }}</th>
                
                                            </tr>
                
                
                                            <tr>
                
                                                <th class="heading_td">@lang('report.payments_pending')</th>
                
                                                <th colspan="2" class="text-right">{{ @num_format($purchasereturns-$purchasereturns_payment) }}</th>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                                </div>
                                
                                <table class="table table-bordered table-striped @if(empty($activate_financial_status)) no-print @endif" id="financail_status_table">
                
                                    <thead>
                
                                    <tr>
                
                                        <th class="text-left"><span
                                                    style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.financial_status')</span></th>
                
                                        <th>@lang('report.cash')</th>
                
                                        <th>@lang('report.customer_cheques')</th>
                                        
                                        <th>@lang('report.banks')</th>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <th>@lang('report.cpc')</th>
                                        @endif
                
                                        <th>@lang('report.card')</th>
                
                                        <th>@lang('report.credit_sales')</th>
                                        
                                        <th>@lang('report.account_payable')</th>
                
                                    </tr>
                
                                    </thead>
                
                                    <tbody style="background-color: #FFF0D9">
                
                                    <tr>
                
                                        <td>@lang('report.previous_day_balance')</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['cash']+$cash_OB) }}</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['cheque']+$cheque_OB) }}</td>
                                        <td class="text-right">{{ @num_format($previous_day_balance['banks']+$banks_OB) }}</td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($previous_day_balance['cpc']+$cpc_OB) }}</td>
                                        @endif
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['card']+$card_OB) }}</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['previous_day_balance']+$credit_OB) }}</td>
                                        
                                        <td class="text-right">{{ @num_format($previous_day_balance['ap']+$ap_OB) }}</td>
                
                                    </tr>
                
                                    <tr>
                
                                        <td>@lang('report.total_in')</td> 
                
                                        <td class="text-right" data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountBook("{{$url_cash}}")'>
                                            {{ @num_format($todayscashsummary['debit']-$cash_OB) }}
                                        </td>
                
                                        <td class="text-right" data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountBook("{{$url_cheque}}")'>
                                            {{ @num_format($todayschequesummary['debit']-$cheque_OB) }}
                                        </td>
                                         
                                        <td class="text-right" data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountsArray("bank")'>
                                            {{ @num_format($todaysbankssummary['debit']-$banks_OB) }}
                                        </td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right"  data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountsArray("cpc")'>
                                                {{ @num_format($todayscpcsummary['debit']-$cpc_OB) }}
                                            </td>
                                        @endif
                                        
                
                                        <td class="text-right"  data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountsArray("card")'>
                                            {{ @num_format($todayscardsummary['debit']-$card_OB) }}
                                        </td>
                                        
                                        <td class="text-right" data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                            title="Click to view detail!" style="cursor: pointer" 
                                            onClick='viewAccountBook("{{$url_credit}}")'>
                                            {{ @num_format($todayssummary['debit']-$credit_OB) }}
                                        </td>
                                        
                                         <td class="text-right" data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" 
                                                title="Click to view detail!" style="cursor: pointer" 
                                                onClick='viewAccountBook("{{$url_payable}}")'>
                                            {{ @num_format($todaysapsummary['credit']-$ap_OB) }}
                                        </td>
                
                                    </tr>
                
                
                                    <tr>
                
                                        <td>@lang('report.total_out')</td>
                
                                        <td class="text-right">{{ @num_format($todayscashsummary['credit']) }}</td>
                
                                        <td class="text-right">{{ @num_format($todayschequesummary['credit']) }}</td>
                                        
                                        <td class="text-right">{{ @num_format($todaysbankssummary['credit']) }}</td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($todayscpcsummary['credit']) }}</td>
                                        @endif
                                        
                
                                        <td class="text-right">{{ @num_format($todayscardsummary['credit']) }}</td>
                
                                        <td class="text-right">{{ @num_format($todayssummary['credit']) }}</td>
                                        
                                        <td class="text-right">{{ @num_format($todaysapsummary['debit']) }}</td>
                
                
                                    </tr>
                                    
                                    <tr>
                
                                        <td>@lang('report.balance')</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['cash']+$todayscashsummary['debit']-$todayscashsummary['credit']) }}</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['cheque']+$todayschequesummary['debit']-$todayschequesummary['credit']) }}</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['banks']+$todaysbankssummary['debit']-$todaysbankssummary['credit']) }}</td>
                                        
                                        
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($previous_day_balance['cpc']+$todayscpcsummary['debit']-$todayscpcsummary['credit']) }}</td>
                                        
                                        @endif
                                        
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['card']+$todayscardsummary['debit']-$todayscardsummary['credit']) }}</td>
                
                                        <td class="text-right">{{ @num_format($previous_day_balance['previous_day_balance']+$todayssummary['debit']-$todayssummary['credit']) }}</td>
                                        
                                        <td class="text-right">{{ @num_format($previous_day_balance['ap']+$todaysapsummary['credit']-$todaysapsummary['debit']) }}</td>
                
                                    </tr>
                
                                    
                                    </tbody>
                
                                </table>
                                
                                <table class="table table-bordered table-striped @if(empty($activate_financial_status_2)) no-print @endif" id="financail_status_table_2">
                
                                <thead>
                        
                                <tr>
                        
                                    <th class="text-left"><span
                                                style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.financial_status_2')</span></th>
                        
                                    @foreach($accounts as $one)
                                        <th>{{$one->name}}</th>
                                    @endforeach
                        
                                </tr>
                        
                                </thead>
                        
                                <tbody style="background-color: #FFF0D9">
                        
                                <tr>
                        
                                    <td>@lang('report.previous_Day_balance')</td>
                                    
                                    @foreach($accounts as $one)
                                        <td>{{ @num_format($previous_day_balance_r[$one->id]+$OB[$one->id]) }}</td>
                                    @endforeach
                        
                                </tr>
                        
                                <tr>
                        
                                    <td>@lang('report.total_in')</td> 
                        
                                    @foreach($accounts as $one)
                                        <td>{{ @num_format($debits[$one->id]-$OB[$one->id]) }}</td>
                                    @endforeach
                        
                                </tr>
                        
                        
                                <tr>
                        
                                    <td>@lang('report.total_out')</td>
                                    
                                    @foreach($accounts as $one)
                                        <td>{{ @num_format($credits[$one->id]) }}</td>
                                    @endforeach
                        
                                </tr>
                                
                                <tr>
                        
                                    <td>@lang('report.balance')</td>
                                    
                                    @foreach($accounts as $one)
                                        <td>{{ @num_format($previous_day_balance_r[$one->id] + $debits[$one->id] -$credits[$one->id]) }}</td>
                                    @endforeach
                                </tr>
                        
                                
                                </tbody>
                        
                            </table>
                                
                                <table class="table table-bordered table-striped @if(empty($activate_financial_status_breakups)) no-print @endif" id="financail_status_breakups_table">
                
                                    <thead>
                
                                    <tr>
                
                                        <th class="text-left"><span
                                                    style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.financial_status_breakups')</span></th>
                
                                        <th>@lang('report.cash')</th>
                
                                        <th  >@lang('report.customer_cheques')</th>
                                        
                                        <th>@lang('report.banks')</th>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <th>@lang('report.cpc')</th>
                                        @endif
                
                                        <th>@lang('report.card')</th>
                
                                        <th>@lang('report.credit_sales')</th>
                
                                    </tr>
                
                                    </thead>
                
                                    <tbody style="background-color: #FFF0D9">
                
                                    <tr>
                
                                        <td>@lang('report.deposited')</td>
                
                                        <td class="text-right">{{ @num_format($deposit['cash']) }}</td>
                
                                        <td class="text-right">{{ @num_format($deposit['cheque']) }}</td>
                                        <td class="text-right">{{ @num_format($deposit['bank']) }}</td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($deposit['cpc']) }}</td>
                                        @endif
                
                                        <td class="text-right">{{ @num_format($deposit['card']) }}</td>
                
                                        <td class="text-right">{{ @num_format(0) }}</td>
                
                                    </tr>
                
                                    <tr>
                
                                        <td>@lang('report.purchases')</td> 
                
                                        <td class="text-right">
                                            {{ @num_format($total_purchase_by_cash) }}
                                        </td>
                
                                        <td class="text-right">
                                            {{ @num_format($cheque_purchases) }}
                                        </td>
                                         
                                        <td class="text-right">
                                            {{ @num_format($bank_purchases) }}
                                        </td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">
                                                {{ @num_format($cpc_purchases) }}
                                            </td>
                                        @endif
                                        
                
                                        <td class="text-right">
                                            {{ @num_format($card_purchases) }}
                                        </td>
                                        
                                        <td class="text-right">
                                            {{ @num_format($credit_purchases) }}
                                        </td>
                
                                    </tr>
                                    
                                    <tr>
                
                                        <td>@lang('report.expenses')</td> 
                
                                        <td class="text-right">
                                            {{ @num_format($direct_cash_expenses) }}
                                        </td>
                
                                        <td class="text-right">
                                            {{ @num_format($cheque_expenses) }}
                                        </td>
                                         
                                        <td class="text-right">
                                            {{ @num_format($bank_expenses) }}
                                        </td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">
                                                {{ @num_format($cpc_expenses) }}
                                            </td>
                                        @endif
                                        
                
                                        <td class="text-right">
                                            {{ @num_format($card_expenses) }}
                                        </td>
                                        
                                        <td class="text-right">
                                            {{ @num_format($credit_expenses) }}
                                        </td>
                
                                    </tr>
                                    
                                    <tr>
                
                                        <td>@lang('report.journal_in')</td> 
                
                                        <td class="text-right">{{ @num_format($journal_in['cash']) }}</td>
                
                                        <td class="text-right">{{ @num_format($journal_in['cheque']) }}</td>
                                        <td class="text-right">{{ @num_format($journal_in['bank']) }}</td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($journal_in['cpc']) }}</td>
                                        @endif
                
                                        <td class="text-right">{{ @num_format($journal_in['card']) }}</td>
                
                                        <td class="text-right">{{ @num_format($journal_in['credit']) }}</td>
                
                                    </tr>
                                    
                                    <tr>
                
                                        <td>@lang('report.journal_out')</td> 
                
                                        <td class="text-right">{{ @num_format($journal_out['cash']) }}</td>
                
                                        <td class="text-right">{{ @num_format($journal_out['cheque']) }}</td>
                                        <td class="text-right">{{ @num_format($journal_out['bank']) }}</td>
                                        
                                        @if(!empty($cpc) && sizeof($cpc) > 0)
                                            <td class="text-right">{{ @num_format($journal_out['cpc']) }}</td>
                                        @endif
                
                                        <td class="text-right">{{ @num_format($journal_out['card']) }}</td>
                
                                        <td class="text-right">{{ @num_format($journal_out['credit']) }}</td>
                
                                    </tr>
                
                                    
                                    </tbody>
                
                                </table>
                                
                                
                                <div class="row">
                
                                    <div class="col-md-6 width-50 f-left @if(empty($activate_outstanding_details)) no-print @endif">
                
                                        <table class="table table-bordered table-striped" id="outstanding_details_table">
                
                                            <thead>
                
                                            <tr>
                
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.outstanding_details')</span></th>
                
                                                <th>@lang('report.amount')</th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody style="background-color: #E2EFDA">
                
                                            <tr>
                
                                                <td class="heading_td">@lang('report.previous_day_balance')</td>
                
                                                <td class="text-right">{{ @num_format($previous_day_balance['previous_day_balance']+$credit_OB) }}</td>
                
                                            </tr>
                
                                            <tr>
                
                                                <td class="heading_td">@lang('report.credit_sales_given')</td>
                
                                                <td class="text-right">{{ @num_format($todayssummary['debit']-$credit_OB) }}</td>
                
                                            </tr>
                
                                            <tr   data-toggle="tooltip" data-trigger="hover" data-delay="{ show: 500, hide: 100 }" title="Click to view in detail!" style="cursor: pointer" onClick="viewDetails('outstanding_details')">
                
                                                <td class="heading_td">@lang('report.credit_sales_received')</td>
                
                                                <td class="text-right">{{ @num_format($todayssummary['credit']) }}</td>
                
                                            </tr>
                
                                            <tr>
                
                                                <td class="heading_td">@lang('report.balance_outstanding')</td>
                
                                                <td class="text-right">{{ @num_format($previous_day_balance['previous_day_balance']+$todayssummary['debit']-$todayssummary['credit']) }}</td>
                
                                            </tr>
                
                                            <tr>
                
                                                <td>&nbsp; </td>
                
                                                <td>&nbsp; </td>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                
                                    <div class="col-md-6 width-50 @if(empty($activate_stock_value_status)) no-print @endif">
                
                                        <table class="table table-bordered table-striped" id="daily_report_table">
                
                                            <thead>
                
                                            <tr>
                
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.stock_value_status')</span></th>
                
                                                <th colspan="2">@lang('report.amount')</th>
                
                                            </tr>
                
                                            </thead>
                
                                            <tbody style="background-color: #F4DDFF">
                
                                            <tr>
                
                
                
                                                <td class="heading_td">@lang('report.previous_day_stock')</td>
                
                                                <td colspan="2" class="text-right">{{ @num_format($stock_values['previous_day_stock']) }}
                                                </td>
                
                                            </tr>
                
                                            <tr>
                
                
                
                                                <td class="heading_td">@lang('report.sale_returned_stock')</td>
                
                                                <td colspan="2" class="text-right">{{ @num_format($stock_values['sale_return']) }}</td>
                
                                            </tr>
                
                                            <tr>
                
                
                
                                                <td class="heading_td">@lang('report.purchase_stock')</td>
                
                                                <td colspan="2" class="text-right">{{ @num_format(abs($stock_values['purchase_stock'])) }}
                                                </td>
                
                                            </tr>
                
                                            <tr>
                
                
                
                                                <td class="heading_td">@lang('report.cold_stock_value_in_cost')</td>
                
                                                <td colspan="2" class="text-right">{{ @num_format(abs($stock_values['sold_stock'])) }}</td>
                
                                            </tr>
                
                
                
                                            <tr>
                
                                                <td class="heading_td">@lang('report.balance_stock')</td>
                
                                                <td colspan="2" class="text-right">{{ @num_format($stock_values['balance']) }}</td>
                
                                            </tr>
                
                                            </tbody>
                
                                        </table>
                
                                    </div>
                
                                </div>
                
                                @if ($petro_module)
                                    <div class="row">
                
                                        <div class="col-md-6 width-50 f-left @if(empty($activate_pump_operators_shortage)) no-print @endif">
                
                                            <table class="table table-bordered table-striped" id="outstanding_details_table">
                
                                                <thead>
                
                                                <tr>
                
                                                    <th><span
                                                                style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.pump_operator_shortage')</span></th>
                
                                                    <th>@lang('report.amount')</th>
                
                                                </tr>
                
                                                </thead>
                
                                                <tbody>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.previous_day_shortage_balance')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_shortage['previous_day']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.today_shortage')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_shortage['given']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.shortage_recovered')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_shortage['received']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.balance_shortage')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_shortage['balance']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td>&nbsp; </td>
                
                                                    <td>&nbsp; </td>
                
                                                </tr>
                
                                                </tbody>
                
                                            </table>
                
                                        </div>
                
                                        <div class="col-md-6 width-50 @if(empty($activate_pump_operators_excess)) no-print @endif">
                
                                            <table class="table table-bordered table-striped" id="outstanding_details_table">
                
                                                <thead>
                
                                                <tr>
                
                                                    <th><span
                                                                style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.pump_operator_excess')</span></th>
                
                                                    <th>@lang('report.amount')</th>
                
                                                </tr>
                
                                                </thead>
                
                                                <tbody>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.previous_day_excess_balance')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_excess['previous_day']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.today_excess')</td>
                
                                                    <td class="text-right">{{ @num_format(abs($pump_operator_excess['given'])) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.excess_paid')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_excess['received']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td class="heading_td">@lang('report.balance_excess')</td>
                
                                                    <td class="text-right">{{ @num_format($pump_operator_excess['balance']) }}</td>
                
                                                </tr>
                
                                                <tr>
                
                                                    <td>&nbsp; </td>
                
                                                    <td>&nbsp; </td>
                
                                                </tr>
                
                                                </tbody>
                
                                            </table>
                
                                        </div>
                
                
                                        <div class="col-md-12 @if(empty($activate_dip_details)) no-print @endif">
                
                                            <table style="font-size:12px"  class="table table-bordered table-striped" id="dip_details"> <!-- @eng 12/2 -->
                
                                                <thead>
                
                                                <tr>
                
                                                    <th><span
                                                                style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.dip_details')</span></th>
                
                                                    <th>@lang('report.product_name')</th>
                
                                                    <th>@lang('report.qty_on_dip_reading')</th>
                
                                                    <th>@lang('report.current_qty')</th>
                
                                                    <th>@lang('report.difference')</th>
                
                                                    <th>@lang('report.difference_value')</th>
                
                                                    <th>@lang('report.date')</th>
                
                                                    <th>@lang('report.note')</th>
                
                                                </tr>
                
                                                </thead>
                
                                                <tbody style="background-color: #E2EFDA">
                
                                                @foreach ($dip_details as $dip_detail)
                                                    <tr>
                
                                                        <td>{{ $dip_detail->tank_name }}</td>
                
                                                        <td>{{ $dip_detail->product_name }}</td>
                
                                                        <td class="text-right">{{ @num_format($dip_detail->fuel_balance_dip_reading) }}</td>
                
                                                        <td class="text-right">{{ @num_format($dip_detail->current_qty) }}</td>
                
                                                        <td class="text-right">{{ @num_format($dip_detail->fuel_balance_dip_reading - $dip_detail->current_qty) }}
                                                        </td>
                
                                                        <td class="text-right">{{ @num_format(($dip_detail->fuel_balance_dip_reading - $dip_detail->current_qty) * $dip_detail->sell_price_inc_tax) }}
                                                        </td>
                
                                                        <td>{{ @format_date($dip_detail->transaction_date)}}</td>
                
                                                        <td>{{ $dip_detail->note }}</td>
                
                                                    </tr>
                                                @endforeach
                
                                                </tbody>
                
                                            </table>
                
                                        </div>
                                        
                
                                    </div>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                
                </section>
                
           </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td>
                <div class="footer-space"></div>
          </td>
        </tr>
    </tfoot>
</table>


<div class="modal" id="reviewModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal body -->
      <div class="modal-body" style="font-size: 14px !important">
        <div class="row">
            <div class="col-sm-6">
                <h5>@lang('report.date'): @if(!empty($reviewed)) <b>{{date('d M Y H:i',strtotime($reviewed->date_reviewed))}}</b> @else <span class="badge">@lang('report.date_range')</span>@endif</h5>
            </div>
            <div class="col-sm-6">
                <h5>Final Review By: @if(!empty($reviewed)) <b>{{$reviewed->first_name}}</b> @else <span class="badge">@lang('report.not_reviewed')</span>@endif</h5>
            </div>
        </div>
        <hr>
        @php 
        
            $reviewed_temp = DB::table('daily_report_review_status')
                    ->select('daily_report_review_status.*')
                    ->whereDate('reiew_date', '=', date('Y-m-d',strtotime($print_s_date)))
                    ->where('business_id', '=', request()->session()->get('user.business_id'))
                    ->first();
                
            $sections = !empty($reviewed_temp) ? (!empty($reviewed_temp->reviewed_sections) ? json_decode($reviewed_temp->reviewed_sections,true) : array() ) : array();
            $uid = request()->session()->get('user.id');
            
            function reviewedBy($id){
                $user = DB::table('users')->where('id',$id)->first();
                
                return $user->first_name;
            }
        
        @endphp
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-3"></div>
            <div class="col-sm-2"><b>@lang('report.reviewed_by')</b></div>
            <div class="col-sm-2"></div>
        </div>
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.sales')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($total_sales_amount) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['total_sales'])) {{reviewedBy($sections['total_sales'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['total_sales'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="total_sales">@lang('report.ok')</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.customer_outstanding_balance')</b><br>(@lang('report.in_cash_cheques_cards'))</div>
            <div class="col-sm-3"><span>{{ @num_format($total_received_outstanding_ra) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['customer_outstanding'])) {{reviewedBy($sections['customer_outstanding'])}} @endif</span></div>
            
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['customer_outstanding'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="customer_outstanding">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.cash_withdrawn')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($withdrawal_cash) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cash_withdrawn'])) {{reviewedBy($sections['cash_withdrawn'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cash_withdrawn'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cash_withdrawn">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.expenses_in_sales')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($expense_in_settlement) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['sales_expenses'])) {{reviewedBy($sections['sales_expenses'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['sales_expenses'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="sales_expenses">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.cash_received_today')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($todayscashsummary['debit']-$cash_OB) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cash_received_today'])) {{reviewedBy($sections['cash_received_today'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cash_received_today'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cash_received_today">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.cheques_received_today')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($todayschequesummary['debit']-$cheque_OB) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cheque_received_today'])) {{reviewedBy($sections['cheque_received_today'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cheque_received_today'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cheque_received_today">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.card_amount_today')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($todayscardsummary['debit']-$card_OB) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['card_received_today'])) {{reviewedBy($sections['card_received_today'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['card_received_today'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="card_received_today">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.credit_sales_today')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($todayssummary['debit']-$credit_OB) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['credit_sales_today'])) {{reviewedBy($sections['credit_sales_today'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['credit_sales_today'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="credit_sales_today">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.shortages_recovered_today')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($shortage_recover['cash'] + $shortage_recover['cheque'] + $shortage_recover['card'] + $shortage_recover['credit_sale']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['shortages_today'])) {{reviewedBy($sections['shortages_today'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['shortages_today'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="shortages_today">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.excess_and_commission_paid')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($excess_commission['cash'] + $excess_commission['cheque'] + $excess_commission['card'] + $excess_commission['credit_sale']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['excess_paid'])) {{reviewedBy($sections['excess_paid'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['excess_paid'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="excess_paid">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.direct_cash_expenses')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($direct_cash_expenses) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['direct_expenses'])) {{reviewedBy($sections['direct_expenses'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['direct_expenses'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="direct_expenses">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.purchases_by_cash')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($total_purchase_by_cash) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cash_purchase'])) {{reviewedBy($sections['cash_purchase'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cash_purchase'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cash_purchase">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.deposited')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($todaysbankssummary['debit']-$banks_OB) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['deposited'])) {{reviewedBy($sections['deposited'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['deposited'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="deposited">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.cash_balance')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($previous_day_balance['cash']+$todayscashsummary['debit']-$todayscashsummary['credit']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cash_balance'])) {{reviewedBy($sections['cash_balance'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cash_balance'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cash_balance">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.cheque_balance')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($previous_day_balance['cheque']+$todayschequesummary['debit']-$todayschequesummary['credit']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['cheque_balance'])) {{reviewedBy($sections['cheque_balance'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['cheque_balance'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="cheque_balance">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.card_balance')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($previous_day_balance['card']+$todayscardsummary['debit']-$todayscardsummary['credit']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['card_balance'])) {{reviewedBy($sections['card_balance'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['card_balance'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="card_balance">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <div class="col-sm-4"><b>@lang('report.credit_sales_balance')</b></div>
            <div class="col-sm-3"><span>{{ @num_format($previous_day_balance['previous_day_balance']+$todayssummary['debit']-$todayssummary['credit']) }}</span></div>
            <div class="col-sm-2"><span class="reviewed_by">@if(!empty($sections['credit_sales_balance'])) {{reviewedBy($sections['credit_sales_balance'])}} @endif</span></div>
            <div class="col-sm-2">
                @if(auth()->user()->can('DailyReviewOne'))<span class="badge @if(!empty($sections['credit_sales_balance'])) bg-success unOkbutton  @else bg-danger okbutton  @endif" data-string="credit_sales_balance">OK</span> @endif
            </div>
        </div><hr style="margin: 3px !important;border:0.1px solid #c6c6c6">
        
        <div class="row">
            <span class="badge reviewed pull-right" style="margin: 20px">@lang('report.reviewed_on') {{date('d M Y H:i')}}</span>
            @if((empty($reviewed) || $reviewed->status == 0) && auth()->user()->can('DailyReviewAll') )
                <span class="badge bg-danger allchecked pull-right">@lang('report.all_checked')</span>
            @endif
        </div>
        
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewPaymentOutstanding">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.received_payment_for_outstanding')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              @if($paginate)
                                    <table id="table-step-1" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>@lang('report.date')</th>
                                        <th>@lang('report.amount')</th>
                                        <th>@lang('report.customer_name')</th>
                                        <th>@lang('report.payment_method')</th>
                                        <th>@lang('report.bank_account_number')</th>
                                        <th>@lang('report.cheque_numbers')</th>
                                        <th>@lang('report.cheque_date')</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                        <th>@lang('report.totals')</th>
                                        <th><span class="display_currency" id="od_total" data-currency_symbol ="true"></span></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @else
                
                                    @if(isset($total_received_outstanding_detail_rows)) 
                                        <table class="table table-bordered table-striped">
                        
                                            <thead>
                        
                                            <tr>
                        
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.received_outstanding_details')</span></th>
                        
                                                <th>@lang('report.date')</th>
                                                <th>@lang('report.amount')</th>
                                                <th>@lang('report.customer_name')</th>
                                                <th>@lang('report.payment_method')</th>
                                                <th>@lang('report.bank_account_number')</th>
                                                <th>@lang('report.cheque_numbers')</th>
                                                <th>@lang('report.cheque_date')</th>
                                            </tr>
                        
                                            </thead>
                        
                                            <tbody>
                                            @foreach ($total_received_outstanding_detail_rows as $row)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $row->operation_date }}</td>
                        
                                                    <td>{{ @num_format($row->amount) }}</td>
                                                    <td>customer name</td>
                        
                                                    <td>{{ $row->payment_method }}</td>
                        
                                                    <td>{{ $row->bank_account_number }}</td>
                        
                                                    <td>{{ $row->cheque_numbers }}</td>
                        
                                                    <td>{{ $row->cheque_date }}</td>
                        
                                                </tr>
                                            @endforeach
                        
                                            </tbody>
                        
                                        </table>
                                    @endif <!-- @eng END 12/2 -->
                                @endif
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewDepositDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.deposit_details')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.customer')</th>
                            <th>@lang('report.customer_code')</th>
                            <th>@lang('report.type')</th>
                            <th>@lang('report.invoice_no')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_method')</th>
                        </tr>
                    </thead>
                        <tbody>
                            @php $depositTotal = 0; @endphp
                            
                        @if ($deposit_details->count())
                            @foreach ($deposit_details as $row)
                                <tr>
                                    <td>{{ @format_datetime($row->tdate) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    <td>{{ $row->contact_id }}</td>
                                    <td>{{ $row->cname == "advance_payment" ? "Advance": "Deposit" }}</td>
                                    <td>{{ $row->invoice_no }}</td>
                                    <td class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                        
                                            $depositTotal += $row->amount;
                                        
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                </tr>
                            @endforeach
                        
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th  class="text-right">{{ @num_format($depositTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewPurchaseDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.purchase_details')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.supplier')</th>
                            <th>@lang('report.amount')</th>
                        </tr>
                    </thead>
                        <tbody>
                            @php $purTotal = 0; @endphp
                            
                        @if ($purchase_details->count())
                            @foreach ($purchase_details as $row)
                                @php $purTotal += $row->amount; @endphp
                                <tr>
                                    <td>{{ @format_datetime($row->transaction_date) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    <td class="text-right">{{ @num_format($row->amount) }}</td>
                                    
                                </tr>
                            @endforeach
                        
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('report.total') </th>
                            <th  class="text-right">{{ @num_format($purTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewExcessComissionDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.excess_and_commission_paid')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.created_by')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_method')</th>
                        </tr>
                    </thead>
                        <tbody>
                            @php $exComTotal = 0; @endphp
                            
                        @if ($excess_commission_details->count())
                            @foreach ($excess_commission_details as $row)
                                <tr>
                                    <td>{{ @format_datetime($row->tdate) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    <td  class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                        
                                            $exComTotal += $row->amount;
                                        
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th>
                            <th  class="text-right">{{ @num_format($exComTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewDirectExpenseDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.expenses_in_sales')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.created_by')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_method')</th>
                        </tr>
                    </thead>
                        <tbody>
                            @php $expTotal = 0; @endphp
                            
                        @if ($direct_expense_details->count())
                            @foreach ($direct_expense_details as $row)
                                @php $expTotal += $row->amount; @endphp
                                <tr>
                                    <td>{{ @format_datetime($row->transaction_date) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    <td  class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th>
                            <th class="text-right">{{ @num_format($expTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewAccountBook">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        
        <div class="modal-header">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">X</button> &nbsp;
          <!--<button class="btn btn-primary pull-right scrollTopBtn"> ^ </button>&nbsp;-->
          
      </div>

      <div class="modal-body">
          
          <div class="card">
              
              <img style="width: 100px; height: 100px; margin: auto; display: block" src="{{ asset('v2/images/loading.gif') }}" id="loader_image">
              
              <iframe style="width:100%; height: 80vh;border: 0px solid white;" id="iframe"></iframe>
              
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewAccountsArray">
  <div class="modal-dialog">
    <div class="modal-content">
        
        <div class="modal-header">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">X</button>
      </div>

      <div class="modal-body">
          
          <div class="card">
              <div class="form-group" id="cardArea">
                  <label>@lang('report.choose_card_account')</label>
                  <select id="cardAccounts" class="form-control select2 accountsSelector">
                      <option value="">--@lang('report.select_one')</option>
                      @foreach($allaccounts['card'] as $key => $value)
                        <option value="{{ action('AccountController@show', [$key,'is_iframe' => 1]) }}">{{ $value }}</option>
                      @endforeach
                  </select>
              </div>
              
              <div class="form-group" id="bankArea">
                  <label>@lang('report.choose_bank_account')</label>
                  <select id="bankAccounts" class="form-control select2 accountsSelector">
                      <option value="">--@lang('report.select_one')</option>
                      @foreach($allaccounts['banks'] as $key => $value)
                        <option value="{{ action('AccountController@show', [$key,'is_iframe' => 1]) }}">{{ $value }}</option>
                      @endforeach
                  </select>
              </div>
              
              <div class="form-group" id="cpcArea">
                  <label>@lang('report.choose_cpc_account')</label>
                  <select id="cpcAccounts" class="form-control select2 accountsSelector">
                      <option value="">--@lang('report.select_one')</option>
                      @foreach($allaccounts['cpc'] as $key => $value)
                        <option value="{{ action('AccountController@show', [$key,'is_iframe' => 1]) }}">{{ $value }}</option>
                      @endforeach
                  </select>
              </div>
              
          </div>
          
      </div>
     
    </div>
  </div>
</div>

<div class="modal" id="viewSellReturnDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.sales_returned')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.supplier')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_method')</th>
                        </tr>
                    </thead>
                        <tbody>
                        @php $sDetailsTotal = 0; @endphp
                    
                        @if ($sell_return_details->count())
                            @foreach ($sell_return_details as $row)
                                @php $sDetailsTotal += $row->amount; @endphp
                                <tr>
                                    <td>{{ @format_datetime($row->transaction_date) }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td  class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                            
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th>
                            <th class="text-right">{{ @num_format($sDetailsTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewPurchaseReturnDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.purchase_returns')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.location')</th>
                            <th>@lang('report.po_number')</th>
                            <th>@lang('report.invoice_no')</th>
                            <th>@lang('report.supplier')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_amount')</th>
                        </tr>
                    </thead>
                        <tbody>
                        @php $pDetailsTotal = 0; @endphp
                    
                        @if ($purchase_return_details->count())
                            @foreach ($purchase_return_details as $row)
                                @php $pDetailsTotal += $row->amount ; @endphp
                                <tr>
                                    <td>{{$row->business_location}}</td>
                                    <td>{{$row->parent_sale}}</td>
                                    <td>{{$row->invoice_no}}</td>
                                    <td>{{ @format_datetime($row->transaction_date) }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                            
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th class="text-right">{{ @num_format($pDetailsTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewShortageDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.shortage_recovered')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.created_by')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.payment_method')</th>
                            <th>@lang('report.customer')</th>
                        </tr>
                    </thead>
                        <tbody>
                        @php $shortageTotal = 0; @endphp
                    
                        @if ($shortage_details->count())
                            @foreach ($shortage_details as $row)
                                <tr>
                                    <td>{{ @format_datetime($row->created_at) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    <td class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>
                                        @php
                                        
                                            $shortageTotal += $row->amount;
                                        
                                            switch($row->method){
                                                case 'cash':
                                                    echo "Cash";
                                                    break;
                                                case 'cpc':
                                                    echo "CPC";
                                                    break;
                                                case 'bank_transfer':
                                                    echo "Bank Transfrer";
                                                    break;
                                                case 'card':
                                                    echo "Credit Card";
                                                    break;
                                                case 'direct_bank_deposit':
                                                    echo "Direct Bank Deposit";
                                                    break;
                                                case 'cheque':
                                                    echo "Cheque <br> <b>Cheque no:</b> $row->cheque_number <br> <b>Date:</b> $row->cheque_date <br> <b>Bank Name:</b> $row->bank_name";
                                                    break;
                                            }
                                        
                                        @endphp
                                    
                                    </td>
                                    <td>{{ $row->customer }}</td>
                                </tr>
                            @endforeach
                       @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('report.totals')</th><th></th>
                            <th class="text-right">{{ @num_format($shortageTotal) }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewWithdrawalDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.withdrawal_cash_from_bank')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              <table class="table withdrawaldata datatable table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('report.date') </th>
                            <th>@lang('report.account')</th>
                            <th>@lang('report.amount')</th>
                            <th>@lang('report.withdrawn_by')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $withTotal = 0; @endphp
                            
                        @if ($withdrawal_details->count())
                            @foreach ($withdrawal_details as $row)
                                @php $withTotal += $row->amount; @endphp
                                <tr>
                                    <td>{{ @format_datetime($row->operation_date) }}</td>
                                    <td>{{ $row->accname }}</td>
                                    <td class="text-right">{{ @num_format($row->amount) }}</td>
                                    <td>{{ $row->cname }}</td>
                                    
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    <tfoot>
                        <tr>
                            <th></th><th></th>
                            <th class="text-right">{{ @num_format($withTotal) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewItemsSold">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.sold_item_details')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card" style="overflow-x: auto">
              @if($paginate)
                                    <table id="table-step-3" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <!-- @eng start 8/2 1837 -->
                                        <tr>
                                            <th>@lang('report.date')</th>
                                            <th>@lang('report.invoice_no')</th>
                                            <th>@lang('report.customer_name')</th>
                                            <th>@lang('report.customer_code')</th>
                                            <th>@lang('report.location')</th>
                                            <th>@lang('report.payment_method')</th>
                                            <th>@lang('report.total_amount')</th>
                                            <th>@lang('report.total_paid')</th>
                                            <th>@lang('report.sell_return_due')</th>
                                            <th>@lang('report.sell_due')</th>
                                            <th>@lang('report.shipping_status')</th>
                                            <th>@lang('report.total_items')</th>
                                            <th>@lang('report.types_of_service')</th>
                                            <th>@lang('report.added_by')</th>
                                            <th>@lang('report.staff_note')</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                             <th>@lang('report.totals')</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th><span class="display_currency" id="is_total" data-currency_symbol ="true"></span></th>
                                            <th><span class="display_currency" id="is_paid" data-currency_symbol ="true"></span></th>
                                            <th><span class="display_currency" id="is_sell_return_due" data-currency_symbol ="true"></span></th>
                                            <th><span class="display_currency" id="is_sell_due" data-currency_symbol ="true"></span></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <!-- @eng end 8/2 1837 -->
                                        </tfoot>
                                    </table>
                                @else
                                    <!-- @eng START 12/2 -->
                                    @if(isset($getSoldItemsReportDetail))
                                        <table class="table table-bordered table-striped">
                                            <thead>
                    
                                            <tr>
                    
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.sold_items_details')</span></th>
                    
                                                <th>@lang('report.date') </th>
                                                <th>@lang('report.product')</th>
                                                <th>@lang('report.unit_price')</th>
                                                <th>@lang('report.qty_sold')</th>
                                                <th>@lang('report.discount_type')</th>
                                                <th>@lang('report.discount_amount')</th>
                                                <th>@lang('report.sub_total_after_discount')</th>
                    
                                            </tr>
                    
                                            </thead>
                                            <tbody>
                                            @if ($getSoldItemsReportDetail->count())
                                                @foreach ($getSoldItemsReportDetail as $row)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $row->created_at }}</td>
                                                        <td>{{ $row->product->name }}</td>
                                                        <td class="text-right">{{ $row->unit_price }}</td>
                                                        <td class="text-right">{{ $row->quantity }}</td>
                                                        <td class="text-right">{{ $row->line_discount_type }}</td>
                                                        <td class="text-right">{{ $row->line_discount_amount }}</td>
                                                        <td class="text-right">{{ $row->unit_price_before_discount - $row->line_discount_amount }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">@lang('report.no_record')</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    @endif
                                    <!-- @eng END 12/2 -->
                                @endif
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>


<div class="modal" id="viewMeterSales">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.meter_sales')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              @if($paginate)
                                         <!-- @eng 7/2 15:55 -->
                                        <table id="Atable-step-2" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th>@lang('report.date') </th>
                                                <th>@lang('report.settlement_no')</th>
                                                <th>@lang('report.location')</th>
                                                <th>@lang('report.pump_operator')</th>
                                                <th>@lang('report.pump')</th>
                                                <th>@lang('report.product')</th>
                                                <th>@lang('report.starting_m')</th>
                                                <th>@lang('report.closing_m')</th>
                                                <th>@lang('report.testing')</th><!-- @eng 8/2 1324 -->
                                                <th>@lang('report.unit_rs')</th>
                                                <th>@lang('report.qty')</th>
                                                <!--<th>Discount type</th>-->
                                                <th>@lang('report.discount_rs')</th>
                                                <th>@lang('report.total_amount')</th> <!-- @eng 8/2 1314 -->
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                <td>Totals</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td><!-- @eng 8/2 1324 -->
                                                <td> </td>
                                                <td></td>
                                                <!--<th>Discount type</th>-->
                                                <td><span class="display_currency" id="ms_discount" data-currency_symbol ="true"></span></td>
                                                <td><span class="display_currency" id="ms_total" data-currency_symbol ="true"></span></td> <!-- @eng 8/2 1314 -->
                                            </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <!-- @eng START 12/2 -->
                                        @if(isset($getMeterSalesDetails))
                                            <table id="table-step-2" class="table table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.meter_sales')</span></th>
                                                    <th>@lang('report.date') </th>
                                                    <th>@lang('report.pump')</th>
                                                    <th>@lang('report.product')</th>
                                                    <th>@lang('report.starting_meter')</th>
                                                    <th>@lang('report.closing_meter')</th>
                                                    <th>@lang('report.unit_price')</th>
                                                    <th>@lang('report.qty_sold')</th>
                                                    <th>@lang('report.discount_type')</th>
                                                    <th>@lang('report.discount_amount')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($getMeterSalesDetails->count())
                                                    @foreach ($getMeterSalesDetails as $row)
                                                        <tr>
                                                            <td></td>
                                                            <td>{{ $row->created_at }}</td>
                                                            <td>{{ $row->pump->pump_name }}</td>
                                                            <td>{{ $row->product->name }}</td>
                                                            <td class="text-right">{{ $row->starting_meter }}</td>
                                                            <td class="text-right">{{ $row->closing_meter }}</td>
                                                            <td class="text-right">{{ $row->price }}</td>
                                                            <td class="text-right">{{ $row->qty }}</td>
                                                            <td class="text-right">{{ $row->discount_type }}</td>
                                                            <td class="text-right">{{ $row->discount_amount }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="10" class="text-center">@lang('report.no_records')</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                    
                                            </table>
                                        @endif
                                        <!-- @eng END 12/2 -->
                                    @endif
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewChequesReceived">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.cheque_received_details')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              @if($paginate)
                                   <table id="table-step-4" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                            <!-- @eng START 8/2 2057 -->
                                            <th>@lang('report.date')</th>
                                            <th>@lang('report.cheque_number')</th>
                                            <th>@lang('report.note')</th>
                                            <th>@lang('report.image')</th>
                                            <th>@lang('report.added_by')</th>
                                            <th>@lang('report.opening_balance')</th>
                                            <th>@lang('report.debit')</th>
                                            <th>@lang('report.credit')</th>
                                            <th>@lang('report.remaining_balance')</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <!-- @eng START 8/2 2057 -->
                                            <th>@lang('report.date')</th>
                                            <th>@lang('report.cheque_number')</th>
                                            <th>@lang('report.note')</th>
                                            <th>@lang('report.image')</th>
                                            <th>@lang('report.added_by')</th>
                                            <th>@lang('report.opening_balance')</th>
                                            <th>@lang('report.debit')</th>
                                            <th>@lang('report.credit')</th>
                                            <th>@lang('report.remaining_balance')</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <!-- @eng START 12/2 -->
                                    @if(isset($getchequesReceivedReport))
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.cheques_received_details')</span></th>
                                                <th>@lang('report.date')</th>
                                                <th>@lang('report.customer_name')</th>
                                                <th>@lang('report.amount')</th>
                                                <th>@lang('report.bank')</th>
                                                <th>@lang('report.cheque_date')</th>
                                                <th>@lang('report.cheque_number')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if (!empty($getchequesReceivedReport))
                                                @foreach ($getchequesReceivedReport as $row)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $row->created_at }}</td>
                                                        <td>{{ $row->customer->first_name ?? ''}} {{ $row->customer->last_name ?? ''}}</td>
                                                        <td class="text-right">{{ $row->amount }}</td>
                                                        <td class="text-right">{{ $row->bank_name }}</td>
                                                        <td>{{ $row->cheque_date }}</td>
                                                        <td>{{ $row->cheque_number }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">@lang('report.no_records')</td>
                                                </tr>
                                            @endif
                                            </tbody>
                        
                                        </table>
                                    @endif
                                    <!-- @eng END 12/2 -->
                                @endif
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="viewExpenseDetails">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-body">
          
          <div class="row">
              <h4 class="text-center">{{ request()->session()->get('business.name') }}</h4>
              <h5 class="text-center">@lang('report.expense_details')</h5>
              @if(!empty($reviewed))
                   <button class="btn btn-success no-print reviewButton" style="display: block; margin: auto">@lang('report.reviewed')</button>
                @else 
                   <button class="btn btn-danger no-print reviewButton" style="display: block; margin: auto">@lang('report.review')</button>
                @endif
          </div>
          
          <hr>
          
          <div class="card"  style="overflow-x: auto">
              @if($paginate)
                                    <table id="table-step-5" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>@lang('report.date')</th>
                                            <th>@lang('report.reference_no')</th>
                                            <th>@lang('report.payee_name')</th><!-- @eng start 8/2 1740 -->
                                            <th>@lang('report.expense_category')</th>
                                            <th>@lang('report.payment_status')</th>
                                            <th>@lang('report.amount')</th>
                                            <th>@lang('report.payment_due')</th>
                                            <th>@lang('report.payment_method')</th>
                                            <th>@lang('report.expense_for')</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>@lang('report.date')</th>
                                            <th>@lang('report.reference_no')</th>
                                            <th>@lang('report.payee_name')</th><!-- @eng start 8/2 1740 -->
                                            <th>@lang('report.expense_category')</th>
                                            <th>@lang('report.payment_status')</th>
                                            <th>@lang('report.amount')</th>
                                            <th>@lang('report.payment_due')</th>
                                            <th>@lang('report.payment_method')</th>
                                            <th>@lang('report.expense_for')</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <!-- @eng START 12/2 -->
                                    @if(isset($getexpensesReport))
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th><span
                                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.expenses_details')</span></th>
                        
                                                <th>@lang('report.date')</th>
                                                <th>@lang('report.expense_category')</th>
                                                <th>@lang('report.expense_ref_no')</th>
                                                <th>@lang('report.amount')</th>
                                                <th>@lang('report.paid_or_due')</th>
                                            </tr>
                                            </thead>
                        
                                            <tbody>
                                            @if ($getexpensesReport->count())
                                                @foreach ($getexpensesReport as $row)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $row->transaction_date }}</td>
                                                        <td>{{ !empty($row->expenseCategory->name) ? $row->expenseCategory->name : '' }}</td>
                                                        <td>{{ $row->ref_no }}</td>
                                                        <td>{{ $row->final_total}}</td>
                                                        <td>{{ $row->payment_status }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">@lang('report.no_records')</td>
                                                </tr>
                                            @endif
                                            </tbody>
                        
                                        </table>
                                    @endif
                                @endif
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('report.close')</button>
      </div>

    </div>
  </div>
</div>


@if(!empty($reviewed))
<script>
    $('.okbutton').hide();
    $('.unOkbutton').hide();
    $('.email_report').show();
    $('.whatsapp_report').show();
</script>
@else
<script>
    $('.okbutton').show();
    $('.unOkbutton').show();
    $('.email_report').hide();
    $('.whatsapp_report').hide();
</script>
@endif


<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
        $("#loader_image").hide();
        $("#iframe").hide();
        
        $(".select2").select2();
        
      });
  

    $('.reviewed').hide();
    $('.allchecked').hide();
    $('.reviewButton').click(function() {
        var start_d = "{{$print_s_date}}";
        var end_d = "{{$print_e_date}}";
        
        if(start_d == end_d){
            checkSpans();
            $("#reviewModal").modal('show');
        }else{
            toastr.error("Review not available on a range");
        }
        
    })
    
    $(document).on('click', '.okbutton', function() {
        var daily_review = "{{ $daily_review }}";
        if(daily_review == true){
            $('.okbutton').hide();
             $(this).removeClass('bg-danger').addClass('bg-success');
             $(this).removeClass('okbutton').addClass('unOkbutton');
             
             $(this).closest('.row').find('.reviewed_by').text("{{Auth::User()->first_name}}");
             
            $.ajax({
              type: 'POST',
              url: '/daily-review',
              data: {
                date_reviewed: "{{$print_s_date}}",
                reviewed_section: $(this).data("string")
              },
              success: function(response) {
                  if(response.success){
                    toastr.success("Success");  
                   
                    $('.okbutton').show();
                    $('.unOkbutton').show();
                    
                  }else{
                    toastr.error("Something went wrong");    
                  }
                  
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  toastr.error("An error occurred, please try again!");
                $('.okbutton').hide();
                $(this).hide();
              }
            });
        }else{
            toastr.error("{{__('daily_report.you_have_not_subscribed')}}");
        }
        
      checkSpans();
    });
    
    $(document).on('click', '.unOkbutton', function() {
        var daily_review = "{{ $daily_review }}";
        if(daily_review == true){
            $('.unOkbutton').hide();
             $(this).removeClass('bg-success').addClass('bg-danger');
             $(this).removeClass('unOkbutton').addClass('okbutton');
             $(this).closest('.row').find('.reviewed_by').text("");
            $.ajax({
              type: 'POST',
              url: '/daily-review-undo',
              data: {
                date_reviewed: "{{$print_s_date}}",
                reviewed_section: $(this).data("string")
              },
              success: function(response) {
                  if(response.success){
                    toastr.success("Success");
                    $('.okbutton').show();
                    $('.unOkbutton').show();
                  }else{
                    toastr.error("Something went wrong");    
                  }
                  
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  toastr.error("An error occurred, please try again!");
                $('.okbutton').hide();
                $(this).hide();
              }
            });
        }else{
            toastr.error("{{__('daily_report.you_have_not_subscribed')}}");
        }
        
      checkSpans();
    });
    
    $('.allchecked').click(function() {
        
        var daily_review = "{{ $daily_review }}";
        if(daily_review == true){
            $('.okbutton').hide();
            $('.unOkbutton').hide();
            $(this).hide();
          
            
            $.ajax({
              type: 'POST',
              url: '/review-all',
              data: {
                date_reviewed: "{{$print_s_date}}"
              },
              success: function(response) {
                  toastr.success("Reviewed successfully");
                $('.reviewed').show();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
                
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  toastr.error("An error occurred, please try again!");
                $('.okbutton').hide();
                $(this).hide();
              }
            });
        }else{
            toastr.error("{{__('daily_report.you_have_not_subscribed')}}");
        }
        
        
    });
    
    function checkSpans() {
        $('.unOkbutton').text("UNDO");
        $('.okbutton').text("OK");
        
      if ($('.okbutton').length === 0) {
        $('.allchecked').show();
      } else {
        // Not all spans have the .bg-success class, hide the button
        $('.allchecked').hide();
        $('.allchecked').removeClass('bg-success').addClass('bg-danger');
      }
    }
    
    function viewDetails(report){
        if(report == 'outstanding_details'){
            $("#viewPaymentOutstanding").modal('show');
        }
        
        if(report == 'sales_details'){
            $("#viewMeterSales").modal('show');
        }
        
        if(report == 'customer_cheques'){
            $("#viewChequesReceived").modal('show');
        }
        
        if(report == 'items_sold'){
            $("#viewItemsSold").modal('show');
        }
        
        if(report == 'expense_details'){
            $("#viewExpenseDetails").modal('show');
        }
        
        
        
        if(report == 'deposit_details'){
            $("#viewDepositDetails").modal('show');
        }
        
        if(report == 'withdrawal_details'){
            $("#viewWithdrawalDetails").modal('show');
        }
        
        if(report == 'shortage_details'){
            $("#viewShortageDetails").modal('show');
        }
        
        if(report == 'purchase_return_details'){
            $("#viewPurchaseReturnDetails").modal('show');
        }
        
        if(report == 'sell_return_details'){
            $("#viewSellReturnDetails").modal('show');
        }
        
        if(report == 'direct_expense_details'){
            $("#viewDirectExpenseDetails").modal('show');
        }
        
        if(report == 'excess_commission_details'){
            $("#viewExcessComissionDetails").modal('show');
        }
        
        if(report == 'purchase_details'){
            $("#viewPurchaseDetails").modal('show');
        }
    }
    
    function viewAccountBook(url){
        
        var canShow = "{{ auth()->user()->can('account.access') }}";
        var canEdit = "{{ auth()->user()->can('account.edit') }}";
        
        if(canEdit == true || canShow == true){
            
            $("#loader_image").show();
            $("#iframe").hide();
            
            $('#viewAccountBook').modal('show');
            
            var $iframe = $('#iframe');
              $iframe.attr('src', url);
            
              $iframe.on('load', function() {
                
                var iframeDoc = $('#iframe').contents();
    
                iframeDoc.find('.header-area').hide();
                
                iframeDoc.find('.hide-in-iframe').hide();
                
                
                $("#loader_image").hide();  
                $("#iframe").show();
                $('.scrollTopBtn').click(function() {
                    var iframe = $('#iframe');
                    var iframeDoc = iframe.contents()[0];
                    var hiddenDiv = iframeDoc.getElementById('hiddenDiv');
                    var scrollTop = $(hiddenDiv).offset().top;
                    console.log(scrollTop);
                    iframe.contents().scrollTop(1354.1875);
                  });
                
              });
            
        }else{
            toastr.error("You are not permitted to view Account Books");
        }
        
    }
    
    
    
    
    function viewAccountsArray(acc){
        $("#cardArea").hide()
        $("#bankArea").hide()
        $("#cpcArea").hide()
        if(acc == 'card'){
            $("#cardArea").show();
        }
        if(acc == 'bank'){
            $("#bankArea").show();
        }
        if(acc == 'cpc'){
            $("#cpcArea").show();
        }
        $("#viewAccountsArray").modal('show');
    }
    
     $('.accountsSelector').change(function() {
        $("#viewAccountsArray").modal('hide');
        viewAccountBook($(this).val());
        
    });

    
</script>
<!-- @eng END 14/2 -->
