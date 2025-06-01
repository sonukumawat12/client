@extends('layouts.app')
@section('title', __('contact.edit_customer_loan'))
@section('content')
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title pull-left">@lang('contact.edit_customer_loan')</h4>
            </div>
        </div>
    </div>
    <!-- Main content -->

    <section class="content main-content-inner d-flex justify-content-center align-items-center"
        style="min-height: 100vh; ">
        <div class="row justify-content-center w-100 bg-light-subtle"   style="height: 80%; margin:auto; ">
            <div class="col-md-10 bg-light"   style="min-height: 100vh; width: 100%; ">
                <div class="card" style="background-color: #f5f5f5; padding:1rem">
                    <div class="card-header bg-primary text-white" >
                        <h2 class="text-center">@lang('contact.edit_customer_loan')</h2>
                    </div>
                    <div class="card-body">
                        {!! Form::open([
                            'route' => ['show_customer_loans.update', $loan->id],
                            'method' => 'put',
                            'class' => 'form-vertical',
                        ]) !!}

                        <div class="flex-justify-between">
                            <div class="form-group mb-2">
                                {!! Form::label('transaction_date', 'Transaction Date', ['class' => 'control-label']) !!}
                                {!! Form::text('transaction_date', $loan->transaction_date, ['class' => 'form-control', 'required', 'disabled', 'placeholder' => 'Transaction Date']) !!}
                            </div>

                             <div class="form-group mb-2">
                                {!! Form::label('reference', 'Reference', ['class' => 'control-label']) !!}
                                {!! Form::text('reference', $loan->ref_no, ['class' => 'form-control', 'required', 'disabled', 'placeholder' => 'Reference']) !!}
                            </div>

                            <div class="form-group mb-2">
                                {!! Form::label('contact_name', 'Contact Name', ['class' => 'control-label']) !!}
                                {!! Form::text('contact_name', $loan->contact->name, ['class' => 'form-control', 'required','disabled', 'placeholder' => 'Contact Name']) !!}
                            </div>
                      
                            <div class="form-group mb-2">
                              {!! Form::label('type', 'Added Page', ['class' => 'control-label']) !!}
                                @if($loan->is_settlement)
                                    {!! Form::text('type', 'Settlement', ['class' => 'form-control', 'required', 'disabled']) !!}
                                @else
                                    {!! Form::text('type', 'Customer Ledger', ['class' => 'form-control', 'required', 'disabled']) !!}
                                @endif
                            </div>

                             <div class="form-group mb-2 ">
                                {!! Form::label('approved_user', 'Approved User', ['class' => 'control-label']) !!}
                                {!! Form::text('approved_user', $loan->approved_user, ['class' => 'form-control', 'required', 'disabled', 'placeholder' => 'Approved User']) !!}
                            </div>

                             <div class="form-group mb-3">
                                {!! Form::label('final_total', 'Final Total', ['class' => 'control-label']) !!}
                                {!! Form::number('final_total', $loan->final_total, ['class' => 'form-control', 'required', 'step' => '0.01', 'placeholder' => 'Final Total']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('transaction_note', 'Transaction Note', ['class' => 'control-label']) !!}
                            {!! Form::textarea('transaction_note', $loan->transaction_note, [
                                'class' => 'form-control',
                              
                                'rows' => 4,
                                'placeholder' => 'Transaction Note'
                            ]) !!}
                        </div>


                        <div class="form-group text-center">
                            {!! Form::submit('Update Loan', ['class' => 'btn btn-success']) !!}
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection