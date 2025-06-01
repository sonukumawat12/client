<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\Form9ASettingsController@store'), 'method' => 'post', 'id' => 'add_9a_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.add_form_9_a_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.opening_date')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="datepicker" data-date-format="yyyy/mm/dd">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Starting Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.form_starting_number') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="form_starting_number" class="form-control" required>
                    </div>
                </div>

                <!-- Ref Previous Form Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.ref_previous_form_number') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="ref_previous_form_number" class="form-control" required>
                    </div>
                </div>

                <!-- Total Sale up to Previous Day -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.total_sale_up_to_previous_day') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="total_sale_up_to_previous_day" name="total_sale_up_to_previous_day" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Cash Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cash_sale') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_cash_sale" name="previous_day_cash_sale" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Card Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_card_sale') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_card_sale" name="previous_day_card_sale" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Credit Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_credit_sale') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_credit_sale" name="previous_day_credit_sale" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Cash -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cash') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_cash" name="previous_day_cash" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Cheques / Cards -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_cheques_cards') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_cheques_cards" name="previous_day_cheques_cards" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_total') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_total" name="previous_day_total" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Balance in Hand (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_balance_in_hand') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_balance_in_hand" name="previous_day_balance_in_hand" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Grand Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.previous_day_grand_total') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="previous_day_grand_total" name="previous_day_grand_total" class="form-control" required>
                    </div>
                </div>
 <!-- Previous Day Grand Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.no_of_rows_to_show_per_page') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="no_of_rows_to_show_per_page" name="no_of_rows_to_show_per_page" class="form-control" required>
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

<script>
    $('#datepicker').datepicker('setDate', new Date());
</script>
