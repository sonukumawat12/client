@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\HelperController@update', $helper->id), 'method' =>
    'put', 'id' => 'helper_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.helper' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'fleet::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.joined_date' )]) !!}
        </div>
        <div class="col-md-12">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('hrm_enabled', '1', $helper->hrm_enabled == 1, [ 'class' => 'input-icheck', 'id' => 'hrm_enabled']) !!}
                    {{ __( 'fleet::lang.hrm_enabled' ) }}
                </label>
            </div>
        </div>

        
        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('department', __( 'essentials::lang.department' ) .":") !!}
          {!! Form::select('department', $departments, $helper->department, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]) !!}
        </div>

        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('designation', __( 'essentials::lang.designation' ) .":") !!}
          {!! Form::select('designation', [], $helper->designation, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]) !!}
        </div>
        <div class="form-group col-sm-12 employee_select" hidden>
          {!! Form::label('employee_select', __( 'fleet::lang.helper_name' ) .":") !!}
          {!! Form::select('employee_select', $employees,  $helper->employee_no, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('employee_no', __( 'fleet::lang.employee_no' ) . ':*') !!}
          {!! Form::text('employee_no', $helper->employee_no, ['class' => 'form-control', 'placeholder' => __(
          'fleet::lang.employee_no'), 'id'
          => 'update_employee_no', 'readonly']) !!}
        </div>
        <div class="form-group col-sm-12 helper_name_text">
          {!! Form::label('helper_name', __( 'fleet::lang.helper_name' ) . ':*') !!}
          {!! Form::text('helper_name', $helper->helper_name, ['class' => 'form-control', 'placeholder' => __(
          'fleet::lang.helper_name'), 'id'
          => 'helper_name']) !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('nic_number', __( 'fleet::lang.nic_number' ) . ':*') !!}
          {!! Form::text('nic_number', $helper->nic_number, ['class' => 'form-control', 'placeholder' => __(
          'fleet::lang.nic_number'), 'id'
          => 'update_nic_number']) !!}
        </div>
        <div class="form-group col-sm-12">
            {!! Form::label('pass_no', __( 'fleet::lang.pass_no' ) . ':') !!}
           {!! Form::text('pass_no', $helper->pass_no, ['class' => 'form-control', 'placeholder' => __('fleet::lang.pass_no'), 'required']); !!}
      
        </div>
        
        <div class="form-group col-sm-12">
            {!! Form::label('pass_expiry_date', __( 'fleet::lang.pass_expiry_date' ) . ':') !!}
           {!! Form::date('pass_expiry_date', $helper->pass_expiry_date, ['class' => 'form-control', 'placeholder' => __('fleet::lang.pass_expiry_date')]); !!}
      
        </div>
        <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('salary_expense_category', __( 'fleet::lang.salary_expense_category'
              )) !!} {!! Form::select('salary_expense_category', $expense_categories, $helper->salary_expense_category,
              ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'fleet::lang.please_select' ), 'id' => 'salary_expense_category']) !!}
            </div>
         </div>
         
         <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('advance_expense_category', __( 'fleet::lang.advance_expense_category'
              )) !!} {!! Form::select('advance_expense_category', $expense_categories, $helper->advance_expense_category,
              ['class' => 'form-control select2', 'required','placeholder' => __(
              'fleet::lang.please_select' ), 'id' => 'advance_expense_category']); !!}
            </div>
         </div>
         
         <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('bata_expense_category', __( 'fleet::lang.bata_expense_category'
              )) !!} {!! Form::select('bata_expense_category', $expense_categories, $helper->bata_expense_category,
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
  $('#joined_date').datepicker('setDate', '{{@format_date($helper->joined_date)}}');
  $(".select2").select2();
 
  $(document).ready(function() {
    var is_hr_module = @json($is_hr_module);
    if(is_hr_module == 1){
      $('#hrm_enabled').prop('disabled', false);
    }else{
      $('#hrm_enabled').prop('disabled', true);
    }
    if ($('#hrm_enabled').is(':checked')) {
        $(".employee_select").prop('hidden', false);
        $('#update_employee_no').prop('disabled', true);
        $('#update_nic_number').prop('disabled', true);
        $(".helper_name_text").prop('hidden', true);
    } else {
        $('#update_employee_no').prop('disabled', false);
        $('#update_nic_number').prop('disabled', false);
        $('#employee_select').val("");
        $('#add_helper_name').val("");
        $('#update_nic_number').val("");
        $(".helper_name_text").prop('hidden', false);
        $(".employee_select").prop('hidden', true);
    }

    $('#hrm_enabled').change(function() {
        if ($(this).is(':checked')) {
            debugger;
            $(".employee_select").prop('hidden', false);
            $('#update_employee_no').prop('disabled', true);
            $('#update_nic_number').prop('disabled', true);
            $(".helper_name_text").prop('hidden', true);
        } else {
            debugger;
            $('#update_employee_no').prop('disabled', false);
            $('#update_nic_number').prop('disabled', false);
            // Clear fields
            $('#employee_select').val("");
            $('#add_helper_name').val("");
            $('#update_nic_number').val("");
            $(".helper_name_text").prop('hidden', false);
            $(".employee_select").prop('hidden', true);
        }
    });
    
    // department
    debugger;
    var department_id = @json($helper->department);
    var designation = @json($helper->designation);
      if(department_id){
          $.ajax({
          method: 'POST',
          url: '/fleet-management/get-designation-by-department-id',
          dataType: 'html',
          data: { department_id: department_id },
          success: function(result) {
            if (result) {
              $('#designation').empty().append(result);
              $('#designation').val(designation).trigger('change');
            }
          },
        });
      }
      //designation
      var helperData = [
        {!! (int) $helper->employee_no !!},
        @json($helper->nic_number),
        @json($helper->helper_name)
      ];
      if(helperData){
          $.ajax({
          method: 'POST',
          url: '/hrm/designation-employees',
          dataType: 'html',
          data: { designation: designation },
          success: function(result) {
              
            if (result) {
              $('#employee_select').empty().append(result);
              $('#employee_select').val(JSON.stringify(helperData)).trigger('change');
            }
          },
        });
      }  
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
              $('#employee_select').val(JSON.stringify(helperData)).trigger('change');
            }
          },
        });
      } 
  });
  });
  $('#employee_select').on('change', function() {
        var selectedValue = $(this).val();
        var values = JSON.parse(selectedValue);
        
        var selectedText = $(this).find('option:selected').text();
        
        if(selectedValue){
            $('#update_employee_no').val(values[0]);
            $('#update_nic_number').val(values[1]);
            // $('#add_helper_name').val(selectedText);
        }
        
    });
  
</script>