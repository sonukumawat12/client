<div class="modal fade" id="add_department_modal" tabindex="-1" role="dialog"
     aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsDepartmentController::class, 'store']), 'method' => 'post', 'id' => 'add_department_form']) !!}

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('essentials::lang.add_department')</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', __('essentials::lang.department') . ':*') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.department')]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('short_code', __('essentials::lang.department_id') . ':*') !!}
                    {!! Form::text('short_code', null, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.department_id')]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('description', __('lang_v1.description') . ':*') !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.description'), 'rows' => 3]) !!}
                </div>

                @if(!empty($parent_departments))
                    <div class="form-group">
                        {!! Form::label('parent_id', __('essentials::lang.select_parent_department') . ':') !!}
                        {!! Form::select('parent_id', $parent_departments, null, ['class' => 'form-control', 'placeholder' => __('essentials::lang.select_parent_department')]) !!}
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
