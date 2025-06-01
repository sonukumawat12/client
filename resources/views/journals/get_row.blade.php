<div class="row journal_row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::select('journal['.$index.'][account_type_id]', $account_types, null, [ 'class' =>
            'form-control select2 account_type_ids','id' => 'account_type'.$index,'style' => 'width:100%',
            'required' , 'placeholder'
            =>
            'Please select']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::select('journal['.$index.'][account_id]', [], null, [ 'class' =>
            'form-control select2 account_ids','id' => 'account_id'.$index,'style' => 'width:100%',
            'required' , 'placeholder'
            =>
            'Please select']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            {!! Form::text('journal['.$index.'][debit_amount]', null, [ 'class' => 'debit-top form-control debit_amount'.$index, 'id' => 'debit'.$index,
            'required' , 'placeholder' => __('account.amount')]) !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::text('journal['.$index.'][credit_amount]', null, [ 'class' => 'credit-top form-control credit_amount'.$index,'id' => 'credit'.$index,
            'required' , 'placeholder' => __('account.amount')]) !!}
        </div>
    </div>

    <div class="col-md-2">
    <button class="btn btn-xs btn-danger remove_row" data-index="{{$index}}"
            style="margin-top: 5px;">-</button>
    </div>
</div>
