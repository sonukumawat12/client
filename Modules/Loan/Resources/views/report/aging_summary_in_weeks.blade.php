@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.aging_summary_in_weeks', 1) }} {{ trans_choice('core.report', 1) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
@endsection

@section('content')
    
    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">

        <div class="row">
            @component('components.widget')
                @slot('slot')
                    <form method="get" action="{{ Request::url() }}">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="start_date">{{ trans_choice('core.start_date', 1) }}</label>
                                        <input type="text" value="{{ $start_date }}"
                                            class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date"
                                            id="start_date" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="end_date">{{ trans_choice('core.end_date', 1) }}</label>
                                        <input type="text" value="{{ $end_date }}"
                                            class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date" id="end_date" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-md-2">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn bg-olive btn-flat">{{ trans_choice('core.filter', 1) }}
                                        </button>
                                    </span>
                                    <span class="input-group-btn">
                                        <a href="{{ Request::url() }}"
                                            class="btn bg-purple  btn-flat pull-right">{{ trans_choice('core.reset', 1) }}!
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                @endslot
            @endcomponent
        </div>

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

                    <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-condensed table-hover">
                            <thead>
                                <tr class="bg-cyan">
                                    <th>
                                        <span>
                                            {{ trans_choice('core.day', 2) }}
                                            {{ trans_choice('core.in', 1) }}
                                            {{ trans_choice('core.arrears', 1) }}
                                        </span>
                                    </th>

                                    <th>
                                        <span>{{ trans_choice('loan::general.no_of_loans', 1) }}</span>
                                    </th>

                                    <th style="text-align: right">
                                        <span>
                                            {{ trans_choice('core.original', 1) }}
                                            {{ trans_choice('core.principal', 1) }}
                                        </span>
                                    </th>

                                    <th style="text-align: right">
                                        {{ trans_choice('core.original', 1) }}
                                        {{ trans_choice('core.interest', 1) }}
                                    </th>

                                    <th style="text-align: right">
                                        {{ trans('core.principal') }}
                                        {{ trans('loan::general.repaid') }}
                                    </th>

                                    <th style="text-align: right">
                                        {{ trans_choice('core.interest', 1) }}
                                        {{ trans('loan::general.repaid') }}
                                    </th>

                                    <th style="text-align: right">
                                        {{ trans('core.principal') }}
                                        {{ trans('core.overdue') }}
                                    </th>

                                    <th style="text-align: right">
                                        {{ trans_choice('core.interest', 1) }}
                                        {{ trans('core.overdue') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($weekly_bands))
                                    @foreach ($weekly_bands as $weekly_band)
                                        @php
                                            $loans_for_branch = $data->where('arrears_weeks_band', $weekly_band);
                                            $principal_outstanding = $loans_for_branch->reduce(function ($carry, $item) {
                                                return $carry + $item->repayment_schedules->sum('principal_due');
                                            });
                                            $principal_disbursed = $loans_for_branch->reduce(function ($carry, $item) {
                                                return $carry + $item->repayment_schedules->sum('principal');
                                            });
                                            $par_percentage = ($principal_outstanding / max($principal_disbursed, 1)) * 100;
                                            $weekly_band_label = trans('core.' . $weekly_band);
                                        @endphp
                                        {{-- Location --}}
                                        <tr>
                                            <td>
                                                {{ $weekly_band_label }}
                                            </td>

                                            {{-- No of loans --}}
                                            <td>
                                                {{ $loans_for_branch->count() }}
                                            </td>

                                            {{-- Original Principal --}}
                                            <td style="text-align: right">
                                                {{ number_format($principal_disbursed, 2) }}
                                            </td>

                                            {{-- Original Interest --}}
                                            <td style="text-align: right">
                                                {{ number_format(
                                                    $loans_for_branch->reduce(function ($carry, $item) {
                                                        return $carry + $item->repayment_schedules->sum('total_interest');
                                                    }),
                                                    2,
                                                ) }}
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

                                            {{-- Interest Repaid --}}
                                            <td style="text-align: right">
                                                {{ number_format(
                                                    $loans_for_branch->reduce(function ($carry, $item) {
                                                        return $carry + $item->repayment_schedules->sum('interest_repaid_derived');
                                                    }),
                                                    2,
                                                ) }}
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

                                            {{-- Interest Overdue --}}
                                            <td style="text-align: right">
                                                {{ number_format(
                                                    $loans_for_branch->reduce(function ($carry, $item) {
                                                        return $carry + $item->overdue_repayment_schedules->sum('interest_due');
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
                @endslot
            @endcomponent
        </div>
    </section>
@stop

@section('javascript')
    <script>
        $(function() {
            $('#data-table').DataTable({
                ordering: false
            });
        });
    </script>
    <script>
        var app = new Vue({
            el: "#vue-app",
            data() {
                return {
                    records: {!! json_encode($data) !!},
                    selectAll: false,
                    selectedRecords: [],
                    location_id: this.getParam('location_id'),
                    loan_officer_id: this.getParam('loan_officer_id'),
                    loan_product_id: this.getParam('loan_product_id'),
                    status: this.getParam('status'),
                }
            },

            computed: {
                has_filter() {
                    return (this.getParam('location_id').length > 0) ||
                        (this.getParam('loan_officer_id').length > 0) ||
                        (this.getParam('loan_product_id').length > 0) ||
                        (this.getParam('status').length > 0);
                },

                filter_btn_text() {
                    return this.has_filter ? 'Filter Applied' : 'Filter';
                }
            },

            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },

                getParam(param) {
                    const url_string = window.location.href;
                    const url = new URL(url_string);
                    return url.searchParams.get(param) ?? '';
                }
            },
        })
    </script>

@endsection
