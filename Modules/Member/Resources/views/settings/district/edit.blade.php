<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\DistrictController@update',
    $district->id), 'method' => 'PUT', 'id' => 'district_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_district' )</h4>
    </div>

    <div class="modal-body">
      
      <div class="form-group">
        {!! Form::label('name', __( 'member::lang.district' )) !!}
        {!! Form::text('name',$district->name, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.district' ), 'id' => 'district_id']);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('province', __('member::lang.provinces') . ':*') !!}
        {!! Form::select('province',$provinces, $district->province_id, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_district_btn">@lang( 'member::lang.update'
        )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#gramaseva_vasama_date').datepicker({
        format: 'mm/dd/yyyy'
    });
</script>