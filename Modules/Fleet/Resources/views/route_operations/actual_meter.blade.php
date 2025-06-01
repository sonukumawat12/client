<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' =>
    action('\Modules\Fleet\Http\Controllers\RouteOperationController@updateactualmeter',[$data->id]), 'method' =>
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
      <h4 class="modal-title">@lang( 'fleet::lang.actual_meter' )</h4>
    </div>
    <div class="modal-body">
        <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('actual_meter_added_on', __( 'fleet::lang.actual_meter_added_on' )) !!} {!!
              Form::text('actual_meter_added_on', !empty($data->actual_meter_added_on) ? @format_datetime($data->actual_meter_added_on) :null, ['class' => 'form-control',
              'disabled', 'placeholder' => __( 'fleet::lang.date' )]); !!}
            </div>
          </div>
          
        <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('trip_completed_on', __( 'fleet::lang.trip_completed' )) !!} {!!
              Form::date('trip_completed_on', !empty($data->trip_completed_on) ? date('Y-m-d',strtotime($data->trip_completed_on)) : date('m/d/Y'), ['class' => 'form-control',
              'required', 'placeholder' => __( 'fleet::lang.date' )]); !!}
            </div>
          </div>
          
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('user', __( 'fleet::lang.actual_meter_user'
              )) !!} {!! Form::text('user', !empty($data->actual_meter_user) ? $data->actual_meter_user : null, ['class'
              => 'form-control', 'placeholder' => __(
              'fleet.lang.user'),'disabled']); !!}
            </div>
            <input type="hidden" value="{{$data->id}}" name="id">
          </div>
      
      
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('code_for_vehicle', __( 'fleet::lang.actual_meter'
          )) !!} {!! Form::text('actual_meter', $data->actual_meter, ['class'
          => 'form-control', 'placeholder' => __(
          'fleet.lang.actual_meter'), 'id' => 'actaul_meter']); !!}
        </div>
        <input type="hidden" value="{{$data->id}}" name="id">
      </div>
      
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('note', __( 'fleet::lang.note'
          )) !!} {!! Form::textarea('notes', $data->notes, ['class'
          => 'form-control', 'rows' => '3', 'placeholder' => __(
          'fleet.lang.note'), 'id' => 'note']); !!}
        </div>
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
