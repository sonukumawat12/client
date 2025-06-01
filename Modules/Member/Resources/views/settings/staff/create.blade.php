<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberStaffController@store'), 'method' =>
    'post', 'id' => 'member_staff_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_staff_member' )</h4>
    </div>
   

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'joining_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('name', __( 'member::lang.staff_name' )) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.staff_name' ), 'id' => 'staff_name']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('designation', __( 'member::lang.designation' )) !!}
        {!! Form::text('designation', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.designation' ), 'id' => 'designation']);
        !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_electrorate_btn">@lang( 'messages.save' )</button>
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