@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.active', 1) . ' ' . trans_choice('loan::general.loan', 2) . ' ' . trans('loan::general.in_last_installment') }}
@endsection

@section('content')

    
   

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @include('loan::report.partials.filters')

        <div class="row">
            @component('components.widget')
                @slot('slot')
                    <div class="row mt-2">
                        <div class="col-12">
                            <!--If Filter has been applied-->
                            @if (Request::get('user_id'))
                                <div class="alert alert-info">
                                    {{ trans('loan::general.showing_loans_pending_approval') }}
                                    <a href="{{ Request::url() }}"> {{ trans('loan::general.show_all') }} </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover datatable">
                                <thead>
                                    <tr class="bg-cyan">
                                        <th>
                                            <span>{{ trans_choice('core.id', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexsystemid'))</span>
                                        </th>

                                        <th>
                                            <span>{{ trans_choice('core.location', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexbranch'))</span>
                                        </th>

                                        <th>
                                            <span>{{ trans_choice('core.contact', 1) }}
                                                {{ trans('core.name') }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexclient'))</span>
                                        </th>

                                        <th style="text-align:right">
                                            <span>
                                                {{ trans_choice('core.loan', 1) }}
                                                {{ trans_choice('core.amount', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexamount'))</span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans_choice('core.interest', 1) }}
                                                {{ trans_choice('core.rate', 1) }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans_choice('loan::general.disbursement', 1) }}
                                                {{ trans_choice('core.date', 1) }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans_choice('loan::general.repaid', 1) }}
                                                {{ trans_choice('core.principal', 1) }}
                                            </span>
                                        </th>

                                        <th style="text-align: right">
                                            <span>
                                                {{ trans_choice('loan::general.repaid', 1) }}
                                                {{ trans_choice('core.interest', 1) }}
                                            </span>
                                        </th>

                                        <th style="text-align: right">
                                            <span>
                                                {{ trans('loan::general.outstanding') }}
                                                {{ trans('core.principal') }}
                                            </span>
                                        </th>

                                        <th style="text-align: right">
                                            <span>
                                                {{ trans('loan::general.outstanding') }}
                                                {{ trans_choice('core.interest', 1) }}
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
                                    @if (!empty($data))
                                        @foreach ($data as $key)
                                            <tr>
                                                {{-- Loan Id --}}
                                                <td>
                                                    <a href="{{ url('contact_loan/' . $key->id . '/show') }}">
                                                        {{ $key->id }}
                                                    </a>
                                                </td>

                                                {{-- Business Location --}}
                                                <td>
                                                    {{ $key->business_location->name }}
                                                </td>

                                                {{-- Contact --}}
                                                <td>
                                                    <a href="{{ action('ContactController@show', [$key->contact->id]) }}">
                                                        {{ $key->contact->name }}
                                                    </a>
                                                </td>

                                                {{-- Loan Amount --}}
                                                <td style="text-align:right">
                                                    {{ number_format($key->repayment_schedules->sum('principal'), 2) }}
                                                </td>

                                                {{-- Interest Rate --}}
                                                <td>
                                                    {{ number_format($key->interest_rate, 2) }}
                                                </td>

                                                {{-- Disbursal Date --}}
                                                <td>
                                                    {{ $key->disbursed_on_date }}
                                                </td>

                                                {{-- Repaid Principal --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Repaid Interest --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Outstanding Principal --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('total_principal') - $key->repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Outstanding principal --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('total_interest') - $key->repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Loan Officer --}}
                                                <td>
                                                    {{ $key->loan_officer->user_full_name }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endslot
            @endcomponent
        </div>
    </section>

@stop
@section('javascript')
@endsection
