<div class="modal-dialog" role="document" style="width:60%;">
    <div class="modal-content">

        {!! Form::open(['id' => 'add_booking_room']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('messages.add') @lang('hms::lang.rooms')</h4>
        </div>

        <div class="modal-body">
            <div class="modify_field">
                <!-- Dynamic Fields Container -->
                <div id="dynamic-fields">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('type', __('hms::lang.type') . '*') !!}
                                {!! Form::select('type', $types, '', [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'type',
                                    'placeholder' => __('hms::lang.type'),
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('no_of_adult', __('hms::lang.no_of_adult') . '*') !!}
                                {!! Form::select('no_of_adult', [], '', [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'no_of_adult',
                                    'placeholder' => __('hms::lang.no_of_adult'),
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('no_of_child', __('hms::lang.no_of_child') . '*') !!}
                                {!! Form::select('no_of_child', [], '', [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'no_of_child',
                                    'placeholder' => __('hms::lang.no_of_child'),
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('room_no', __('hms::lang.room_no') . '*') !!}
                                {!! Form::select('room_no', [], '', [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'room_no',
                                    'placeholder' => __('hms::lang.room_no'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="btn btn-primary">
                @lang('cheque.save')
            </button>
        </div>

        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->