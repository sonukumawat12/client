@extends('layouts.app')
@section('title')
    {{ trans('loan::general.active_loans_summary') }}
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
                                            <span>{{ trans_choice('core.location', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.no_of_clients', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('loan::general.no_of_active_loans', 1) }}</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('loan::general.no_of_loans_in_arrears', 1) }}</span>
                                        </th>
                                        <th style="text-align: right">
                                            <span>{{ trans_choice('loan::general.total_loans_disbursed', 1) }}</span>
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
                                            {{ trans_choice('core.total', 1) }}
                                            {{ trans_choice('core.interest', 1) }}
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
                                            {{ trans('loan::general.total') }}
                                            {{ trans_choice('core.fee', 2) }}
                                        </th>

                                        <th style="text-align: right">
                                            {{ trans('loan::general.total') }}
                                            {{ trans_choice('core.fee', 2) }}
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

                                        <th style="text-align: right">
                                            {{ trans('loan::general.portfolio_at_risk') }} (%)
                                            @show_tooltip(__('loan::lang.tooltip_portfolio_at_risk_calculation').'
                                            '.__('core.as_a_percentage'))
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($data))
                                        @foreach ($business_locations as $location)
                                            @php
                                                $loans_for_branch = $data->where('location_id', $location->id);
                                                $principal_outstanding = $loans_for_branch->reduce(function ($carry, $item) {
                                                    return $carry + $item->repayment_schedules->sum('principal_due');
                                                });
                                                $principal_disbursed = $loans_for_branch->reduce(function ($carry, $item) {
                                                    return $carry + $item->repayment_schedules->sum('principal');
                                                });
                                                $par_percentage = ($principal_outstanding / max($principal_disbursed, 1)) * 100;
                                            @endphp
                                            <tr>
                                                {{-- Location --}}
                                                <td>
                                                    {{ $location->name }}
                                                </td>

                                                {{-- No of clients --}}
                                                <td>
                                                    {{ $loans_for_branch->unique('contact_id')->count() }}
                                                </td>

                                                {{-- No of active loans --}}
                                                <td>
                                                    {{ $loans_for_branch->count() }}
                                                </td>

                                                {{-- No of loans in arrears --}}
                                                <td>
                                                    {{ $loans_for_branch->where('arrears_days', '>', 0)->count() }}
                                                </td>

                                                {{-- Total loans disbursed --}}
                                                <td style="text-align: right">
                                                    {{ number_format($principal_disbursed, 2) }}
                                                </td>

                                                {{-- Principal Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('principal_repaid_derived');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Principal Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format($principal_outstanding, 2) }}
                                                </td>

                                                {{-- Principal Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->overdue_repayment_schedules->sum('principal_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Total Interest --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('total_interest');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Interest Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('interest_repaid_derived');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Interest Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('interest_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Interest Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->overdue_repayment_schedules->sum('interest_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Total Fees --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('total_fees');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Fees Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('fees_repaid_derived');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Fees Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('fees_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Fees Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->overdue_repayment_schedules->sum('fees_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Total Penalties --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('total_penalties');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Penalties Repaid --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('penalties_repaid_derived');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Penalties Outstanding --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->repayment_schedules->sum('penalties_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Penalties Overdue --}}
                                                <td style="text-align: right">
                                                    {{ number_format(
                                                        $loans_for_branch->reduce(function ($carry, $item) {
                                                            return $carry + $item->overdue_repayment_schedules->sum('penalties_due');
                                                        }),
                                                        2,
                                                    ) }}
                                                </td>

                                                {{-- Portfolio at Risk % --}}
                                                <td style="text-align: right">
                                                    {{ number_format($par_percentage, 2) }}%
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
