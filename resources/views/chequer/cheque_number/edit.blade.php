@extends('layouts.app')
@section('title', __('cheque.cheque_number_edit'))

@section('content')

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('cheque.cheque_number_edit')])
    
    {!! Form::open(['url' => action('Chequer\ChequeNumberController@update', $cheque->id), 'method' => 'put', 'id' => 'frmEditNumber']) !!}
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('date_time', __('cheque.date') . ':') !!}
                {!! Form::text('date_time', $cheque->date_time, ['class' => 'form-control', 'required']); !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('reference_no', __('cheque.Cheque Book No') . ':') !!}
                {!! Form::text('reference_no', $cheque->reference_no, ['class' => 'form-control', 'required', 'readonly']); !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('account_number', __('account.bank_account') .':') !!}
                {!! Form::select('account_number', $accounts, $cheque->account_no, ['class' => 'form-control', 'required']); !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('first_cheque_no', __('cheque.first_cheque_number') . ':') !!}
                {!! Form::number('first_cheque_no', $cheque->first_cheque_no, ['class' => 'form-control', 'required', 'readonly', 'oninput' => 'calculateChequeLeaves()']); !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('last_cheque_no', __('cheque.last_cheque_number') . ':') !!}
                {!! Form::number('last_cheque_no', $cheque->last_cheque_no, ['class' => 'form-control', 'required', 'oninput' => 'calculateChequeLeaves()']); !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('no_of_cheque_leaves', __('cheque.no_of_cheque_leaves') . ':') !!}
                {!! Form::number('no_of_cheque_leaves', $cheque->no_of_cheque_leaves, ['class' => 'form-control', 'required', 'readonly', 'id' => 'no_of_cheque_leaves']); !!}
            </div>
        </div>
    </div>

    <div class="row">
        <button type="button" class="pull-right btn btn-secondary" style="margin-right: 5px;" data-dismiss="modal">Close</button>
        <button type="submit" class="pull-right btn btn-primary" style="margin-right: 5px;">@lang('cheque.save')</button>
    </div>
    
    {!! Form::close() !!}
    @endcomponent
</section>

@endsection

@section('javascript')
<script>
    function calculateChequeLeaves() {
        var firstChequeNo = parseInt(document.querySelector('input[name="first_cheque_no"]').value) || 0;
        var lastChequeNo = parseInt(document.querySelector('input[name="last_cheque_no"]').value) || 0;
        var noOfLeaves = lastChequeNo - firstChequeNo + 1;

        document.getElementById('no_of_cheque_leaves').value = noOfLeaves > 0 ? noOfLeaves : '';
    }
</script>
@endsection
