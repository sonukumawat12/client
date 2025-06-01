<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\MPCS\Http\Controllers\Form9ASettingsController::class, 'update'], [$settings->id]), 'method' => 'post', 'id' => 'update_9a_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.edit_form_9_a_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.date_and_time')</label>
                        {!! Form::date('datepicker', date('Y-m-d', strtotime($settings->date)), [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.date_and_time' ),
                            'required',
                            'id' => 'datepicker'
                        ]) !!}
                    </div>
                </div>

                <!-- Form Starting Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.form_starting_number') <span class="required" aria-required="true">*</span></label>
                        {!! Form::text('form_starting_number', $settings->starting_number, [
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
                        {!! Form::text('ref_previous_form_number', $settings->ref_pre_form_number, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.ref_previous_form_number' ),
                            'required',
                            'id' => 'ref_previous_form_number'
                        ]); !!}
                    </div>
                </div>

                <!-- Total Sale up to Previous Day -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.total_sale_up_to_previous_day') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('total_sale_up_to_previous_day', $settings->total_sale_to_pre, [
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
                        {!! Form::number('previous_day_cash_sale', $settings->pre_day_cash_sale, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_cash_sale' ),
                            'required',
                            'id' => 'previous_day_cash_sale'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Card Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_card_sale') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_card_sale', $settings->pre_day_card_sale, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_card_sale' ),
                            'required',
                            'id' => 'previous_day_card_sale'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Credit Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_credit_sale') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_credit_sale', $settings->pre_day_credit_sale, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_credit_sale' ),
                            'required',
                            'id' => 'previous_day_credit_sale'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Cash -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cash') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_cash', $settings->pre_day_cash, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_cash' ),
                            'required',
                            'id' => 'previous_day_cash'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Cheques / Cards -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cheques_cards') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_cheques_cards', $settings->pre_day_cheques, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_cheques_cards' ),
                            'required',
                            'id' => 'previous_day_cheques_cards'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_total') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_total', $settings->pre_day_total, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_total' ),
                            'required',
                            'id' => 'previous_day_total'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Balance in Hand (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_balance_in_hand') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_balance_in_hand', $settings->pre_day_balance, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_balance_in_hand' ),
                            'required',
                            'id' => 'previous_day_balance_in_hand'
                        ]); !!}
                    </div>
                </div>

                <!-- Previous Day Grand Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_grand_total') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('previous_day_grand_total', $settings->pre_day_grand_total, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_day_grand_total' ),
                            'required',
                            'id' => 'previous_day_grand_total'
                        ]); !!}
                    </div>
                </div>
 <!-- Previous Day Grand Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.no_of_rows_to_show_per_page') <span class="required" aria-required="true">*</span></label>
                        {!! Form::number('no_of_rows_to_show_per_page', $settings->no_of_rows_to_show_per_page, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.no_of_rows_to_show_per_page' ),
                            'required',
                            'id' => 'no_of_rows_to_show_per_page'
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
