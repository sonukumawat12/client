<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatBankDetailController@store'), 'method' => 'post', 'id' =>
        'transfer_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('bank_name', __( 'vat::lang.bank_name' ) . ':*') !!}
                        {!! Form::text('bank_name', null , ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.bank_name' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('bank_branch', __( 'vat::lang.bank_branch' ) . ':*') !!}
                        {!! Form::text('bank_branch', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.bank_branch' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('account_number', __( 'vat::lang.account_number' ) . ':*') !!}
                        {!! Form::text('account_number', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.account_number' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('account_name', __( 'vat::lang.account_name' ) . ':*') !!}
                        {!! Form::text('account_name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.account_name' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', __( 'vat::lang.status' ) . ':*') !!}
                        {!! Form::select('status',['1' => __('vat::lang.active'),'0' => __('vat::lang.inactive')], null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('special_instructions', __( 'vat::lang.special_instructions' ) . ':*') !!}
                        {!! Form::text('special_instructions', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                        'vat::lang.special_instructions' ), 'style' => 'width: 100%;']); !!}
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

  