<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\RecipientController@update', $driver->id), 'method' =>
    'put', 'id' => 'driver_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.recipient' )</h4>
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
          {!! Form::label('postal_code', __( 'shipping::lang.postal_code' ) . ':*') !!}
          {!! Form::text('postal_code', $driver->postal_code, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.postal_code'), 'id'
          => 'add_postal_code']); !!}
        </div> 
        
        <div class="form-group col-sm-12">
          {!! Form::label('landmarks', __( 'shipping::lang.landmarks' ) . ':*') !!}
          {!! Form::text('landmarks', $driver->landmarks, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.landmarks'), 'id'
          => 'add_landmarks']); !!}
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
  $('#driver_add_form').on('submit', function(event) {
      let isValid = true;

      // Iterate through all required fields
      $('#driver_add_form .form-group').each(function() {
          const input = $(this).find('input, select, textarea');
          if (input.val().trim() === "") {
              isValid = false;
              $(this).addClass('has-error');
              if (!$(this).find('.error-message').length) {
                  $(this).append('<span class="error-message" style="color: red;">Required to Fill</span>');
              }
          } else {
              $(this).removeClass('has-error');
              $(this).find('.error-message').remove();
          }
      });

      if (!isValid) {
          event.preventDefault();
          toastr.error('Please fill all required fields.');
      }
  });

  // Remove error highlight on input
  $('#driver_add_form input, #driver_add_form select, #driver_add_form textarea').on('input change', function() {
      const formGroup = $(this).closest('.form-group');
      if ($(this).val().trim() !== "") {
          formGroup.removeClass('has-error');
          formGroup.find('.error-message').remove();
      }
  });
 $('#joined_date').datepicker('setDate', '{{@format_date($driver->joined_date)}}');
</script>