@php
function numberTowords($num)
{

$ones = array(
0 =>"ZERO",
1 => "ONE",
2 => "TWO",
3 => "THREE",
4 => "FOUR",
5 => "FIVE",
6 => "SIX",
7 => "SEVEN",
8 => "EIGHT",
9 => "NINE",
10 => "TEN",
11 => "ELEVEN",
12 => "TWELVE",
13 => "THIRTEEN",
14 => "FOURTEEN",
15 => "FIFTEEN",
16 => "SIXTEEN",
17 => "SEVENTEEN",
18 => "EIGHTEEN",
19 => "NINETEEN",
"014" => "FOURTEEN"
);
$tens = array( 
0 => "ZERO",
1 => "TEN",
2 => "TWENTY",
3 => "THIRTY", 
4 => "FORTY", 
5 => "FIFTY", 
6 => "SIXTY", 
7 => "SEVENTY", 
8 => "EIGHTY", 
9 => "NINETY" 
); 
$hundreds = array( 
"HUNDRED", 
"THOUSAND", 
"MILLION", 
"BILLION", 
"TRILLION", 
"QUARDRILLION" 
); /*limit t quadrillion */
$num = number_format($num,2,".",","); 
$num_arr = explode(".",$num); 
$wholenum = $num_arr[0]; 
$decnum = $num_arr[1]; 
$whole_arr = array_reverse(explode(",",$wholenum)); 
krsort($whole_arr,1); 
$rettxt = ""; 
foreach($whole_arr as $key => $i){
	
while(substr($i,0,1)=="0")
		$i=substr($i,1,5);
if($i < 20){ 
/* echo "getting:".$i; */
$rettxt .= !empty($ones[$i]) ? $ones[$i] : ""; 
}elseif($i < 100){ 
if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
}else{ 
if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
} 
if($key > 0){ 
$rettxt .= " ".$hundreds[$key]." "; 
}
} 
if($decnum > 0){
$rettxt .= " and ";
if($decnum < 20){
$rettxt .= $ones[$decnum];
}elseif($decnum < 100){
$rettxt .= $tens[substr($decnum,0,1)];
$rettxt .= " ".$ones[substr($decnum,1,1)];
}
}
return $rettxt;
}

$vehicle_no = "";

if(sizeof($fleet_items) > 0){
    $vehicle_no = $fleet_items[0]->vehicle_number;
}

@endphp
<style>
    .main{
        padding: 30px !important;
    }
</style>
<div class="main">
<div class="row">
    @if(!empty($logo))
    <div class="col-xs-1">
        <img src="{{$logo}}" class="img img-responsive center-block"
		height="{{$il->logo_height}}" width="{{$il->logo_width}}">
    </div>
    @endif
    <div class="col-xs-11">
         <h1 class="text-center main-header">{{strtoupper($location_details['name'])}}<br>
         <small>{{$rec['city']}}</small>
         </h1>
         <div class="row">
             
             <div class="col-xs-6 text-center">
                <h4>A/C No: {{$fleet_acc->account_number}}</h4> 
             </div>
             <div class="col-xs-6 text-center">
                <h4>Phone no: {{$location_details->mobile}}</h4> 
             </div>
         </div>
    </div>
   
</div>


<div class="row" style="margin-top: 25px;">
    <h5 class="text-muted text-center">TRANSPORT OF LANKA PETROL 92 OCTANE, LANKA PETROL 95 OCTANE, LANKA AUTO DIESEL, LANKA SUPER DIESEL AND LANKA KEROSENE TO OUTLET IN DEALER OWNED BOWSER</h5>
    <hr>
    <div class="row" style="margin-top: 25px;">
        <div class="col-xs-1"><h5><strong>Dealer:</strong></h5></div>
        <div class="col-xs-5"><h5>{{$fleet_acc->dealer_name}}</h5></div>
        
        <div class="col-xs-1"><h5><strong>Outlet:</strong></h5></div>
        <div class="col-xs-5"><h5>{{$location_details['name']}}</h5></div>
    </div>
    
     <div class="row">
        <div class="col-xs-1"><h5><strong>A/C No:</strong></h5></div>
        <div class="col-xs-5"><h5>{{$fleet_acc->account_number}}</h5></div>
        
        <div class="col-xs-1"><h5><strong>Location:</strong></h5></div>
        <div class="col-xs-5"><h5>{{$rec['address']}}</h5></div>
    </div>
</div>

<div class="row" style="margin-top: 25px;">
    <p class="">I request payment for the transport of product from <strong>{{!empty($route) ? $route->orignal_location : "" }}</strong> to the above mentioned outlet in my own bowser bearing the under noted No: <strong>{{$vehicle_no}}</strong>  for the period <b>{{@format_date($fleet_invoice->date_from)}}</b> to <b>{{@format_date($fleet_invoice->date_to)}}</b></p>
</div>

<div class="row" style="margin-top: 15px;">
     <div class="col-xs-4">
        <h5><strong>Name of bank:</strong> {{$fleet_acc->bank_name}}</h5> 
     </div>
     <div class="col-xs-4">
        <h5><strong>Dealer A/C No:</strong> {{$fleet_acc->dealer_account_number}}</h5> 
     </div>
     <div class="col-xs-4">
        <h5><strong>Branch:</strong> {{$fleet_acc->branch}}</h5> 
     </div>
 </div>
 
 <div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="route_operation_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang( 'fleet::lang.date' )</th>
                        <th>@lang( 'fleet::lang.bowser_no' )</th>
                        <th>@lang( 'fleet::lang.invoice_no' )</th>
                        <th>@lang( 'fleet::lang.product' )</th>
                        <th>@lang( 'fleet::lang.qty' )</th>
                        <th>@lang( 'fleet::lang.round_trip_mileage' )</th>
                        <th>@lang( 'fleet::lang.account_no' )</th>
                        <th>@lang( 'fleet::lang.tr_payment' )</th>
                    </tr>
                </thead>
                <tbody>
                    @php $amount = 0; @endphp
                    
                    @php
                    
                    function formatQuantities($string){
                        $to_replace = [];
                        $no = "";
                        
                        for($i = 0; $i < strlen($string); $i++){
                            if(substr($string,$i,1) == "."){
                                if(substr($string,$i+3,1) == ","){
                                    $to_replace[] = $i;
                                    $to_replace[] = $i+1;
                                    $to_replace[] = $i+2;
                                    $to_replace[] = $i+3;
                                }
                            }
                            
                            if(in_array($i,$to_replace)){
                                $no .= "_"; 
                            }else{
                                $no .= substr($string,$i,1);
                            }
                        
                        }
                        
                        
                        return $no;
                    }
                    
                    
                    @endphp
                    
                    @foreach($fleet_items as $item)
                    @php 
                    $amount += $item->invoice_amount; 
                        $prodArr = explode(',',$item->product);
                        if(empty($prodArr)){
                            $prod = "";
                        }else{
                            if(sizeof($prodArr) == 1){
                                $prod = $prodArr[0];
                            }else{
                                $prod = "";
                                foreach($prodArr as $key => $one){
                                    if(!empty($one)){
                                        $prod .= $one;
                                        $prod .= !empty($prodArr[$key+1]) ? " + " : "";
                                    }
                                    
                                }
                            }
                        }
                        
                        $qtyArr = explode('____',formatQuantities($item->qty));
                        if(empty($qtyArr)){
                            $qty = "";
                        }else{
                            if(sizeof($qtyArr) == 1){
                                $qty = number_format(str_replace(',','',$qtyArr[0]),0,','.',');
                            }else{
                                $qty = "";
                                foreach($qtyArr as $key => $one){
                                    if(!empty($one)){
                                        $qty .= number_format(str_replace(',','',$one),0,','.',');
                                        $qty .= !empty($qtyArr[$key+1]) ? " + " : "";
                                    }
                                    
                                }
                            }
                        }
                    
                    
                    @endphp
                        <tr>
        					<td style="word-break: break-all; font-size: {{$il->font_size}}px !important;">
        					    {{@format_date($item->date)}}
        					</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{$item->vehicle_number}} </td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{$item->invoice_no}}</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{$prod}}
        					</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{$qty}}</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{number_format($item->mileage,0,','.',')}}</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{$fleet_acc->delivered_to_acc_no}}</td>
        					<td style="font-size: {{$il->font_size}}px !important;">{{@num_format($item->invoice_amount)}}</td>
        				</tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">@lang( 'fleet::lang.total_payment' )</td>
                        <td>{{@num_format($amount)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>



<div class="row">
    <p style="margin-top: 25px;">Total claim for the period <b>{{@format_date($fleet_invoice->date_from)}}</b> to <b>{{@format_date($fleet_invoice->date_to)}}</b>, the amount of Rupees  <b>{{$amount > 0 ? ucfirst(strtolower(numberTowords($amount))) : ""}}</b> </p>
    
    <p style="margin-top: 25px;">I certify the above details are true & correct</p><br>
</div>

<div class="row">
    <div class="col-sm-6">
        <h5><strong>Signature of the Dealer: .................................................................</strong></h5>
    </div>
    <div class="col-sm-6 text-right">
        <h5><strong>Date: .................................................................</strong></h5> 
    </div>
    
    <br><p style="margin-top: 35px;">I hereby certify that all above stock have been received to {{$location_details->name}},{{$location_details->city}}  through respective invoices</p>
    
    <div class="col-sm-6">
        <br><h5><strong>Supervisor Signature: .................................................................</strong></h5>
    </div>
</div>

<div class="row" style="margin-top: 25px;">
    <h5 class="text-center"><u><b>FOR OFFICIAL USE ONLY</b></u></h5>
</div>

<div class="row" style="margin-top: 25px;">
    <div class="col-sm-6">
        <h5><u><b>Checked By</b></u></h5>
        <p>Accountant (Payment)</p>
        <p>Above Details are true & Correct, please make a Payment</p>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <h5><strong>Regional Manager (EAST)</strong></h5>
    </div>
    <div class="col-sm-6">
        <h5><strong>Date:</strong></h5> 
    </div>
</div>

</div>