<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open([
            'url' => action('\Modules\Shipping\Http\Controllers\DimensionController@update', $type->id),
            'method' => 'put',
            'id' => 'types_add_form',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.shipping_delivery_days')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    
                    {!! Form::label('added_date', __('shipping::lang.added_date') . ':*') !!}
                    {!! Form::text('added_date', null, [
                        'class' => 'form-control',
                        'required',
                        'readonly',
                        'id' => 'added_date',
                        'placeholder' => __('shipping::lang.added_date'),
                    ]) !!}
                </div>
                <div class="form-group col-sm-12">
                  {!! Form::label('dimension_type', __( 'shipping::lang.dimension_type' ) .":") !!}
                  {!! Form::select('dimension_type', $dimension_type, $type->dimension_type, ['class' => 'form-control select2', 'required', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12 to_hide Weight" hidden>
                  {!! Form::label('weight', __( 'shipping::lang.weight' ) .":") !!}
                  {!! Form::select('weight', $weight, $type->weight, ['class' => 'form-control select2', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12 to_hide Length" hidden>
                  {!! Form::label('length', __( 'shipping::lang.length' ) .":") !!}
                  {!! Form::select('length', $length, $type->length, ['class' => 'form-control select2',  'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12 to_hide Width" hidden>
                  {!! Form::label('width', __( 'shipping::lang.width' ) .":") !!}
                  {!! Form::select('width', $length, $type->width, ['class' => 'form-control select2', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12 to_hide Height" hidden>
                  {!! Form::label('height', __( 'shipping::lang.height' ) .":") !!}
                  {!! Form::select('height', $length, $type->height, ['class' => 'form-control select2',  'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('status', __('shipping::lang.status') . ':*') !!}<br>
                    {!! Form::checkbox('status', 1, !empty($type->status) ? true : false, ['class' => 'input-icheck']) !!} {{ __('shipping::lang.status') }}
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#added_date').datepicker('setDate', new Date());
    
    $(document).on("change", "#dimension_type", function() {
        var value = $(this).val();
        $(".to_hide").hide();
        $("."+value).show();
    });
</script>
