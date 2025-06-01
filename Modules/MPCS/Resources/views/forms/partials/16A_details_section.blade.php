 @php
                            $totalUnitPurchasePrice = 0;
                            $counter = 1;
                            @endphp
                    
                            @foreach ($lastRecords as $lastRecord)
                       <tr>
                                <td scope="row" ><span id="form-index">{{ $lastRecord['id']}}</span></td>
                                <td >{{ $lastRecord['product'] }}</td>
                                <td >{{  number_format($lastRecord['received_qty'],  $qty_precision) }} </td>
                                <td  id="unit_price_value">{{ ($lastRecord['unit_purchase_price'] - floor($lastRecord['unit_purchase_price'])) == 0 ? '' : number_format($lastRecord['unit_purchase_price'] - floor($lastRecord['unit_purchase_price']), $currency_precision, '.', '') }}</td>
                                <td id="unit_price_decimal">{{ ($lastRecord['unit_purchase_price'] - floor($lastRecord['unit_purchase_price'])) == 0 ? '' : number_format($lastRecord['unit_purchase_price'] - floor($lastRecord['unit_purchase_price']), $qty_precision, '.', '') }}</td>                           
                                <td  id="unit_price_value1">{{ number_format($lastRecord['total_purchase_price'], 0, '.', ',') }} </td>
                                 <td id="unit_price_decimal1">{{ ($lastRecord['total_purchase_price'] - floor($lastRecord['total_purchase_price'])) == 0 ? '' : number_format($lastRecord['total_purchase_price'] - floor($lastRecord['total_purchase_price']), $currency_precision, '.', '') }}</td>
                                <td colspan="1"  id="unit_price_value1">{{  number_format($lastRecord['default_sell_price'], 0, '.', ',') }} </td>
                                <td colspan="1" id="unit_price_decimal1"> {{ ($lastRecord['default_sell_price'] - floor($lastRecord['default_sell_price'])) == 0 ? '' : number_format($lastRecord['default_sell_price'] - floor($lastRecord['default_sell_price']), $currency_precision, '.', '') }}</td>                               
                                <td colspan="1"   id="unit_price_value1">{{  number_format(($lastRecord['default_sell_price'])*$lastRecord['received_qty'], 0, '.', ',') }} </td>
                                <td colspan="1" id="unit_price_decimal1"> {{ ($lastRecord['default_sell_price'] * $lastRecord['received_qty'] - floor($lastRecord['default_sell_price'] * $lastRecord['received_qty'])) == 0 ? '' : number_format(($lastRecord['default_sell_price'] * $lastRecord['received_qty'] - floor($lastRecord['default_sell_price'] * $lastRecord['received_qty'])), $currency_precision, '.', '') }}</td>
                                
                                
                                  
                                       @if (!empty($form_f16a))
                                          @if (!empty($edit) )
                                         
                                          
                                          <td><input type="text" class="form-control input_text new_price_value" id="new_price_value" value="{{ $form_f16a['book_no'] ?? '' }}"></td>
                                        <td><input type="text" class="form-control input_text new_price_value2" id="new_price_value2" value="{{ $form_f16a['book_stock'] ?? '' }}"></td>
                                          @else
                                          
                                          <td>{{ $form_f16a['book_no'] ?? '' }}</td>
                                          <td>{{ $form_f16a['book_stock'] ?? '' }}></td>
                                        @endif
                              
                                    @else
                                        <td><input type="text" class="form-control input_text new_price_value" id="new_price_value" value="{{ $form_f16a['book_no'] ?? '' }}"></td>
                                    <td><input type="text" class="form-control input_text new_price_value2" id="new_price_value2" value="" onchange="updateGrandBookValue()"></td>
                                       
                                    @endif
                                
                              </tr>
                    
                     </tr>
                     @php
                     $counter+=1;
                 
                     @endphp
                     @endforeach
                     <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.total_this_page')</td>
                                  <td class="text-red text-bold" id="footer_F16A_total_purchase_price">  {{ number_format(floor($total), 0, '.', ',') }}</td>

                                     <td>{{ number_format( ($total - floor( $total)), $currency_precision, '.', '')  }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" id="footer_F16A_total_sale_price">
                                        
                                        
                                    </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    @if (!empty($form_f16a))
                                        @if (!empty($edit))
                                       <td colspan="1" ></td>
                                        @else
                                          <td colspan="1" ></td>
                                        
                                         @endif
                                    @else
                                      <td colspan="1" ></td>
                                        
                                    @endif
                                   
                                </tr>