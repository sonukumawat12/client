<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatPrefixController@update',[$data->id]), 'method' => 'put', 'id' =>
        'transfer_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add_prefix' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('prefix', __( 'vat::lang.prefix' ) ) !!}
                        {!! Form::text('prefix', $data->prefix , ['class' => 'form-control',  'placeholder' => __(
                        'vat::lang.prefix' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('starting_no', __( 'vat::lang.starting_no' ) . ':*') !!}
                        {!! Form::text('starting_no', $data->starting_no, ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.starting_no' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                 
                
            </div>
           
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

  