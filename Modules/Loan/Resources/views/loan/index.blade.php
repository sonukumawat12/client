@extends('layouts.app')
@section('title', __('loan::lang.view_loans'))

@section('content')

    <!-- Main content -->
    <section class="content no-print" id="vue-app">

        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <form method="get" action="{{ Request::url() }}">
                        <div class="modal-body">
                            <div class="form-group float-right" v-show="has_filter">
                                <a href="{{ Request::url() }}" class="btn btn-sm btn-success">
                                    {{ trans_choice('core.clear', 1) }} {{ trans_choice('core.filter', 2) }}
                                </a>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="location_id" class="control-label">{{ trans_choice('core.location', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanindexbranch'))</label>
                                    <select class="form-control" name="location_id" id="location_id" v-model="location_id">
                                        <option value="">{{ trans_choice('core.all', 1) }}
                                            {{ trans_choice('core.location', 2) }}
                                        </option>
                                        @foreach ($business_locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
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
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
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

                                <div class="form-group col-md-6">
                                    <label for="status" class="control-label">{{ trans_choice('loan::general.status', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanindexstatus'))</label>
                                    <select class="form-control" name="status" id="status" v-model="status">
                                        <option value="">{{ trans_choice('core.all', 1) }}
                                            {{ trans_choice('loan::general.status', 2) }}</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->status }}">{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ trans_choice('core.filter', 1) }}</button>
                        </div>
                    </form>
                @endcomponent
            </div>
        </div>
        @can('product.view')
            <div class="row">
                @component('components.widget')
                    @slot('header')
                        <div class="box-tools">
                            <a href="{{ url('contact_loan/create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ trans_choice('core.add', 1) }}
                                @show_tooltip(__('loan::lang.tooltip_loanindexadd'))
                            </a>
                        </div>
                    @endslot

                    @slot('slot')
                        <div class="row mt-2">
                            <div class="col-12">
                                <!--If Filter has been applied-->
                                @if (Request::get('user_id'))
                                    <div class="alert alert-info">
                                        {{ trans('loan::general.showing_loans_pending_approval') }}
                                        <a href="{{ Request::url() }}"> {{ trans('loan::general.show_all') }} </a>
                                    </div>

                                    <!--If filter has not been applied and there are loans to approve-->
                                @elseif($has_loans_to_approve)
                                    <div class="alert alert-info">
                                        {{ trans('loan::general.you_have_loans_to_approve') }}
                                        <a href="{{ Request::url() . '?user_id=' . Auth::id() .'&status=submitted' }}">
                                            {{ trans_choice('core.view', 1) }} {{ trans_choice('loan::general.loan', 2) }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-condensed table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ trans_choice('core.action', 1) }} @show_tooltip(__('loan::lang.tooltip_loanindexaction'))
                                            </th>
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
                                                <span>{{ trans_choice('loan::general.balance', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loanindexbalance'))</span>
                                            </th>
                                            <th>
                                                <span>{{ trans('loan::general.disbursed') }}
                                                    {{ trans('core.date') }}
                                                    @show_tooltip(__('loan::lang.tooltip_loanindexdisburseddate'))</span>
                                            </th>
                                            <th>
                                                <span>{{ trans_choice('loan::general.product', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loanindexproduct'))</span>
                                            </th>
                                            <th>
                                                {{ trans_choice('core.interest', 1) }}
                                                {{ trans_choice('core.due', 1) }}
                                            </th>

                                            <th>
                                                {{ trans_choice('core.maturity_date', 1) }}
                                            </th>

                                            <th>
                                                <span>{{ trans_choice('loan::general.status', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loanindexstatus'))</span>
                                            </th>

                                            <th>
                                                {{ trans_choice('core.fee', 2) }}
                                            </th>

                                            <th>
                                                {{ trans_choice('core.penalty', 2) }}
                                            </th>

                                            <th>
                                                {{ trans_choice('core.current', 1) }}
                                                {{ trans_choice('core.amount', 1) }}
                                                {{ trans_choice('core.due', 1) }}
                                            </th>

                                            <th>
                                                {{ trans_choice('core.total', 1) }}
                                                {{ trans_choice('core.amount', 1) }}
                                                {{ trans_choice('core.paid', 1) }}
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
                                                        <div class="btn-group">

                                                            <button href="#" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown"
                                                                aria-expanded="false"> {{ trans_choice('core.action', 1) }}
                                                                <span class="caret"></span><span class="sr-only"></span>
                                                            </button>

                                                            <div class="dropdown-menu dropdown-menu-left">
                                                                <a href="{{ url('contact_loan/' . $key->id . '/show') }}" class="dropdown-item">
                                                                    <i class="fas fa-eye"></i>
                                                                    <span>{{ trans_choice('core.detail', 2) }}
                                                                        @show_tooltip(__('loan::lang.tooltip_loanindexactiondetails'))</span>
                                                                </a>

                                                                @if (($key->status == 'submitted' || $key->status == 'pending') && Auth::user()->can('loan.loans.edit'))
                                                                    <a href="{{ url('contact_loan/' . $key->id . '/edit') }}" class="dropdown-item">
                                                                        <i class="fas fa-edit"></i>
                                                                        <span>{{ trans_choice('core.edit', 1) }}
                                                                            @show_tooltip(__('loan::lang.tooltip_loanindexactionedit'))</span>
                                                                    </a>

                                                                    @if (($key->status == 'submitted' || $key->status == 'pending') && Auth::user()->can('loan.loans.edit'))
                                                                        <a href="{{ url('contact_loan/' . $key->id . '/destroy') }}"
                                                                            class="dropdown-item confirm">
                                                                            <i class="fas fa-trash"></i>
                                                                            <span>{{ trans_choice('core.delete', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_loanindexactiondelete'))
                                                                            </span>
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>

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
                                                        <span>{{ number_format($key->principal, 2) }}</span>
                                                    </td>

                                                    {{-- Balance --}}
                                                    <td style="text-align:right">
                                                        <span>{{ number_format($key->current_balance, 2) }}</span>
                                                    </td>

                                                    {{-- Disursement date --}}
                                                    <td>
                                                        <span>{{ $key->disbursed_on_date }}</span>
                                                    </td>

                                                    {{-- Loan product --}}
                                                    <td>
                                                        <span>{{ $key->loan_product->name }}</span>
                                                    </td>

                                                    {{-- Interest --}}
                                                    <td>
                                                        <span>
                                                            {{-- Interest (amount) --}}
                                                            {{ number_format($key->repayment_schedules->sum('total_interest')) }}
                                                        </span>
                                                        {{-- Interest (%) --}}
                                                        <p class="text-muted">({{ number_format($key->interest_rate) }} %)</p>
                                                    </td>

                                                    {{-- Maturity date --}}
                                                    <td>
                                                        {{ $key->maturity_date }}
                                                    </td>

                                                    {{-- Status --}}
                                                    <td>
                                                        {!! $key->status_label !!}
                                                    </td>

                                                    {{-- Fees --}}
                                                    <td>
                                                        {{ number_format($key->repayment_schedules->sum('total_fees'), 2) }}
                                                    </td>

                                                    {{-- Penalties --}}
                                                    <td>
                                                        {{ number_format($key->repayment_schedules->sum('total_penalties'), 2) }}
                                                    </td>

                                                    {{-- Amount due --}}
                                                    <td>
                                                        {{ number_format($key->current_amount_due, 2) }}
                                                    </td>

                                                    {{-- Amount paid --}}
                                                    <td>
                                                        {{ number_format($key->repayment_schedules->sum('total_paid'), 2) }}
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
        @endcan
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
