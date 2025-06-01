@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.expected', 1) }} {{ trans_choice('loan::general.loan', 1) }}
    {{ trans_choice('loan::general.repayment', 2) }} {{ trans_choice('core.report', 2) }}
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
                                            {{ trans_choice('loan::general.expected', 1) }} {{ trans_choice('loan::general.repayment', 2) }}
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
                                                    <label class="control-label"
                                                        for="start_date">{{ trans_choice('core.start_date', 1) }}</label>
                                                    <input type="text" value="{{ $start_date }}"
                                                        class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date"
                                                        id="start_date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="end_date">{{ trans_choice('core.end_date', 1) }}</label>
                                                    <input type="text" value="{{ $end_date }}"
                                                        class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date"
                                                        id="end_date" required />
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
                                                    <a href="{{ Request::url() }}" class="btn bg-purple  btn-flat pull-right">
                                                        {{ trans_choice('core.reset', 1) }}!
                                                    </a>
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
                                        <table id="data-table" class="table table-bordered table-condensed table-striped table-hover">
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
                                                    <th colspan="3">{{ trans_choice('core.start_date', 1) }}: {{ $start_date }}</th>
                                                    <th colspan="3">{{ trans_choice('core.end_date', 1) }}: {{ $end_date }}</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="5">{{ trans_choice('loan::general.expected', 1) }}</th>
                                                    <th colspan="5">{{ trans_choice('loan::general.actual', 1) }}</th>
                                                    <th></th>
                                                </tr>
                                                <tr style="background-color: #D1F9FF">
                                                    <th>{{ trans_choice('core.location', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.principal', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.interest', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.fee', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.penalty', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.total', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.principal', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.interest', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.fee', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.penalty', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.total', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.balance', 1) }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_actual_principal = 0;
                                                    $total_actual_interest = 0;
                                                    $total_actual_fees = 0;
                                                    $total_actual_penalties = 0;
                                                    $total_actual_amount = 0;
                                                    $total_expected_principal = 0;
                                                    $total_expected_interest = 0;
                                                    $total_expected_fees = 0;
                                                    $total_expected_penalties = 0;
                                                    $total_expected_amount = 0;
                                                @endphp
                                                @foreach ($data as $key)
                                                    @php
                                                        $total_actual_principal = $total_actual_principal + $key->principal_repaid_derived;
                                                        $total_actual_interest = $total_actual_interest + $key->interest_repaid_derived;
                                                        $total_actual_fees = $total_actual_fees + $key->fees_repaid_derived;
                                                        $total_actual_penalties = $total_actual_penalties + $key->penalties_repaid_derived;
                                                        
                                                        $total_expected_principal = $total_expected_principal + $key->principal;
                                                        $total_expected_interest = $total_expected_interest + $key->interest;
                                                        $total_expected_fees = $total_expected_fees + $key->fees;
                                                        $total_expected_penalties = $total_expected_penalties + $key->penalties;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key->business_location }}</td>
                                                        <td style="text-align:right">{{ number_format($key->principal, 2) }}
                                                        </td>
                                                        <td style="text-align:right">{{ number_format($key->interest, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->fees, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->penalties, 2) }}</td>
                                                        <td style="text-align:right">
                                                            {{ number_format($key->principal + $key->interest + $key->fees + $key->penalties, 2) }}
                                                        </td>
                                                        <td style="text-align:right">{{ number_format($key->principal_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->interest_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->fees_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->penalties_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">
                                                            {{ number_format($key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived, 2) }}
                                                        </td>
                                                        <td style="text-align:right">
                                                            {{ number_format($key->principal + $key->interest + $key->fees + $key->penalties - ($key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived), 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th><b>{{ trans_choice('core.total', 1) }}</b></th>
                                                    <th style="text-align:right">{{ number_format($total_expected_principal, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_expected_interest, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_expected_fees, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_expected_penalties, 2) }}</th>
                                                    <th style="text-align:right">
                                                        {{ number_format($total_expected_principal + $total_expected_interest + $total_expected_fees + $total_expected_penalties, 2) }}
                                                    </th>
                                                    <th style="text-align:right">{{ number_format($total_actual_principal, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_actual_interest, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_actual_fees, 2) }}</th>
                                                    <th style="text-align:right">{{ number_format($total_actual_penalties, 2) }}</th>
                                                    <th style="text-align:right">
                                                        {{ number_format($total_actual_principal + $total_actual_interest + $total_actual_fees + $total_actual_penalties, 2) }}
                                                    </th>
                                                    <th style="text-align:right">
                                                        {{ number_format($total_expected_principal + $total_expected_interest + $total_expected_fees + $total_expected_penalties - ($total_actual_principal + $total_actual_interest + $total_actual_fees + $total_actual_penalties), 2) }}
                                                    </th>
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
@endsection
