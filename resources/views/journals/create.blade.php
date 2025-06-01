@php

use App\Account;
$cash_account_id = Account::getAccountByAccountName('Cash')->id;

@endphp

<div class="modal-dialog modal-lg" role="document" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('account.add_journal')</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('JournalController@store'), 'method' => 'post' ]) !!}
            <input type="hidden" id="index" name="index" value="1">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('journal_id', __('account.journal_no')) !!}
                            {!! Form::text('journal_id', $journal_id, ['class' => 'form-control
                            journal_id',
                            'required', 'readonly'])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('date', __('account.date')) !!}
                            {!! Form::text('date', null, ['class' => 'form-control
                            journal_date',
                            'required'])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('select_location', __('account.select_location')) !!}
                            {!! Form::select('location_id', $locations, $default_location_id, [ 'class' =>
                            'form-control select2','style' => 'width:100%', 'id' => 'location_id',
                            'required' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('is_opening_balance', __('account.opening_balance')) !!}
                            {!! Form::select('is_opening_balance',['yes' => 'Yes', 'no' => 'No'], null, [ 'class' =>
                            'form-control select2','style' => 'width:100%', 'id' => 'is_opening_balance',
                            'required' , 'placeholder', 'disabled'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('note', __('account.note')) !!}
                            {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => 2, 'cols' => 10,'required'])
                            !!}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('show_in_ledger', __('account.show_in_ledger')) !!}
                            {!! Form::select('show_in_ledger',['no' => 'No need to show', 'customer' => 'Customer Ledger','supplier' => 'Supplier Ledger'], null, [ 'class' =>
                            'form-control','style' => 'width:100%', 'id' => 'show_in_ledger']) !!}
                        </div>
                    </div>
                    </div>
                <div class="row">
                    <div class="col-md-12" id="show_in_fields" hidden>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('show_in', __('account.show_in')) !!}
                                {!! Form::select('show_in',['credit' => 'Credit', 'debit' => 'Debit'], null, [ 'class' =>
                                'form-control select2','style' => 'width:100%', 'id' => 'show_in',
                                 'placeholder'
                                =>
                                'Please select']) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-6" hidden id="supplier_show_in_fields">
                            <div class="form-group">
                                {!! Form::label('supplier_show_in', __('lang_v1.supplier')) !!}
                                {!! Form::select('supplier_show_in',$suppliers, null, [ 'class' =>
                                'form-control select2','style' => 'width:100%', 'id' => 'supplier_show_in',
                                 'placeholder'
                                =>
                                'Please select']) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-6" hidden id="customer_show_in_fields">
                            <div class="form-group">
                                {!! Form::label('customer_show_in', __('lang_v1.customer')) !!}
                                {!! Form::select('customer_show_in',$customers, null, [ 'class' =>
                                'form-control select2','style' => 'width:100%', 'id' => 'customer_show_in',
                                 'placeholder'
                                =>
                                'Please select']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('account_type_id', __('account.select_account_type')) !!}
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('account_id', __('account.select_account')) !!}
                            
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('amount', __('account.debit_amount')) !!}
                            
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('amount', __('account.credit_amount')) !!}
                            
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <button class="btn btn-xs btn-primary add_row"
                            style="margin-top: 7px;">+</button>
                    </div>
                    
                   

                    
                </div>
                <div class="dynamic_rows"></div>
                <div class="row journal_row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('', $account_types, null, ['id' => 'account_type0', 'class' =>
                            'form-control select2 account_type_ids','style' => 'width:100%',
                            'required' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('', [], null, ['id' => 'account_id0', 'class' =>
                            'form-control select2 account_ids','style' => 'width:100%',
                            'required' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('', null, ['id' => "debit0", 'class' => 'debit-top form-control debit_amount0',
                             'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('', null, [ 'id'=> 'credit0','class' => 'credit-top form-control credit_amount0',
                             'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>
                    
                    

                    
                </div>
                <div class="row journal_row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('', $account_types, null, ['id' => 'account_type1',  'class' =>
                            'form-control select2 account_type_ids','style' => 'width:100%',
                            'required' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('', [], null, ['id' => 'account_id1',  'class' =>
                            'form-control select2 account_ids','style' => 'width:100%',
                            'required' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('', null, ['id' => 'debit1',  'class' => 'debit-top form-control debit_amount1',
                            'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('', null, [ 'id'=> 'credit1','class' => 'credit-top form-control credit_amount1',
                             'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>


                    <div class="col-md-2">
                        <button type="button" class="btn btn-xs btn-primary add_row_create" data-index="1"
                            style="margin-top: 7px;">Add</button>
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-6">
                    <h4>@lang('account.total')</h4>
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('debit_total_top', null, [ 'class' => 'form-control debit_total_top', 'readonly', 'style' =>
                        'width:100%;'
                        ]) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('credit_total_top', null, [ 'class' => 'form-control credit_total_top', 'readonly', 'style'
                        => 'width:100%;'
                        ]) !!}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="journal_details">
                        <thead>
                            <tr>
                                <th>@lang('account.account')</th>
                                <th>@lang('account.debit')</th>
                                <th>@lang('account.credit')</th>
                                <th>@lang('account.show_in_ledger')</th>
                            </tr>
                        </thead>
                        
                    
            
                    </table>
                </div>
                
            </div>


            <div class="modal-footer">
                <div class="col-md-6">
                    <h4>@lang('account.total')</h4>
                </div>
                <div class="col-md-2">
                    {!! Form::text('debit_total', null, [ 'class' => 'form-control debit_total', 'readonly', 'style' =>
                    'width:100%;',
                    'required']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::text('credit_total', null, [ 'class' => 'form-control credit_total', 'readonly', 'style'
                    => 'width:100%;',
                    'required']) !!}
                </div>
                
                <div class="clearfix"></div>

                <button type="submit" class="btn btn-primary add_btn">@lang( 'messages.submit' )</button>

                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.journal_date').datepicker("setDate", new Date());
        $('.select2').select2();
        $('.add_btn').attr('disabled', true);    

    </script>