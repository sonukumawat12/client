<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsDesignationController::class, 'update'], [$designation->id]), 'method' => 'put', 'id' => 'edit_designation_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('essentials::lang.edit_designation')</h4>
        </div>

        <div class="modal-body">

            <div class="form-group">
                {!! Form::label('name', __('essentials::lang.designation') . ':*') !!}
                {!! Form::text('name', $designation->name, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.designation')]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('department_id', __('essentials::lang.department') . ':*') !!}
                {!! Form::select('department_id', $departments, $designation->department_id, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.select_department')]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('description', __('lang_v1.description') . ':') !!}
                {!! Form::textarea('description', $designation->description, ['class' => 'form-control', 'placeholder' => __('lang_v1.description'), 'rows' => 3]) !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
