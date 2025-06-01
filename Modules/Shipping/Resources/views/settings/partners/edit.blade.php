<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\PartnerController@update', $driver->id), 'method' =>
    'put', 'id' => 'driver_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.partner' )</h4> 
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'shipping::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'shipping::lang.joined_date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name', __( 'shipping::lang.name' ) . ':*') !!}
          {!! Form::text('name', $driver->name, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.name'), 'id'
          => 'add_name']); !!}
        </div>
       
        <div class="form-group col-sm-12">
          {!! Form::label('address', __( 'shipping::lang.address' ) . ':*') !!}
          {!! Form::text('address', $driver->address, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.address'), 'id'
          => 'add_address']); !!}
        </div> 
        
        <div class="form-group col-sm-12">
          {!! Form::label('mobile_1', __( 'shipping::lang.mobile_1' ) . ':*') !!}
          {!! Form::text('mobile_1', $driver->mobile_1, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.mobile_1'), 'id'
          => 'add_mobile_1']); !!}
        </div> 
        
        <div class="form-group col-sm-12">
          {!! Form::label('mobile_2', __( 'shipping::lang.mobile_2' ) . ':*') !!}
          {!! Form::text('mobile_2', $driver->mobile_2, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.mobile_2'), 'id'
          => 'add_mobile_2']); !!}
        </div> 
        
        <div class="form-group col-sm-12">
          {!! Form::label('land_no', __( 'shipping::lang.land_no' ) . ':*') !!}
          {!! Form::text('land_no', $driver->land_no, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.land_no'), 'id'
          => 'add_land_no']); !!}
        </div> 
        <div class="form-group col-sm-12">
          {!! Form::label('opening_balance', __( 'shipping::lang.opening_balance' ) . ':*') !!}
          {!! Form::number('opening_balance', $driver->opening_balance, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.opening_balance'), 'id'
          => 'add_opening_balance']); !!}
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