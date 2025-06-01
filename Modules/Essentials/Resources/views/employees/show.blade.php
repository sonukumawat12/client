<div class="modal-dialog" role="document">
  <div class="modal-content">

    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.employee' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
	        	{!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
	          	{!! Form::text('name', $employee->name, ['class' => 'form-control', 'disabled', 'placeholder' => __( 'lang_v1.name' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('nic', __( 'lang_v1.nic' ) . ':*') !!}
	          	{!! Form::text('nic', $employee->nic, ['class' => 'form-control', 'disabled', 'placeholder' => __( 'lang_v1.nic' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('address', __( 'lang_v1.address' ) . ':*') !!}
	          	{!! Form::text('address', $employee->address, ['class' => 'form-control', 'disabled', 'placeholder' => __( 'lang_v1.address' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('dob', __( 'lang_v1.dob' ) . ':*') !!}
	          	{!! Form::date('dob', date('Y-m-d',strtotime($employee->dob)), ['class' => 'form-control', 'disabled', 'placeholder' => __( 'lang_v1.dob' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('employee_no', __( 'essentials::lang.employee_no' ) . ':*') !!}
	          	{!! Form::text('employee_no', $employee->employee_no, ['class' => 'form-control', 'disabled', 'placeholder' => __( 'essentials::lang.employee_no' ), 'required','readonly']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('salary', __( 'essentials::lang.salary' ) . ':*') !!}
	          	{!! Form::text('salary', $employee->salary, ['class' => 'form-control', 'disabled', 'placeholder' => __( 'essentials::lang.salary' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-6">
	        	{!! Form::label('date_joined', __( 'essentials::lang.date_joined' ) . ':*') !!}
		        	
		        {!! Form::date('date_joined', date('Y-m-d',strtotime($employee->date_joined)), ['class' => 'form-control', 'disabled', 'placeholder' => __( 'essentials::lang.date_joined' ), 'required' ]); !!}
		          	
	        	
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! Form::label('probation_ends', __( 'essentials::lang.probation_ends' ) . ':*') !!}
	        	{!! Form::date('probation_ends',date('Y-m-d',strtotime($employee->probation_ends)), ['class' => 'form-control', 'disabled', 'placeholder' => __( 'essentials::lang.probation_ends' ) ]); !!}
	        	
	      	</div>

	      	

	      	<div class="form-group col-md-12">
	        	{!! Form::label('department', __( 'essentials::lang.department' ) . ':') !!}
	          	{!! Form::select('department', $departments, $employee->department, ['class' => 'form-control select2 department_input', 'disabled', 'placeholder' => __( 'essentials::lang.department' ),'required' ]); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-12">
	        	{!! Form::label('designation', __( 'essentials::lang.designation' ) . ':') !!}
	          	{!! Form::select('designation', $designations, $employee->designation, ['class' => 'form-control designation_input select2', 'disabled', 'placeholder' => __( 'essentials::lang.designation' ),'required' ]); !!}
	      	</div>

	      	
    	</div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->