<div class="row" style="margin-bottom: 20px">
    <section class="content-headertext-center">
        <h4 class="text-center"><b>{{strtoupper($business->name)}}</b><br>{{@format_date($start_date)}} - {{@format_date($end_date)}}</h4>
    </section>
</div>
<div class="row" style="margin-bottom: 20px">
    <table style="width: 100%;">
            
        <tr>
            <td "col-sm-4" style="width: 34% !important">
                <h5><b>{{ __('lang_v1.daily_sales_and_stock_report')}}</b></h5>
                <table class="table table-striped table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.tank')</th>
                            <th>@lang('lang_v1.tank_opening_stock')</th>
                            <th>@lang('lang_v1.product_opening_stock')</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                         
                     @foreach ($prod_arr as $prod)
                        @php $i = 0; @endphp
                        @foreach($prod['tanks'] as $fuel)
                            <tr>
                                <td>{{ strtoupper($fuel->fuel_tank_number) }}</td>
                                <td>{{@num_format($fuel->balance)}}</td>
                                @if($i==0)
                                    <td rowspan="{{$prod['rowspan']}}" style="vertical-align: middle;">
                                        {{@num_format($prod['product_total'])}}
                                    </td>
                                @endif
                            </tr>
                            @php $i++; @endphp
                        @endforeach
                    @endforeach
                    </tbody>
                    
                </table>
            </td>
            <td class="col-sm-8" style="width: 66% !important;vertical-align: top;">
                <h5><b>{{ __('petro::lang.cumulative_sales_amount_for_the_month')}}</b></h5>
                <table class="table table-striped table-bordered"  style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.product')</th>
                            <th>@lang('lang_v1.sales_upto_previous_day')</th>
                            <th>Today Sale</th>
                           <th>Cummulative Sales</th>
                        </tr>
                    </thead>
                      <tbody>
                        @foreach($sales_arr as $sale)
                            <tr>
                                <td>{{$sale['product']}}</td>
                                <td>{{@num_format($sale['ob'])}}</td>
                                <td>{{@num_format($sale['today'])}}</td>
                                <td>{{@num_format($sale['cummulative'])}}</td>
                            </tr>
                        @endforeach
                     </tbody>
                   
                </table>
            </td>
        </tr>
    </table>
        
    
    
</div>

<div class="row" style="margin-bottom: 20px">
    <div class="col-sm-12">
        <table class="table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    @foreach($pump_sales as $key => $pump_sale)
                        <th>@lang('petro::lang.pump')</th>
                        <th>@lang('lang_v1.qty')</th>
                        
                        @if($key != (sizeof($pump_sales)-1))
                           <th></th>
                        @endif
                        
                    @endforeach
                    
                   
                </tr>
            </thead>
            <tbody>
                @php $total_qty = []; $total_val = []; @endphp
                @php for($i = 0; $i < $highest; $i++){ @endphp
                    <tr>
                        @foreach($pump_sales as $key => $pump_sale)
                            @php
                                if(empty($total_qty[$key])){
                                     $total_qty[$key] = 0;
                                }
                                
                                if(empty($total_val[$key])){
                                     $total_val[$key] = 0;
                                }
                            @endphp
                            <td>
                                @if(!empty($pump_sale[$i]))
                                    {{ $pump_sale[$i]['pump_name'] }}
                                @endif
                            </td>
                            <td>
                                 @if(!empty($pump_sale[$i]))
                                    @php 
                                        $total_qty[$key] += $pump_sale[$i]['total_sold']; 
                                        $total_val[$key] += $pump_sale[$i]['total_income']; 
                                    @endphp
                                    {{ @num_format($pump_sale[$i]['total_sold']) }}
                                @endif
                            </td>
                            
                            @if($i == 0 && $key != (sizeof($pump_sales)-1))
                                <td rowspan="{{$highest}}"></td>
                            @endif
                            
                        @endforeach
                    </tr>
                @php } @endphp  
            </tbody>
            
            <tfoot>
                <tr>
                    @foreach($pump_sales as $key => $pump_sale)
                        <th>
                            @if($key == 0)
                               @lang('petro::lang.total_quantity_sold')
                            @endif
                        </th>
                        <th>
                            {{@num_format($total_qty[$key])}}
                        </th>
                        
                        <th>
                            
                        </th>
                        
                        
                    @endforeach
                </tr>
                
                <tr>
                    @php $grand_total = 0; @endphp
                    @foreach($pump_sales as $key => $pump_sale)
                        @php $grand_total += $total_val[$key]; @endphp
                        <th>
                            @if($key == 0)
                               @lang('petro::lang.total_sold_amount')
                            @endif
                        </th>
                        <th>
                            {{@num_format($total_val[$key])}}
                        </th>
                        
                        <th>
                            
                        </th>
                        
                        
                    @endforeach
                </tr>
                
                <tr style="padding-bottom: 20px;">
                    <th colspan="{{sizeof($pump_sales)}}">&nbsp;</th>
                </tr>
                
                <tr>
                    @php $grand_total_ob = 0; @endphp
                    @foreach($pump_sales as $key => $pump_sale)
                        @php $grand_total_ob += $ob_totals[$key]; @endphp
                        <th>
                            @if($key == 0)
                               @lang('petro::lang.stock_value_on_cost')
                            @endif
                        </th>
                        <th>
                            {{@num_format($ob_totals[$key])}}
                        </th>
                        
                        <th>
                            
                        </th>
                        
                        
                    @endforeach
                </tr>
                
            </tfoot>
            
        </table>
    </div>
</div>

<div class="row" style="margin-bottom: 20px">
    <div class="col-sm-3">
        
    </div>
    
     <div class="col-md-6">
            <table class="table table-striped table-bordered"  style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('petro::lang.total_opening_stock_value_on_cost')</th>
                        <th>{{@num_format($grand_total_ob)}}</th>
                    </tr>
                    <tr>
                        <th>@lang('petro::lang.total_sale')</th>
                        <th>{{@num_format($grand_total)}}</th>
                    </tr>
                </thead>
                  
               
            </table>
        </div>
    
</div>
