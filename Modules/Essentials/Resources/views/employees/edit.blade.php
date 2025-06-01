<div class="modal-dialog" role="document"  data-backdrop="static" data-keyboard="false">
    <div class="modal-content">

        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'update'], [$employee->id]), 'method' => 'put', 'id' => 'add_employee_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'essentials::lang.edit_employee' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <!-- Department Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('department', __( 'essentials::lang.department' ) . ':*') !!}
                    {!! Form::select('department', $departments, $employee->department, ['class' => 'form-control select2 department_input', 'placeholder' => __( 'essentials::lang.department' ), 'required', 'id' => 'department_select' ]); !!}
                </div>

                <!-- Designation Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('designation', __( 'essentials::lang.designation' ) . ':*') !!}
                    {!! Form::select('designation', $designations, $employee->designation, ['class' => 'form-control select2 designation_input', 'placeholder' => __( 'essentials::lang.designation' ), 'required', 'id' => 'designation_select' ]); !!}
                </div>

                <!-- Probation Period Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_period', __( 'essentials::lang.probation_period' ) . ':*') !!}
                    {!! Form::number('probation_period', null, [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.probation_period' ),
                        'id' => 'probation_period',
                        'oninput' => 'calculateProbationEnd()' 
                    ]); !!}
                </div>

                <!-- Period Value Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_period_value', __( 'essentials::lang.period' ) . ':') !!}
                    {!! Form::text('probation_period_value', null, [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.period' ),
                        'readonly' => 'readonly',
                        'id' => 'probation_period_value'
                    ]); !!}
                </div>

                <!-- Date Joined Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('date_joined', __( 'essentials::lang.date_joined' ) . ':*') !!}
                    {!! Form::date('date_joined', date('Y-m-d', strtotime($employee->date_joined)), [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.date_joined' ),
                        'required',
                        'id' => 'date_joined',
                        'onblur' => 'calculateProbationEnd()'
                    ]); !!}
                </div>

                <!-- Probation Ends Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_ends', __( 'essentials::lang.probation_ends' ) . ':*') !!}
                    {!! Form::date('probation_ends', date('Y-m-d', strtotime($employee->probation_ends)), [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.probation_ends' ),
                        'readonly' => 'readonly',
                        'id' => 'probation_ends'
                    ]); !!}
                </div>

                <!-- Name Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
                    {!! Form::text('name', $employee->name, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.name' ), 'required']); !!}
                </div>

                <!-- DOB Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('dob', __( 'lang_v1.dob' ) . ':*') !!}
                    {!! Form::date('dob', date('Y-m-d', strtotime($employee->dob)), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.dob' ), 'required']); !!}
                </div>

                <!-- NIC Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('nic', __( 'lang_v1.nic' ) . ':*') !!}
                    {!! Form::text('nic', $employee->nic, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.nic' ), 'required']); !!}
                </div>

                <!-- Address Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('address', __( 'lang_v1.address' ) . ':*') !!}
                    {!! Form::text('address', $employee->address, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.address' ), 'required']); !!}
                </div>

                <!-- Employee Number Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('employee_no', __( 'essentials::lang.employee_no' ) . ':*') !!}
                    {!! Form::text('employee_no', $employee->employee_no, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.employee_no' ), 'required', 'readonly']); !!}
                </div>

                <!-- Salary Field -->
                <div class="form-group col-md-12">
                    {!! Form::label('salary', __( 'essentials::lang.salary' ) . ':*') !!}
                    {!! Form::text('salary', $employee->salary, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.salary' ), 'required']); !!}
                </div>

                <!-- Note Field -->
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __( 'vat::lang.note' ) . ':*') !!}
                        {!! Form::textarea('note', $employee->note, ['class' => 'form-control', 'style' => 'width: 100%;', 'rows' => '3']); !!}
                    </div>
                </div>

                <!-- Sales Target Checkbox -->
                <div class="form-group col-md-12">
                    <div class="checkbox">
                        <br />
                        <label>
                            {!! Form::checkbox('sales_target_applicable', 1, $employee->sales_target_applicable, [ 'class' => 'input-icheck']); !!}
                            {{ __( 'essentials::lang.sales_target_applicable' ) }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div>
</div>
<script>
    $(document).ready(function () {
        $('.department_input').change(function () {
            var departmentId = $(this).val();

            if (departmentId) {
                $.ajax({
                    url: '{{ route('hrm.get.designations') }}',
                    type: 'POST',
                    data: {
                        department_id: departmentId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        $('.designation_input').empty().append('<option value="">{{ __("essentials::lang.select_designation") }}</option>');
                        $.each(data, function (key, value) {
                            $('.designation_input').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#designation_input').empty().append('<option value="">{{ __("essentials::lang.select_designation") }}</option>');
            }
        });
    });
</script>