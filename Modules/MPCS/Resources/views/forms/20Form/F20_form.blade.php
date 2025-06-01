<?php
if(!isset($is_ajax)){
    ?>
{{-- @extends('layouts.app') --}}
@extends($layout)
@section('title', 'F20 form')

@section('content')
    <!-- Main content -->
    <section class="content">
        <?php
}
?>


        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="breadcrumbs-area clearfix">
                        <h4 class="page-title pull-left">FORM F20</h4>
                        <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                            <li><a href="#">F20</a></li>
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
                        @if (auth()->user()->can('f16a_form'))
                            <li class="active">
                                <a href="#21c_form_tab" class="21c_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>20 Form Details</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f21c_form'))
                            <li class="">
                                <a href="#21c_form_list_tab" class="21c_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>20 Form Settings</strong>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->can('F21_form'))
                            <div class="tab-pane active" id="21c_form_tab">
                                @include('mpcs::forms.20Form.20_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('F21_form'))
                            <div class="tab-pane" id="21c_form_list_tab">
                                @include('mpcs::forms.20Form.list_f20')
                            </div>
                        @endif


                    </div>

                </div>
            </div>
        </div>


        @if (empty($is_ajax))
    </section>
    <!-- /.content -->
@endsection
@section('javascript')
    @endif
    <script type="text/javascript">
        $(document).ready(function () {
            // Store product IDs from the initial page load
            const productIds = @json(array_keys($products->toArray()));
            window.reloadTable = function () {
            // Function to reload table data via AJAX
                const formDateRange = $('#form_20_date_range_data').val();
                const formType = $('#form_type_select').val();

                // Show loading indicator
                $('#form_20_table_data tbody').html(
                    `<tr><td colspan="${4 + productIds.length}" class="text-center">
                        <i class="fa fa-spinner fa-spin"></i> Loading data...
                    </td></tr>`
                );

                $.ajax({
                    url: '/mpcs/get-form-20-datas', // Endpoint to fetch data
                    method: 'GET',
                    data: {
                        form_date_range: formDateRange,
                        form_type: formType,
                    },
                    success: function (response) {
                    console.log(response);

                    // Clear existing table rows
                    $('#form_20_table_data tbody').empty();

                    // Check if response.products exists and is iterable
                    if (response.products && Array.isArray(response.products)) {
                        const totalQuantities = {};
                        productIds.forEach(productId => {
                            totalQuantities[productId] = 0;
                        });

                        // Loop through each product block
                        response.products.forEach(product => {
                            const billNo = product.bill_no || '';
                            const productId = product.product_id;

                            // Loop through each transaction (detail)
                            product.details.forEach(detail => {
                                let rowHtml = `
                                    <tr class="table-row">
                                        <td>${billNo}</td>
                                        <td>${detail.settlement_no || ''}</td>
                                        <td>${productId}</td>
                                        <td>${product.product_name || ''}</td>`;

                                // Render quantities per product column
                                productIds.forEach(pid => {
                                    const qty = pid == productId ? parseFloat(detail.qty).toFixed(2) : '';
                                    if (pid == productId && qty) {
                                        totalQuantities[pid] += parseFloat(qty);
                                    }
                                    rowHtml += `<td>${qty}</td>`;
                                });

                                rowHtml += `</tr>`;
                                $('#form_20_table_data tbody').append(rowHtml);
                            });
                        });

                        // Render totals
                        if (response.totals && typeof response.totals === 'object') {
                            let qtyRow = '<td></td><td></td><td class="text-bold" colspan="2">Total Qty</td>';
                            let priceRow = '<td></td><td></td><td class="text-bold" colspan="2">Unit Sales Price</td>';
                            let amountRow = '<td></td><td></td><td class="text-bold" colspan="2">Total Amount</td>';

                            productIds.forEach(productId => {
                                const totalData = response.totals[productId] || { qty: 0, unit_price: 0, amount: 0 };

                                qtyRow += `<td class="text-bold">${parseFloat(totalData.qty).toFixed(2)}</td>`;
                                priceRow += `<td class="text-bold">${parseFloat(totalData.unit_price).toFixed(2)}</td>`;
                                amountRow += `<td class="text-bold">${parseFloat(totalData.amount).toFixed(2)}</td>`;
                            });

                            $('#form_20_table_data tfoot').html(`
                                <tr>${qtyRow}</tr>
                                <tr>${priceRow}</tr>
                                <tr>${amountRow}</tr>
                            `);
                        }
                    } else {
                        $('#form_20_table_data tbody').html(
                            `<tr><td colspan="${4 + productIds.length}" class="text-center">
                                No data available for selected filters
                            </td></tr>`
                        );
                        $('#form_20_table_data tfoot').html('');
                    }
                },
                            error: function (xhr, status, error) {
                                console.error("Error fetching data:", error);
                                $('#form_20_table_data tbody').html(
                                    `<tr><td colspan="${4 + productIds.length}" class="text-center text-danger">
                                        Error loading data. Please try again.
                                    </td></tr>`
                                );
                                $('#form_20_table_data tfoot').html('');
                            },
                        });
                };

            // Listen for changes in date range and form type
            $('#form_20_date_range_data, #form_type_select').on('change', function () {
                reloadTable();
            });

            // Initial load of the table
            reloadTable();
        });
        $('#form_20_date_range_data').daterangepicker({
            singleDatePicker: true, // For selecting a single date
            showDropdowns: true, // To show the dropdown for predefined date ranges
            locale: {
                format: 'YYYY-MM-DD', // Adjust the date format according to your needs
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            }
        }, function(start, end, label) {
            if (label === 'Custom Date Range') {
                // Show the modal for manual input
                $('.custom_date_typing_modal').modal('show');
                // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
            }else{
                // Set the input value when a date is selected
                $('#form_20_date_range_data').val(start.format('YYYY-MM-DD'));
            }
        });

        $('#custom_date_apply_button').on('click', function () {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);
                let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                // === Update #9c_date_range if it exists ===
                if ($('#form_20_date_range_data').length) {
                    $('#form_20_date_range_data').val(fullRange);
                    $('#form_20_date_range_data').data('daterangepicker').setStartDate(moment(startDate));
                    $('#form_20_date_range_data').data('daterangepicker').setEndDate(moment(endDate));
                    $("#report_date_range").text("Date Range: " + fullRange);
                    reloadTable();
                }
                // Hide the modal
                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        });

        // Reset the field when the cancel button is clicked
        $('#form_20_date_range_data').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });

        // Set the default selected date range when initializing the date picker
        $('#form_20_date_range_data').data('daterangepicker').setStartDate(moment().startOf('day'));
        $('#form_20_date_range_data').data('daterangepicker').setEndDate(moment().endOf('day'));

        // Display the selected date range on the page
        let date = $('#form_20_date_range_data').val().split(' - ');
        $('.from_date').text(date[0]);
        $('.to_date').text(date[1]);


        $('#f14b_location_id option:eq(1)').attr('selected', true);
        $('#20_location_id option:eq(1)').attr('selected', true);

        
        document.addEventListener("DOMContentLoaded", function() {
            const rowsPerPage = 100; // Number of rows per page
            const table = document.getElementById('form_20_table_data');
            const tableRows = table.querySelectorAll('.table-row'); // Select all table rows you want to paginate
            const paginationControls = document.getElementById('pagination-controls');

            let currentPage = 1; // Start on the first page
            let totalPages = Math.ceil(tableRows.length / rowsPerPage); // Calculate total pages

            // Function to show the rows for the current page
            function showPage(pageNumber) {
                // Hide all rows initially
                tableRows.forEach(row => row.style.display = 'none');

                // Calculate the start and end index for the current page
                const startIndex = (pageNumber - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;

                // Show the rows that belong to the current page
                for (let i = startIndex; i < endIndex && i < tableRows.length; i++) {
                    tableRows[i].style.display = '';
                }

                // Update the pagination controls
                updatePaginationControls(pageNumber);
            }

            // Function to update the pagination buttons
            function updatePaginationControls(pageNumber) {
                let controlsHTML = '';

                // Generate pagination buttons
                for (let i = 1; i <= totalPages; i++) {
                    const activeClass = (i === pageNumber) ? 'active' : ''; // Highlight the current page button
                    controlsHTML +=
                        `<button class="page-button ${activeClass}" onclick="showPage(${i})">${i}</button>`;
                }

                // Insert the pagination buttons into the controls container
                paginationControls.innerHTML = controlsHTML;
            }

            // Show the first page initially
            showPage(currentPage);
        });

        document.addEventListener("DOMContentLoaded", function() {
            let table = document.getElementById("form_20_table_data");
            let formNoElement = document.getElementById("form_no1");

            if (!table || !formNoElement) {
                console.error("Table or form_no1 element not found!");
                return;
            }

            function updateFormNumber() {
                let tableHeight = table.scrollHeight; // Get the table's height in pixels
                let A4Height = 1122; // Approx. A4 page height in pixels (for 96 DPI screens)

                let pages = Math.ceil(tableHeight / A4Height); // Calculate number of pages
                let baseFormNumber = formNoElement.dataset
                    .formNo; // Get original form number (store it in a data attribute)

                if (!baseFormNumber) {
                    baseFormNumber = "{{ $F20_form_sn }}"; // Get the form number from Blade (fallback)
                    formNoElement.dataset.formNo = baseFormNumber; // Store for later use
                }

                formNoElement.textContent = baseFormNumber + '-' + pages; // Update form number
            }

            // Run the function on page load
            updateFormNumber();

            // If content changes dynamically, listen for changes
            new MutationObserver(updateFormNumber).observe(table, {
                childList: true,
                subtree: true
            });
        });
    </script>
    @if (empty($is_ajax))
    @endsection
@endif
