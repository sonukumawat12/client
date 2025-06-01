<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open([
            'url' => action('\Modules\Shipping\Http\Controllers\PriceController@update', $type->id),
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
                    {!! Form::label('shipping_package', __('shipping::lang.package') . ':*') !!}
                    
                    {!! Form::select('shipping_package', $shipping_packages, $type->shipping_package, ['class' => 'form-control select2', 'required', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('shipping_partner', __('shipping::lang.shipping_partner') . ':*') !!}
                    {!! Form::select('shipping_partner', $shipping_partners, $type->shipping_partner, ['class' => 'form-control select2', 'required', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12">
                  {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) .":") !!}
                  {!! Form::select('shipping_mode', $shipping_modes, $type->shipping_mode, ['class' => 'form-control select2', 'required', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
               <div class="form-group col-sm-12">
                    {!! Form::label('constant_value', __('shipping::lang.constant_value') . ':*') !!}
                    {!! Form::text('constant_value', $type->constant_value, [
                        'class' => 'form-control',
                        'placeholder' => __('shipping::lang.constant_value'),
                        'required',
                        'id' => 'add_constant_value',
                    ]) !!}
                </div>

                <div class="form-group col-sm-12">

                    {!! Form::label('fixed_price', __('shipping::lang.price_only') . ':*') !!}

                    {!! Form::select('fixed_price', [__('shipping::lang.per_kg'),__('shipping::lang.fixed_price')],$type->fixed_price, ['class' => 'form-control select2 add_per_kg', 'required', 'placeholder' => __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::text('per_kg', $type->per_kg, [
                        'class' => 'form-control',
                        'placeholder' => __('shipping::lang.per_kg'),
                        'required',
                        'id' => 'add_per_kg',
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
