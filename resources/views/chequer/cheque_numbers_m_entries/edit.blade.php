<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.edit_cheque_number_m_entries')</h3>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('Chequer\ChequeNumbersMEntryController@update', $cheque_numbers_m_entry->id), 'method' => 'post','id'=>'frmEditNumber']) !!}
            {!! Form::hidden('_method', 'PUT') !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('bank_id', __('account.bank_account') .':') !!}
                        <div class="input-group">
                            {!! Form::select('bank_id', $accounts, $cheque_numbers_m_entry->bank_id, ['class' => 'form-control',
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
                            {!! Form::select('cheque_number_id', $cheque_numbers, $cheque_numbers_m_entry->cheque_number_id, ['class' => 'form-control',
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
                            {!! Form::number('next_cheque_number_to_print', $cheque_numbers_m_entry->next_cheque_number_to_print, ['class' => 'form-control', 'placeholder' => __('cheque.Next_Check_Number_to_Print'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('new_cheque_number_to_print', __('cheque.New_Cheque_Number_to_Print') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('new_cheque_number_to_print', $cheque_numbers_m_entry->new_cheque_number_to_print, ['class' => 'form-control', 'placeholder' => __('cheque.New_Cheque_Number_to_Print'), '']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('next_cheque_number_to_auto_print', __('cheque.Next_Cheque_Number_to_Auto_print') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('next_cheque_number_to_auto_print', $cheque_numbers_m_entry->next_cheque_number_to_auto_print, ['class' => 'form-control',
                            'placeholder' =>
                            __('cheque.Next_Cheque_Number_to_Auto_print'), '']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('note', __('cheque.note') . ' (Compulsory to Enter):') !!}
                        <div class="input-group">
                            {!! Form::textarea('note', $cheque_numbers_m_entry->note, ['class' => 'form-control',
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