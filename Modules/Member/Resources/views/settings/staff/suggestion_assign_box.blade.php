<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberStaffController@storeAssignedStaff'), 'method' =>
      'post', 'id' => 'staff_assign_form' ])
      !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'member::lang.assigned_staff_heading' )</h4>
      </div>
  
      <div class="modal-body">
        {!! Form::hidden('suggestion', $suggestion->id) !!}
        <div class="form-group">
          {!! Form::label('staff_member', __('member::lang.staff') . ':*') !!}
          {!! Form::select('staff_member',$staff,$suggestion->member_staff_id, [
              'class' => 'form-control select2',
              'id' => 'staff_member',
              'required',
              'placeholder' => __('messages.please_select'),
          ]) !!}
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
    $('#staff_member').select2({
          width: '100%'
      });
     
  </script>  