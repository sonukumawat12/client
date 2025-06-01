<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\FuelController@update', $fuel->id), 'method' =>
    'put', 'id' => 'fleet_fuel_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.edit_fuel_type' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'placeholder' => __(
          'fleet::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('type', __( 'fleet::lang.fuel_type' ) . ':*') !!}
          {!! Form::text('type', $fuel->type, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.fuel_types'), 'id'
          => 'fuel_type']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('price_per_litre', __( 'fleet::lang.current_price' ) . ':*') !!}
          {!! Form::text('price_per_litre', $fuel->price_per_litre, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.price_per_litre'), 'id'
          => 'price_per_litre']); !!}
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
 $('#date').datepicker('setDate', '{{@format_date($fuel->date)}}');
</script>