
    <div class="col-md-12">
        @php
            $bill_no = 1;
        @endphp
        @if(count($credit_sales) > 0)
        @foreach ($credit_sales as $sale)
        <div class="col-md-6 border">
            <div class="col-md-12"
                style="border: 1px solid #333; padding : 10px !important; margin-bottom: 10px !important">
                <div class="row">
                    <div class="col-md-11 text-center"><b>{{request()->session()->get('business.name')}}</b></div>
                    <div class="col-md-1 text-right">F14</div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.tel') :</b> {{ $sale->tel }}</div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.filling_station')</b></div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.date'):</b> {{ date('m/d/Y', strtotime($sale->transaction_date)) }}</div>
                    <div class="col-md-6">&nbsp;</div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.customer'):</b> {{ $sale->customer}}</div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.order_no'):</b> {{$sale->order_no}}</div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.vehicle_no'):</b> {{$sale->customer_reference}}</div>
                    <div class="col-md-6"><b>@lang('Stocktaking::lang.bill_no'):</b> {{$bill_no}}</div>
                </div>
                <table class="table table-bordered table-striped credit_sale_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>@lang('Stocktaking::lang.voucher_no')</th>
                            <th>@lang('Stocktaking::lang.balance_qty')</th>
                            <th>@lang('Stocktaking::lang.description')</th>
                            <th>@lang('Stocktaking::lang.unit_price')</th>
                            <th>@lang('Stocktaking::lang.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$sale->order_no}}</td>
                            <td>{{$sale->balance_qty}}</td>
                            <td>{{$sale->description}}</td>
                            <td>{{$sale->unit_price}}</td>
                            <td>{{$sale->final_total}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center">@lang('Stocktaking::lang.total_amount')</td>
                            <td>{{$sale->final_total}}</td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        @php
            $bill_no++;
        @endphp
        @endforeach
        @else
        <p class="text-center">No record found</p>
        @endif
    </div>
 