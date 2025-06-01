<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'store']), 'method' => 'post', 'id' => 'add_employee_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'essentials::lang.add_employee' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <!-- Department Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('department', __( 'essentials::lang.department' ) . ':*') !!}
                    {!! Form::select('department', $departments, null, ['class' => 'form-control select2 department_input', 'placeholder' => __( 'essentials::lang.department' ),'required', 'id' => 'department_select' ]); !!}
                </div>

                <!-- Designation Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('designation', __( 'essentials::lang.designation' ) . ':*') !!}
                    {!! Form::select('designation', [], null, ['class' => 'form-control select2 designation_input', 'placeholder' => __( 'essentials::lang.designation' ),'required', 'id' => 'designation_select' ]); !!}
                </div>

                <!-- Probation Period Field -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_period', __( 'essentials::lang.probation_period' ) . ':*') !!}
                    {!! Form::number('probation_period', null, [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.probation_period' ),
                        'required',
                        'id' => 'probation_period',
                        'oninput' => 'calculateProbationEnd()' // Add the oninput event here
                    ]); !!}
                </div>

                <!-- Period Value Field (Read-only) -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_period_value', __( 'essentials::lang.period' ) . ':') !!}
                    {!! Form::text('probation_period_value', null, [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.period' ),
                        'readonly' => 'readonly',
                        'id' => 'probation_period_value'
                    ]); !!}
                </div>
                <div class="form-group col-md-6">
        {!! Form::label('date_joined', __( 'essentials::lang.date_joined' ) . ':*') !!}
        {!! Form::date('date_joined', null, [
            'class' => 'form-control',
            'placeholder' => __( 'essentials::lang.date_joined' ),
            'required',
            'id' => 'date_joined', // Add an ID for referencing in JavaScript
            'onblur' => 'calculateProbationEnd()' // Add the onblur event
        ]); !!}
    </div>

                <!-- Probation Ends Field (Read-only) -->
                <div class="form-group col-md-6">
                    {!! Form::label('probation_ends', __( 'essentials::lang.probation_ends' ) . ':*') !!}
                    {!! Form::date('probation_ends', null, [
                        'class' => 'form-control',
                        'placeholder' => __( 'essentials::lang.probation_ends' ),
                        'readonly' => 'readonly',
                        'id' => 'probation_ends'
                    ]); !!}
                </div>

               
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.name' ), 'required']); !!}
                </div>
                	<div class="form-group col-md-12">
					{!! Form::label('dob', __( 'lang_v1.dob' ) . ':*') !!}
					{!! Form::date('dob', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.dob' ), 'required', 'id' => 'dob']); !!}
				</div>

                <div class="form-group col-md-12">
                    {!! Form::label('nic', __( 'lang_v1.nic' ) . ':*') !!}
                    {!! Form::text('nic', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.nic' ), 'required']); !!}
                </div>
                     	<div class="form-group col-md-12">
	        	{!! Form::label('address', __( 'lang_v1.address' ) . ':*') !!}
	          	{!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.address' ), 'required']); !!}
	      	</div>
                <div class="form-group col-md-12">
                    {!! Form::label('employee_no', __( 'essentials::lang.employee_no' ) . ':*') !!}
                    {!! Form::text('employee_no', $employee_no, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.employee_no' ), 'required','readonly']); !!}
                </div>

                <!-- Other Fields (Salary, Note, Checkbox) -->
                <div class="form-group col-md-12">
                    {!! Form::label('salary', __( 'essentials::lang.salary' ) . ':*') !!}
                    {!! Form::text('salary', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.salary' ), 'required']); !!}
                </div>
          
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __( 'vat::lang.note' ) . ':*') !!}
                        {!! Form::textarea('note',null, ['class' => 'form-control', 'style' => 'width: 100%;', 'rows' => '3']); !!}
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <div class="checkbox">
                        <br />
                        <label>
                            {!! Form::checkbox('sales_target_applicable', 1, false, ['class' => 'input-icheck']); !!}
                            {{ __( 'essentials::lang.sales_target_applicable' ) }}
                        </label>
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
        $('#department_select, #designation_select').change(function(){
            if (!$('#department_select').val() || !$('#designation_select').val()){
                return;
            }
            calculateProbationEnd();
        });
    });
</script>