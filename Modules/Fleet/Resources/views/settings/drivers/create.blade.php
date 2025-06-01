
@php
use Modules\HR\Entities\Employee;

$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\DriverController@store'), 'method' =>
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
         <div class="col-md-12">
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
        <div class="form-group col-sm-12 driver_name_div">
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
          {!! Form::date('expiry_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'placeholder' => __(
          'fleet::lang.expiry_date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
            {!! Form::label('pass_no', __( 'fleet::lang.pass_no' ) . ':') !!}
           {!! Form::text('pass_no', null, ['class' => 'form-control', 'placeholder' => __('fleet::lang.pass_no')]); !!}
      
        </div>
        
        <div class="form-group col-sm-12">
            {!! Form::label('pass_expiry_date', __( 'fleet::lang.pass_expiry_date' ) . ':') !!}
           {!! Form::date('pass_expiry_date', null, ['class' => 'form-control', 'placeholder' => __('fleet::lang.pass_expiry_date')]); !!}
      
        </div>
        
        
        <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('salary_expense_category', __( 'fleet::lang.salary_expense_category'
              )) !!} {!! Form::select('salary_expense_category', $expense_categories, null,
              ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'fleet::lang.please_select' ), 'id' => 'salary_expense_category']); !!}
            </div>
         </div>
         
         <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('advance_expense_category', __( 'fleet::lang.advance_expense_category'
              )) !!} {!! Form::select('advance_expense_category', $expense_categories, null,
              ['class' => 'form-control select2', 'required','placeholder' => __(
              'fleet::lang.please_select' ), 'id' => 'advance_expense_category']); !!}
            </div>
         </div>
         <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('bata_expense_category', __( 'fleet::lang.bata_expense_category'
              )) !!} {!! Form::select('bata_expense_category', $expense_categories, null,
              ['class' => 'form-control select2', 'required','placeholder' => __(
              'fleet::lang.please_select' ), 'id' => 'bata_expense_category']); !!}
            </div>
         </div>

      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div>


<script>
 $('#joined_date').datepicker('setDate', new Date());
// $("#expiry_date").datepicker();
 
$(document).ready(function() {
  var is_hr_module = @json($is_hr_module);
  if(is_hr_module == 1){
    $('#hrm_enabled').prop('disabled', false);
  }else{
    $('#hrm_enabled').prop('disabled', true);
  }
});
 $(".select2").select2();
  $('#hrm_enabled').change(function() {
    if ($(this).is(':checked')) {
      debugger;
        $(".employee_select").prop('hidden', false);
        $(".driver_name_div").prop('hidden', true);
        $('#driver_nic_number').prop('disabled', true);
    } else {
        // $('#employee_select').val("").change();
        $('#employee_select').val("");
        $('#driver_employee_no').val("{{$employee_no}}");
        $('#add_driver_name').val("");
        $('#driver_nic_number').val("");
      $(".employee_select").prop('hidden', true);
      $(".driver_name_div").prop('hidden', false);
      $('#driver_nic_number').prop('disabled', false);
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
      var department_id = $(this).val();
      debugger;
      if(department_id){
          $.ajax({
          method: 'POST',
          url: '/fleet-management/get-designation-by-department-id',
          dataType: 'html',
          data: { department_id: department_id },
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
    
//     $('#driver_add_form').submit(function(event) {
//     event.preventDefault(); // Prevent form from submitting normally

//     var formData = $(this).serialize(); // Serialize form data
//     console.log('Form data being sent: ', formData); // Log the serialized data

//     $.ajax({
//         url: action('\Modules\Fleet\Http\Controllers\DriverController@store'), // Ensure this matches your backend endpoint
//         method: 'POST',
//         data: formData,
//         success: function(response) {
//         // Handle success response
//         console.log('Form submitted successfully:', response);
//     },
//         error: function(xhr, status, error) {
//         console.log("Error during form submission:", error);
//         console.log("XHR Status:", xhr.status);
//         console.log("Response Text:", xhr.responseText);  // Log response
//         console.log("Response Headers:", xhr.getAllResponseHeaders());  // Log headers
        
//         let errorMessage = "An unexpected error occurred.";

//         // Handle specific error codes
//         if (xhr.status === 404) {
//             errorMessage = "Requested resource not found.";
//         } else if (xhr.status === 500) {
//             errorMessage = "Internal server error.";
//         } else if (xhr.status === 422) {
//             errorMessage = "Unprocessable entity - check input fields.";
//         }

//         // Display the error message
//         alert(errorMessage);
//     }
//     });
// });

</script>