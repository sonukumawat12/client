@php
    $pumps_sold = array();
@endphp
@foreach($pumps as $key => $pump)
    <div class="col-md-6">
        <h5><b>{{$pump}}</b></h5>
    </div>
    <div class="col-md-6">
        @php
            $pumps_sold[] = $key; 
            $settlements = Modules\Petro\Entities\MeterSale::where('pump_id',$key)->leftjoin('settlements','settlements.id','meter_sales.settlement_no')->whereDate('settlements.transaction_date',$date)->pluck('settlements.settlement_no')->toArray() ?? [];
        
        @endphp
        
        {{ implode(', ',$settlements); }}
    </div>
    <div class="clearfix"></div>
@endforeach
<input type="hidden" name="sold_pumps" value="{{json_encode($pumps_sold)}}">