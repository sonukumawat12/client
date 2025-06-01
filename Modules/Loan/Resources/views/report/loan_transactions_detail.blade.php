@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }}
    {{ trans_choice('loan::general.transaction', 1) }}
    {{ trans_choice('core.detail', 2) }}
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

                                <div class="form-group col-md-4">
                                    <label for="loan_officer_id" class="control-label">{{ trans_choice('core.created_by', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanindexofficer'))</label>
                                    <select class="form-control" name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id">
                                        <option value="">{{ trans_choice('core.all', 1) }}
                                            {{ trans_choice('loan::general.loan_officer', 2) }}</option>
                                        @foreach ($loan_officers as $loan_officer)
                                            <option value="{{ $loan_officer->id }}">{{ $loan_officer->user_full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-2 col-xs-2">
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
                            <table class="table table-striped table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('core.created_by', 1) }}</th>
                                        <th>{{ trans_choice('core.date', 1) }}</th>
                                        <th>{{ trans_choice('core.submitted_on', 1) }}</th>
                                        <th>{{ trans_choice('loan::general.transaction', 1) }}
                                            {{ trans_choice('core.type', 1) }}</th>
                                        <th>{{ trans_choice('loan::general.transaction', 1) }}
                                            {{ trans_choice('core.id', 1) }}</th>
                                        <th style="text-align:right">{{ trans_choice('general.debit', 1) }}
                                        </th>
                                        <th style="text-align:right">{{ trans_choice('general.credit', 1) }}
                                        </th>
                                        <th style="text-align:right">{{ trans_choice('loan::general.balance', 1) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($loan_transactions))
                                        <?php
                                        $balance = 0;
                                        ?>
                                        @foreach ($loan_transactions as $key)
                                            <?php
                                            $balance = $balance + $key->debit - $key->credit;
                                            ?>
                                            <tr>
                                                <td>{{ $key->created_by->first_name }} {{ $key->created_by->last_name }}
                                                </td>
                                                <td>{{ $key->created_on }}</td>
                                                <td>{{ $key->submitted_on }}</td>
                                                <td>{{ $key->loan_transaction_type }}</td>
                                                <td>{{ $key->id }}</td>
                                                <td style="text-align:right">
                                                    {{ number_format($key->debit, get_decimal_places()) }}
                                                </td>
                                                <td style="text-align:right">
                                                    {{ number_format($key->credit, get_decimal_places()) }}
                                                </td>
                                                <td style="text-align:right">
                                                    {{ number_format($balance, get_decimal_places()) }}
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
                    location_id: this.getParam('location_id'),
                    loan_officer_id: this.getParam('loan_officer_id'),
                    loan_product_id: this.getParam('loan_product_id'),
                    status: this.getParam('status'),
                }
            },

            methods: {
                getParam(param) {
                    const url_string = window.location.href;
                    const url = new URL(url_string);
                    return url.searchParams.get(param) ?? '';
                }
            },
        })
    </script>

@endsection
