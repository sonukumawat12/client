<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsProbationController::class, 'update'], [$probation->id]), 'method' => 'put', 'id' => 'edit_probation_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('essentials::lang.edit_probation')</h4>
        </div>

        <div class="modal-body">

            <!-- Date and Time -->
            <div class="form-group">
                {!! Form::label('date_time', __('essentials::lang.date_time') . ':*') !!}
                {!! Form::datetimeLocal('date_time', $probation->date_time, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.date_time')]) !!}
            </div>

            <!-- Department Selection -->
            <div class="form-group">
                @php
                    $departmentOptions = ['0' => "All Departments"] + $departments->toArray();
                @endphp
                {!! Form::label('department_id', __('essentials::lang.department') . ':*') !!}
                {!! Form::select('department', $departmentOptions, $probation->department_id, ['class' => 'form-control', 'required']) !!}
            </div>

            <!-- Designation Selection -->
            <div class="form-group">
                @php
                    $designationOptions = ['0' => "All Designations"] + $designations->toArray();
                @endphp
                {!! Form::label('designation_id', __('essentials::lang.designation') . ':*') !!}
                {!! Form::select('designation', $designationOptions, $probation->designation_id, ['class' => 'form-control', 'required']) !!}
            </div>

            <!-- Period Dropdown -->
            <div class="form-group">
                {!! Form::label('period', __('essentials::lang.period') . ':*') !!}
                {!! Form::select('period', ['days' => 'days', 'months' => 'months'], $probation->period, ['class' => 'form-control', 'required']) !!}
            </div>

            <!-- Status Dropdown -->
            <div class="form-group">
                {!! Form::label('status', __('essentials::lang.status') . ':*') !!}
                {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], $probation->status, ['class' => 'form-control', 'required']) !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
