
    <div class="col-md-12">
        @php
            $bill_no = count($credit_sales);
        @endphp
        @if(count($credit_sales) > 0)
        @foreach ($credit_sales as $sale)
        <div class="col-md-6 border">
            <div class="col-md-12"
                style="border: 1px solid #333; padding : 10px !important; margin-bottom: 10px !important">
                <div class="row">
                    <div class="col-md-11 text-center">
                        <b>{{request()->session()->get('business.name')}}</b><br/>
                    <b>@lang('mpcs::lang.filling_station')</b><br/>
                    <b>@lang('mpcs::lang.tel') :</b> {{ $sale->tel }}
                    </div>
                    <div class="col-md-1 text-right">F14</div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6"><b>@lang('mpcs::lang.date'):</b> {{ date('m/d/Y', strtotime($sale->settlement_date)) }}</div>
                    <div class="col-md-6"><b>@lang('mpcs::lang.bill_no'):</b> {{$bill_no}}</div>
                    <div class="col-md-6"><b>@lang('mpcs::lang.customer'):</b> {{ $sale->customer}}</div>
                    <div class="col-md-6"><b>@lang('mpcs::lang.order_no'):</b> {{$sale->order_no}}</div>
                    <div class="col-md-6"><b>@lang('mpcs::lang.vehicle_no'):</b> {{$sale->customer_reference}}</div>
                    <div class="col-md-6"><b>@lang('mpcs::lang.our_ref'):</b> {{$sale->sattlement_no ?? 'N/A'}}</div>
                </div>
                <table class="table table-bordered table-striped credit_sale_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>@lang('mpcs::lang.voucher_no')</th>
                            <th>@lang('mpcs::lang.balance_qty')</th>
                            <th>@lang('mpcs::lang.description')</th>
                            <th>@lang('mpcs::lang.unit_price')</th>
                            <th>@lang('mpcs::lang.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$sale->order_no}}</td>
                            <td>{{number_format($sale->balance_qty,2)}}</td>
                            <td>{{$sale->description}}</td>
                            <td>{{number_format($sale->sell_price_inc_tax,$sale->currency_precision)}}</td>
                            <td>{{number_format($sale->final_total,$sale->currency_precision)}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center">@lang('mpcs::lang.total_amount')</td>
                            <td>{{number_format($sale->final_total,$sale->currency_precision)}}</td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        @php
            $bill_no--;
        @endphp
        @endforeach
        @else
        <p class="text-center">No record found</p>
        @endif
    </div>
 