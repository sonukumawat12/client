@php

    $vehicle_type = ['Type A' => 'Type A','Type B' => 'Type B' ];

@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\TripCategoryController@store'), 'method' =>
    'post', 'id' => 'trip_category_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.trip_category' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_datetime(date('Y-m-d H:i')), ['class' => 'form-control', 'readonly', 'placeholder' => __(
          'fleet::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name', __( 'fleet::lang.trip_category_name' ) . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.trip_category_name'), 'id'
          => 'trip_category_name']); !!}
        </div>
        <div class="form-group col-sm-12">
            {!! Form::label('vehicle_type', __( 'fleet::lang.vehicle_type' ) . ':*') !!}
              {!! Form::select('vehicle_type', $vehicle_type, null, ['class' => 'form-control select2', 'placeholder' =>
              __('fleet::lang.select_vehicle_type')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('amount_method', __( 'fleet::lang.amount_calculated_method' ) . ':*') !!}
          <div class="form-group col-sm-12">
              {!! Form::select('manual_entry', ['manual_entry' => 'Manual Entry', 'automatic' => 'Automatic'], null, ['class' => 'form-control', 'id' => 'manual_entry', 'placeholder' => 'Select Entry Way']) !!}
          </div>
        
          <div class="clearfix"></div>
          <div class="col-md-6">
            <label>
                <input type="radio" name="amount_method" class="amount_method" value="km_distance" id="radio_km_distance" checked>
                (Per km Rate) * (Distance)
            </label>
          </div>
          
          <div class="col-md-6">
            <label>
                <input type="radio" name="amount_method" class="amount_method" value="km_distance_qty" id="radio_km_distance_qty">
                (Per km Rate) * (Distance) * (Quantity)
            </label>
          </div>
        
          <div class="form-group col-sm-12" id="manual_entry_amount_field" style="display: block;">
            {!! Form::label('trip_amount', 'Trip Amount:') !!}
            {!! Form::text('trip_amount', null, ['class' => 'form-control', 'id' => 'trip_amount', 'placeholder' => 'Enter Trip Amount']) !!}
          </div>
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
document.addEventListener('DOMContentLoaded', function () {
    const manualEntryDropdown = document.getElementById('manual_entry');
    const radioKmDistance = document.getElementById('radio_km_distance');
    const radioKmDistanceQty = document.getElementById('radio_km_distance_qty');
    const manualEntryAmountField = document.getElementById('manual_entry_amount_field');
    const tripAmountInput = document.getElementById('trip_amount');

    // Function to toggle fields based on dropdown value
    function toggleFields() {
        if (manualEntryDropdown.value === 'manual_entry') {
            // Manual Entry selected
            radioKmDistance.disabled = true;
            radioKmDistanceQty.disabled = true;

            manualEntryAmountField.style.display = 'block'; // Show Trip Amount field
            tripAmountInput.required = true; // Make Trip Amount required
        } else if (manualEntryDropdown.value === 'automatic') {
            // Automatic selected
            radioKmDistance.disabled = false;
            radioKmDistanceQty.disabled = false;

            manualEntryAmountField.style.display = 'none'; // Hide Trip Amount field
            tripAmountInput.required = false; // Remove required attribute from Trip Amount
        }
    }

    // Restrict input to numbers and multiple decimals
    tripAmountInput.addEventListener('input', function (e) {
        const value = e.target.value;
        const regex = /^[0-9.]*$/; // Allow only numbers and decimals
        if (!regex.test(value)) {
            // Remove invalid characters
            e.target.value = value.replace(/[^0-9.]/g, '');
        }
    });

    // Bind event listener to dropdown
    manualEntryDropdown.addEventListener('change', toggleFields);

    // Set default state on page load
    toggleFields();
});

// Ensure JavaScript is executed after the modal is shown (if dynamically loaded)
    $(document).on('shown.bs.modal', function () {
        document.dispatchEvent(new Event('DOMContentLoaded'));
    });

</script>