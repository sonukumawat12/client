@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 2) }} {{ trans_choice('loan::general.awaiting_disbursement', 1) }}
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
                                                    <span>{{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans('lang_v1.account_number') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('loan::general.product', 1) }}</span>
                                                </th>
                                                <th style="text-align:right">
                                                    <span>
                                                        {{ trans_choice('core.loan', 1) }}
                                                        {{ trans_choice('core.principal', 1) }}
                                                        {{ trans_choice('core.amount', 1) }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans_choice('core.term', 1) }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans_choice('core.repayment', 1) }}
                                                        {{ trans_choice('loan::general.frequency', 1) }}
                                                    </span>
                                                </th>
                                                <th style="text-align:right">
                                                    <span>
                                                        {{ trans_choice('loan::general.annual_nominal_interest_rate', 1) }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans('core.approval') }}
                                                        {{ trans('core.date') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans('core.expected') }}
                                                        {{ trans('loan::general.disbursal') }}
                                                        {{ trans('core.date') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans_choice('core.day', 2) }}
                                                        {{ trans('core.to') }}
                                                        {{ trans('core.expected') }}
                                                        {{ trans('loan::general.disbursal') }}
                                                        {{ trans('core.date') }}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span>
                                                        {{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans_choice('loan::general.purpose', 1) }}
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

                                                    {{-- Loan Account Number --}}
                                                    <td>
                                                        N/A
                                                    </td>

                                                    {{-- Loan Product --}}
                                                    <td>
                                                        {{ $key->loan_product->name }}
                                                    </td>

                                                    {{-- Loan Principal --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($key->repayment_schedules->sum('principal'), 2) }}
                                                    </td>

                                                    {{-- Loan Term --}}
                                                    <td>
                                                        {{ $key->loan_term_label }}
                                                    </td>

                                                    {{-- Repayment Frequency --}}
                                                    <td>
                                                        {{ $key->repayment_frequency_label }}
                                                    </td>

                                                    {{-- Nominal interest rate --}}
                                                    <td style="text-align: right">
                                                        {{ number_format($key->interest_rate, 2) }}
                                                    </td>

                                                    {{-- Approval date --}}
                                                    <td>
                                                        {{ $key->approved_on_date }}
                                                    </td>

                                                    {{-- Expected Disbursement Date --}}
                                                    <th>
                                                        {{ $key->expected_disbursement_date }}
                                                    </th>

                                                    {{-- Days to Expected Disbursement --}}
                                                    <td>
                                                        {{ number_format($key->days_to_expected_disbursement_date) }}
                                                    </td>

                                                    <td>
                                                        {{ $key->loan_purpose->name }}
                                                    </td>

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
