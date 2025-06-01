@if(empty($dsr_ob))
    <div class="alert alert-danger">@lang('dsr::lang.please_add_dsr_ob')</div>
@else
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="text-center" colspan="13">{{ $product->name }}</th>
            </tr>
            <tr>
                <th rowspan="2">@lang('dsr::lang.date')</th>
                <th colspan = "{{$pumps->count()}}" class="text-center">@lang('dsr::lang.pump_meter_reading')</th>
                <th colspan="2" class="text-center">@lang('dsr::lang.sales_in_ltrs')</th>
                <th colspan="2" class="text-center">@lang('dsr::lang.purchases_in_ltrs')</th>
                <th colspan="{{ $tanks->count() }}" class="text-center">@lang('dsr::lang.stock_levels_in_ltrs')</th>
                <th rowspan="2">@lang('dsr::lang.quantity_of_ltrs_poured')</th>
            </tr>
            <tr>
                @foreach($pumps as $pump)
                <th>{{$pump->pump_name}}</th>
                @endforeach
                
                <th>@lang('dsr::lang.today_sales')</th>
                <th>@lang('dsr::lang.accumulative_sales')</th>
                <th>@lang('dsr::lang.quantity_invoice_no')</th>
                <th>@lang('dsr::lang.accumulative_purchases')</th>
                
                @foreach($tanks as $tank)
                <th>{{$tank->fuel_tank_number}}</th>
                @endforeach
                
            </tr>
        </thead>
        <tbody>
            @foreach($tdata as $td)
                <tr>
                    <td>{{@format_date($td['date'])}}</td>
                    @foreach($pumps as $key => $pump)
                        <td>{{($td['pump_'.($key+1)])}}</td>
                    @endforeach
                    <td>{{@num_format($td['today_sales'])}}</td>
                    <td>{{@num_format($td['accumulative_sales'])}}</td>
                    <td>{{@num_format($td['today_purchases'])}}</td>
                    <td>{{@num_format($td['accumulative_purchases'])}}</td>
                    
                    @foreach($tanks as $key => $tank)
                        <td>{!! ($td['tank_'.($key+1)]) !!}</td>
                    @endforeach
                    
                    <td>{{@num_format($td['testing_qty'])}}</td>
                </tr>
            @endforeach
            
        </tbody>
    </table>
</div>
@endif
