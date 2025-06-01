<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.add_cheque_number_m_entries')</h3>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('Chequer\ChequeNumbersMEntryController@store'), 'method' => 'post','id'=>'frmAddNumber']) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date_time', __('cheque.date') . ':') !!}
                        <div class="input-group">
                            {!! Form::text('date_time', date('Y/m/d'), ['class' => 'form-control', 'placeholder' =>
                            __('cheque.date'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('bank_id', __('account.bank_account') .':') !!}
                        <div class="input-group">
                            {!! Form::select('bank_id', $accounts, null, ['class' => 'form-control',
                            'placeholder' =>
                            __('account.bank_account'), 'required', 'id' => 'bank_id', 'style' =>
                            'width: 100%']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cheque_number_id', __('cheque.Cheque Book No') .':') !!}
                        <div class="input-group">
                            {!! Form::select('cheque_number_id', $cheque_numbers, null, ['class' => 'form-control',
                            'placeholder' =>
                            __('cheque.Cheque Book No'), 'required', 'id' => 'cheque_number_id', 'style' =>
                            'width: 100%']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('next_cheque_number_to_print', __('cheque.Next_Check_Number_to_Print') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('next_cheque_number_to_print', null, ['class' => 'form-control', 'placeholder' => __('cheque.Next_Check_Number_to_Print'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('new_cheque_number_to_print', __('cheque.New_Cheque_Number_to_Print') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('new_cheque_number_to_print', null, ['class' => 'form-control', 'placeholder' => __('cheque.New_Cheque_Number_to_Print'), '']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('next_cheque_number_to_auto_print', __('cheque.Next_Cheque_Number_to_Auto_print') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('next_cheque_number_to_auto_print', null, ['class' => 'form-control',
                            'placeholder' =>
                            __('cheque.Next_Cheque_Number_to_Auto_print'), '']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('note', __('cheque.note') . ' (Compulsory to Enter):') !!}
                        <div class="input-group">
                            {!! Form::textarea('note', null, ['class' => 'form-control',
                            'placeholder' =>
                            __('cheque.note'), 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                    data-dismiss="modal">Close</button>
                <button type="submit" style="margin-right: 5px;"
                    class="pull-right btn btn-primary">@lang('cheque.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>