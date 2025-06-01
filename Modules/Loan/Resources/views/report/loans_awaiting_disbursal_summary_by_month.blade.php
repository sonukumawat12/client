@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 2) }}
    {{ trans_choice('loan::general.awaiting_disbursal', 1) }}
    {{ trans_choice('core.summary', 1) }}
    {{ trans_choice('core.by', 1) }}
    {{ trans_choice('core.month', 1) }}
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
                                            <span>{{ trans_choice('core.location', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexbranch'))</span>
                                        </th>

                                        <th>
                                            <span>{{ trans_choice('core.contact', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexclient'))</span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('lang_v1.account_number') }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>{{ trans_choice('loan::general.product', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexproduct'))</span>
                                        </th>

                                        <th>
                                            <span>{{ trans('loan::general.disbursed') }}
                                                {{ trans('core.date') }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexdisburseddate'))</span>
                                        </th>

                                        <th style="text-align:right">
                                            <span>{{ trans_choice('core.loan', 1) }}
                                                {{ trans_choice('core.amount', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loanindexamount'))</span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('loan::general.written_off') }}
                                                {{ trans('core.date') }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('loan::general.written_off') }}
                                                {{ trans('core.principal') }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('loan::general.written_off') }}
                                                {{ trans_choice('core.interest', 1) }}
                                            </span>
                                        </th>

                                        <th>
                                            <span>
                                                {{ trans('loan::general.written_off') }}
                                                {{ trans_choice('core.note', 2) }}
                                            </span>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($data))
                                        @foreach ($data as $key)
                                            <tr>
                                                <td>
                                                    <a href="{{ url('location/' . $key->location_id . '/show') }}">
                                                        <span>{{ $key->business_location->name }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                {{-- Contact --}}
                                                <td>
                                                    <a href="{{ action('ContactController@show', [$key->contact_id]) }}">
                                                        <span>{{ $key->contact->name }}</span> <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>

                                                {{-- Account Number --}}
                                                <td>
                                                    <span>{{ $key->contact->contact_id ?: trans('core.none') }}</span>
                                                </td>

                                                {{-- Loan product --}}
                                                <td>
                                                    <span>{{ $key->loan_product->name }}</span>
                                                </td>

                                                {{-- Disursement date --}}
                                                <td>
                                                    <span>{{ $key->disbursed_on_date }}</span>
                                                </td>

                                                {{-- Principal --}}
                                                <td style="text-align:right">
                                                    <span>{{ number_format($key->repayment_schedules->sum('principal'), 2) }}</span>
                                                </td>

                                                <td>
                                                    <span>
                                                        {{ $key->written_off_on_date }}
                                                    </span>
                                                </td>

                                                {{-- Written principal --}}
                                                <td>
                                                    <span>{{ number_format($key->repayment_schedules->sum('principal'), 2) }}</span>
                                                </td>

                                                {{-- Written off interest --}}
                                                <td>
                                                    <span>
                                                        {{-- Interest (amount) --}}
                                                        {{ number_format($key->repayment_schedules->sum('total_interest'), 2) }}
                                                    </span>
                                                </td>

                                                {{-- Written off notes --}}
                                                <td>
                                                    <span>
                                                        {{ $key->written_off_notes }}
                                                    </span>
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
