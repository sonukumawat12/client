<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\MPCS\Http\Controllers\FormsSettingController::class, 'mpcs16Update'], [$settings->id]), 'method' => 'post', 'id' => 'update_16a_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.edit_form_16_a_form_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.date')</label>
                        {!! Form::date('datepicker', date('Y-m-d', strtotime($settings->date)), [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.date_and_time' ),
                            'required',
                            'id' => 'datepicker',
                            'disabled'
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.time')</label>
                        <div class="input-group">
                            <input class="form-control timepicker" id="time" name="time" type="time" value="{{ $settings->time }}" readonly>
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>            

                <!-- Form Starting Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.form_starting_number') <span class="required" aria-required="true">*</span></label>
                        {!! Form::text('starting_number', $settings->starting_number, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.form_starting_number' ),
                            'required',
                            'id' => 'form_starting_number'
                        ]); !!}
                    </div>
                </div>

                <!-- Ref Previous Form Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.ref_previous_form_number') <span class="required" aria-required="true">*</span></label>
                        {!! Form::text('ref_pre_form_number', $settings->ref_pre_form_number, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.ref_previous_form_number' ),
                            'required',
                            'id' => 'ref_previous_form_number',
                        ]); !!}
                    </div>
                </div>

                <!-- Total Sale up to Previous Day -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.total_sale_up_to_previous_day') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('total_purchase_price_with_vat', $settings->total_purchase_price_with_vat, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.total_sale_up_to_previous_day' ),
                            'required',
                            'id' => 'total_sale_up_to_previous_day'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Cash Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cash_sale') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('total_sale_price_with_vat', $settings->total_sale_price_with_vat, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_cash_sale' ),
                            'required',
                            'id' => 'previous_day_cash_sale'
                        ]); !!}
                    </div>
                </div>
               
                <!-- Other fields remain unchanged -->

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
