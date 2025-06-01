<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\SettlementController@updateMeterSale', $meter_sale->id), 'method' =>
        'post','id' =>'edit_settlement_date_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit_settlement_date' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('settlement_no', __( 'petro::lang.settlement_no' ) . ':*') !!}
                            {!! Form::text('settlement_no', $transaction->invoice_no, ['class' => 'form-control settlement_no', 'disabled',
                            'placeholder' => __(
                            'petro::lang.settlement_no' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pump_name', __( 'lang_v1.created_at' ) . ':*') !!}
                            <input type="datetime-local" name="created_at" required value="{{date('Y-m-d H:i',strtotime($meter_sale->created_at))}}" class="form-control">
                        </div>
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

   