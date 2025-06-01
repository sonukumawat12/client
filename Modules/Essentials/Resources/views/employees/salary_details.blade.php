<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_employee' )</h4>
    </div>

    <div class="modal-body">
        
        <div class="row">
    		<div class="form-group col-md-3">
	        	{!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
	          	{!! Form::text('name', $employee->name, ['class' => 'form-control','disabled', 'placeholder' => __( 'lang_v1.name' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-3">
	        	{!! Form::label('employee_no', __( 'essentials::lang.employee_no' ) . ':*') !!}
	          	{!! Form::text('employee_no', $employee->employee_no, ['class' => 'form-control','disabled', 'placeholder' => __( 'essentials.lang.employee_no' ), 'required','readonly']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-3">
	        	{!! Form::label('salary', __( 'essentials::lang.starting_salary' ) . ':*') !!}
	          	{!! Form::text('salary', $employee->salary, ['class' => 'form-control','disabled', 'placeholder' => __( 'essentials.lang.salary' ), 'required']); !!}
	      	</div>

	      	<div class="form-group col-md-3">
	        	{!! Form::label('salary', __( 'essentials::lang.current_salary' ) . ':*') !!}
	          	{!! Form::text('employee_no', $current_salary, ['class' => 'form-control','disabled', 'placeholder' => __( 'essentials.lang.employee_no' ), 'required','readonly']); !!}
	      	</div>
	      	
    	</div>
    	
    	<hr>
        
        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'post_salary_details'], [$employee->id]), 'method' => 'post', 'id' => 'add_employee_form' ]) !!}
    	<div class="row">
    		<div class="form-group col-md-3">
	        	{!! Form::label('salary', __( 'essentials::lang.new_salary' ) . ':*') !!}
	          	{!! Form::text('salary', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.new_salary' ), 'required']); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-3">
	        	{!! Form::label('applicable_date', __( 'essentials::lang.applicable_date' ) . ':*') !!}
	          	{!! Form::date('applicable_date', null, ['class' => 'form-control', 'placeholder' => __( 'essentials.lang.applicable_date' ), 'required']); !!}
	      	</div>
	      	
	      	<input type="hidden" name="current_salary" value="{{$current_salary}}">
	      	
	      	<div class="form-group col-md-6">
	      	    <br><button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
	      	</div>

    	</div>
    	
    	{!! Form::close() !!}
    	
    	<hr>
    	
    	<div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.salary_history' )])
                    
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="salary_history_table">
                            <thead>
                                <tr>
                                    <th>@lang( 'essentials::lang.date_changed' )</th>
                                    <th>@lang( 'essentials::lang.current_salary' )</th>
                                    <th>@lang( 'essentials::lang.new_salary' )</th>
                                    <th>@lang( 'essentials::lang.salary_increased' )</th>
                                    <th>@lang( 'essentials::lang.applicable_date' )</th>
                                    <th>@lang( 'essentials::lang.added_by' )</th>
                                    <th>@lang( 'messages.action' )</th>
                                   
                                </tr>
                            </thead>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>
    </div>
    

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $(document).ready(function() {
            salary_history_table = $('#salary_history_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'list_salary_history'], ['id' => $employee->id])}}",
                    "data" : function(d) {
                        
                    }
                },
                
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'current_salary', name: 'current_salary'},
                    { data: 'new_salary', name: 'new_salary' },
                    { data: 'salary_increased', name: 'salary_increased'},
                    { data: 'applicable_date', name: 'applicable_date'},
                    { data: 'username', name: 'username'},
                    { data: 'action', name: 'action' },
                    
                ],
            });
        });

</script>