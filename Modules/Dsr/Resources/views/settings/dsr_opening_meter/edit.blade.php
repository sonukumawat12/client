<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        {!! Form::model($dsr_settings, ['url' => action('\Modules\Dsr\Http\Controllers\DsrSettingsController@update', $dsr_settings->id), 'method' => 'put', 'id' => 'dsr_ob_settings' ]) !!}
        <div class="modal-body">
            <div class="container">
                <div class="row">


                    <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('date_time', __('dsr::lang.date_time')) !!}
                                {!! Form::date('date_time', date('Y-m-d',strtotime($dsr_settings->date_time)), ['class' => 'form-control', 'placeholder' => __('dsr::lang.date_time'),'required']) !!}
                                <div class="text-danger" id="date_time-error"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('product_id', __('dsr::lang.product') . ':') !!}
                                {!! Form::select('product_id', $fuel_products, null, ['class' => 'form-control
                                select2',
                                'placeholder' => __('dsr::lang.all'), 'style' => 'width:100%']); !!}
                            </div>
                            <div class="text-danger" id="product_id-error"></div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('accumulative_sale', __('dsr::lang.accumulative_sale')) !!}
                                {!! Form::text('accumulative_sale', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.accumulative_sale')]) !!}
                             <div class="text-danger" id="accumulative_sale-error"></div>
                        </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('accumulative_purchase', __('dsr::lang.accumulative_purchase')) !!}
                                    {!! Form::text('accumulative_purchase', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.accumulative_purchase')]) !!}
                                    <div class="text-danger" id="accumulative_purchase-error"></div>
                                </div>
                        </div>


                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="update_dsr_settings">@lang('messages.update')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>