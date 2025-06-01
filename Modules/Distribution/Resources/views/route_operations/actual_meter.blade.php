<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' =>
    action('\Modules\Distribution\Http\Controllers\RouteOperationController@updateactualmeter',[$data->id]), 'method' =>
    'post', 'id' => 'fleet_form', 'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'account.actual_meter' )</h4>
    </div>
    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('code_for_vehicle', __( 'account.actual_meter'
          )) !!} {!! Form::text('actual_meter', $data->actual_meter, ['class'
          => 'form-control', 'placeholder' => __(
          'account.actual_meter'), 'id' => 'actaul_meter']); !!}
        </div>
        <input type="hidden" value="{{$data->id}}" name="id">
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_leads_btn">
        @lang( 'messages.save' )
      </button>
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    {!! Form::close() !!}
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
