
@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\FuelController@store'), 'method' =>
    'post', 'id' => 'fuel_type_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.fuel_type' )</h4>
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
          {!! Form::text('type', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.fuel_types'), 'id'
          => 'fuel_type']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('price_per_litre', __( 'fleet::lang.current_price' ) . ':*') !!}
          {!! Form::text('price_per_litre', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.price_per_litre'), 'id'
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
$("#date").datepicker();
 
 $(".select2").select2();
  $('#hrm_enabled').change(function() {
    if ($(this).is(':checked')) {
        $(".employee_select").prop('hidden', false);
    } else {
        // $('#employee_select').val("").change();
        $('#employee_select').val("");
        $('#driver_employee_no').val("{{$employee_no}}");
        $('#add_driver_name').val("");
        $('#driver_nic_number').val("");
      $(".employee_select").prop('hidden', true);
    }
  });

  
  $('#employee_select').on('change', function() {
        var selectedValue = $(this).val();
        var values = JSON.parse(selectedValue);
        
        var selectedText = $(this).find('option:selected').text();
        
        if(selectedValue){
            $('#driver_employee_no').val(values[0]);
            $('#driver_nic_number').val(values[1]);
            $('#add_driver_name').val(selectedText);
        }
        
    });
    
    
    $('#department').on('change', function() {
        var cat = $(this).val();
        
        if(cat){
           $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
              if (result) {
                $('#designation').empty().append(result);
              }
            },
          });
        }
        
    });
    
    $('#designation').on('change', function() {
        var cat = $(this).val();
        
        if(cat){
           $.ajax({
            method: 'POST',
            url: '/hrm/designation-employees',
            dataType: 'html',
            data: { designation: cat },
            success: function(result) {
               
              if (result) {
                $('#employee_select').empty().append(result);
              }
            },
          });
        }
        
    });
</script>