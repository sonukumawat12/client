@extends('layouts.app')
@section('title', __('loan::lang.loan') . ' ' . __('business.dashboard'))

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>
            @lang('loan::lang.loan')
            <small>@lang('business.dashboard')</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content no-print">

        <div class="row">

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h4><strong><span class="no_loans_pending">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.pending_approval_loans') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->


            <!-- /.col -->
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h4><strong><span class="no_loans_awaiting_disbursement">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.loans_awaiting_disbursements') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info')<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h4><strong><span class="no_loans_active">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.active_loans') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h4><strong><span class="total_disbursed">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.total_loans_disbursed') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->


            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h4><strong><span class="total_repayment">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.total_loans_repayments') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->


            <!-- /.col -->
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h4><strong><span class="total_outstanding">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.total_loans_outstanding') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info')<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h4><strong><span class="total_arrears">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.total_loans_arrears') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h4><strong><span class="no_loans_not_taken_up">&nbsp;</span></strong></h4>
                        <p>{{ __('loan::lang.no_loans_taken_up') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ url('loan') }}" class="small-box-footer">@lang('lang.more_info') <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>

        {{-- Charts --}}
        <div id="charts" v-cloak>
            <div class="row">
                <div class="col-md-4">
                    <label for="year">{{ trans_choice('core.year', 1) }}</label>
                    <div class="form-group d-flex">
                        <input type="text" class="form-control year-datepicker" name="year" id="year" v-model="year">
                        <button class="btn btn-primary" @click="setYear" :disabled="loading">
                            <div v-if="loading">{{ trans('core.loading') }}</div>
                            <div v-if="!loading">{{ trans('core.submit') }}</div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div v-bind:class="`col-sm-${chart.columns}`" v-for="chart in charts">
                    @component('components.widget', ['class' => 'box-warning'])
                        @slot('title')
                            @{{ chart.label }}
                            <div v-if="chart.is_all_time == undefined">@{{ year }}</div>
                        @endslot

                        <div class="row">
                            <div class="col-6">
                                <canvas v-bind:id="chart.id"></canvas>
                            </div>
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>

    </section>

@stop
@section('javascript')
    <script src="{{ Module::asset('loan:js/loans.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const charts = new Vue({
            el: '#charts',
            data() {
                return {
                    year: "{{ get_default_year() }}",
                    chart_objects: {},
                    loading: false,
                    charts: [{
                            id: 'loans_awaiting_disbursement_chart',
                            url: '/contact_loan/dashboard/get_loans_awaiting_disbursement_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.awaiting_disbursement', 1) }}"
                        },
                        {
                            id: 'loans_rejected_chart',
                            url: '/contact_loan/dashboard/get_loans_rejected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.rejected', 1) }}"
                        },
                        {
                            id: 'principal_due_chart',
                            url: '/contact_loan/dashboard/get_principal_projected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.principal', 1) . ' ' . trans_choice('loan::general.projected', 1) }}"
                        },
                        {
                            id: 'principal_collected_chart',
                            url: '/contact_loan/dashboard/get_principal_collected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.principal', 1) . ' ' . trans_choice('loan::general.collected', 1) }}"
                        },
                        {
                            id: 'interest_due_chart',
                            url: '/contact_loan/dashboard/get_interest_projected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.interest', 1) . ' ' . trans_choice('loan::general.projected', 1) }}"
                        },
                        {
                            id: 'interest_collected_chart',
                            url: '/contact_loan/dashboard/get_interest_collected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.interest', 1) . ' ' . trans_choice('loan::general.collected', 1) }}"
                        },
                        {
                            id: 'penalties_due_chart',
                            url: '/contact_loan/dashboard/get_penalties_projected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.penalty', 2) . ' ' . trans_choice('loan::general.projected', 1) }}"
                        },
                        {
                            id: 'penalties_collected_chart',
                            url: '/contact_loan/dashboard/get_penalties_collected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.penalty', 2) . ' ' . trans_choice('loan::general.collected', 1) }}"
                        },
                        {
                            id: 'fees_due_chart',
                            url: '/contact_loan/dashboard/get_fees_projected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.fee', 2) . ' ' . trans_choice('loan::general.projected', 1) }}"
                        },
                        {
                            id: 'fees_collected_chart',
                            url: '/contact_loan/dashboard/get_fees_collected_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.fee', 2) . ' ' . trans_choice('loan::general.collected', 1) }}"
                        },
                        {
                            id: 'total_paid_chart',
                            url: '/contact_loan/dashboard/get_total_paid_chart',
                            columns: 6,
                            label: "{{ trans_choice('loan::general.total', 2) . ' ' . trans_choice('loan::general.paid', 2) }}"
                        },
                        // {
                        //     id: 'open_loans_statuses',
                        //     url: '/get_open_loans_statuses_chart',
                        //     columns: 6,
                        //     label: "{{ trans_choice('loan::general.open_loans_statuses', 2) }}",
                        //     is_all_time: true
                        // },
                    ]
                }
            },

            mounted() {
                this.populateCharts();
                $(".year-datepicker").datepicker({
                    format: "yyyy",
                    viewMode: "years",
                    minViewMode: "years",
                }).attr('readonly', '');
            },

            methods: {
                createChart({
                    chart_id,
                    type,
                    labels,
                    datasets,
                }) {
                    const data = {
                        labels,
                        datasets
                    };

                    const config = {
                        type: type,
                        data
                    };

                    //For updating the charts
                    if (this.chart_objects[chart_id] !== undefined) {
                        this.updateChart({
                            chart: this.chart_objects[chart_id],
                            data,
                        });
                        return;
                    }

                    const chart = new Chart(
                        document.getElementById(chart_id),
                        config
                    );

                    this.chart_objects[chart_id] = chart;
                },

                updateChart({
                    chart,
                    data
                }) {
                    chart.data = data;
                    chart.update();
                },

                populateCharts() {
                    let promises = [];

                    for (const chart of this.charts) {
                        const data = {
                            chart_id: chart.id,
                            label: chart.label,
                            year: this.year,
                        };

                        this.loading = true;

                        const promise = fetch(`${chart.url}?${new URLSearchParams(data).toString()}`)
                            .then(response => response.json())
                            .then(data => this.createChart(data))
                            .catch((error) => console.error('Error:', error));

                        promises.push(promise);
                    }

                    Promise.all(promises).then(() => this.loading = false);
                },

                setYear() {
                    this.year = document.getElementById('year').value;
                }
            },

            watch: {
                year(value) {
                    this.populateCharts();
                }
            }
        });
    </script>
@endsection
