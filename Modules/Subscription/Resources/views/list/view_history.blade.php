<div class="modal-dialog modal-lg" role="document" >
  <div class="modal-content">
    <style>
      .select2 {    
        width: 100% !important;
      }
    </style>
   
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'subscription::lang.view_history' )</h4>
    </div>
    
    

    <div class="modal-body">
      <div class="row">
          <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="view_history_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'subscription::lang.product' )</th>
                                    <th>@lang( 'subscription::lang.expiry' )</th>
                                    <th>@lang( 'subscription::lang.amount' )</th>
                                    <th>@lang( 'subscription::lang.paid_on' )</th>
                                    <th>@lang( 'subscription::lang.note' )</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($payments as $key => $payment)
                                    
                                    @php
                                            
                                            
                                            $html = "";
                                            $moduleUtil = new \App\Utils\ModuleUtil();
                                            
                                            foreach(json_decode($payment->settings_id,true) as $settings_id){
                                                $product = \Modules\Subscription\Entities\SubscriptionSetting::find($settings_id)->product ?? '';
                                                $price = \Modules\Subscription\Entities\SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                                                if(!empty($price)){
                                                    $html .= "<b>".$product.": </b>".$moduleUtil->num_f($price->new_amount)."<br>";
                                                }
                                            }
                                            
                                        @endphp
                                        
                                        <tr>
                                            <td>{!! $html !!}</td>
                                            <td>{{@format_date($payment->expiry_date)}}</td>
                                            <td>{{@num_format($payment->amount)}}</td>
                                            <td>{{@format_date($payment->transaction_date)}}</td>
                                            <td>{{nl2br($payment->note)}}</td>
                                        </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
      </div>
    
    <div class="modal-footer">
        
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<script>
</script>