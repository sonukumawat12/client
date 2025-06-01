@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan_funds_movement', 2) }}
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
                                            <span>{{ trans_choice('core.id', 1) }}</span>
                                        </th>
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
                                            <span>
                                                {{ trans('loan::general.disbursal') }}
                                                {{ trans('core.date') }}
                                            </span>
                                        </th>
                                        <th style="text-align:right">
                                            <span>
                                                {{ trans_choice('core.loan', 1) }}
                                                {{ trans_choice('core.amount', 1) }}
                                                {{ trans_choice('core.disbursed', 1) }}
                                            </span>
                                        </th>
                                        <th style="text-align:right">
                                            <span>
                                                {{ trans_choice('core.no', 1) }}
                                                {{ trans_choice('core.of', 1) }}
                                                {{ trans_choice('loan::general.installment', 2) }}
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
                                        <th>
                                            <span>
                                                {{ trans_choice('core.principal', 1) }}
                                                {{ trans_choice('loan::general.outstanding', 1) }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.interest', 1) }}
                                                {{ trans_choice('loan::general.outstanding', 1) }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.fee', 2) }}
                                                {{ trans_choice('loan::general.outstanding', 1) }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.penalty', 2) }}
                                                {{ trans_choice('loan::general.outstanding', 1) }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.arrears', 2) }}
                                                {{ trans_choice('loan::general.amount', 1) }}
                                            </span>
                                        </th>
                                        <th>
                                            <span>
                                                {{ trans_choice('core.arrears', 2) }}
                                                {{ trans_choice('loan::general.day', 2) }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key)
                                        <tr>
                                            {{-- Id --}}
                                            <td>
                                                {{ $key->id }}
                                            </td>

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

                                            {{-- Disbursal Date --}}
                                            <th>
                                                {{ $key->disbursed_on_date }}
                                            </th>

                                            {{-- Loan Principal --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->repayment_schedules->sum('principal'), 2) }}
                                            </td>

                                            {{-- No of Installments --}}
                                            <td style="text-align: center">
                                                {{ $key->number_of_installments }}
                                            </td>

                                            {{-- Loan Term --}}
                                            <td>
                                                {{ $key->loan_term_label }}
                                            </td>

                                            {{-- Repayment Frequency --}}
                                            <td>
                                                {{ $key->repayment_frequency_label }}
                                            </td>

                                            {{-- Principal Outstanding --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->repayment_schedules->sum('principal_due'), 2) }}
                                            </td>

                                            {{-- Interest Outstanding --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->repayment_schedules->sum('interest_due'), 2) }}
                                            </td>

                                            {{-- Fees Repaid --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->repayment_schedules->sum('fees_repaid_derived'), 2) }}
                                            </td>

                                            {{-- Penalties Outstanding --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->repayment_schedules->sum('penalties_due'), 2) }}
                                            </td>

                                            {{-- Arrears Amount --}}
                                            <td style="text-align: right">
                                                {{ number_format($key->arrears_amount, 2) }}
                                            </td>

                                            {{-- Arrears days --}}
                                            <td style="text-align: right">
                                                {{ $key->arrears_days }}
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
