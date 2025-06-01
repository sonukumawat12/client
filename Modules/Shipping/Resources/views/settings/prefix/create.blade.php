<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open([
            'url' => action('\Modules\Shipping\Http\Controllers\PrefixController@store'),
            'method' => 'post',
            'id' => 'types_add_form',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.prefix')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {!! Form::label('added_date', __('shipping::lang.added_date') . ':*') !!}
                    {!! Form::text('added_date', @format_date(date('Y-m-d')), [
                        'class' => 'form-control',
                        'required',
                        'readonly',
                        'id' => 'added_date',
                        'placeholder' => __('shipping::lang.added_date'),
                    ]) !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('starting_no', __('shipping::lang.starting_no') . ':*') !!}
                    {!! Form::text('starting_no', null, [
                        'class' => 'form-control',
                        'placeholder' => __('shipping::lang.starting_no'),
                        'id' => 'add_starting_no',
                    ]) !!}
                </div>
                
                
                <div class="form-group col-sm-12">
                  {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) .":") !!}
                  {!! Form::select('shipping_mode', $shipping_modes, null, ['class' => 'form-control select2', 'required', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('prefix', __('shipping::lang.prefix') . ':*') !!}
                    {!! Form::text('prefix', null, [
                        'class' => 'form-control',
                        'placeholder' => __('shipping::lang.prefix'),
                        'required',
                        'id' => 'add_prefix',
                    ]) !!}
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('status', __('shipping::lang.status') . ':*') !!}<br>
                    {!! Form::checkbox('status', 1, false, ['class' => 'input-icheck']) !!} {{ __('shipping::lang.status') }}
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
