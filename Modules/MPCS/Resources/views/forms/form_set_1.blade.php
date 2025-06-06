@extends('layouts.app')
@section('title', __('mpcs::lang.form_set_1'))

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        @if (auth()->user()->can('f9c_form'))
                            <li class="active">
                                <a href="#9c_form_tab" class="9c_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.9c_form')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f15a9abc_form'))
                            <li class="">
                                <a href="#15a9ab_form_tab" class="15a9ab_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.15a9ab_form')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f15a9abc_form'))
                            <li class="">
                                <a href="#9b_form_tab" class="9b_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.9b_form')</strong>
                                </a>
                            </li>
                        @endif

                        <!--@if (auth()->user()->can('f21c_form'))
    -->
                        <!--<li class="">-->
                        <!--    <a href="#21c_form_tab" class="21c_form_tab" data-toggle="tab">-->
                        <!--        <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.21c_form')</strong>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <!--
    @endif-->
                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->can('f9c_form'))
                            <div class="tab-pane active" id="9c_form_tab">
                                @include('mpcs::forms.partials.9c_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('f15a9abc_form'))
                            <div class="tab-pane" id="15a9ab_form_tab">
                                @include('mpcs::forms.partials.15a9ab_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('f15a9abc_form'))
                            <div class="tab-pane" id="9b_form_tab">
                                @include('mpcs::forms.partials.9b_form')
                            </div>
                        @endif

                        @if (auth()->user()->can('f21c_form'))
                            <div class="tab-pane" id="21c_form_tab">
                                @include('mpcs::forms.partials.21c_form')
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script type="text/javascript">
        $('#form_9C_location_id option:eq(1)').attr('selected', true);
        $('#f15a9ab_location_id option:eq(1)').attr('selected', true);
        $('#16a_location_id option:eq(1)').attr('selected', true);
        $('#f21c_location_id option:eq(1)').attr('selected', true);

        $(document).ready(function() {

            //form 9c section
            // $('#9c_date_range').daterangepicker();
            // if ($('#9c_date_range').length == 1) {
            //     $('#9c_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            //         $('#9c_date_range').val(
            //             start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            //         );
            //     });
            //     $('#9c_date_range').on('cancel.daterangepicker', function(ev, picker) {
            //         $('#product_sr_date_filter').val('');
            //     });
            //     $('#9c_date_range')
            //         .data('daterangepicker')
            //         .setStartDate(moment().startOf('month'));
            //     $('#9c_date_range')
            //         .data('daterangepicker')
            //         .setEndDate(moment().endOf('month'));
            // }

            $('#9c_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end, label) {
                     // Detect if custom range was selected
                     if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Update input field with selected date
                        $('#9c_date_range').val(
                            start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                        );
                        console.log(label);
                       
    
                        // Call your custom function
                        get9CForm();
                    }
                }
            );

            $('#form_15a9ab_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end, label) {
                     // Detect if custom range was selected
                     if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Update input field with selected date
                        $('#form_15a9ab_date_range').val(
                            start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                        );
                        console.log(label);
                       
    
                        // Call your custom function
                        get9CForm();
                    }
                }
            );
            $('#9b_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end, label) {
                     // Detect if custom range was selected
                     if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Update input field with selected date
                        $('#9b_date_range').val(
                            start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                        );
                        // console.log(label);
                       
    
                        // Call your custom function
                        get9CForm();
                    }
                }
            );
            

            $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#9c_date_range').length) {
                        $('#9c_date_range').val(fullRange);
                        $('#9c_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#9c_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        if (typeof get9CForm === 'function') get9CForm();
                    }

                    // === Update #form_15a9ab_date_range if it exists ===
                    if ($('#form_15a9ab_date_range').length) {
                        $('#form_15a9ab_date_range').val(fullRange);
                        $('#form_15a9ab_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#form_15a9ab_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range_15a9ab").text("Date Range: " + fullRange);
                        // if (typeof getForm15A9AB === 'function') getForm15A9AB();
                    }
                    if ($('#9b_date_range').length) {
                        $('#9b_date_range').val(fullRange);
                        $('#9b_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#9b_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range_15a9ab").text("Date Range: " + fullRange);
                        // if (typeof getForm15A9AB === 'function') getForm15A9AB();
                    }
                    

                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });


            $('#9c_date_range').on('apply.daterangepicker', function(ev, picker) {
                // console.log("this is executing");

                // Access the selected range label
                let chosenLabel = picker.chosenLabel;
                // console.log("Chosen Label:", chosenLabel);

                // If label is 'Custom range', show the modal
                if (chosenLabel === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                }else{

                    get9CForm();
                }

            });

            $('#form_15a9ab_date_range').on('apply.daterangepicker', function(ev, picker) {
                console.log("this is executing");

                // Access the selected range label
                let chosenLabel = picker.chosenLabel;
                console.log("Chosen Label:", chosenLabel);

                // If label is 'Custom range', show the modal
                if (chosenLabel === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                }else{

                    get9CForm();
                }

            });
            $('#9b_date_range').on('apply.daterangepicker', function(ev, picker) {
                // console.log("this is executing");

                // Access the selected range label
                let chosenLabel = picker.chosenLabel;
                // console.log("Chosen Label:", chosenLabel);

                // If label is 'Custom range', show the modal
                if (chosenLabel === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                }else{

                    get9CForm();
                }

            });
            

            

            $('#form_9C_location_id').change(function() {
                get9CForm();
            });
            get9CForm();

            function get9CForm() {
                var start_date = $('input#9c_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#9c_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                start_date = start_date;
                end_date = end_date;
                location_id = $('#form_9C_location_id').val();

                $.ajax({
                    method: 'get',
                    url: '/mpcs/get-21c-form',
                    data: {
                        start_date,
                        end_date,
                        location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        console.log(result);
                        $('#9c_details_section').empty().append(result);
                        get_this_page_total_9c();
                        get_previous_value_9c();
                    },
                });
            }

            function get_previous_value_9c() {
                var start_date = $('input#9c_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#9c_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                var location_id = $('#form_9C_location_id').val();

                $.ajax({
                    method: 'get',
                    url: '/mpcs/get_previous_value_9c',
                    data: {
                        start_date,
                        end_date,
                        location_id
                    },
                    success: function(result) {
                        let pre_total_amount = 0;
                        Object.keys(result).forEach((i) => {
                            pre_total_amount += parseFloat(result[i].amount);
                            $('#footer_f9c_qty_pre_page_' + i).text(__number_f(result[i].qty,
                                false, false, __currency_precision));
                            $('#footer_f9c_amount_pre_page_' + i).text(__number_f(result[i]
                                .amount, false, false, __currency_precision));

                        });
                        $('#footer_f9c_total_amount_pre_page').text(__number_f(pre_total_amount, false,
                            false, __currency_precision));

                        @foreach ($sub_categories as $sub_cat)
                            var this_page_qty_{{ $sub_cat->id }} = __read_number_from_text($(
                                '#footer_f9c_qty_this_page_{{ $sub_cat->id }}'));
                            var this_page_amount_{{ $sub_cat->id }} = __read_number_from_text($(
                                '#footer_f9c_amount_this_page_{{ $sub_cat->id }}'));

                            var pre_page_qty_{{ $sub_cat->id }} = __read_number_from_text($(
                                '#footer_f9c_qty_pre_page_{{ $sub_cat->id }}'));
                            var pre_page_amount_{{ $sub_cat->id }} = __read_number_from_text($(
                                '#footer_f9c_amount_pre_page_{{ $sub_cat->id }}'));

                            var grand_total_qty_{{ $sub_cat->id }} =
                                this_page_qty_{{ $sub_cat->id }} + pre_page_qty_{{ $sub_cat->id }};
                            var grand_total_amount_{{ $sub_cat->id }} =
                                this_page_amount_{{ $sub_cat->id }} +
                                pre_page_amount_{{ $sub_cat->id }};

                            $('#footer_f9c_qty_grand_{{ $sub_cat->id }}').text(__number_f(
                                grand_total_qty_{{ $sub_cat->id }}, false, false,
                                __currency_precision));
                            $('#footer_f9c_amount_grand_{{ $sub_cat->id }}').text(__number_f(
                                grand_total_amount_{{ $sub_cat->id }}, false, false,
                                __currency_precision));
                        @endforeach

                        var footer_f9c_total_amount_this_page = __read_number_from_text($(
                            '#footer_f9c_total_amount_this_page'));
                        console.log("total:" + footer_f9c_total_amount_this_page);
                        var footer_f9c_total_amount_pre_page = __read_number_from_text($(
                            '#footer_f9c_total_amount_pre_page'));


                        $('#footer_f9c_total_amount_grand').text(__number_f(
                            footer_f9c_total_amount_this_page +
                            footer_f9c_total_amount_pre_page, false, false, __currency_precision
                        ));
                    },
                });
            }

            function get_this_page_total_9c() {

                @foreach ($sub_categories as $item)

                    var total_qty_{{ $item->id }} = sum_table_col($('#form_9c_table'),
                        '{{ $item->id }}_qty');
                    $('#footer_f9c_qty_this_page_{{ $item->id }}').text(__number_f(
                        total_qty_{{ $item->id }}, false, false, __currency_precision));
                    var total_amount_{{ $item->id }} = sum_table_col($('#form_9c_table'),
                        '{{ $item->id }}_amount');
                    $('#footer_f9c_amount_this_page_{{ $item->id }}').text(__number_f(
                        total_amount_{{ $item->id }}, false, false, __currency_precision));
                @endforeach
                var total_amount = sum_table_col($('#form_9c_table'), 'total_amount');

                $('#footer_f9c_total_amount_this_page').text(__number_f(total_amount, false, false,
                    __currency_precision));
            }


            //form 16a section
            $('#form_16a_date').datepicker({
                autoclose: true
            }).on('changeDate', function() {
                form_16a_table.ajax.reload();
            });

            //form_16a_table 
            form_16a_table = $('#form_16a_table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    url: '/mpcs/get-form-16a',
                    data: function(d) {
                        var selectedDate = $('#form_16a_date').datepicker('getDate');
                        if (selectedDate) {
                            var formattedDate = selectedDate.getFullYear() + '-' +
                                ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                                ('0' + selectedDate.getDate()).slice(-2);
                            $('.from_date').text(formattedDate);
                        }
                        d.start_date = formattedDate;
                        d.end_date = formattedDate;
                        d.location_id = $('#16a_location_id').val();
                    }
                },
                columns: [{
                        data: 'index_no',
                        name: 'index_no'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'received_qty',
                        name: 'received_qty'
                    },
                    {
                        data: 'unit_purchase_price',
                        name: 'unit_purchase_price'
                    },
                    {
                        data: 'total_purchase_price',
                        name: 'total_purchase_price'
                    },
                    {
                        data: 'unit_sale_price',
                        name: 'unit_sale_price'
                    },
                    {
                        data: 'total_sale_price',
                        name: 'total_sale_price'
                    },
                    {
                        data: 'reference_no',
                        name: 'reference_no'
                    },
                    {
                        data: 'stock_book_no',
                        name: 'stock_book_no'
                    },
                ],
                fnDrawCallback: function(oSettings) {
                    // Handle the pagination update and update form numbers
                    let pageInfo = form_16a_table.page.info(); // Get pagination info
                    let pageNumber = pageInfo.page + 1; // Get the current page number (1-based)

                    // Set the form number based on the page number
                    let formNumber = "{{ $form_9a_no }}"; // Or customize this logic as needed
                    // Check if the total pages are more than 1
                    if (pageInfo.pages > 1) {
                        // If more than one page, append the page number to the form number
                        let newFormNumber = formNumber + '-' + pageNumber;
                        $('#form_no1').text(newFormNumber); // Update the form number on the page
                    } else {
                        // If there's only one page, don't append '-1'
                        $('#form_no1').text(formNumber); // Just show the base form number
                    }
                    caculateF16AFromTotal();
                    get_previous_value_16a();
                },
            });

            // let selected_date = date[0];
            var F16A_this_total = [];
            var F16A_pre_total = [];
            var F16A_grand_total = [];

            function caculateF16AFromTotal() {
                var total_purchase_price =
                    @if (optional($setting)->F16A_first_day_after_stock_taking == 1)
                        0
                    @else
                        sum_table_col($('#form_16a_table'), 'total_purchase_price')
                    @endif ;
                $('#footer_F16A_total_purchase_price').text(__number_f(total_purchase_price, false, false,
                    __currency_precision));
                var total_sale_price =
                    @if (optional($setting)->F16A_first_day_after_stock_taking == 1)
                        0
                    @else
                        sum_table_col($('#form_16a_table'), 'total_sale_price')
                    @endif ;
                $('#footer_F16A_total_sale_price').text(__number_f(total_sale_price, false, false,
                    __currency_precision));
                $('#total_this_p').val(total_purchase_price);
                $('#total_this_s').val(total_sale_price);
            }

            $('#16a_location_id').change(function() {
                form_16a_table.ajax.reload();
                if ($('#16a_location_id').val() !== '' && $('#16a_location_id').val() !== undefined) {
                    $('.f16a_location_name').text($('#16a_location_id :selected').text())
                } else {
                    $('.f16a_location_name').text('All')
                }
            });

            function get_previous_value_16a() {
                var selectedDate = $('#form_16a_date').datepicker('getDate');
                if (selectedDate) {
                    var formattedDate = selectedDate.getFullYear() + '-' +
                        ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                        ('0' + selectedDate.getDate()).slice(-2);
                }
                var location_id = $('#16a_location_id').val();

                $.ajax({
                    method: 'get',
                    url: '/mpcs/get_previous_value_16a',
                    data: {
                        start_date: formattedDate,
                        end_date: formattedDate,
                        location_id
                    },
                    success: function(result) {

                        let footer_total_purchase_price = __read_number($('#total_this_p'));
                        let footer_total_sale_price = __read_number($('#total_this_s'));

                        $('#pre_F16A_total_purchase_price').text(__number_f(result
                            .pre_total_purchase_price, false, false, __currency_precision))
                        $('#pre_F16A_total_sale_price').text(__number_f(result.pre_total_sale_price,
                            false, false, __currency_precision))
                        let grand_total_purchase_price = footer_total_purchase_price + parseFloat(result
                            .pre_total_purchase_price);
                        let grand_total_sale_price = footer_total_sale_price + parseFloat(result
                            .pre_total_sale_price);
                        $('#grand_F16A_total_purchase_price').text(__number_f(
                            grand_total_purchase_price, false, false, __currency_precision))
                        $('#grand_F16A_total_sale_price').text(__number_f(grand_total_sale_price, false,
                            false, __currency_precision))

                    },
                });
            }

            //form 21C
            $('#form_21c_date_range').daterangepicker();
            $('#form_21c_date_range').daterangepicker({
                onSelect: function() {
                    $(this).change();
                }
            });
            if ($('#form_21c_date_range').length == 1) {
                $('#form_21c_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                    $('#form_21c_date_range').val(
                        start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                    );
                });
                $('#form_21c_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#product_sr_date_filter').val('');
                });
                $('#form_21c_date_range')
                    .data('daterangepicker')
                    .setStartDate(moment().startOf('month'));
                $('#form_21c_date_range')
                    .data('daterangepicker')
                    .setEndDate(moment().endOf('month'));

                get_21_c_form_all_query();
            }

            let f21c_date = $('#form_21c_date_range').val().split(' - ');

            $('.21c_from_date').text(f21c_date[0]);
            $('.21c_to_date').text(f21c_date[1]);

            if ($('#f21c_location_id').val() !== '' && $('#f21c_location_id').val() !== undefined) {
                $('.f21c_location_name').text($('#f21c_location_id :selected').text())
            } else {
                $('.f21c_location_name').text('All')
            }

            $('#form_21c_date_range, #f21c_location_id').change(function() {
                let f21c_date = $('#form_21c_date_range').val().split(' - ');
                $('.21c_from_date').text(f21c_date[0]);
                $('.21c_to_date').text(f21c_date[1]);

                if ($('#f21c_location_id').val() !== '' && $('#f21c_location_id').val() !== undefined) {
                    $('.f21c_location_name').text($('#f21c_location_id :selected').text())
                } else {
                    $('.f21c_location_name').text('All')
                }
                console.log('change');
                get_21_c_form_all_query();
            });


            $('#form_21c_date_range').click(function() {
                get_21_c_form_all_query();
            });

            var today = 0;
            var previous = 0;
            var opening = 0;
            var today_inc = 0;
            var predate_inc = 0;


            $('#_own_usage_sales_today').on('keyup', function() {
                var cash_sales_today = $('#_cash_sales_today').val() == "" ? 0 : $('#_cash_sales_today')
                    .val();
                var credit_sales_today = $('#_credit_sales_today').val() == "" ? 0 : $(
                    '#_credit_sales_today').val();
                var own_usage_sales_today = $('#_own_usage_sales_today').val() == "" ? 0 : $(
                    '#_own_usage_sales_today').val();
                var price_reduction_today = $('#_price_reduction_today').val() == "" ? 0 : $(
                    '#_price_reduction_today').val();
                var price_reduction_predate = $('#_price_reduction_predate').val() == "" ? 0 : $(
                    '#_price_reduction_predate').val();

                $('#_price_reduction_total').val(parseInt(price_reduction_today) + parseInt(
                    price_reduction_predate));

                $('#_total_issued_today').val(parseInt(cash_sales_today) + parseInt(credit_sales_today) +
                    parseInt(own_usage_sales_today) + parseInt(price_reduction_today))

            });

            $('#_price_reduction_today').on('keyup', function() {
                var cash_sales_today = $('#_cash_sales_today').val() == "" ? 0 : $('#_cash_sales_today')
                    .val();
                var credit_sales_today = $('#_credit_sales_today').val() == "" ? 0 : $(
                    '#_credit_sales_today').val();
                var own_usage_sales_today = $('#_own_usage_sales_today').val() == "" ? 0 : $(
                    '#_own_usage_sales_today').val();
                var price_reduction_today = $('#_price_reduction_today').val() == "" ? 0 : $(
                    '#_price_reduction_today').val();
                var price_reduction_predate = $('#_price_reduction_predate').val() == "" ? 0 : $(
                    '#_price_reduction_predate').val();

                $('#_price_reduction_total').val(parseInt(price_reduction_today) + parseInt(
                    price_reduction_predate));

                $('#_total_issued_today').val(parseInt(cash_sales_today) + parseInt(credit_sales_today) +
                    parseInt(own_usage_sales_today) + parseInt(price_reduction_today))

            })

            function get_21_c_form_all_query() {
                var start_date = $('input#form_21c_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#form_21c_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                var location_id = $('#f21c_location_id').val();

                $.ajax({
                    method: 'get',
                    url: '/mpcs/get_21_c_form_all_query',
                    data: {
                        start_date,
                        end_date,
                        location_id
                    },
                    success: function(result) {

                        console.log(result);
                        today = $('#_today').val(result.today);

                        previous = $('#_previous_day').val(result.previous_day);

                        opening = $('#_opening_stock').val(Math.round(result.opening_stock * Math.pow(
                            10, 2)) / Math.pow(10, 2));
                        //   Added by rmtemplate
                        $('#_total_receipts').val(parseInt(result.today) + parseInt(result
                            .previous_day));
                        today_inc = $('#_price_increment_today').val(0);
                        predate_inc = $('#_price_increment_pre_date').val(0);

                        $('#_cash_sales_today').val(result.cash_sales_today);
                        $('#_credit_sales_today').val(result.credit_sales_today);
                        $('#_own_usage_sales_today').val(0);
                        $('#_price_reduction_today').val(0);

                        if (result.form17_decrease_previous == null) {
                            $('#_price_reduction_predate').val(0);
                        } else {
                            $('#_price_reduction_predate').val(parseFloat(result
                                .form17_decrease_previous.new_price));
                        }

                        $('#_price_reduction_total').val(parseInt($('#_price_reduction_predate')
                            .val()) + parseInt($('#_own_usage_sales_today').val()));

                        $('#_total_issued_today').val(parseInt(result.cash_sales_today) + parseInt(
                            result.credit_sales_today) + parseInt($('#_own_usage_sales_today')
                            .val()) + parseInt($('#_price_reduction_predate').val()))


                        var price_increment_previous = $('#_price_increment_pre_date').val() == "" ? 0 :
                            $('#_price_increment_pre_date').val();

                        var price_increment_today = $('#_price_increment_today').val() == "" ? 0 : $(
                            '#_price_increment_today').val();

                        $('#_price_increment_total').val(parseInt(price_increment_previous) + parseInt(
                            price_increment_today));

                        //  End
                        // cal_total_receipt(parseInt(today),parseInt(previous),parseInt(opening),parseInt(today_inc),parseInt(predate_inc));
                        cal_total_receipt();
                    },
                });
            }

            $('#_price_increment_today').on('keyup', function() {

                cal_total_receipt(today, previous, opening, today_inc, predate_inc)

            });

            function cal_total_receipt(today, previous, opening, today_inc, predate_inc) {

                today = $('#_today').val() == "" ? 0 : $('#_today').val();

                previous = $('#_previous_day').val() == "" ? 0 : $('#_previous_day').val();

                opening = $('#_opening_stock').val() == "" ? 0 : $('#_opening_stock').val();

                var price_increment_previous = $('#_price_increment_pre_date').val() == "" ? 0 : $(
                    '#_price_increment_pre_date').val();

                var price_increment_today = $('#_price_increment_today').val() == "" ? 0 : $(
                    '#_price_increment_today').val();

                $('#_price_increment_total').val(parseInt(price_increment_previous) + parseInt(
                    price_increment_today));

                $('#_total_receipt_to_date').val(parseInt(today) + parseInt(previous) + parseInt(opening) +
                    parseInt(price_increment_previous) + parseInt(price_increment_today));
            }

            // //form 15a9ab
            // $('#form_15a9ab_date_range').daterangepicker();
            // if ($('#form_15a9ab_date_range').length == 1) {
            //     $('#form_15a9ab_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            //         $('#form_15a9ab_date_range').val(
            //             start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            //         );
            //     });
            //     $('#form_15a9ab_date_range').on('cancel.daterangepicker', function(ev, picker) {
            //         $('#product_sr_date_filter').val('');
            //     });
            //     $('#form_15a9ab_date_range')
            //         .data('daterangepicker')
            //         .setStartDate(moment().startOf('month'));
            //     $('#form_15a9ab_date_range')
            //         .data('daterangepicker')
            //         .setEndDate(moment().endOf('month'));
            // }

            // let f15a9ab_date = $('#form_15a9ab_date_range').val().split(' - ');

            // $('.15a9ab_from_date').text(f15a9ab_date[0]);
            // $('.15a9ab_to_date').text(f15a9ab_date[1]);

            // if ($('#f15a9ab_location_id').val() !== '' && $('#f15a9ab_location_id').val() !== undefined) {
            //     $('.f15a9ab_location_name').text($('#f15a9ab_location_id :selected').text())
            // } else {
            //     $('.f15a9ab_location_name').text('All')
            // }

            // $('#form_15a9ab_date_range, #f15a9ab_location_id').change(function() {
            //     let f15a9ab_date = $('#form_15a9ab_date_range').val().split(' - ');
            //     $('.15a9ab_from_date').text(f15a9ab_date[0]);
            //     $('.15a9ab_to_date').text(f15a9ab_date[1]);

            //     if ($('#f15a9ab_location_id').val() !== '' && $('#f15a9ab_location_id').val() !==
            //         undefined) {
            //         $('.f15a9ab_location_name').text($('#f15a9ab_location_id :selected').text())
            //     } else {
            //         $('.f15a9ab_location_name').text('All')
            //     }
            // });
        });
    </script>
@endsection
