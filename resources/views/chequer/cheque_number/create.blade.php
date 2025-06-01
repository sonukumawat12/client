<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.add_cheque_number')</h3>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => action('Chequer\ChequeNumberController@store'), 'method' => 'post','id'=>'frmAddNumber']) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('date_time', __('cheque.date') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('date_time', date('Y/m/d'), ['class' => 'form-control', 'placeholder' =>
                                    __('cheque.date'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('ref_no', __('cheque.Cheque Book No') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('reference_no',$check_book_number, ['class' => 'form-control', 'placeholder' =>
                                    __('cheque.ref_no'), 'required','readonly']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('account_number', __('account.bank_account') .':') !!}
                                <div class="input-group">
                                    {!! Form::select('account_number', $accounts, null, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('account.bank_account'), 'required', 'id' => 'name', 'style' =>
                                    'width: 100%']); !!}

                                </div>
                            </div>
                        </div>
                      <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('first_cheque_no', __('cheque.first_cheque_number') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('first_cheque_no', null, ['class' => 'form-control', 'placeholder' => __('cheque.first_cheque_number'), 'required', 'oninput' => 'calculateChequeLeaves()']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('last_cheque_no', __('cheque.last_cheque_number') . ':') !!}
                        <div class="input-group">
                            {!! Form::number('last_cheque_no', null, ['class' => 'form-control', 'placeholder' => __('cheque.last_cheque_number'), 'required', 'oninput' => 'calculateChequeLeaves()']); !!}
                        </div>
                    </div>
                </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('no_of_cheque_leaves', __('cheque.no_of_cheque_leaves') . ':') !!}
                                <div class="input-group">
                                    {!! Form::number('no_of_cheque_leaves',$no_of_cheque_leaves, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('cheque.no_of_cheque_leaves'), 'required','readonly']); !!}
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
        <script>
    function calculateChequeLeaves() {
        var firstChequeNo = parseInt(document.querySelector('input[name="first_cheque_no"]').value) || 0;
        var lastChequeNo = parseInt(document.querySelector('input[name="last_cheque_no"]').value) || 0;
        var noOfLeaves = lastChequeNo - firstChequeNo + 1; // Calculate the number of cheque leaves

        // Update the "No of Cheque Leaves" field
        document.getElementById('no_of_cheque_leaves').value = noOfLeaves > 0 ? noOfLeaves : '';
    }
</script>
 