@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.obligation_met_loans_details', 1) }} {{ trans_choice('core.report', 1) }}
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

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="location_id">{{ trans_choice('core.location', 1) }}</label>
                                        <select class="form-control select2" name="location_id" id="location_id">
                                            <option value="" disabled selected>{{ trans_choice('core.select', 1) }}</option>
                                            @foreach ($business_locations as $key)
                                                <option value="{{ $key->id }}" @if (Request::get('location_id') == $key->id) selected @endif>
                                                    {{ $key->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="loan_officer_id" class="control-label">{{ trans_choice('loan::general.loan_officer', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanindexofficer'))</label>
                                    <select class="form-control" name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id">
                                        <option value="">{{ trans_choice('core.all', 1) }}
                                            {{ trans_choice('loan::general.loan_officer', 2) }}</option>
                                        @foreach ($loan_officers as $loan_officer)
                                            <option value="{{ $loan_officer->id }}">{{ $loan_officer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="loan_product_id" class="control-label">{{ trans_choice('loan::general.loan_product', 1) }}
                                            @show_tooltip(__('loan::lang.tooltip_loanindexproduct'))</label>
                                        <select class="form-control" name="loan_product_id" id="loan_product_id" v-model="loan_product_id">
                                            <option value="">{{ trans_choice('core.all', 1) }}
                                                {{ trans_choice('loan::general.loan_product', 2) }}</option>
                                            @foreach ($loan_products as $loan_product)
                                                <option value="{{ $loan_product->id }}">{{ $loan_product->name }}</option>
                                            @endforeach
                                        </select>
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
                                                {{ trans('core.status') }}
                                            </span>
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
                                        <th>
                                            <span>
                                                {{ trans_choice('core.principal', 1) }}
                                                {{ trans('loan::general.repaid') }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.interest', 1) }}
                                                {{ trans('loan::general.repaid') }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.fee', 1) }}
                                                {{ trans('loan::general.repaid') }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.penalty', 1) }}
                                                {{ trans('loan::general.repaid') }}
                                            </span>
                                        </th>
                                        <th>
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

                                            {{-- Loan Status --}}
                                            <td>
                                                {!! $key->status_label !!}
                                            </td>

                                            {{-- Loan Product --}}
                                            <td>
                                                {{ $key->loan_product->name }}
                                            </td>

                                            {{-- Loan Principal --}}
                                            <td>
                                                {{ number_format($key->repayment_schedules->sum('principal'), 2) }}
                                            </td>

                                            {{-- Nominal interest rate --}}
                                            <td>
                                                {{ $key->interest_rate_label }}
                                            </td>

                                            {{-- Disbursed date --}}
                                            <td>
                                                {{ $key->disbursed_on_date }}
                                            </td>

                                            {{-- Principal Repaid --}}
                                            <td>
                                                {{ number_format($key->repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                            </td>

                                            {{-- Interest Repaid --}}
                                            <td>
                                                {{ number_format($key->repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                            </td>

                                            {{-- Fees Repaid --}}
                                            <td>
                                                {{ number_format($key->repayment_schedules->sum('fees_repaid_derived'), 2) }}
                                            </td>

                                            {{-- Penalties Repaid --}}
                                            <td>
                                                {{ number_format($key->repayment_schedules->sum('penalties_repaid_derived'), 2) }}
                                            </td>

                                            {{-- Total Repaid --}}
                                            <td>
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
