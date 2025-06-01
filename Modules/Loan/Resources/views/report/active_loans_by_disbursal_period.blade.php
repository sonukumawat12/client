@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.active_loans_by_disbursal_period', 1) }}
    {{ trans_choice('core.report', 1) }}
@endsection

@section('content')

    
    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @include('loan::report.partials.filters')

        <div class="row">
            @component('components.widget')
                @slot('slot')
                    <!-- Main content -->
                    <section class="content" id="vue-app">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed table-hover datatable">
                                        <thead>
                                            <tr class="bg-cyan">
                                                <th>
                                                    <span>{{ trans_choice('core.location', 1) }}</span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('core.currency', 1) }}</span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('core.contact', 1) }}
                                                        {{ trans('lang_v1.account_number') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('core.contact', 1) }}</span>
                                                    <span>{{ trans_choice('core.name', 1) }}</span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('loan::general.product', 1) }}</span>
                                                </th>
                                                <th style="text-align:right">
                                                    <span>
                                                        {{ trans_choice('core.loan', 1) }}
                                                        {{ trans_choice('core.amount', 1) }}
                                                    </span>
                                                </th>
                                                <th style="text-align:right">
                                                    <span>
                                                        {{ trans_choice('loan::general.interest_rate', 1) }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans('core.disbursed') }}
                                                        {{ trans('core.date') }}
                                                    </span>
                                                </th>
                                                <th style="text-align: right">
                                                    <span>
                                                        {{ trans('core.total') }}
                                                        {{ trans('core.due') }}
                                                    </span>
                                                </th>
                                                <th style="text-align: right">
                                                    <span>
                                                        {{ trans('core.total') }}
                                                        {{ trans('loan::general.repaid') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans_choice('loan::general.loan_officer', 1) }}
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $key)
                                                <tr>
                                                    {{-- Location --}}
                                                    <td>
                                                        {{ $key->business_location->name }}
                                                    </td>

                                                    {{-- Currency --}}
                                                    <td>
                                                        {{ $key->currency->name }}
                                                    </td>

                                                    {{-- Contact Account Number --}}
                                                    <td>
                                                        {{ $key->contact->contact_id ?: trans('loan::general.none') }}
                                                    </td>

                                                    {{-- Contact name --}}
                                                    <td>
                                                        {{ $key->contact->name }}
                                                    </td>

                                                    {{-- Loan Product --}}
                                                    <td>
                                                        {{ $key->loan_product->name }}
                                                    </td>

                                                    {{-- Loan Principal --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($key->repayment_schedules->sum('principal'), 2) }}
                                                    </td>

                                                    {{-- Nominal interest rate --}}
                                                    <td style="text-align: right">
                                                        {{ $key->interest_rate_label }}
                                                    </td>

                                                    {{-- Disbursed date --}}
                                                    <td>
                                                        {{ $key->disbursed_on_date }}
                                                    </td>

                                                    {{-- Total Due --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($key->repayment_schedules->sum('total_due'), 2) }}
                                                    </td>

                                                    {{-- Total Repaid --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($key->repayment_schedules->sum('total_paid'), 2) }}
                                                    </td>

                                                    {{-- Loan Officer --}}
                                                    <td>
                                                        {{ $key->loan_officer->user_full_name }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                @endslot
            @endcomponent
        </div>
    </section>

@stop
@section('javascript')
@endsection
