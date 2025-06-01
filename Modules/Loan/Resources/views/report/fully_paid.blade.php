@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 2) }} {{ trans_choice('loan::general.fully_paid', 1) }}
    {{ trans_choice('core.report', 1) }}
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
                                            <span>{{ trans_choice('core.id', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexsystemid'))</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.location', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexbranch'))</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('loan::general.loan_officer', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexofficer'))</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('core.contact', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexclient'))</span>
                                        </th>
                                        <th style="text-align:right">
                                            <span>{{ trans_choice('core.principal', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexamount'))</span>
                                        </th>
                                        <th style="text-align:right">
                                            {{ trans_choice('core.interest', 1) }}
                                        </th>
                                        <th style="text-align:right">
                                            {{ trans_choice('core.fee', 2) }}
                                        </th>
                                        <th style="text-align:right">
                                            {{ trans_choice('core.penalty', 2) }}
                                        </th>
                                        <th style="text-align:right">
                                            {{ trans('core.expected') }}
                                            {{ trans_choice('core.repayment', 1) }}
                                            {{ trans('core.amount') }}
                                        </th>
                                        <th style="text-align:right">
                                            <span>{{ trans('loan::general.disbursed') }}
                                                {{ trans('core.date') }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexdisburseddate'))</span>
                                        </th>
                                        <th>
                                            <span>{{ trans_choice('loan::general.product', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexproduct'))</span>
                                        </th>

                                        <th style="text-align:right">
                                            {{ trans_choice('core.total', 1) }}
                                            {{ trans_choice('core.amount', 1) }}
                                            {{ trans_choice('core.paid', 1) }}
                                        </th>

                                        <th>
                                            {{ trans_choice('core.maturity_date', 1) }}
                                        </th>

                                        <th>
                                            {{ trans('core.last') }}
                                            {{ trans_choice('core.repayment', 1) }}
                                            {{ trans('core.date') }}
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

                                                {{-- Interest --}}
                                                <td style="text-align:right">
                                                    <span>
                                                        {{-- Interest (amount) --}}
                                                        {{ number_format($key->repayment_schedules->sum('total_interest')) }}
                                                    </span>
                                                    {{-- Interest (%) --}}
                                                    <p class="text-muted">({{ number_format($key->interest_rate) }} %)</p>
                                                </td>

                                                {{-- Fees --}}
                                                <td style="text-align:right">
                                                    {{ number_format($key->repayment_schedules->sum('total_fees'), 2) }}
                                                </td>

                                                {{-- Penalties --}}
                                                <td style="text-align:right">
                                                    {{ number_format($key->repayment_schedules->sum('total_penalties'), 2) }}
                                                </td>

                                                {{-- Expected Repayment Amount --}}
                                                <td style="text-align:right">
                                                    {{ number_format($key->repayment_schedules->sum('amount_due'), 2) }}
                                                </td>

                                                {{-- Disursement date --}}
                                                <td>
                                                    <span>{{ $key->disbursed_on_date }}</span>
                                                </td>

                                                {{-- Loan product --}}
                                                <td>
                                                    <span>{{ $key->loan_product->name }}</span>
                                                </td>

                                                {{-- Amount paid --}}
                                                <td style="text-align:right">
                                                    {{ number_format($key->repayment_schedules->sum('total_paid'), 2) }}
                                                </td>

                                                {{-- Maturity date --}}
                                                <td>
                                                    {{ $key->maturity_date }}
                                                </td>

                                                {{-- Last repayment date --}}
                                                <td>
                                                    {{ $key->last_repayment_date }}
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
