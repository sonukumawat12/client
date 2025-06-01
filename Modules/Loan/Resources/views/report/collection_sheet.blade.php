@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.collection_sheet', 1) }}
    {{ trans_choice('core.report', 1) }}
@endsection

@section('content')

    
    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <div class="card">
                                <div class="card-header with-border">
                                    <h4 class="card-title">
                                        @if (!empty($start_date))
                                            {{ trans_choice('loan::general.collection_sheet', 1) }}
                                            {{ trans('core.for_period') }}: <b>{{ $start_date }} {{ trans('core.to') }}
                                                {{ $end_date }}</b>
                                        @endif
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="{{ Request::url() }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label"
                                                        for="location_id">{{ trans_choice('core.location', 1) }}</label>
                                                    <select class="form-control select2" name="location_id" id="location_id">
                                                        <option value="" disabled selected>{{ trans_choice('core.select', 1) }}</option>
                                                        @foreach ($business_locations as $key)
                                                            <option value="{{ $key->id }}" @if ($location_id == $key->id) selected @endif>
                                                                {{ $key->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="start_date">{{ trans_choice('core.start_date', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportstartdate'))</label>
                                                    <input type="text" value="{{ $start_date }}"
                                                        class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date"
                                                        id="start_date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="end_date">{{ trans_choice('core.end_date', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportenddate'))</label>
                                                    <input type="text" value="{{ $end_date }}"
                                                        class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date"
                                                        id="end_date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="loan_officer_id">{{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans_choice('loan::general.officer', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportloanofficer'))</label>
                                                    <select class="form-control select2" name="loan_officer_id" id="loan_officer_id">
                                                        <option value="" disabled selected>{{ trans_choice('core.select', 1) }}</option>
                                                        @foreach ($users as $key)
                                                            <option value="{{ $key->id }}" @if ($loan_officer_id == $key->id) selected @endif>
                                                                {{ $key->first_name }}
                                                                {{ $key->last_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="loan_product_id">{{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans_choice('loan::general.product', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportproduct'))</label>
                                                    <select class="form-control select2" name="loan_product_id" id="loan_product_id">
                                                        <option value="" disabled selected>{{ trans_choice('core.select', 1) }}</option>
                                                        @foreach ($loan_products as $key)
                                                            <option value="{{ $key->id }}" @if ($loan_product_id == $key->id) selected @endif>
                                                                {{ $key->name }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn bg-olive btn-flat">{{ trans_choice('core.filter', 1) }}
                                                    </button>
                                                </span>
                                                <span class="input-group-btn">
                                                    <a href="{{ Request::url() }}"
                                                        class="btn bg-purple  btn-flat pull-right">{{ trans_choice('core.reset', 1) }}!</a>
                                                </span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->

                            @if (!empty($start_date))
                                <div class="card">
                                    <div class="card-body table-responsive p-0">
                                        <table id="data-table" class="table table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">
                                                        @if (!empty($data->first()) && !empty($location_id))
                                                            {{ trans_choice('core.location', 1) }}:
                                                            {{ $data->first()->business_location }}
                                                        @endif
                                                    </th>
                                                    <th colspan="2">
                                                        @if (!empty($data->first()) && !empty($loan_product_id))
                                                            {{ trans_choice('loan::general.product', 1) }}:
                                                            {{ $data->first()->loan_product }}
                                                        @endif
                                                    </th>
                                                    <th colspan="2">
                                                        @if (!empty($data->first()) && !empty($loan_officer_id))
                                                            {{ trans_choice('loan::general.officer', 1) }}:
                                                            {{ $data->first()->loan_officer }}
                                                        @endif
                                                    </th>
                                                    <th colspan="2">{{ trans_choice('core.start_date', 1) }}: {{ $start_date }}</th>
                                                    <th colspan="2">{{ trans_choice('core.end_date', 1) }}: {{ $end_date }}</th>
                                                </tr>
                                                <tr style="background-color: #D1F9FF">
                                                    <th>
                                                        {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.officer', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportloanofficer'))
                                                    </th>
                                                    <th>
                                                        {{ trans_choice('core.location', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportlocation'))
                                                    </th>
                                                    <th>
                                                        {{ trans_choice('core.contact', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportcontact'))
                                                    </th>
                                                    <th>
                                                        {{ trans_choice('core.mobile', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportclientmobile'))
                                                    </th>
                                                    <th>{{ trans_choice('loan::general.loan', 1) }}#
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportclientloansid'))
                                                    </th>
                                                    <th>{{ trans_choice('loan::general.product', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportproduct'))</th>
                                                    <th>{{ trans_choice('loan::general.expected', 1) }}
                                                        {{ trans_choice('loan::general.maturity', 1) }}
                                                        {{ trans_choice('core.date', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportexpectedmaturitydate'))</th>
                                                    <th>{{ trans_choice('loan::general.repayment', 1) }} {{ trans_choice('core.date', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportrepaymentdate'))</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.expected', 1) }}
                                                        {{ trans_choice('loan::general.amount', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreportexpectedrepayment'))
                                                    </th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.total', 1) }}
                                                        {{ trans_choice('loan::general.due', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanreporttotaldue'))
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_due = 0;
                                                    $total_expected_amount = 0;
                                                @endphp
                                                @foreach ($data as $key)
                                                    @php
                                                        $total_due = $total_due + $key->total_due;
                                                        $total_expected_amount = $total_expected_amount + $key->expected_amount;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key->loan_officer }}</td>
                                                        <td>{{ $key->business_location }}</td>
                                                        <td>{{ $key->contact }}</td>
                                                        <td>{{ $key->mobile }}</td>
                                                        <td>{{ $key->loan_id }}</td>
                                                        <td>{{ $key->loan_product }}</td>
                                                        <td>{{ $key->expected_maturity_date }}</td>
                                                        <td>{{ $key->due_date }}</td>
                                                        <td style="text-align:right">{{ number_format($key->expected_amount, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->total_due, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="8"><b>{{ trans_choice('core.total', 1) }}</b></td>
                                                    <td style="text-align:right">{{ number_format($total_expected_amount, 2) }}</td>
                                                    <td style="text-align:right">{{ number_format($total_due, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </section>
                    @endslot
                @endcomponent
            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable();
        });
    </script>
    <script src="{{ Module::asset('accounting:js/payment.js?v=' . $asset_v) }}"></script>
@endsection
