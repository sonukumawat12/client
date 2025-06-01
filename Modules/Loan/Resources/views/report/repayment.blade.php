@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.repayment', 2) }}
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
                            <div class="card ">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        @if (!empty($start_date))
                                            {{ trans_choice('loan::general.repayment', 2) }}
                                            {{ trans('core.for_period') }}: <b>{{ $start_date }} {{ trans('core.to') }}
                                                {{ $end_date }}</b>
                                        @endif
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="{{ Request::url() }}" class="">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label"
                                                        for="location_id">{{ trans_choice('core.location', 1) }}</label>
                                                    <select class="form-control select2" name="location_id" id="location_id" required>
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
                                                    <button type="submit" class="btn bg-olive btn-flat">
                                                        {{ trans_choice('core.filter', 1) }}
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
                                                    <th colspan="3">{{ trans_choice('core.end_date', 1) }}: {{ $end_date }}</th>
                                                </tr>
                                                <tr style="background-color: #D1F9FF">
                                                    <th>{{ trans_choice('core.id', 1) }}</th>
                                                    <th>{{ trans_choice('core.date', 1) }}</th>
                                                    <th>{{ trans_choice('core.contact', 1) }}</th>
                                                    <th>{{ trans_choice('loan::general.loan', 1) }}#</th>
                                                    <th>
                                                        {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.officer', 1) }}
                                                    </th>
                                                    <th>{{ trans_choice('core.payment', 1) }} {{ trans_choice('core.type', 1) }}
                                                    </th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.principal', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.interest', 1) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.fee', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.penalty', 2) }}</th>
                                                    <th style="text-align:right">{{ trans_choice('loan::general.total', 1) }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_principal = 0;
                                                    $total_interest = 0;
                                                    $total_fees = 0;
                                                    $total_penalties = 0;
                                                    $total_amount = 0;
                                                @endphp
                                                @foreach ($data as $key)
                                                    @php
                                                        $total_principal = $total_principal + $key->principal_repaid_derived;
                                                        $total_interest = $total_interest + $key->interest_repaid_derived;
                                                        $total_fees = $total_fees + $key->fees_repaid_derived;
                                                        $total_penalties = $total_penalties + $key->penalties_repaid_derived;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key->id }}</td>
                                                        <td>{{ $key->contact }}</td>
                                                        <td>{{ $key->loan_id }}</td>
                                                        <td>{{ $key->submitted_on }}</td>
                                                        <td>{{ $key->loan_officer }}</td>
                                                        <td>{{ $key->payment_type }}</td>
                                                        <td style="text-align:right">{{ number_format($key->principal_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->interest_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->fees_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">{{ number_format($key->penalties_repaid_derived, 2) }}</td>
                                                        <td style="text-align:right">
                                                            {{ number_format($key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6"><b>{{ trans_choice('core.total', 1) }}</b></td>
                                                    <td style="text-align:right">{{ number_format($total_principal, 2) }}</td>
                                                    <td style="text-align:right">{{ number_format($total_interest, 2) }}</td>
                                                    <td style="text-align:right">{{ number_format($total_fees, 2) }}</td>
                                                    <td style="text-align:right">{{ number_format($total_penalties, 2) }}</td>
                                                    <td style="text-align:right">
                                                        {{ number_format($total_principal + $total_interest + $total_fees + $total_penalties, 2) }}
                                                    </td>
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
