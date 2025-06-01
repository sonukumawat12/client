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
      <h4 class="modal-title">@lang( 'subscription::lang.view_expiring' )</h4>
    </div>
    
    

    <div class="modal-body">
      <div class="row">
          <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="view_history_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'subscription::lang.customer' )</th>
                                    <th>@lang( 'subscription::lang.expiry' )</th>
                                    <th>@lang( 'subscription::lang.product' )</th>
                                    <th>@lang( 'subscription::lang.subscription_amount' )</th>
                                    <th>@lang( 'subscription::lang.days_to_expiry' )</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @forelse($settings as $key => $payment)
                                        @php
                                            
                                            $total_price = 0;
                                            foreach(json_decode($payment->settings_id,true) as $settings_id){
                                                $price = \Modules\Subscription\Entities\SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                                                if(!empty($price)){
                                                    $total_price += $price->new_amount ;
                                                }
                                            }
                                            
                                            
                                            $html = "";
                                            $moduleUtil = new \App\Utils\ModuleUtil();
                                            
                                            
                                            foreach(json_decode($payment->settings_id,true) as $settings_id){
                                                $product = \Modules\Subscription\Entities\SubscriptionSetting::find($settings_id)->product ?? '';
                                                $price = \Modules\Subscription\Entities\SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                                                if(!empty($price)){
                                                    $html .= "<b>".$product.": </b>".$moduleUtil->num_f($price->new_amount)."<br>";
                                                }
                                            }
                                            
                                            $payments = \Modules\Subscription\Entities\SubscriptionPayment::where('list_id',$payment->id)->latest()->first();
                                            if(!empty($payments)){
                                                $expiry_date = $payments->expiry_date;
                                            }else{
                                                $expiry_date =  $payment->expiry_date;
                                            }
                                            
                                            // Calculate the difference in days between the expiry date and today's date
                                            $daysUntilExpiry = \Carbon\Carbon::parse($expiry_date)->diffInDays(\Carbon\Carbon::now());
                                        @endphp
                                        @if ($daysUntilExpiry <= 10)
                                            <tr>
                                                <td>{{ $payment->contact_name }}</td>
                                                <td>{{ @format_date($expiry_date) }}</td>
                                                <td>{!! $html !!}</td>
                                                <td>{{ @num_format($total_price) }}</td>
                                                <td class="text-danger"><strong>{{ ($daysUntilExpiry) }}</strong></td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="5">No records found</td>
                                        </tr>
                                    @endforelse

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