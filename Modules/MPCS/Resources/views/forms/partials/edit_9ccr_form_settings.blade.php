<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\MPCS\Http\Controllers\Form9CCRSettingsController::class, 'update'], [$settings->id]), 'method' => 'post', 'id' => 'update_9c_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.edit_form_9_ccr_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.date_and_time')</label>
                        {!! Form::date('datepicker', date('Y-m-d', strtotime($settings->date_time)), [
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
                        <label>@lang('mpcs::lang.previous_note') <span class="required" aria-required="true">*</span></label>
                        {!! Form::text('ref_previous_form_number', $settings->ref_pre_form_number, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.previous_note' ),
                            'required',
                            'id' => 'ref_previous_form_number'
                        ]); !!}
                    </div>
                </div>

              

                <!-- Previous Day Grand Total (Payments Section) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.user_added') <span class="required" aria-required="true">*</span></label>
                        {!! Form::text('user_added', $settings->added_user, [
                            'class' => 'form-control',
                            'placeholder' => __( 'mpcs::lang.user_added' ),
                            'id' => 'user_added'
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
