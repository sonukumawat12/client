<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\ElectrorateController@update',
    $item->id), 'method' => 'PUT', 'id' => 'electrorate_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_electrorate' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'member::lang.electrorate' )) !!}
        {!! Form::text('name',$item->name , ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.electrorate' ), 'id' => 'electrorate_name']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('district', __('member::lang.district') . ':*') !!}
        {!! Form::select('district',$districts,$item->district_id, [
            'class' => 'form-control',
            'id' => 'district_electrorate',
            'required', 
            'disabled' => 'disabled',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>
      <div class="form-group">
        {!! Form::label('province', __('member::lang.provinces') . ':*') !!}
        {!! Form::select('province',$provinces, $item->province_id, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'disabled' => 'disabled',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>


    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_electrorate_btn">@lang( 'member::lang.update'
        )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#province_select').select2({
        width: '100%'
  });
  $('#district_electrorate').select2({
        width: '100%'
  });
</script>