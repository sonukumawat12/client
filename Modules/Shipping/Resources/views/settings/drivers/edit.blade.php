<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\DriverController@update', $driver->id), 'method' =>
    'put', 'id' => 'driver_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.driver' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'shipping::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'shipping::lang.joined_date' )]); !!}
        </div>
        @if($enable_hrm)
            <div class="form-group col-sm-12">
              {!! Form::label('employee_no', __( 'shipping::lang.employee_no' ) . ':*') !!}
              {!! Form::text('employee_no', $driver->employee_no, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.employee_no'), 'id'
              => 'employee_no', 'readonly']); !!}
            </div>
        @else
            <div class="form-group col-sm-12">
              {!! Form::label('employee_no', __( 'shipping::lang.employee_no' ) . ':*') !!}
              {!! Form::text('employee_no', $driver->employee_no, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.employee_no'), 'id'
              => 'employee_no']); !!}
            </div>
        @endif
        <div class="form-group col-sm-12">
          {!! Form::label('driver_name', __( 'shipping::lang.driver_name' ) . ':*') !!}
          {!! Form::text('driver_name', $driver->driver_name, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.driver_name'), 'id'
          => 'driver_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('nic_number', __( 'shipping::lang.nic_number' ) . ':*') !!}
          {!! Form::text('nic_number', $driver->nic_number, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.nic_number'), 'id'
          => 'nic_number']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('dl_number', __( 'shipping::lang.dl_number' ) . ':*') !!}
          {!! Form::text('dl_number', $driver->dl_number, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.dl_number'), 'id'
          => 'dl_number']); !!}
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
 $('#joined_date').datepicker('setDate', '{{@format_date($driver->joined_date)}}');
</script>