<div class="modal-dialog modal-xl" role="document" style="height: 95vh; overflow-y: auto">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Fleet\Http\Controllers\FleetController@updateFuelDetails',[$id]), 'method' =>
    'post',  'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'fleet::lang.add_fuel_details' )</h4>
    </div>
    

    <div class="modal-body">
      <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('date', __( 'fleet::lang.date' )) !!} {!!
          Form::text('date', date('m/d/Y',strtotime($fuelDetail->date_of_operation)), ['class' => 'form-control',
          'required', 'placeholder' => __( 'fleet::lang.date' ), 'id' =>
          'leads_date']); !!}
        </div>
      </div>
     
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('driver_id', __( 'fleet::lang.driver' )) !!} {!!
          Form::select('driver_id', $drivers, $fuelDetail->driver_id, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' )]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('previous_odometer', __( 'fleet::lang.previous_odometer' ))
          !!} {!! Form::text('previous_odometer', $fuelDetail->previous_odometer, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.previous_odometer' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('current_odometer', __( 'fleet::lang.current_odometer' ))
          !!} {!! Form::text('current_odometer', $fuelDetail->current_odometer, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.current_odometer' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('fuel_type', __( 'fleet::lang.fuel_type' )) !!} {!!
          Form::select('fuel_type', $fuel_types, $fuelDetail->fuel_type, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' )]); !!}
        </div>
      </div>
      
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('liters', __( 'fleet::lang.liters' ))
          !!} {!! Form::text('liters', $fuelDetail->liters, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.liters' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('price_per_liter', __( 'fleet::lang.price_per_liter' ))
          !!} {!! Form::text('price_per_liter', $fuelDetail->price_per_liter, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.price_per_liter' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('total_amount', __( 'fleet::lang.total_amount' ))
          !!} {!! Form::text('total_amount', $fuelDetail->total_amount, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.total_amount' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('fuel_cost', __( 'fleet::lang.fuel_cost' ))
          !!} {!! Form::text('fuel_cost', $fuelDetail->fuel_cost, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.fuel_cost' )]); !!}
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



<script>
  
  $("#modal_location_id option:eq(1)").attr("selected", true);
  $("#leads_date").datepicker({
    format: "mm/dd/yyyy",
  });
  $(".select2").select2();
  
  $(document).ready(function() {
    $('#liters, #price_per_liter').on('change', function() {
        var liters = parseFloat($('#liters').val());
        var pricePerLiter = parseFloat($('#price_per_liter').val());

        if (!isNaN(liters) && !isNaN(pricePerLiter)) {
            var totalAmount = (liters * pricePerLiter).toFixed(2); // Assuming you want 2 decimal places
            $('#total_amount').val(totalAmount);
        }
    });
    
    
    $("#fuel_type").change(function () {
        var id = $(this).val();
        $.ajax({
          type: "POST",
          url: "{{ action('\Modules\Fleet\Http\Controllers\FleetController@oneFuelType') }}",
          data: {
            id : id,
            _token: "{{ csrf_token() }}",
          },
          success: function (data) {
            $("#price_per_liter").val(data.fuel.price_per_litre);
            $("#price_per_liter").trigger('change');
          },
        });
    });
    
});


</script>
