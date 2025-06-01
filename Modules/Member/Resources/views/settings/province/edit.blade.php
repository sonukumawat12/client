<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\ProvinceController@update',
    $province->id), 'method' => 'PUT', 'id' => 'province_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_province' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'member::lang.province' )) !!}
        {!! Form::text('name',$province->name, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.province' ), 'id' => 'province_name']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('country', __('member::lang.country') . ':*') !!}
        {!! Form::select('country',$countries, $province->country_id, [
            'class' => 'form-control select2',
            'id' => 'country_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_province_btn">@lang( 'member::lang.update'
        )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#country_select').select2({
        width: '100%'
  });
</script>