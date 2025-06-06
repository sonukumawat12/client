@php 
    $url_credit = action('AccountController@show', [$allaccounts['credit'],'is_iframe' => 1]);
    $url_cash = action('AccountController@show', [$allaccounts['cash'],'is_iframe' => 1]);
    $url_cheque = action('AccountController@show', [$allaccounts['cheque'],'is_iframe' => 1]);
    
    
@endphp
<!-- Main content -->

<!-- @eng START 14/2 -->
<style>
    
    #daily_report_header_table{
        display: block !important;
        width:100% !important;
        text-align: center !important;
    }
   
    table{
        margin-top: 20px !important;
        width: 100% !important;
        border-collapse: collapse !important;
    }
    tr, td {
    border: 1px solid #c9c9c9 !important;
    padding: 5px !important;
    text-align: left !important;
  }
  .text-right{
      text-align: right !important;
  }
  .text-left{
      text-align: left !important;
  }
  #daily_report_header_table{
      font-size: 14px !important;
  }
  
  .no-print{
      display: none !important;
  }
</style>


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
                                
                                
                                <table class="table table-bordered table-striped" id="sale_table">
                
                                    <thead>
                
                                    <tr>
                
                                        <th class="text-left"><span
                                                    style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">@lang('report.sale')</span>
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
                                        <div class="col-md-12">
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
                                                        <td class="text-right">{{@num_format($pump_operator_sale->cash_total + $pump_operator_sale->deposit_total)}}</td>
                                                        <td class="text-right">{{@num_format($pump_operator_sale->cheque_total)}}</td>
                                                        <td class="text-right">{{@num_format($pump_operator_sale->card_total)}}</td>
                                                        <td class="text-right">{{@num_format($pump_operator_sale->credit_sale_total)}}</td>
                                                        
                                                        <td class="text-right">{{@num_format($pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total)}}</td>
                                                        <td class="text-right">{{@num_format($pump_operator_sale->drawing_total)}}</td>
                                                        
                                                        <td class="text-right">{{@num_format($pump_operator_sale->shortage_amount)}}</td>
                                                        <td class="text-right">{{@num_format(abs($pump_operator_sale->excess_amount))}}</td>
                                                        <td class="text-right">{{@num_format($pump_operator_sale->expense_amount)}}</td>
                                                        
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
                
                                    <div class="col-md-6">
                
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
                                                $totalIncome = $total_sales_amount+$total_received_outstanding_ra+$deposit_by_customer +$withdrawal_cash + $total_shortage + $total_purchase_return;
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
                
                                    <!--------------------->
                
                                    <!------   LESS    ----->
                
                                    <!--------------------->
                
                                    <div class="col-md-6">
                
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
                                <div class="row">
                                    <div class="col-md-6">
                
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
                                    <div class="col-md-6">
                
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
                                <table class="table table-bordered table-striped" id="financail_status_table">
                
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
                                                onClick='viewAccountBook("{{$url_cash}}")'>
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
                                        
                                        <td class="text-right">{{ @num_format($previous_day_balance['ap']-$todaysapsummary['debit']+$todaysapsummary['credit']) }}</td>
                
                                    </tr>
                
                                    
                                    </tbody>
                
                                </table>
                                
                                <table class="table table-bordered table-striped" id="financail_status_table_2">
                
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
                                
                                <table class="table table-bordered table-striped" id="financail_status_breakups_table">
                
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
                
                                    <div class="col-md-6">
                
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
                
                                    <div class="col-md-6">
                
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
                
                                        <div class="col-md-6">
                
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
                
                                        <div class="col-md-6">
                
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
                
                
                                        <div class="col-md-12">
                
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