@extends('layouts.app')
@section('title')
    {{ trans('loan::general.active_loans_details') }}
    {{ trans_choice('core.report', 1) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
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
                                            <span>{{ trans_choice('core.id', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.location', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.currency', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('loan::general.loan_officer', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.contact', 1) }}</span>
                                        </th>
                                        <th style="text-align:right">
                                            <span>{{ trans_choice('core.principal', 1) }}</span>
                                        </th>
                                        <th style="text-align:right">
                                            <span>
                                                {{ trans_choice('loan::general.interest_rate', 1) }}</span>
                                        </th>

                                        <th>
                                            <span>{{ trans_choice('loan::general.product', 1) }}</span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('loan::general.disbursed') }}
                                                {{ trans('core.date') }}</span>
                                        </th>

                                        <th>
                                            {{ trans('core.expected') }}
                                            {{ trans_choice('core.maturity_date', 1) }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans('core.principal') }}
                                            {{ trans('loan::general.repaid') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans('core.principal') }}
                                            {{ trans('core.outstanding') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans('core.principal') }}
                                            {{ trans('core.overdue') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.interest', 1) }}
                                            {{ trans('loan::general.repaid') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.interest', 1) }}
                                            {{ trans('core.outstanding') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.interest', 1) }}
                                            {{ trans('core.overdue') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.fee', 2) }}
                                            {{ trans('loan::general.repaid') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.fee', 2) }}
                                            {{ trans('core.outstanding') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.fee', 2) }}
                                            {{ trans('core.overdue') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.penalty', 2) }}
                                            {{ trans('loan::general.repaid') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.penalty', 2) }}
                                            {{ trans('core.outstanding') }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans_choice('core.penalty', 2) }}
                                            {{ trans('core.overdue') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($data))
                                        @foreach ($data as $key)
                                            <tr>
                                                <td>
                                                    <a href="{{ url('contact_loan/' . $key->id . '/show') }}">
                                                        <span>{{ $key->id }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                {{-- Location --}}
                                                <td>
                                                    <a href="{{ url('location/' . $key->location_id . '/show') }}">
                                                        <span>{{ $key->business_location->name }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                <td>
                                                    {{ $key->currency->name }}
                                                </td>

                                                {{-- Loan Officer --}}
                                                <td>
                                                    <a href="{{ url('user/' . $key->loan_officer_id . '/show') }}">
                                                        <span>{{ $key->loan_officer->user_full_name }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                {{-- Contact --}}
                                                <td>
                                                    <a href="{{ action('ContactController@show', [$key->contact_id]) }}">
                                                        <span>{{ $key->contact->name }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                {{-- Principal --}}
                                                <td style="text-align:right">
                                                    <span>{{ number_format($key->repayment_schedules->sum('principal'), 2) }}</span>
                                                </td>

                                                {{-- Balance --}}
                                                <td style="text-align:right">
                                                    <span>
                                                        {{ $key->interest_rate_label }}
                                                    </span>
                                                </td>

                                                {{-- Loan product --}}
                                                <td>
                                                    <span>{{ $key->loan_product->name }}</span>
                                                </td>

                                                {{-- Disursement date --}}
                                                <td>
                                                    <span>{{ $key->disbursed_on_date }}</span>
                                                </td>

                                                {{-- Maturity date --}}
                                                <td>
                                                    {{ $key->maturity_date }}
                                                </td>

                                                {{-- Principal Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Principal Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('principal_due'), 2) }}
                                                </td>

                                                {{-- Principal Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->overdue_repayment_schedules->sum('principal_due'), 2) }}
                                                </td>

                                                {{-- Interest Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Interest Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('interest_due'), 2) }}
                                                </td>

                                                {{-- Interest Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->overdue_repayment_schedules->sum('interest_due'), 2) }}
                                                </td>

                                                {{-- Fees Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('fees_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Fees Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('fees_due'), 2) }}
                                                </td>

                                                {{-- Fees Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->overdue_repayment_schedules->sum('fees_due'), 2) }}
                                                </td>

                                                {{-- Penalties Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('penalties_repaid_derived'), 2) }}
                                                </td>

                                                {{-- Penalties Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->repayment_schedules->sum('penalties_due'), 2) }}
                                                </td>

                                                {{-- Penalties Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format($key->overdue_repayment_schedules->sum('penalties_due'), 2) }}
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
    <script>
        var app = new Vue({
            el: "#vue-app",
            data() {
                return {
                    location_id: this.getParam('location_id'),
                    loan_officer_id: this.getParam('loan_officer_id'),
                    loan_product_id: this.getParam('loan_product_id'),
                    status: this.getParam('status'),
                }
            },

            methods: {
                getParam(param) {
                    const url_string = window.location.href;
                    const url = new URL(url_string);
                    return url.searchParams.get(param) ?? '';
                }
            },
        })
    </script>

@endsection
