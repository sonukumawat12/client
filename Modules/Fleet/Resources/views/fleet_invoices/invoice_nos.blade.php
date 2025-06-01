<style>
  .justify-content-between{
    justify-content:space-between!important;
  }
  .main-modal {
  height:675px;
  overflow:auto;
}
  </style>
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.invoice_nos' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
       
        
        
        <div class="form-group col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="view_incentive_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'fleet::lang.date' )</th>
                    <th>@lang( 'fleet::lang.invoice_no' )</th>
                   <th>@lang( 'fleet::lang.order_no' )</th>
                    <th>@lang( 'fleet::lang.product' )</th>
                    <th>@lang( 'fleet::lang.qty' )</th>
                </tr>
            </thead>
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
                    
                    use Modules\Fleet\Entities\RouteOperation;
                    
                    
                    @endphp
                    
                    @foreach($invoices as $item)
                    @php 
                    $ro = RouteOperation::where('business_id',$business_id)->where('order_number',$item->invoice_no)->first();
                    if(empty($item->invoice_no) || empty($ro)){
                        $invoice_no = "";
                    }else{
                        $invoice_no = $ro->invoice_no;
                    }
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
        					<td style="word-break: break-all;">
        					    {{@format_date($item->date)}}
        					</td>
        					<td>{{$invoice_no}}</td>
        					<td>{{$item->invoice_no}}</td>
        					<td>{{$prod}}</td>
        					<td>{{$qty}}</td>
        					
        				</tr>
                    @endforeach
                </tbody>
            
          </table>
        </div>
          </div>
        

      </div>

    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
 $('#view_incentive_table').dataTable();

</script>