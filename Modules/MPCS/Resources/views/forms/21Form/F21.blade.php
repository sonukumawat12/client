@extends('layouts.app')
@section('title', __('mpcs::lang.f21_form'))
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="breadcrumbs-area clearfix">
                        <h4 class="page-title pull-left">@lang('mpcs::lang.f21_form')</h4>
                        <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                            <li><a href="#">21 Form</a></li>
                            <li><span>Last Record</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        <!-- @if (auth()->user()->can('f16a_form'))
    <li class="active">
                                                                                                                        <a href="#16a_form_tab" class="16a_form_tab" data-toggle="tab">
                                                                                                                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.16A_form')</strong>
                                                                                                                        </a>
                                                                                                                    </li>
    @endif -->
                        @if (auth()->user()->can('f16a_form'))
                            <li class="active">
                                <a href="#16A_form_tab" class="16A_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f21_form')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f16a_form'))
                            <!-- <li class="">
                                                                                                                        <a href="#16a_form_list_tab" class="16a_form_tab" data-toggle="tab">
                                                                                                                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.16A_form_settings')</strong>
                                                                                                                        </a>
                                                                                                                    </li> -->
                        @endif
                    </ul>
                    <div class="tab-content">

                        @if (auth()->user()->can('f16a_form'))
                            <div class="tab-pane active" id="16A_form_tab">
                                @include('mpcs::forms.21Form.21_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('16a_form'))
                            <div class="tab-pane" id="16a_form_list_tab">

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
        //form 9c section
        // $('#form_21_date_range').daterangepicker();
        // if ($('#form_21_date_range').length == 1) {
        //     $('#form_21_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        //         $('#form_21_date_range').val(
        //             start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
        //         );
        //     });
        //     $('#form_21_date_range').on('cancel.daterangepicker', function(ev, picker) {
        //         $('#product_sr_date_filter').val('');
        //     });
        //     $('#form_21_date_range')
        //         .data('daterangepicker')
        //         .setStartDate(moment().startOf('month'));
        //     $('#form_21_date_range')
        //         .data('daterangepicker')
        //         .setEndDate(moment().endOf('month'));
        // }
        $('#form_21_date_range').daterangepicker({
            singleDatePicker: false, // Allows single date selection
            showDropdowns: true,    // Show year/month dropdowns
            autoUpdateInput: false, // We control what appears in the input
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear',
                applyLabel: 'Apply'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Custom Date Range': [moment().startOf('month'), moment().endOf('month')]
            }
        }, function(start, end, label) {
            if (label === 'Custom Date Range') {
                // Show custom modal for manual input
                $('.custom_date_typing_modal').modal('show');
            } else {
                // Set selected date in the input
                $('#form_21_date_range').val(start.format('YYYY-MM-DD'));

                // Optionally: reload related DataTable or content here
                form_f21_list_table.ajax.reload();
            }
        });

        // Apply button in custom date range modal
        $('#custom_date_apply_button').on('click', function () {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStart = moment(startDate).format('YYYY-MM-DD');
                let formattedEnd = moment(endDate).format('YYYY-MM-DD');
                let fullRange = formattedStart + ' ~ ' + formattedEnd;

                $('#form_21_date_range').val(fullRange);
                $('#form_21_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#form_21_date_range').data('daterangepicker').setEndDate(moment(endDate));
                $("#report_date_range").text("Date Range: " + fullRange);

                form_f21_list_table.ajax.reload();
                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        });


        //form_f21_list_table = $('#form_f21_list_table').DataTable();

        var form_f21_list_table;

        function loadDataTable(transactionType) {
            form_f21_list_table = $('#form_f21_list_table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true, // Destroy existing instance before re-initializing
                ajax: {
                    url: getAjaxUrl(transactionType),
                    data: function(d) {
                        if ($('#form_21_date_range').val()) {
                            var start = $('#form_21_date_range').data('daterangepicker').startDate.format(
                                'YYYY-MM-DD');
                            var end = $('#form_21_date_range').data('daterangepicker').endDate.format(
                                'YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 0;
                        d.location_id = $('#form_21_location_id').val();
                        d.product_id = $('#form_21_product_id').val();
                    }
                },
                columns: [{
                        data: 'transaction_date',
                        name: 'date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'bill_no'
                    },
                    {
                        data: 'book_no',
                        name: 'book_no'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'product_code',
                        name: 'product_code'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'received_qty',
                        name: 'received_qty'
                    },
                    {
                        data: 'sold_qty',
                        name: 'sold_qty'
                    },
                    {
                        data: 'balance_qty',
                        name: 'balance_qty'
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    // Handle the pagination update and update form numbers
                    let pageInfo = form_f21_list_table.page.info(); // Get pagination info
                    let pageNumber = pageInfo.page + 1; // Get the current page number (1-based)

                    // Set the form number based on the page number
                    let formNumber = pageNumber; // Or customize this logic as needed
                    $('#form_no').text(formNumber); // Update the form number on the page

                    let newFormNumber = `${formNumber}` + '-' + pageNumber; // Append pagination number
                    $('#form_no').text(newFormNumber); // Update the form number text

                },
            });
        }

        function getAjaxUrl(transactionType) {
            switch (parseInt(transactionType)) {
                case 0:
                    return "/mpcs/getpos";
                case 1:
                    return "/mpcs/get-settlement";
                case 2:
                    return "/mpcs/get-purchase-order";
                case 3:
                    return "/mpcs/get-sales-return";
                case 4:
                    return "/mpcs/get-purchase-return";
                default:
                    return "/mpcs/getpos";
            }
        }

        $(document).off('change', '#f21_transaction_type, #form_21_date_range, #form_21_location_id, #form_21_product_id');

        $(document).on('change', '#f21_transaction_type, #form_21_date_range, #form_21_location_id, #form_21_product_id',
            function() {
                var transactionType = $('#f21_transaction_type').val();

                if ($(this).attr('id') === 'f21_transaction_type') {
                    loadDataTable(transactionType);
                } else {
                    form_f21_list_table.ajax.reload();
                }
            });


        $(document).ready(function() {
            loadDataTable(0);
        });


        $(document).on('click', '#f21_print', function() {
            var startDate = $('#form_21_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var endDate = $('#form_21_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            var location = $('#form_21_location_id option:selected').text();
            var product = $('#form_21_product_id option:selected').text();
            var transactionType = $('#f21_transaction_type option:selected').text();
            var formNumber = $('#formnumber').val();

            var tableData = form_f21_list_table.rows({
                search: "applied"
            }).data();

            var printWindow = window.open("", "_blank");
            var printContent = `
        <html>
        <head>
            <title>Print Report</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                h2 { text-align: center; }
                .print-header { width: 100%; margin-bottom: 15px; }
                .print-header h5 { margin: 5px 0; font-weight: bold; }
                .text-red { color: red; }
                .text-center { text-align: center; }
            </style>
        </head>
        <body>
            <h2>Transaction Report : ${transactionType}</h2>
            <table>
                <tr class="header-row">
                    <td>Date Range: ${startDate} to ${endDate}</td>
                    <td>Location: ${location}</td>
                    <td>Product: ${product}</td>
                    <td>Transaction Type: ${transactionType}</td>
                </tr>
            </table>

            <!-- Additional Header Section -->
            <div class="print-header">
                <table width="100%">
                    <tr>
                        <td class="text-center"><h5>Filling Station: _________________</h5></td>
                        <td class="text-center"><h5>Date Range From: ${startDate}</h5></td>
                        <td class="text-center"><h5>To: ${endDate}</h5></td>
                        <td class="text-center"><h5>Form No: ${formNumber}</h5></td>
                    </tr>
                </table>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Book No</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Received Qty</th>
                        <th>Sold Qty</th>
                        <th>Balance Qty</th>
                    </tr>
                </thead>
                <tbody>`;

            tableData.each(function(row) {
                printContent += `
            <tr>
                <td>${row.transaction_date}</td>
                <td>${row.invoice_no}</td>
                <td>${row.book_no}</td>
                <td>${row.product_code}</td>
                <td>${row.product_name}</td>
                <td>${row.received_qty}</td>
                <td>${row.sold_qty}</td>
                <td>${row.balance_qty}</td>
            </tr>`;
            });

            printContent += `
                </tbody>
            </table>

        <script>    
            window.print(); // Auto trigger print
            window.onafterprint = function() { window.close(); } // Close after print
        <\/script>    
        </body>
        </html>`;

            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
        });
    </script>
@endsection
