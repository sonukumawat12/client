@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('core.account_statement', 2) }}
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
                                            {{ trans_choice('core.account_statement', 2) }}
                                            {{ trans('core.for_period') }}: <b>{{ $start_date }} {{ trans('core.to') }}
                                                {{ $end_date }}</b>
                                        @endif
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="{{ Request::url() }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="contact_id" class="control-label">{{ trans_choice('core.contact', 1) }}</label>
                                                    <v-select label="name" :options="contacts" :reduce="contact => contact.id" v-model="contact_id">
                                                        <template #search="{attributes, events}">
                                                            <input autocomplete="off" class="vs__search @error('contact_id') is-invalid @enderror"
                                                                v-bind="attributes" v-bind:required="!contact_id" v-on="events" />
                                                        </template>
                                                    </v-select>
                                                    <input type="hidden" name="contact_id" v-model="contact_id">
                                                    @error('contact_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="account_number" class="control-label">{{ trans_choice('loan::general.loan', 1) }}#</label>
                                                    <v-select label="id" :options="available_loans" :reduce="loans => loans.id" v-model="loan_id">
                                                        <template #search="{attributes, events}">
                                                            <input autocomplete="off" class="vs__search @error('loan_id') is-invalid @enderror"
                                                                v-bind="attributes" v-bind:required="!loan_id" v-on="events" />
                                                        </template>
                                                    </v-select>
                                                    <input type="hidden" name="loan_id" v-model="loan_id">
                                                    @error('loan_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label"
                                                        for="location_id">{{ trans_choice('core.location', 1) }}</label>
                                                    <v-select label="name" :options="business_locations" :reduce="location => location.id"
                                                        v-model="location_id">
                                                        <template #search="{attributes, events}">
                                                            <input autocomplete="off" class="vs__search @error('location_id') is-invalid @enderror"
                                                                v-bind="attributes" v-bind:required="!location_id" v-on="events" />
                                                        </template>
                                                    </v-select>
                                                    <input type="hidden" name="location_id" v-model="location_id">
                                                    @error('location_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label"
                                                        for="start_date">{{ trans_choice('core.start_date', 1) }}</label>
                                                    <input type="text" v-model="start_date"
                                                        class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date"
                                                        id="start_date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label" for="end_date">{{ trans_choice('core.end_date', 1) }}</label>
                                                    <input type="text" v-model="end_date"
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
                                <div class="box box-white">
                                    <div class="box-body table-responsive no-padding">
                                        <table id="data-table" class="table table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">
                                                        {{ trans_choice('core.location', 1) }}:
                                                        @if (!empty($data->first()))
                                                            {{ $data->first()->business_location }}
                                                        @endif
                                                    </th>
                                                    <th colspan="2">
                                                        {{ trans_choice('core.contact', 1) }}:
                                                        @if (!empty($data->first()))
                                                            {{ $data->first()->contact }}
                                                        @endif
                                                    </th>
                                                    <th colspan="2">
                                                        {{ trans_choice('loan::general.loan', 1) }}#:
                                                        @if (!empty($data->first()))
                                                            {{ $data->first()->loan_account_number }}
                                                        @endif
                                                    </th>
                                                    <th colspan="1">{{ trans_choice('core.start_date', 1) }}: {{ $start_date }}</th>
                                                    <th colspan="2">{{ trans_choice('core.end_date', 1) }}: {{ $end_date }}</th>
                                                </tr>
                                                <tr style="background-color: #D1F9FF">
                                                    <th>{{ trans_choice('core.id', 1) }}</th>
                                                    <th>{{ trans_choice('core.date', 1) }}</th>
                                                    <th>{{ trans_choice('core.location', 1) }}</th>
                                                    <th>{{ trans_choice('core.type', 1) }}</th>
                                                    <th>{{ trans_choice('core.receipt', 1) }}</th>
                                                    <th>{{ trans_choice('core.payment', 1) }} {{ trans_choice('core.type', 1) }}
                                                    </th>
                                                    <th style="text-align: right">{{ trans_choice('general.debit', 1) }}</th>
                                                    <th style="text-align: right">{{ trans_choice('general.credit', 1) }}</th>
                                                    <th style="text-align: right">{{ trans_choice('core.balance', 1) }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $credit_total = 0;
                                                    $debit_total = 0;
                                                @endphp
                                                @foreach ($data as $key)
                                                    @php
                                                        $credit_total = $credit_total + $key->credit;
                                                        $debit_total = $debit_total + $key->debit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key->id }}</td>
                                                        <td>{{ $key->submitted_on }}</td>
                                                        <td>{{ $key->business_location }}</td>
                                                        <td>{{ $key->transaction_type }}</td>
                                                        <td>{{ $key->receipt }}</td>
                                                        <td>{{ $key->payment_type }}</td>
                                                        <td style="text-align: right">{{ number_format($key->debit, 2) }}</td>
                                                        <td style="text-align: right">{{ number_format($key->credit, 2) }}</td>
                                                        <td style="text-align: right">{{ number_format($key->balance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6"><b>{{ trans_choice('core.total', 1) }}</b></td>
                                                    <td style="text-align: right">{{ number_format($debit_total, 2) }}</td>
                                                    <td style="text-align: right">{{ number_format($credit_total, 2) }}</td>
                                                    <td colspan="1"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">
                                                        <b>
                                                            {{ trans_choice('core.current', 1) }}
                                                            {{ trans_choice('core.balance', 1) }}
                                                        </b>
                                                    </td>
                                                    <td style="text-align: right">{{ number_format($debit_total - $credit_total, 2) }}</td>
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
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                contact_id: parseInt("{{ $contact_id }}"),
                loan_id: parseInt("{{ $loan_id }}"),
                location_id: parseInt("{{ $location_id }}"),
                start_date: "{{ $start_date }}",
                end_date: "{{ $end_date }}",
                business_locations: {!! json_encode($business_locations) !!},
                contacts: {!! json_encode($contacts) !!},
                loans: {!! json_encode($loans) !!},
            },
            computed: {
                available_loans() {
                    return this.loans.filter(item => item.contact_id == this.contact_id);
                }
            },
        })
    </script>
@endsection
