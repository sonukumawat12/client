<div class="row">
    <div class="col-md-12">
             <div class="box-tools">
                <!-- Standard Print button -->
                <button class="btn btn-primary print_report pull-right" onclick="printDiv_list()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
        @component('components.widget', ['class' => 'box-primary'])
        <div class="col-md-12">
                 <div class="row">
                 
                <div class="col-md-12">
                    <div class="text-center">
                        <h2 style="font-weight: bold;">{{request()->session()->get('business.name')}}  </h2>
                             
                    </div>
                </div>
                 
            </div>
                    <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3" style="margin-top: 14px;">
                      Managers Name: …………………………………………
                    </div>
                    <div class="col-md-3 text-center pull-center" style="margin-top: 1px;">
                        Filling Station
                    </div>
                  <div class="col-md-3" style="margin-top: 1px;">
                      <b>@lang('petro::lang.date_range'): <span class="9c_from_date">{{$start_date}}</span> @lang('petro::lang.to') <span class="9c_to_date">{{$end_date}}</span> </b>
                 
                    </div>
                     <div class="col-md-3" style="margin-top: 1px;">
                      Balance stock for the day
                    </div>
                    <div class="row">
                  <div  <div class="col-md-12 text-right" style="margin-top: 1px;">
                      Form no :1
                    </div>
                 </div>
                </div>
             </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_16a_table_listss" style="width:100%">
                               <thead>
                              <col />
                              <colgroup span="2"></colgroup>
                              <colgroup span="2"></colgroup>
                              <tr>
                                
                                 <th rowspan="3" class="align-middle text-center" style="vertical-align: bottom;width:100%"><b>@lang('mpcs::lang.description')</b></th>
                                  <th rowspan="3" class="align-middle text-center"style="vertical-align: bottom;"><b> @lang('mpcs::lang.no')</b></th>
                                  @foreach ($sub_categories as $item)
                                    <th colspan="2" class="text-center">{{$item->name}}</th>
                                    @endforeach
                              
                                     <th   colspan="2"  class="text-red" scope="colgroup">@lang('mpcs::lang.other')</th>
                                        <th   colspan="2"  class="text-red" scope="colgroup">@lang('mpcs::lang.total')</th>
                              </tr>
                              <tr>
                           
                                  @foreach ($sub_categories as $item)
                                   <th scope="col" colspan="2">@lang('mpcs::lang.tank_capacity')</th>
                                   
                                    @endforeach
                                     <th scope="col" colspan="2">@lang('mpcs::lang.tank_capacity')</th>
                                      <th scope="col" colspan="2">@lang('mpcs::lang.tank_capacity')</th>
                              </tr>
                              <tr>
                                  @foreach ($sub_categories as $item)
                                  <th scope="col" >@lang('mpcs::lang.qty')</th>
                                <th scope="col">@lang('mpcs::lang.value')</th>
                                    @endforeach
                                       <th scope="col">@lang('mpcs::lang.qty')</th>
                                <th scope="col">@lang('mpcs::lang.value')</th>
                            <th scope="col">@lang('mpcs::lang.qty')</th>
                                <th scope="col">@lang('mpcs::lang.value')</th>
                            
                            
                           
                              </tr>
                         </thead>
                          <tbody>
                               
                                 @foreach ($receipts as $index => $receipt) 
                                 
                               <tr>
                                     @if($index == 0)
                              <td class="align-left text-left" style="color: brown; font-size: 16px;"><b>Receipt</b></td>
                              @else
                              <td ></td>
                              @endif
                              <td> {{$receipt->invoice_no}}</td>
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
								 $total_amount = 0; 
                                    $total_qty=0;
                                    $qty=0;
                                 @endphp
                               
                                    @if($item->id == $receipt->sub_category_id)
                                        @php
                                            $amount += $receipt->quantity * $receipt->purchase_price;
                                             $qty +=$receipt->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        <td><span class="display_currency {{$item->id}}_qty" data-orig-value="{{$receipt->quantity}}" data-currency_symbol="false">{{@format_quantity($qty)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($receipt->purchase_price, $currency_precision)}}</span></td>
                                  @else
                                  <td></td>
                                  <td></td>
                                  
                                
                               @endif

                                      
                                    @endforeach
                                  <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                           <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>                            
                               
                    
                     </tr>
                             
                             @endforeach  
                       
                         
                          
                       <tr>
                            <td class="align-left text-left"  ><b>@lang('mpcs::lang._today')</b></td>
                              <td></td>
                              @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($todays as $today)
                                    @if($item->id == $today->sub_category_id)
                                        @php
                                            $amount += $today->quantity * $today->unit_price;
                                             $qty +=$today->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
                            
                            
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                           <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                             <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                                
                                
                                
                               
                    
                     </tr>   
                      <tr>
                                <td class="align-left text-left" style="width:100%; white-space: nowrap;"><b>@lang('mpcs::lang._previous_day')</b></td>
                                <td></td>
                                  @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($previous_days as $previous_day)
                                    @if($item->id == $previous_day->sub_category_id)
                                        @php
                                            $amount += $previous_day->quantity * $previous_day->unit_price;
                                             $qty +=$previous_day->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                 <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
                                
                          
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                        <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                                
                               
                    
                     </tr>    
                         <tr>
                        <td class="align-left text-left" style="width:100%; white-space: nowrap;"><b>@lang('mpcs::lang._total_receipts')</b></td>
                                <td></td>
                                   @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($receipts as $receipt)
                                    @if($item->id == $receipt->sub_category_id)
                                        @php
                                            $amount += $receipt->quantity * $receipt->purchase_price;
                                             $qty +=$receipt->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                 <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
                            
                            
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                           <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                         <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                                
                               
                    
                     </tr> 
                      <tr>
                                <td class="align-left text-left" style="width:100%; white-space: nowrap;"><b>@lang('mpcs::lang._opening_stock')</b></td>
                                <td></td>
                                  @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($opening_stocks as $opening_stock)
                                    @if($item->id == $opening_stock->sub_category_id)
                                        @php
                                            $amount += $opening_stock->quantity * $opening_stock->purchase_price;
                                             $qty +=$opening_stock->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
                                
                          
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                           <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                           <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                    
                     </tr>
                     
                      <tr>
                              <td class="align-right text-right"><b>@lang('mpcs::lang.Total_Receipts_todate')</b></td>
                               <td></td>
                               
                                 
                                @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($total_receipts_todates as $receipt)
                                    @if($item->id == $receipt->sub_category_id)
                                        @php
                                            $amount += $receipt->quantity * $receipt->purchase_price;
                                             $qty +=$receipt->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
                            
                            
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                          <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                             
                               
                    
                     </tr>
                      <tr>
                       <td class="align-left text-left" style="color: brown; font-size: 16px;"><b>@lang('mpcs::lang.issue')</b></td>
                               <td></td>
                                    @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($issuess as $issues)
                                    @if($item->id == $issues->sub_category_id)
                                        @php
                                            $amount += $issues->quantity * $issues->unit_price;
                                             $qty +=$issues->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                               
                               
                    
                     </tr>
                      
                      
                      <tr>
                               <td class="align-left text-left"><b>@lang('mpcs::lang._cash_sales_today')</b></td>
                                <td></td>
                                     @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($cash_sales_todays as $cash_sales_today)
                                    @if($item->id == $cash_sales_today->sub_category_id)
                                        @php
                                            $amount += $cash_sales_today->quantity * $cash_sales_today->unit_price;
                                             $qty +=$cash_sales_today->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                           <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                           <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                               
                               
                     </tr>
                      <tr>
                                <td class="align-left text-left"><b>@lang('mpcs::lang._credit_sales_today')</b></td>
                                <td></td>
                              @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($credit_sales as $credit_sale)
                                    @if($item->id == $credit_sale->sub_category_id)
                                        @php
                                            $amount += $credit_sale->quantity * $credit_sale->unit_price;
                                             $qty +=$credit_sale->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                               <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                             <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                                
                                 
                     </tr>
                        <tr >
                                <td class="align-left text-left" style="width:100%; white-space: nowrap;"><b>@lang('mpcs::lang.coporative_section_today')</b></td>
                                <td></td>
                                @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($corporate_section_todates as $corporate_section_todate)
                                    @if($item->id == $corporate_section_todate->sub_category_id)
                                        @php
                                            $amount += $corporate_section_todate->quantity * $corporate_section_todate->unit_price;
                                             $qty +=$corporate_section_todate->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                            <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                           <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                                 
                     </tr>
                      <tr>
                                <td class="align-right text-right"><b>@lang('mpcs::lang.total_issues')</b></td>
                               <td></td>
                             @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($total_issuess as $total_issues)
                                    @if($item->id == $total_issues->sub_category_id)
                                        @php
                                            $amount += $total_issues->quantity * $total_issues->unit_price;
                                             $qty +=$total_issues->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                     </tr>
                      <tr>
                                <td class="align-left text-left"><b>@lang('mpcs::lang.issues_up_last_day')</b></td>
                                <td></td>
                                  @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($total_issuess as $issue_upto_last_day)
                                    @if($item->id == $issue_upto_last_day->sub_category_id)
                                        @php
                                            $amount += $issue_upto_last_day->quantity * $issue_upto_last_day->unit_price;
                                             $qty +=$issue_upto_last_day->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                 <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                             <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                     </tr>
                        <tr>
                                 <td class="align-right text-right"><b>@lang('mpcs::lang.total_issues')(1)</b></td>
                                 <td></td>
                                  @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($total_issue_ones as $issue_upto_last_day)
                                    @if($item->id == $issue_upto_last_day->sub_category_id)
                                        @php
                                            $amount += $issue_upto_last_day->quantity * $issue_upto_last_day->unit_price;
                                             $qty +=$issue_upto_last_day->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                 <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{number_format($qty,$qty_precision)}}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="_todayqty" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="_todayqty" > {{ number_format($total_qty, $qty_precision) }} </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }} </td>
                     </tr>
                      <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang.price_discounts_today')</b></td>
                               <td></td>
                            @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($price_discount_todays as $price_discount_today)
                                    @if($item->id == $price_discount_today->sub_category_id)
                                        @php
                                            $amount +=  $price_discount_today->discount_amount;
                                            
                                            $total_amount+=$amount;
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="_today" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                     </tr>
                           <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang._previous_day')</b></td>
                               <td></td>
                             @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($price_discount_previouss as $price_discount_previous)
                                    @if($item->id == $price_discount_previous->sub_category_id)
                                        @php
                                            $amount +=  $price_discount_previous->discount_amount;
                                            
                                            $total_amount+=$amount;
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                     </tr>
                        <tr>
                                  <td class="align-right text-right"><b>@lang('mpcs::lang.total_disocunts')</b></td>
                               <td></td>
                             @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($total_discount_twos as $total_discount_two)
                                    @if($item->id == $total_discount_two->sub_category_id)
                                        @php
                                            $amount +=  $total_discount_two->discount_amount;
                                            
                                            $total_amount+=$amount;
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}</td>
                     </tr>
                       <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang.balances')</b></td>
                               <td></td>
                               @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($balances as $balance)
                                    @if($item->id == $balance->sub_category_id)
                                        @php
                                        $amount += $balance->quantity * $balance->unit_price + $balance->discount_amount;
                                             $qty +=$balance->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                   <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }} </td>
                     </tr>
                      <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang.sub_total_for_today')</b></td>
                               <td></td>
                               @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($balances as $balance)
                                    @if($item->id == $balance->sub_category_id)
                                        @php
                                        $amount += $balance->quantity * $balance->unit_price + $balance->discount_amount;
                                             $qty +=$balance->quantity ;
                                            $total_amount+=$amount;
                                            $total_qty+=$qty
                                           
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }} </td>
                     </tr>
                       <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang.pump_today_last_meter')</b></td>
                               <td></td>
                            @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($pump_one_last_meters as $pump_one_last_meter)
                                    @if($item->id == $pump_one_last_meter->sub_category_id)
                                        @php
                                            $amount +=  $pump_one_last_meter->last_meter_reading;
                                            
                                            $total_amount+=$amount;
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                   <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }} </td>
                     </tr>
                      <tr>
                                 <td class="align-left text-left" style="width:100%; white-space: nowrap;"><b>@lang('mpcs::lang.pump_today_opening_meter')</b></td>
                               <td></td>
                              @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($pump_one_last_meters as $pump_one_last_meter)
                                    @if($item->id == $pump_one_last_meter->sub_category_id)
                                        @php
                                            $amount +=  $pump_one_last_meter->starting_meter;
                                            
                                            $total_amount+=$amount;
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                 <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false"> </span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{number_format($amount, $currency_precision)}}</span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">{{ number_format($total_amount, $currency_precision) }}  </td>
                     </tr>
                      <tr>
                                  <td class="align-left text-left"><b>@lang('mpcs::lang.issued_qty_for_today')</b></td>
                               <td></td>
                                @php $total_amount = 0; 
                                    $total_qty=0;
                                 @endphp
                                    @foreach ($sub_categories as $item)
                                 @php $amount = 0; 
                                    $qty=0;
                                 @endphp
                                  @foreach ($issue_qty_todays as $issue_qty_today)
                                    @if($item->id == $issue_qty_today->sub_category_id)
                                        @php
                                       
                                             $qty +=$issue_qty_today->quantity ;
                                            
                                            $total_qty+=$issue_qty_today
                                           
                                            
                                           
                                        @endphp
                                        
                                    @endif
                                @endforeach
                                  <td><span class="display_currency {{$item->id}}_qty" data-orig-value="" data-currency_symbol="false">{{ number_format($qty, $qty_precision) }}</span></td>
                                  <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false"></span></td>
                                  
                                
                            @endforeach
              
                            <td class="align-middle text-center" id="" >  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;">  </td>
                            <td class="align-middle text-center" id="" >{{ number_format($total_qty, $qty_precision) }}  </td>
                            <td class="align-middle text-center" id="" style="width: 7%;"> </td>
                     </tr>
                     </tbody>
                        </table>
                    </div>
                </div>
                     <div class="row">
                        <div class="col-md-6">
                            <p>@lang('mpcs::lang.checked_by')____________</p> 
                            
                        </div>
                        <div class="col-md-6 text-center">
                          
                            <p>@lang('mpcs::lang.date')____________</p> 
                        </div>
                    </div>
            </div>
        </div>
        @endcomponent
    </div>
</div>

