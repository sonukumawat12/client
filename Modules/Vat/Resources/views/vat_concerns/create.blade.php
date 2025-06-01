<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatConcernController@store'), 'method' => 'post', 'id' =>
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
                        {!! Form::label('line_1', __( 'vat::lang.line_1' ) . ':*') !!}
                        {!! Form::text('line_1', null , ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.line_1' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('line_2', __( 'vat::lang.line_2' ) . ':*') !!}
                        {!! Form::text('line_2', null, ['class' => 'form-control',  'placeholder' => __(
                        'vat::lang.line_2' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('line_3', __( 'vat::lang.line_3' ) . ':*') !!}
                        {!! Form::text('line_3', null, ['class' => 'form-control', 'placeholder' => __(
                        'vat::lang.line_3' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('line_4', __( 'vat::lang.line_4' ) . ':*') !!}
                        {!! Form::text('line_4', null, ['class' => 'form-control',  'placeholder' => __(
                        'vat::lang.line_4' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('line_5', __( 'vat::lang.line_5' ) . ':*') !!}
                        {!! Form::text('line_5', null, ['class' => 'form-control',  'placeholder' => __(
                        'vat::lang.line_5' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', __( 'vat::lang.status' ) . ':*') !!}
                        {!! Form::select('status',['1' => __('vat::lang.active'),'0' => __('vat::lang.inactive')], null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
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

  