@extends('layouts.app')
@section('title', __('mpcs::lang.14_form'))

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Main content -->
    <section class="content"style="padding-top:10px">
        <div class="page-title-area" id="app">
            <section class="content" style="padding-top:0px">
                <div class="text-right" style="margin-bottom: 10px;">
                    <button class="btn btn-primary" onclick="printForm()">Print</button>
                </div>
                @component('components.widget', ['class' => 'box-primary', 'title' => __('mpcs::lang.F14_form')])
                    <div class="col-md-3" id="location_filter">
                        <div class="form-group">
                            {!! Form::label('f14b_location_id', __('purchase.business_location') . ':') !!}
                            <br />
                            <select v-model="filter.business_location_id" class="form-select filter-select"
                                aria-label="Default select example" @change="setBusinessLocation">
                                <option selected>ALL</option>
                                <template v-for="(business_location,id) in business_locations">
                                    <option :value="id">@{{ business_location }}</option>
                                </template>
                            </select>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Date:</label>
                            <input v-model="filter.date_range"
       class="form-control"
       ref="daterange"
       name="f14b_date"
       type="text"
       readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="type">F14B Form No:</label>
                                <input v-model="filter.form_no" class="form-control" readonly="" name="F14b_from_no"
                                    type="text" value="1" @input="getData" id="F14b_from_no">
                            </div>
                        </div>
                    </div>
                @endcomponent

                @component('components.widget', ['class' => 'box-primary', 'title' => __('mpcs::lang.F14_form')])
                    <div id="form14B_content">
                        <div class="container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li>
                                        <a href="#" aria-label="Previous" @click="prevPage">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <template v-for="(pg,pgind) in pages">
                                        <li :class="(page == (pgind)) ? 'active' : ''"><a href="#"
                                                @click="setPage(pgind)">@{{ pgind + 1 }}</a></li>
                                    </template>
                                    <li>
                                        <a href="#" aria-label="Next" @click="nextPage">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>

                            <div class="row" id="printarea">

                                <template v-for="(sale,ind) in sales">

                                    <div class="col-md-6 col-sm-6  col-xl-4 border">
                                        <div class="col-md-12 "
                                            style="border: 1px solid #333; padding : 10px !important; margin-bottom: 10px !important">
                                            <div class="row">
                                                <div class="col-md-11 col-sm-9 text-center">
                                                    <b>@{{ sale.comapany }}</b><br>
                                                    <b>@lang('mpcs::lang.filling_station')</b><br>
                                                    <b>@lang('mpcs::lang.tel') :</b> @{{ sale.tel }}
                                                </div>
                                                <div class="col-md-1 col-sm-3 text-right">F@{{ setting.F14_form_sn + ind }}</div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6"><b>Date:</b> @{{ sale.date }}</div>
                                                <div class="col-md-6 col-sm-6"><b>Bill No:</b>@{{ setting.F14_form_sn + ind }}</div>
                                                <div class="col-md-6 col-sm-6"><b>Customer:</b> @{{ sale.customer }}a</div>
                                                <div class="col-md-6 col-sm-6"><b>Order No:</b>@{{ sale.order_no }}</div>
                                                <div class="col-md-6 col-sm-6"><b>Vehicle No:</b> @{{ sale.customer_reference }}</div>
                                                <div class="col-md-6 col-sm-6"><b>Our Reference:</b> @{{ sale.sattlement_no }}
                                                </div>
                                            </div>
                                            <table class="table table-bordered table-striped credit_sale_table"
                                                style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('mpcs::lang.voucher_no')</th>
                                                        <th>@lang('mpcs::lang.balance_qty')</th>
                                                        <th>@lang('mpcs::lang.description')</th>
                                                        <th>@lang('mpcs::lang.unit_price')</th>
                                                        <th>@lang('mpcs::lang.amount')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr>
                                                        <td>@{{ sale.order_no }}</td>
                                                        <td>@{{ parseFloat(sale.balance_qty).toFixed((sale.is_fuel) ? fuel_qty_decimals : business.quantity_precision) }}</td>
                                                        <td>@{{ sale.description }}</td>
                                                        <td>@{{ parseFloat(sale.sell_price_inc_tax).toFixed(business.currency_precision) }}</td>
                                                        <td>@{{ parseFloat(sale.final_total).toFixed(sale.currency_precision) }} </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-center">@lang('mpcs::lang.total_amount')</td>
                                                        <td>@{{ parseFloat(sale.final_total).toFixed(sale.currency_precision) }} </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </template>


                            </div>
                        </div>
                    </div>
                @endcomponent

            </section>
        </div>

    </section>

    <script>
        new Vue({
            el: '#app',
            data() {
                return {
                    title: 'F14B Former',
                    business_locations: {!! json_encode($business_locations) !!},
                    setting: {!! json_encode($setting) !!},
                    business: {!! json_encode($business) !!},
                    filter: {
                        business_location_id: "{{ $default_business_location }}",
                        date_range: '',
                        form_no: {{ $setting['F14_form_sn'] }}
                    },
                    fuel_qty_decimals: {{ $fuel_qty_decimals }},
                    credit_sales: [],
                    sales: [],
                    page: 1,
                    pages: []
                };
            },
            mounted() {
                const vm = this; // ✅ Vue instance reference

                window.vueApp = vm; // optional: expose globally if needed outside

                var dateRangeSettings = {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'Custom range': []
                    },
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                };

                // Initialize daterangepicker
                $(this.$refs.daterange).daterangepicker(dateRangeSettings, function(start, end, label) {
                    console.log("Selected label:", label);

                    if (label === "Today" || label === "Yesterday") {
                        vm.filter.date_range = `${start.format('YYYY-MM-DD')}`;
                        vm.getData();
                    } else if (label === 'Custom range') {
                        $('.custom_date_typing_modal').modal('show');
                        $('.daterangepicker').hide();
                    } else {
                        vm.filter.date_range = `${start.format('YYYY-MM-DD')} to ${end.format('YYYY-MM-DD')}`;
                        vm.getData();
                    }
                });

                // Fix custom range click before selection
                $(this.$refs.daterange).on('show.daterangepicker', function() {
                    setTimeout(() => {
                        $('.ranges li').off('click').on('click', function () {
                            const label = $(this).text();
                            if (label === 'Custom range') {
                                $('.custom_date_typing_modal').modal('show');
                                $('.daterangepicker').hide();
                            }
                        });
                    }, 0);
                });

                // Set default date and load data
                vm.filter.date_range = '{{ $startdate }} to {{ $enddate }}';
                vm.getData();

                // ✅ FIXED: Use correct vm reference in delegated handler
                $(document).on('click', '#custom_date_apply_button', function () {
                    console.log("Custom date apply button clicked");

                    let startDate = $('#custom_date_from_year1').val()
                        + $('#custom_date_from_year2').val()
                        + $('#custom_date_from_year3').val()
                        + $('#custom_date_from_year4').val()
                        + "-" + $('#custom_date_from_month1').val()
                        + $('#custom_date_from_month2').val()
                        + "-" + $('#custom_date_from_date1').val()
                        + $('#custom_date_from_date2').val();

                    let endDate = $('#custom_date_to_year1').val()
                        + $('#custom_date_to_year2').val()
                        + $('#custom_date_to_year3').val()
                        + $('#custom_date_to_year4').val()
                        + "-" + $('#custom_date_to_month1').val()
                        + $('#custom_date_to_month2').val()
                        + "-" + $('#custom_date_to_date1').val()
                        + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        const formattedStartDate = moment(startDate).format('YYYY-MM-DD');
                        const formattedEndDate = moment(endDate).format('YYYY-MM-DD');
                        const fullRange = `${formattedStartDate} to ${formattedEndDate}`;

                        vm.filter.date_range = fullRange;
                        vm.getData();

                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });
            },
            methods: {
                setBusinessLocation(a, b) {
                    console.log(this.filter.business_location_id);
                    this.getData();
                },
                prevPage() {
                    if (this.page > 1) {
                        this.setPage(this.page - 1);
                    }
                },
                nextPage() {
                    if (this.page < this.pages.length) {
                        this.setPage(this.page + 1);
                    }
                },
                setPage(page) {
                    this.page = page;
                    this.sales = this.credit_sales[this.pages[this.page]];


                },
                getData() {
                    console.log('get data by axios get');
                    console.log(this.filter.business_location_id);
                    console.log(this.filter.date_range);
                    console.log(this.filter.form_no);

                    axios.get('/mpcs/mpcs/get-form-14', {
                        params: this.filter
                    }).then(res => {
                        if (res.status === 200) {
                            this.credit_sales = res.data;
                            this.pages = Object.keys(res.data);
                            this.setPage(0); // Initialize with the first page
                        }
                    }).catch(err => {
                        console.log(err);
                    });
                },
            }
        });


        function printForm() {
            window.print();
        }
    </script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
            }

            body * {
                visibility: hidden;
                /* Hide everything */
            }

            #printarea,
            #printarea * {
                visibility: visible;
                /* Only make the content div visible */
            }

            #printarea {
                position: absolute;
                top: 0;
                left: 0;
            }
        }

        .filter-select {
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px;
            height: 35px;
            width: 100%;
        }
    </style>
@endsection
