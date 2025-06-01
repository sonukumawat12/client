@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan_classification_by_product', 1) }}
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
                                                <span>{{ trans_choice('loan::general.no_of_loans_disbursed', 1) }}</span>
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
                                                <span>{{ trans_choice('loan::general.total_principal_repaid', 1) }}</span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>{{ trans_choice('loan::general.total_interest_repaid', 1) }}</span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>{{ trans_choice('loan::general.total_principal_outstanding', 1) }}</span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>{{ trans_choice('loan::general.total_interest_outstanding', 1) }}</span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>{{ trans_choice('loan::general.amount_in_arrears', 1) }}</span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>
                                                    {{ trans_choice('loan::general.portfolio_at_risk', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_portfolio_at_risk_calculation'))
                                                </span>
                                            </th>
                                            <th style="text-align: right">
                                                <span>
                                                    {{ trans('core.percentage') }}
                                                    {{ trans('core.of') }}
                                                    {{ trans('core.portfolio') }} (%)
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($data))
                                            @foreach ($loan_products as $loan_product)
                                                @php
                                                    $loans_for_product = $data->where('loan_product_id', $loan_product->id);
                                                    $principal_outstanding = $loans_for_product->reduce(function ($carry, $item) {
                                                        return $carry + $item->repayment_schedules->sum('principal_due');
                                                    });
                                                    $principal_disbursed = $loans_for_product->reduce(function ($carry, $item) {
                                                        return $carry + $item->repayment_schedules->sum('principal');
                                                    });
                                                    $portfolio_at_risk = $principal_outstanding / max($principal_disbursed, 1);
                                                    $percentage_of_portfolio = ($loans_for_product->count() / max($data->count(), 1)) * 100;
                                                @endphp
                                                {{-- Location --}}
                                                <tr>
                                                    <td>
                                                        {{ $loan_product->name }}
                                                    </td>

                                                    {{-- No of loans disbursed --}}
                                                    <td>
                                                        {{ $loans_for_product->count('disbursed_on_date') }}
                                                    </td>

                                                    {{-- No of clients --}}
                                                    <td>
                                                        {{ $loans_for_product->unique('contact_id')->count() }}
                                                    </td>

                                                    {{-- No of active loans --}}
                                                    <td>
                                                        {{ $loans_for_product->where('status', 'active')->count() }}
                                                    </td>

                                                    {{-- No of loans in arrears --}}
                                                    <td>
                                                        {{ $loans_for_product->where('arrears_days', '>', 0)->count() }}
                                                    </td>

                                                    {{-- Total loans disbursed --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($principal_disbursed, 2) }}
                                                    </td>

                                                    {{-- Total Principal Repaid --}}
                                                    <td style="text-align: right">
                                                        {{ number_format(
                                                            $loans_for_product->reduce(function ($carry, $item) {
                                                                return $carry + $item->repayment_schedules->sum('principal_repaid_derived');
                                                            }),
                                                            2,
                                                        ) }}
                                                    </td>

                                                    {{-- Total Interest Repaid --}}
                                                    <td style="text-align: right">
                                                        {{ number_format(
                                                            $loans_for_product->reduce(function ($carry, $item) {
                                                                return $carry + $item->repayment_schedules->sum('interest_repaid_derived');
                                                            }),
                                                            2,
                                                        ) }}
                                                    </td>

                                                    {{-- Total Principal Due --}}
                                                    <td style="text-align: right">
                                                        {{ number_format(
                                                            $loans_for_product->reduce(function ($carry, $item) {
                                                                return $carry + $item->repayment_schedules->sum('principal_due');
                                                            }),
                                                            2,
                                                        ) }}
                                                    </td>

                                                    {{-- Total Interest Due --}}
                                                    <td style="text-align: right">
                                                        {{ number_format(
                                                            $loans_for_product->reduce(function ($carry, $item) {
                                                                return $carry + $item->repayment_schedules->sum('interest_due');
                                                            }),
                                                            2,
                                                        ) }}
                                                    </td>

                                                    {{-- Amount in Arrears --}}
                                                    <td style="text-align: right">
                                                        {{ number_format(
                                                            $loans_for_product->reduce(function ($carry, $item) {
                                                                return $carry + $item->overdue_repayment_schedules->sum('amount_due');
                                                            }),
                                                            2,
                                                        ) }}
                                                    </td>

                                                    {{-- Portfolio at Risk --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($portfolio_at_risk, 2) }}
                                                    </td>

                                                    {{-- Percentage of Portfolio --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($percentage_of_portfolio, 2) }}%
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
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
