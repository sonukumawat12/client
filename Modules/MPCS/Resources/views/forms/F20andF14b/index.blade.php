@extends('layouts.app')
@section('title', __('mpcs::lang.F20andF14b_form'))

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        @if (auth()->user()->can('f20_form'))
                            <li class="">
                                <a href="#20_form" class="20_form" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f20_form')</strong>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">

                        @if (auth()->user()->can('f20_form'))
                            <div class="tab-pane" id="20_form">
                                @include('mpcs::forms.F20andF14b.partials.20_form')
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
    <script>
        // form 14b
        $('#f14b_date').daterangepicker();
        if ($('#f14b_date').length == 1) {
            $('#f14b_date').daterangepicker(dateRangeSettings, function(start, end) {
                $('#f14b_date').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#f14b_date').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#f14b_date')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#f14b_date')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        $(document).ready(function() {
            getForm14b();
            $('#f14b_date, #f14b_location_id').change(function() {
                getForm14b();
            })
        })

        function getForm14b() {
            var start_date = $('input#f14b_date')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#f14b_date')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            start_date = start_date;
            end_date = end_date;
            location_id = $('#f14b_location_id').val();

            $.ajax({
                method: 'get',
                url: '/mpcs/get-form-14b',
                data: {
                    start_date,
                    end_date,
                    location_id
                },
                contentType: 'html',
                success: function(result) {
                    $('#form14B_content').empty().append(result)
                },
            });
        }


        //form 20 
        if ($('#form_20_date_range').length === 1) {
            $('#form_20_date_range').daterangepicker(dateRangeSettings, function(start, end, label) {
                // console.log("Selected label:", label);

                if (label === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                    $('.daterangepicker').hide();
                } else {
                    $('#form_20_date_range').val(
                        start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                    );
                }
            });

            // Handle "Custom Date Range" click BEFORE selecting date
            $('#form_20_date_range').on('show.daterangepicker', function () {
                setTimeout(() => {
                    $('.ranges li').off('click').on('click', function () {
                        const label = $(this).text();
                        if (label === 'Custom Date Range') {
                            $('.custom_date_typing_modal').modal('show');
                            $('.daterangepicker').hide();
                        }
                    });
                }, 0);
            });

            // Handle cancel action
            $('#form_20_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#form_20_date_range').val('');
            });

            // Set default start and end dates
            $('#form_20_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
            $('#form_20_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
            $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#form_20_date_range').length) {
                        $('#form_20_date_range').val(fullRange);
                        $('#form_20_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#form_20_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        form_20_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
        }


        let date = $('#form_20_date_range').val().split(' - ');

        $('.from_date').text(date[0]);
        $('.to_date').text(date[1]);

        $('#f14b_location_id option:eq(1)').attr('selected', true);
        $('#20_location_id option:eq(1)').attr('selected', true);
        $(document).ready(function() {


            //form_20_table 
            form_20_table = $('#form_20_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/mpcs/get-form-20',
                    data: function(d) {
                        var start_date = $('input#form_20_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#form_20_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.location_id = $('#20_location_id').val();
                    }
                },
                columns: [{
                        data: 'DT_Row_Index',
                        name: 'DT_Row_Index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'products.sku'
                    },
                    {
                        data: 'product',
                        name: 'products.name'
                    },
                    {
                        data: 'sold_qty',
                        name: 'transaction_sell_lines.quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'transaction_sell_lines.unit_price'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                ],
                fnDrawCallback: function(oSettings) {
                    var cash_sale = sum_table_col($('#form_20_table'), 'cash_sale');
                    $('#cash_sale').text(__number_f(cash_sale, false, false, __currency_precision));
                    var credit_sale = sum_table_col($('#form_20_table'), 'credit_sale');
                    $('#credit_sale').text(__number_f(credit_sale, false, false, __currency_precision));
                    var grand_total = cash_sale + credit_sale;
                    $('#grand_total').text(__number_f(grand_total, false, false, __currency_precision));
                    // Handle the pagination update and update form numbers
                    let pageInfo = form_20_table.page.info(); // Get pagination info
                    let pageNumber = pageInfo.page + 1; // Get the current page number (1-based)

                    // Set the form number based on the page number
                    let formNumber = "{{ $F20_form_sn }}"; // Or customize this logic as needed
                    if (pageInfo.pages > 1) {
                        $('#form_no1').text(formNumber); // Update the form number on the page

                        let newFormNumber = formNumber + '-' + pageNumber; // Append pagination number
                        $('#form_no1').text(newFormNumber); // Update the form number text
                        $('#F20_form_sn').val(newFormNumber); // Update the form number text
                    } else {
                        // If there's only one page, don't append '-1'
                        $('#form_no1').text(formNumber); // Just show the base form number
                        $('#F20_form_sn').val(
                            formNumber); // Set the base form number in the input field
                    }
                },
            });

            $('#form_20_date_range, #20_location_id').change(function() {
                form_20_table.ajax.reload();
                setTimeout(() => {
                    // get_previous_value_20();
                }, 1500);

                if ($('#20_location_id').val() !== '' && $('#20_location_id').val() !== undefined) {
                    $('.f20_location_name').text($('#20_location_id :selected').text())
                } else {
                    $('.f20_location_name').text('All')
                }
            });

            setTimeout(() => {
                // get_previous_value_20();
            }, 1500);

        });
    </script>
@endsection
