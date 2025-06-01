@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\HelperController@store'), 'method' =>
    'post', 'id' => 'helper_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.helper' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'fleet::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.joined_date' )]) !!}
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
          {!! Form::label('employee_select', __( 'fleet::lang.helper_name' ) .":") !!}
          {!! Form::select('employee_select', $employees, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('employee_no', __( 'fleet::lang.employee_no' ) . ':*') !!}
          {!! Form::text('employee_no', $employee_no, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.employee_no'), 'id'
          => 'add_employee_no', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-12 helper_name_text">
          {!! Form::label('helper_name', __( 'fleet::lang.helper_name' ) . ':*') !!}
          {!! Form::text('helper_name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.helper_name'), 'id'
          => 'add_helper_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('nic_number', __( 'fleet::lang.nic_number' ) . ':*') !!}
          {!! Form::text('nic_number', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.nic_number'), 'id'
          => 'add_helper_nic_number']); !!}
        </div>
        
        <div class="form-group col-sm-12">
            {!! Form::label('pass_no', __( 'fleet::lang.pass_no' ) . ':') !!}
           {!! Form::text('pass_no', null, ['class' => 'form-control', 'placeholder' => __('fleet::lang.pass_no'), 'required']); !!}
      
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

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#joined_date').datepicker('setDate', new Date());
 
 $(".select2").select2();
 $(document).ready(function() {
  var is_hr_module = @json($is_hr_module);
  if(is_hr_module == 1){
    $('#hrm_enabled').prop('disabled', false);
  }else{
    $('#hrm_enabled').prop('disabled', true);
  }
});
 $('#hrm_enabled').change(function() {
    if ($(this).is(':checked')) {
        $(".employee_select").prop('hidden', false);
        $('#add_employee_no').prop('disabled', true);
        $('#add_helper_nic_number').prop('disabled', true);
        $(".helper_name_text").prop('hidden', true);
    } else {
        $('#add_employee_no').prop('disabled', false);
        $('#add_helper_nic_number').prop('disabled', false);
        // $('#employee_select').val("").change();
        $('#employee_select').val("");
        $('#add_employee_no').val("{{$employee_no}}");
        $('#add_helper_name').val("");
        $('#add_helper_nic_number').val("");
        $(".helper_name_text").prop('hidden', false);
      $(".employee_select").prop('hidden', true);
    }
  });
  
  $('#employee_select').on('change', function() {
        var selectedValue = $(this).val();
        var values = JSON.parse(selectedValue);
        
        var selectedText = $(this).find('option:selected').text();
        
        if(selectedValue){
            $('#add_employee_no').val(values[0]);
            $('#add_helper_nic_number').val(values[1]);
            $('#add_helper_name').val(selectedText);
        }
        
    });
      
      
       $('#department').on('change', function() {
        var department_id = $(this).val();
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
</script>