<div class="modal-dialog modal-xl" role="document" style="height: 95vh; overflow-y: auto">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Fleet\Http\Controllers\FleetController@storeFuelDetails'), 'method' =>
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
      <h4 class="modal-title">@lang( 'fleet::lang.add_fuel_details' ) - {{$vehicle_number}}</h4>
    </div>
    
    <input type="hidden" name="fleet_id" value="{{$id}}">

    <div class="modal-body">
      <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('date', __( 'fleet::lang.date' )) !!} {!!
          Form::text('date', date('m/d/Y'), ['class' => 'form-control',
          'required', 'placeholder' => __( 'fleet::lang.date' ), 'id' =>
          'leads_date']); !!}
        </div>
      </div>
     
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('driver_id', __( 'fleet::lang.driver' )) !!} {!!
          Form::select('driver_id', $drivers, null, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' )]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('previous_odometer', __( 'fleet::lang.previous_odometer' )) !!}
            {!! Form::text('previous_odometer', $previous_odometer, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.previous_odometer' ), 'readonly' => 'readonly']) !!}
        </div>
    </div>

      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('current_odometer', __( 'fleet::lang.current_odometer' ))
          !!} {!! Form::text('current_odometer', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.current_odometer' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('fuel_type', __( 'fleet::lang.fuel_type' )) !!} {!!
          Form::select('fuel_type', $fuel_types, $fuel_type, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' )]); !!}
        </div>
      </div>
      
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('liters', __( 'fleet::lang.liters' ))
          !!} {!! Form::text('liters', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.liters' )]); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('price_per_liter', __( 'fleet::lang.price_per_liter' ))
          !!} {!! Form::text('price_per_liter', $currentPrice, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.price_per_liter' ), 'readonly' => 'readonly']); !!}
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('total_amount', __( 'fleet::lang.total_amount' ))
          !!} {!! Form::text('total_amount', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.total_amount' ), 'readonly' => 'readonly']); !!}
        </div>
      </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('expense_category_id', __( 'fleet::lang.expense' )) !!} 
            
            <select class="form-control select2" required name="expense_category_id" id="expense_category_id">
                <option value="">@lang('fleet::lang.please_select')</option>
                @foreach($expenses as $one)
                    <option value="{{$one->id}}" data-id="{{$one->expense_account}}" data-string="{{$one->expense_account_name}}">{{$one->name}}</option>
                @endforeach
            </select>
          </div>
        </div>


        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location').':*') !!} {!!
          Form::select('location_id', $business_locations, $default_location, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' )]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
            {!! Form::text('expense_account_text',null, ['class' =>
            'form-control', 'placeholder' => __('sale.expense_account'), 'disabled','id' => 'expense_account_text']) !!}
            
            <input type="hidden" required name="expense_account" id="expense_account">

          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('payee', 'Select Payee' . ':*') !!}
            {!! Form::text('payee', !empty($payee_name)?$payee_name: 'Payee Not Selected', ['class' =>
            'form-control', 'readonly']); !!}
          </div>
        </div>
        
        <div class="col-md-12">
            <h3 class="box-title">@lang('sale.add_payment')</h3>
            
            <div class="row">
                <div class="col-md-12 payment_row" data-row_id="0">
                  <div id="payment_rows_div">
                    @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                    <hr>
                  </div>
                </div>
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
  
  $(document).on('change','#expense_category_id', function() {
       
        var acc_name = $("#expense_category_id option:selected").data('string');
        var acc_id = $("#expense_category_id option:selected").data('id');
        
        $("#expense_account").val(acc_id);
        $("#expense_account_text").val(acc_name);
    });
  
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
