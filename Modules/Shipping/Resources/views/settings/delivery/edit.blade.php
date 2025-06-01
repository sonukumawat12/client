<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open([
            'url' => action('\Modules\Shipping\Http\Controllers\DeliveryController@update', $type->id),
            'method' => 'put',
            'id' => 'types_add_form',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.shipping_delivery')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {{-- {!! Form::label('added_date', __('shipping::lang.added_date') . ':*') !!}
                    {!! Form::text('added_date', @format_date(date('Y-m-d')), [
                        'class' => 'form-control',
                        'required',
                        'readonly',
                        'id' => 'added_date',
                        'placeholder' => __('shipping::lang.added_date'),
                    ]) !!} --}}
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
                    {!! Form::label('shipping_delivery', __('shipping::lang.shipping_delivery') . ':*') !!}
                    {!! Form::text('shipping_delivery', $type->shipping_delivery, [
                        'class' => 'form-control',
                        'placeholder' => __('shipping::lang.shipping_delivery'),
                        'id' => 'add_shipping_delivery',
                    ]) !!}
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
</script>
