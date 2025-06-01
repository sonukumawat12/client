<div class="row" style="margin-bottom: 20px">
    <section class="content-headertext-center">
        <h4 class="text-center"><b>{{ strtoupper($business->name) }}</b><br>{{ @format_date($start_date) }} - {{ @format_date($end_date) }}</h4>
    </section>
</div>
<div class="row" style="margin-bottom: 20px">
    <h5><b>{{ __('lang_v1.pumpers_daily_summary') }}</b></h5>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>{{ __('petro::lang.name') }}</th>
                <th>{{ __('report.cash') }}</th>
                <th>{{ __('report.cheque') }}</th>
                <th>{{ __('report.card') }}</th>
                <th>{{ __('report.credit_sale') }}</th>
                <th>{{ __('report.loans') }}</th>
                <th>{{ __('report.owners_drawings') }}</th>
                <th>{{ __('report.short') }}</th>
                <th>{{ __('report.excess') }}</th>
                <th>{{ __('report.expense') }}</th>
                <th>{{ __('report.total_sale') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_row = [
                    'cash' => 0,
                    'cheque' => 0,
                    'card' => 0,
                    'credit_sale' => 0,
                    'shortage' => 0,
                    'excess' => 0,
                    'expense' => 0,
                    'loan_total' => 0,
                    'drawings_total' => 0,
                ];
            @endphp
            @foreach ($pump_operator_sales as $pump_operator_sale)
                @php
                    $total_row['cash'] += $pump_operator_sale->cash_total + $pump_operator_sale->deposit_total;
                    $total_row['cheque'] += $pump_operator_sale->cheque_total;
                    $total_row['card'] += $pump_operator_sale->card_total;
                    $total_row['credit_sale'] += $pump_operator_sale->credit_sale_total;
                    $total_row['shortage'] += $pump_operator_sale->shortage_amount;
                    $total_row['excess'] += $pump_operator_sale->excess_amount;
                    $total_row['expense'] += $pump_operator_sale->expense_amount;
                    $total_row['loan_total'] += $pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total;
                    $total_row['drawings_total'] += $pump_operator_sale->drawing_total;
                @endphp
                <tr>
                    <td>{{ $pump_operator_sale->pump_operator_name }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->cash_total + $pump_operator_sale->deposit_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->cheque_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->card_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->credit_sale_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->drawing_total) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->shortage_amount) }}</td>
                    <td class="text-right">{{ @num_format(abs($pump_operator_sale->excess_amount)) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->expense_amount) }}</td>
                    <td class="text-right">{{ @num_format($pump_operator_sale->expense_amount - abs($pump_operator_sale->excess_amount) + $pump_operator_sale->shortage_amount + $pump_operator_sale->credit_sale_total + $pump_operator_sale->card_total + $pump_operator_sale->cheque_total + $pump_operator_sale->cash_total + $pump_operator_sale->deposit_total + $pump_operator_sale->loan_total + $pump_operator_sale->customer_loan_total + $pump_operator_sale->drawing_total) }}</td>
                </tr> 
            @endforeach  
            <tr class="text-red">
                <td colspan="1"><b>{{ __('lang_v1.total') }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['cash']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['cheque']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['card']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['credit_sale']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['loan_total']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['drawings_total']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['shortage']) }}</b></td>
                <td class="text-right"><b>{{ @num_format(abs($total_row['excess'])) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['expense']) }}</b></td>
                <td class="text-right"><b>{{ @num_format($total_row['cash'] + $total_row['cheque'] + $total_row['card'] + $total_row['credit_sale'] + $total_row['shortage'] - abs($total_row['excess']) + $total_row['expense'] + $total_row['loan_total'] + $total_row['drawings_total']) }}</b></td>
            </tr>
        </tbody>
    </table>   
</div>

<div class="row" style="margin-bottom: 20px">
    <table class="table table-bordered table-striped" style="width: 30%;">
        <tbody>
            @php $total = 0; @endphp
            @foreach ($sub_sales as $sale)
                @php $total += $sale['sales']; @endphp
                <tr>
                    <td>{{ $sale['name'] }}</td>
                    <td>{{ @num_format($sale['sales']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>{{ __('petro::lang.total_sale') }}</th>
                <th>{{ @num_format($total) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
