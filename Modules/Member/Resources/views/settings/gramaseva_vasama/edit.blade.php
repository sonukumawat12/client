<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\GramasevaVasamaController@update',
    $gramaseva_vasama->id), 'method' => 'PUT', 'id' => 'gramaseva_vasama_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_gramaseva_vasama' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('electrorate_id', __('member::lang.electrorate') . ':*') !!}
        {!! Form::select('electrorate_id',$electrorates, $gramaseva_vasama->electrorate_id ?? null, [
            'class' => 'form-control select2',
            'id' => 'electrorate_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>
      <div class="form-group">
        {!! Form::label('province_id', __('member::lang.province') . ':*') !!}
        {!! Form::select('province_id',$provinces, $gramaseva_vasama->province_id ?? null, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'disabled' => 'disabled',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>
      <div class="form-group">
        {!! Form::label('district_gram', __('member::lang.district') . ':*') !!}
        {!! Form::text('district_gram',$gramaseva_vasama->electrorate->district->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.district' ),
           'id' => 'district_gram','readonly'=>true]);
           !!}
      </div>
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', \Carbon::parse($gramaseva_vasama->date)->format('m/d/Y'), ['class' => 'form-control',
        'required', 'placeholder' => __( 'member::lang.date' ), 'id' => 'gramaseva_vasama_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('gramaseva_vasama', __( 'member::lang.gramaseva_vasama' )) !!}
        {!! Form::text('gramaseva_vasama', $gramaseva_vasama->gramaseva_vasama, ['class' => 'form-control', 'required',
        'placeholder' => __( 'member::lang.gramaseva_vasama' ), 'id' => 'gramaseva_vasama_name']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_gramaseva_vasama_btn">@lang( 'member::lang.update'
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
    $('#electrorate_select').select2({
        width: '100%'
    });
    $('#province_select').select2({
        width: '100%'
    });

    $('#electrorate_select').change(function(){
        
      $.ajax({
        method: 'post',
        url: '{{action('\Modules\Member\Http\Controllers\ElectrorateController@get')}}',
        data: { 
          id : $(this).val(),
         },
        success: function(result) {
          $('#province_select').val(result.province_id).trigger('change');
          $('#district_gram').val(result.district);
         
        
        },
    });
    })
</script>