<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('DepositsController@postFundTransfer'), 'method' => 'post', 'id' =>
    'fund_transfer_form', 'enctype' => 'multipart/form-data' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'deposits.fund_transfer' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('from_account', __( 'deposits.transfer_from' ) .":*") !!}
        {!! Form::select('from_account', $to_accounts, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.please_select') ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('to_account', __( 'deposits.transfer_to' ) .":*") !!}
        {!! Form::select('to_account', $to_accounts, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.please_select') ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('amount', __( 'sale.amount' ) .":*") !!}
        {!! Form::text('amount', 0, ['class' => 'form-control input_number', 'required','placeholder' => __(
        'sale.amount' ) ]); !!}
      </div>

         
      <div class="form-group">
        {!! Form::label('cheque_number', __( 'lang_v1.cheque_number' ) .":*") !!}
        {!! Form::text('cheque_number', null, ['class' => 'form-control input_number', 'required','placeholder' => __(
        'lang_v1.cheque_number' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('operation_date', __( 'messages.date' ) .":*") !!}
        <div class="input-group date" id='od_datetimepicker'>
          {!! Form::text('operation_date', 0, ['class' => 'form-control', 'required','placeholder' => __(
          'messages.date' ) ]); !!}
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>

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
    $('#od_datetimepicker').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format
    });
    
    $(".select2").select2();
  });
  
  
  $('#account_group_id').change(function () {
    $.ajax({
      method: 'get',
      url: '/deposits-module/get-account-by-group-id/' + $(this).val(),
      data: {  },
      contentType: 'html',
      success: function(result) {
        $('#to_account').empty().append(result);
      },
    });
  })
</script>