<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Distribution\Http\Controllers\DistributionDistrictsController@store'), 'method' =>
    'post', 'id' => 'districts_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">District</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="form-group col-sm-12">
          {!! Form::label('province_id', 'Province' . ':*') !!}
          {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2 input-sm', 
			'placeholder' => __('lang_v1.select_location'),
			'id' => 'province_id', 
			'required', 'autofocus']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name','Name' . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'id'
          => 'name']); !!}
        </div>
        
      </div>

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
