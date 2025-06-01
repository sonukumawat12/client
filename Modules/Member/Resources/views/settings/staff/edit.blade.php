                                           <div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberStaffController@update',
    $item->id), 'method' => 'PUT', 'id' => 'electrorate_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_electrorate' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y',strtotime($item->join_date)), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'joining_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('name', __( 'member::lang.electrorate' )) !!}
        {!! Form::text('name',$item->name , ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.electrorate' ), 'id' => 'electrorate_name']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('designation', __( 'member::lang.designation' )) !!}
        {!! Form::text('designation',$item->designation->job_title, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.designation' ), 'id' => 'designation']);
        !!}
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
  $('#joining_date').datepicker({
        format: 'mm/dd/yyyy'
  });
</script>