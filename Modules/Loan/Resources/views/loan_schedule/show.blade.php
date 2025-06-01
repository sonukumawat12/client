@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.transaction', 1) }} {{ trans_choice('core.detail', 2) }}
@endsection

@section('content')


    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <div class="box-tools">
                            <a href="{{ url('contact_loan/transaction/' . $loan_transaction->id . '/pdf') }}" target="_blank"
                                class="btn btn-info btn-sm">{{ trans_choice('core.pdf', 1) }}</a>
                            <a href="{{ url('contact_loan/transaction/' . $loan_transaction->id . '/print') }}" target="_blank"
                                class="btn btn-info btn-sm">{{ trans_choice('core.print', 1) }}</a>
                            <a href="#" onclick="window.history.back()" class="btn btn-info btn-sm">{{ trans_choice('core.back', 1) }}</a>
                        </div>

                        <table class="table  table-bordered table-hover table-striped" id="">
                            <tbody>
                                <tr>
                                    <td>{{ trans_choice('core.id', 1) }}</td>
                                    <td>{{ $loan_transaction->id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('core.type', 1) }}</td>
                                    <td>
                                        @if ($loan_transaction->loan_transaction_type_id == 1)
                                            {{ trans_choice('loan::general.disbursement', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 2)
                                            {{ trans_choice('loan::general.repayment', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 3)
                                            {{ trans_choice('loan::general.contra', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 4)
                                            {{ trans_choice('loan::general.waive', 1) }} {{ trans_choice('loan::general.interest', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 5)
                                            {{ trans_choice('loan::general.repayment', 1) }} {{ trans_choice('core.at', 1) }}
                                            {{ trans_choice('loan::general.disbursement', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 6)
                                            {{ trans_choice('loan::general.write_off', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 7)
                                            {{ trans_choice('loan::general.marked_for_rescheduling', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 8)
                                            {{ trans_choice('loan::general.recovery', 1) }} {{ trans_choice('loan::general.repayment', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 9)
                                            {{ trans_choice('loan::general.waive', 1) }} {{ trans_choice('loan::general.fee', 2) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 10)
                                            {{ trans_choice('loan::general.fee', 1) }} {{ trans_choice('loan::general.applied', 1) }}
                                        @elseif ($loan_transaction->loan_transaction_type_id == 11)
                                            {{ trans_choice('loan::general.interest', 1) }} {{ trans_choice('loan::general.applied', 1) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('core.date', 1) }}</td>
                                    <td>{{ $loan_transaction->submitted_on }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('core.amount', 1) }}</td>
                                    <td>
                                        {{ number_format($loan_transaction->amount, $loan_transaction->loan->decimals) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <b>{{ trans_choice('core.payment', 1) }} {{ trans_choice('core.detail', 2) }}</b>
                                    </td>
                                </tr>
                                @if (!empty($loan_transaction->payment_detail))
                                    <tr>
                                        <td>{{ trans_choice('core.payment', 1) }} {{ trans_choice('core.type', 1) }}</td>
                                        <td>
                                            @if (!empty($loan_transaction->payment_detail->payment_type))
                                                {{ $loan_transaction->payment_detail->payment_type->name }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (!empty($loan_transaction->payment_detail->account_number))
                                        <tr>
                                            <td>{{ trans_choice('core.account', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->account_number }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->cheque_number))
                                        <tr>
                                            <td>{{ trans_choice('core.cheque', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->cheque_number }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->routing_code))
                                        <tr>
                                            <td>{{ trans_choice('core.routing_code', 1) }}</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->routing_code }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->receipt))
                                        <tr>
                                            <td>{{ trans_choice('core.receipt', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->receipt }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->bank_name))
                                        <tr>
                                            <td>{{ trans_choice('core.bank', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->bank_name }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->description))
                                        <tr>
                                            <td>{{ trans_choice('core.description', 1) }}</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->description }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
@endsection
