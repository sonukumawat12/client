<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('CustomerSmsSettingController@update',[$data->id]), 'method' => 'put', 'id' => 'customer_sms_settings_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'contact.customer_sms_settings' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group col-md-6">
        {!! Form::label('date_time', __( 'contact.date_time' ) . ':*') !!}
          {!! Form::text('date_time', @format_date(date('Y-m-d H:i')), ['class' => 'form-control', 'readonly', 'placeholder' => __( 'contact.date_time' ) ]); !!}
      </div>
      
      <div class="form-group col-md-6">
        {!! Form::label('user_added', __( 'contact.user_added' ) . ':*') !!}
          {!! Form::text('user_added', auth()->user()->username, ['class' => 'form-control', 'readonly', 'placeholder' => __( 'contact.user_added' ) ]); !!}
      </div>
      
      <div class="form-group col-md-6">
        {!! Form::label('location_id', __( 'lang_v1.location' ) . ':*') !!}
          {!! Form::select('location_id', $business_locations, $data->location_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select' ) ]); !!}
      </div>
      
      <div class="form-group col-md-6">
        {!! Form::label('show_customer', __( 'contact.show_customer' ) . ':*') !!}
          {!! Form::select('show_customer', ['0' => 'No', '1' => 'Yes'], $data->show_customer, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select' ) ]); !!}
      </div>
      
      <div class="form-group col-md-6">
        {!! Form::label('show_supplier', __( 'contact.show_supplier' ) . ':*') !!}
          {!! Form::select('show_supplier', ['0' => 'No', '1' => 'Yes'], $data->show_supplier, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select' ) ]); !!}
      </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();
</script>