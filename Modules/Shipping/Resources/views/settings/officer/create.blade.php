@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\CollectionOfficerController@store'), 'method' =>
    'post', 'id' => 'helper_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.helper' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'shipping::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'shipping::lang.joined_date' )]); !!}
        </div>
        <div class="col-md-12 text-center">
            @if($enable_hrm)
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('hrm_enabled', '1', true,
                    [ 'class' => 'input-icheck','id' => 'hrm_enabled']); !!} {{ __( 'shipping::lang.hrm_enabled' ) }}
                </label>
            </div>
            @else
              <div class="checkbox">
                <label>
                    {!! Form::checkbox('hrm_enabled', '1', false,
                    [ 'class' => 'input-icheck ','id' => 'hrm_enabled', 'disabled']) !!} {{ __( 'shipping::lang.hrm_enabled' ) }}
                </label>
            </div>
            @endif
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
          {!! Form::label('employee_select', __( 'shipping::lang.helper_name' ) .":") !!}
          {!! Form::select('employee_select', $employees, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('employee_no', __( 'shipping::lang.employee_no' ) . ':*') !!}
          {!! Form::text('employee_no', $employee_no, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.employee_no'), 'id'
          => 'add_employee_no']); !!}
        </div>
        <div class="form-group col-sm-12" id="add_helper_name_field">
          {!! Form::label('helper_name', __( 'shipping::lang.helper_name' ) . ':*') !!}
          {!! Form::text('helper_name', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.helper_name'), 'id'
          => 'add_helper_name']); !!}
        </div>
       <div class="form-group col-sm-12">
    {!! Form::label('nic_number', __( 'shipping::lang.nic_number' ) . ':*') !!}
    {!! Form::text('nic_number', null, [
        'class' => 'form-control',
        'placeholder' => __( 'shipping::lang.nic_number'),
        'id' => 'add_helper_nic_number'
    ]); !!}
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
 
 $('#hrm_enabled').change(function() {
    if ($(this).is(':checked')) {
        $(".employee_select").prop('hidden', false);
        
        $('#add_helper_nic_number').prop('readonly', true);
        $('#add_employee_no').prop('readonly', true);
        $('#add_helper_name').val("").prop('readonly', true);
        
        var selectedEmployee = $('#employee_select').val();
        if (selectedEmployee) {
            var values = JSON.parse(selectedEmployee);
            $('#add_helper_nic_number').val(values[1]);
        }
    } else {
        $('#employee_select').val("").change();
        $('#add_employee_no').val("{{$employee_no}}");
        $('#add_helper_name').val("");
        $('#add_helper_nic_number').val("");
        
        $(".employee_select").prop('hidden', true);
      
        $('#add_helper_nic_number').prop('readonly', false);
        $('#add_employee_no').prop('readonly', false);
        $('#add_helper_name').val("").prop('readonly', false);
        
    }
  });
  
  $('#employee_select').on('change', function() {
        var selectedValue = $(this).val();
        if(selectedValue){
            var values = JSON.parse(selectedValue);
            var selectedText = $(this).find('option:selected').text();
            $('#add_employee_no').val(values[0]);
            $('#add_helper_nic_number').val(values[1]);
            $('#add_helper_name').val(selectedText);
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
    
    $(document).ready(function() {
        $('#joined_date').datepicker('setDate', new Date());
        $('#hrm_enabled').trigger('change');
        $(".select2").select2();
    });
    
</script>