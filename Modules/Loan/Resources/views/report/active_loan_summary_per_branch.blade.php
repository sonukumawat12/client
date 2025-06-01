@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.active_loan_summary_per_branch', 1) }}
@endsection

@section('content')

    
    <
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($data))
                                                @foreach ($business_locations as $location)
                                                    @php
                                                        $loans_for_branch = $data->where('location_id', $location->id);
                                                    @endphp
                                                    {{-- Location --}}
                                                    <tr>
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
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->repayment_schedules->sum('principal');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>

                                                        {{-- Total Principal Repaid --}}
                                                        <td style="text-align: right">
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->repayment_schedules->sum('principal_repaid_derived');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>

                                                        {{-- Total Interest Repaid --}}
                                                        <td style="text-align: right">
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->repayment_schedules->sum('interest_repaid_derived');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>

                                                        {{-- Total Principal Due --}}
                                                        <td style="text-align: right">
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->repayment_schedules->sum('principal_due');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>

                                                        {{-- Total Interest Due --}}
                                                        <td style="text-align: right">
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->repayment_schedules->sum('interest_due');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>

                                                        {{-- Amount in Arrears --}}
                                                        <td style="text-align: right">
                                                            {{ number_format(
                                                                $loans_for_branch->reduce(function ($carry, $item) {
                                                                    return $carry + $item->overdue_repayment_schedules->sum('amount_due');
                                                                }),
                                                                2,
                                                            ) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
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
