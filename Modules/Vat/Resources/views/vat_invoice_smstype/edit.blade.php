<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatInvoiceSmsTypeController@update',[$data->id]), 'method' => 'put', 'id' =>
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
                        {!! Form::label('date_time', __( 'vat::lang.date_time' ) . ':*') !!}
                        {!! Form::input('datetime-local', 'date_time', $data->date_time, [
                            'class' => 'form-control',
                            'required' => 'required',
                            'placeholder' => __('vat::lang.date_time'),
                            'style' => 'width: 100%;'
                        ]) !!}

                    </div>
                </div>
                    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sms_to', __( 'vat::lang.sms_to' ) . ':*') !!}
                        {!! Form::select('sms_to',['credit_customers' => __('vat::lang.credit_customers'),'all_payment_customers' => __('vat::lang.all_payment_customers') ], $data->sms_to, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;']); !!}
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

  