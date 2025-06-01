
@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\DriverController@store'), 'method' =>
    'post', 'id' => 'driver_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.driver' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'fleet::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.joined_date' )]); !!}
        </div>
         <div class="col-md-12 text-center">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('hrm_enabled', '1', false,
                    [ 'class' => 'input-icheck','id' => 'hrm_enabled']); !!} {{ __( 'fleet::lang.hrm_enabled' ) }}
                </label>
            </div>
        </div>
        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('department', __( 'essentials::lang.department' ) .":") !!}
          {!! Form::select('department', $departments, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>

        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('designation', __( 'essentials::lang.designation' ) .":") !!}
          {!! Form::select('designation', [], null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('employee_select', __( 'fleet::lang.driver_name' ) .":") !!}
          {!! Form::select('employee_select', [], null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('employee_no', __( 'fleet::lang.employee_no' ) . ':*') !!}
          {!! Form::text('employee_no', $employee_no, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.employee_no'), 'id'
          => 'driver_employee_no', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('driver_name', __( 'fleet::lang.driver_name' ) . ':*') !!}
          {!! Form::text('driver_name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.driver_name'), 'id'
          => 'add_driver_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('nic_number', __( 'fleet::lang.nic_number' ) . ':*') !!}
          {!! Form::text('nic_number', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.nic_number'), 'id'
          => 'driver_nic_number']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('dl_number', __( 'fleet::lang.dl_number' ) . ':*') !!}
          {!! Form::text('dl_number', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.dl_number'), 'id'
          => 'dl_number']); !!}
        </div> 
        <div class="form-group col-sm-12">
          {!! Form::label('dl_type', __( 'fleet::lang.dl_type' ) . ':*') !!}
          {!! Form::text('dl_type', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.dl_type'), 'id'
          => 'dl_type']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('expiry_date', __( 'fleet::lang.expiry_date' ) . ':*') !!}
          {!! Form::text('expiry_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'placeholder' => __(
          'fleet::lang.expiry_date' )]); !!}
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
 $('#joined_date').datepicker('setDate', new Date());
$("#expiry_date").datepicker();
 
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