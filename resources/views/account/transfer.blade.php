@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp

<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@postFundTransfer'), 'method' => 'post', 'id' =>
    'fund_transfer_form', 'enctype' => 'multipart/form-data' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.fund_transfer' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        <strong>@lang('account.selected_account')</strong>:
        {{$from_account->name}}
        <span class="text-red pull-right"> @lang('account.balance'): @if(!empty($account_balance->balance))
          {{@num_format($account_balance->balance)}} @else {{0.00}} @endif </span>
        {!! Form::hidden('from_account', $from_account->id) !!}
        {!! Form::hidden('check_insufficient', $check_insufficient, ['id' => 'check_insufficient']) !!}
        {!! Form::hidden('account_balance', !empty($account_balance->balance) ? $account_balance->balance : 0, ['id' => 'account_balance']) !!}
      </div>

      <!-- change account group to transfer to account group by virtual it professional referance docs number 7338 -->
      <div class="form-group">
        {!! Form::label('account_group_id', __( 'account.transfer_account_group' ) .":*") !!}
        {!! Form::select('account_group_id', $account_groups, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select') ]); !!}
      </div>
      
      <!-- replace transfer to field to receive cheque by virtual it professional referance docs number 7338 -->
      <div class="form-group">
        {!! Form::label('to_account', __( 'account.receive_cheque' ) .":*") !!}
        {!! Form::select('to_account', $to_accounts, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.please_select') ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('amount', __( 'sale.amount' ) .":*") !!}
        {!! Form::text('amount', 0, ['class' => 'form-control input_number', 'required','placeholder' => __(
        'sale.amount' ) ]); !!}
      </div>

      <!-- added pd-field-number for hide and remove required field when transfer option select by virtual it professional referance docs number 7338 -->   
      <div class="form-group pd-field-number">
        {!! Form::label('cheque_number', __( 'lang_v1.cheque_number' ) .":*") !!}
        {!! Form::text('cheque_number', null, ['class' => 'form-control input_number cq-number', 'required','placeholder' => __(
        'lang_v1.cheque_number' ) ]); !!}
      </div>

      <div class="form-group">
        <label class="date_label">{{  __( 'lang_v1.cheque_date' ) .":*" }}</label>
        <div class="input-group date" id='od_datetimepicker'>
          {!! Form::text('operation_date', 0, ['class' => 'form-control', 'required','placeholder' => __(
          'messages.date' ) ]); !!}
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>
      
      <div class="clearfix"></div>
      
      <div class="row">
        <div class="col-md-6">
            <label>
                <input type="radio" name="transfer_or_cheque" class="transfer_or_cheque" value="cheque" checked>
                @lang('account.cheques')
            </label>
        </div>
        <div class="col-md-6">
            <label>
                <input type="radio" name="transfer_or_cheque" class="transfer_or_cheque" value="transfer">
                @lang('account.transfer')
            </label>
        </div>
      </div>
      <hr>
      
      @if(!empty($pacakge_details['show_post_dated_cheque']))
          <div class="col-md-6 text-left pd-fields" >
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('post_dated_cheque', '1', false,
                        [ 'class' => 'input-icheck','id' => 'post_dated_cheque']); !!} {{ __( 'account.post_dated_cheque' ) }}
                    </label>
                </div>
            </div>
            
            @if(!empty($pacakge_details['update_post_dated_cheque']))
            <div class="col-md-6 text-left pd-fields" >
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('update_post_dated_cheque', '1', false,
                        [ 'class' => 'input-icheck','id' => 'update_post_dated_cheque']); !!} {{ __( 'account.update_post_dated_cheque' ) }}
                    </label>
                </div>
            </div>
            @endif
            
        @endif

      <div class="form-group">
        {!! Form::label('note', __( 'brand.note' )) !!}
        {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('attachment', __( 'lang_v1.add_image_document' )) !!}
        {!! Form::file('attachment', ['files' => true]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary submit_btn">@lang( 'messages.submit' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<style>
  .swal-title{
    color: red;
  }
</style>
<script type="text/javascript">
  $(document).ready( function(){
      $(".select2").select2();
      
    $('#od_datetimepicker').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format
    });
  });

  //added add/remove functionality for transfer and cheque option by virtual it professional referance docs number 7338
  $(document).on('change','.transfer_or_cheque',function(){
      if($(this).val() == 'transfer'){
          var label = "{{__( 'account.transaction_date' )}}";
          $(".pd-fields").hide();
          $(".pd-field-number").hide();
          $(".cq-number").removeAttr('required');
          $(".date_label").text(label);
      }else{
          $(".pd-fields").show();
          $(".pd-field-number").show();
          $(".cq-number").attr('required', true);
          var label = "{{__( 'lang_v1.cheque_date' )}}";
          $(".date_label").text(label);
      }
  })
  
  @if($group_name != 'Bank Account')
  $('#amount').change(function(){
    let amount = parseFloat($(this).val());
    account_balance = parseFloat($('#account_balance').val());
    if(amount > account_balance){
      swal({
          title: '@lang('account.insufficient_balance_msg')',
          icon: "error",
          buttons: true,
          dangerMode: true,
      })
      $('.submit_btn').prop('disabled', true);
    }else{
      $('.submit_btn').prop('disabled', false);
    }
  })
  @endif
  $('#account_group_id').change(function () {
    $.ajax({
      method: 'get',
      url: '/accounting-module/get-account-by-group-id/' + $(this).val(),
      data: {  },
      contentType: 'html',
      success: function(result) {
        $('#to_account').empty().append(result);
      },
    });
  })
</script>