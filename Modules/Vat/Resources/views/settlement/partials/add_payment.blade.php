@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="modal-dialog" role="document" style="width: 85%;">
   <div class="modal-content">
      {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatSettlementController@store'), 'method' => 'post', 'id' =>'settlement_form' ]) !!}

      <div class="modal-header">
         {{-- 
            @ModifiedBy Afes oktavianus
            @DateBy 31-05-2021
            @Task  3350
         --}}

         <h4 class="modal-title pull-left" style="padding-right: 25px">@lang( 'petro::lang.add_payment' )</h4>
         <h4 class="modal-title pull-left" style="padding-right: 25px">@lang( 'petro::lang.settlement_no' ): {{$settlement->settlement_no}}</h4>
         <h4 class="modal-title">@lang( 'petro::lang.date' ): {{$settlement->transaction_date}}</h4>
         <button type="submit" class="btn btn-danger pull-right" data-dismiss="modal">@lang('petro::lang.back')</button>
      </div>

      <div class="modal-body">
         <div class="col-md-12">
            <div class="row">
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.pump_operator')</b> <br>
                  {{isset($pump_operator->name) ? $pump_operator->name : ''}}
               </div>
              
            </div>
            <br><br>
            <div class="row">
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.total_amount'): </b>
                  <span class="total_amount">{{@num_format($total_amount)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.total_paid'): </b>
                  <span class="total_paid">{{@num_format($total_paid)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.balance'): </b>
                  <span class="total_balance">{{@num_format($total_balance)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
               </div>
               <div class="col-md-3 text-center text-red">
                  <button type="button" id="settlement_save_btn" style="margin-left: 45px;"
                     class="btn btn-primary pull-left @if(!empty($total_balance) && $total_balance == 0) hide @endif">@lang('messages.save')</button>
               </div>
              
            </div>
         </div>
         <input type="hidden" name="settlement_id" value="{{$settlement->settlementt_no}}">
         <input type="hidden" name="total_balance" id="total_balance"
            value="{{!empty($total_balance)? $total_balance : 0 }}">
         <input type="hidden" name="total_amount" id="total_amount"
            value="{{!empty($total_amount)? $total_amount : 0 }}">
         <input type="hidden" name="total_paid" id="total_paid" value="{{!empty($total_paid)? $total_paid : 0 }}">
         <br><br>
         <div class="clearfix"></div>
         <div style="margin-top: 20px;">
            @include('vat::settlement.partials.payment_tabs')
         </div>

         <div class="clearfix"></div>
         {!! Form::close() !!}
         <div class="modal-footer">
            <button type="submit" class="btn btn-default" data-dismiss="modal">@lang('petro::lang.back')</button>
         </div>
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
<script>
   $(document).ready(function () {
    $('#show_bulk_tank').on('ifChecked', function(event){
        $('.store_field').addClass('hide');
        $('.bulk_tank_field').removeClass('hide');
    });

    $('#show_bulk_tank').on('ifUnchecked', function(event){
        $('.store_field').removeClass('hide');
        $('.bulk_tank_field').addClass('hide');
    });
    

    if (total_balance === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
    
    
    $(document).find('li.disabled a').on('click', function(e) { e.preventDefault(); return false; });
    $(document).find('#settlement_form .settlement_tabs li a').on('click', function(e) {
      setTimeout(() => {
         $('#excess_amount').prop('disabled', false);
      }, 500);
      localStorage.setItem("settlement_tabs", $(this).attr('href'));
    });
    if (localStorage.getItem("settlement_tabs")) {
      $("#settlement_form .settlement_tabs li a[href='" + localStorage.getItem("settlement_tabs") + "'").click();
    }
});
</script>