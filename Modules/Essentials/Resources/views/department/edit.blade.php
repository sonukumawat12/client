<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsDepartmentController::class, 'update'], [$department->id]), 'method' => 'put', 'id' => 'edit_department_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'essentials::lang.edit_department' )</h4>
        </div>

        <div class="modal-body">

            <div class="form-group">
                {!! Form::label('name', __('essentials::lang.department') . ':*') !!}
                {!! Form::text('name', $department->name, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.department')]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('short_code', __('essentials::lang.department_id') . ':*') !!}
                {!! Form::text('short_code', $department->short_code, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.department_id')]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('description', __('lang_v1.description') . ':*') !!}
                {!! Form::textarea('description', $department->description, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.description'), 'rows' => 3]) !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->






-->
