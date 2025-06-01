@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="modal-dialog" role="document" style="width: 85%;">
   <div class="modal-content">
      {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\SettlementController@store'), 'method' => 'post', 'id' =>'settlement_form' ]) !!}

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
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.current_short')</b> <br>
                 
                  {{@num_format($operator_bal > 0 ? abs($operator_bal) : 0) }}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.current_excess')</b> <br>
                 
                  {{@num_format($operator_bal < 0 ? abs($operator_bal) : 0) }}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.daily_collections')</b> <br>
                  {{@num_format($total_daily_collection)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.daily_vouchers')</b> <br>
                  {{@num_format(0)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.commision_ammount')</b> <br>
                  {{isset($pump_operator->total_commision) ? @num_format($pump_operator->total_commision) : 0 }}
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
               @php
               $total_balance = $total_amount - $total_paid;
               @endphp
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.balance'): </b>
                 
                  <span class="total_balance">{{@num_format($total_balance)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
               </div>
               <div class="col-md-3 text-center text-red">
                  <!--<button type="button" id="settlement_save_btn" style="margin-left: 45px;"-->
                  <!--   class="btn btn-primary pull-left @if(!empty($total_balance) && $total_balance == 0) hide @endif">@lang('messages.save')</button>-->
                  <button type="button" id="settlement_save_btn" style="margin-left: 45px;"
                     class="btn btn-primary pull-left">@lang('messages.save')</button>
                     <button data-href="{{action('\Modules\Petro\Http\Controllers\AddPaymentController@preview', [$settlement->id])}}" class="btn-modal btn btn-success pull-right" id="payment_review_btn" data-container=".preview_settlement"> @lang("petro::lang.preview")</button>
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
            @include('petro::settlement.partials.payment_tabs')
         </div>

         <div class="clearfix"></div>
         {!! Form::close() !!}
         <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

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
    var total_balance = "{{$total_balance}}";
    var total_excess = "{{$total_excess}}";
    $('#excess_amount').prop('disabled', false);
    $(document).find('li.disabled a').off('click');
    if (total_balance < 0) {
        // When balance is negative:
        $(".shortage_tab").parents("li:first").addClass("disabled");
        $(".excess_tab").parents("li:first").removeClass("disabled");
        
    } else {
        $(".excess_tab").parents("li:first").addClass("disabled"); // Disable the excess tab
        $(".shortage_tab").parents("li:first").removeClass("disabled"); // Enable the shortage tab
        $(".shortage_tab").click(); // Automatically open the shortage tab
    }


    // if (total_balance === 0) {
    //     $("#settlement_save_btn").removeClass("hide");
    // } else {
    //     $("#settlement_save_btn").addClass("hide");
    // }
    // if(total_excess >0){
    //   $('#shortage_add').prop('disabled', true);
    //   $('#shortage_amount').prop('disabled', true);
    // }
    $('#shortage_amount').on('input',function(){
        if($(this).val().length){
            //$('#excess_amount').prop('disabled', true);
        $('.excess_amount_err').removeClass('hidden');
        }else{
            //$('#excess_amount').prop('disabled', false);
            $('.excess_amount_err').addClass('hidden');

        }
    });

    $('#excess_amount').on('input',function(){
        if($(this).val().length){
            $('#shortage_amount').prop('disabled', true);
        $('.shortage_amount_err').removeClass('hidden');

        }else{
            $('#shortage_amount').prop('disabled', false);
            $('.shortage_amount_err').addClass('hidden');
        }

    });
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