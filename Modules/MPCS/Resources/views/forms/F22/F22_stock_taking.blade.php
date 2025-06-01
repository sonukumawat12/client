@extends('layouts.app')
@section('title', __('mpcs::lang.F22StockTaking_form'))

@section('content')
    <style>
        .half-width-input {
            width: 10%;
        }

        .flex-container {
            display: flex;
        }
    </style>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('mpcs::lang.F22StockTaking_form')
            <small>@lang('mpcs::lang.F22StockTaking_form', ['contacts' => __('mpcs::lang.mange_F22StockTaking_form')])</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#f22_form_tab" class="f22_form_tab" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f22_form')</strong>
                            </a>
                        </li>

                        <li>
                            <a href="#f22_last_verified_stock_tab" class="f22_last_verified_stock_tab" style=""
                                data-toggle="tab">
                                <i class="fa fa-check"></i> <strong>
                                    @lang('mpcs::lang.f22_last_verified_stock') </strong>
                            </a>
                        </li>

                        <li>
                            <a href="#list_f22_stock_taking_tab" class="list_f22_stock_taking_tab" style=""
                                data-toggle="tab">
                                <i class="fa fa-sign-in"></i> <strong>
                                    @lang('mpcs::lang.list_f22_stock_taking') </strong>
                            </a>
                        </li>
                        <li>
                            <a href="#list_f22_link_account_tab" class="list_f22_link_account" style=""
                                data-toggle="tab">
                                <i class="fa fa-sign-in"></i> <strong>
                                    @lang('mpcs::lang.F22_link_account') </strong>
                            </a>
                        </li>


                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="f22_form_tab">
                            @include('mpcs::forms.F22.partials.f22_form')
                        </div>

                        <div class="tab-pane" id="f22_last_verified_stock_tab">
                            @include('mpcs::forms.F22.partials.f22_last_verified_stock')
                        </div>

                        <div class="tab-pane" id="list_f22_stock_taking_tab">
                            @include('mpcs::forms.F22.partials.list_f22_stock_taking')
                        </div>
                        <div class="tab-pane" id="list_f22_link_account_tab">
                            @include('mpcs::forms.F22.partials.list_f22_link_account')
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
    <!-- /.content -->

@endsection
@section('javascript')
 <script type="text/javascript">
        $(document).ready(function() {
            let form22EditedValues = {};
           let newPageLength = parseInt({{ $settings->F22_no_of_product_per_page ?? 25 }});
console.log(newPageLength);
    // Update DataTable page length
    
            $(document).on('click', '.toggle-action-btn', function () {
    const button = $(this);
    const rowId = button.data('id');
    const isEnabled = button.data('enabled') === 'true';

    if (!isEnabled) {
        if (!confirm('Enabling this account will disable all others. Continue?')) {
            return;
        }
    }

    // Proceed with the AJAX call
    $.ajax({
        method: 'POST',
        url: '/mpcs/get_link_account_state', // Laravel route
        data: { row_id: rowId, is_enabled: !isEnabled },
        success: function (response) {
            if (response.success) {
                // Refresh the table to reflect changes
                location.reload();
            } else {
                toastr.error('Failed to update row status.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error toggling row status:', error);
        }
    });
});


            // $('#form_16a_date').daterangepicker();
            // if ($('#form_16a_date').length == 1) {
            //     $('#form_16a_date').daterangepicker(dateRangeSettings, function(start, end) {
            //         $('#form_16a_date').val(
            //             start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            //         );
            //     });
            //     $('#form_16a_date').on('cancel.daterangepicker', function(ev, picker) {
            //         $('#product_sr_date_filter').val('');
            //     });
            //     $('#form_16a_date')
            //         .data('daterangepicker')
            //         .setStartDate(moment().startOf('month'));
            //     $('#form_16a_date')
            //         .data('daterangepicker')
            //         .setEndDate(moment().endOf('month'));
            // }

            var dateRangeSettings = {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [
                        moment().subtract(1, 'year').startOf('year'),
                        moment().subtract(1, 'year').endOf('year')
                    ],
                    'Custom Date Range': [moment(), moment()]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: true,
               
            };

            // Initialize only if the element exists
                $('#form_16a_date').daterangepicker(dateRangeSettings, function(start, end, label) {

                    if (label === 'Custom Date Range') {
                        $('.custom_date_typing_modal').modal('show');
                        return;
                    }

                    $('#form_16a_date').val(
                        start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                    );
                });

                $('#form_16a_date').on('cancel.daterangepicker', function(ev, picker) {
                    $('#product_sr_date_filter').val('');
                });

                $('#form_16a_date')
                    .data('daterangepicker')
                    .setStartDate(moment().startOf('month'));

                $('#form_16a_date')
                    .data('daterangepicker')
                    .setEndDate(moment().endOf('month'));

            $('#form_16a_date').change(function() {
                console.log("9ccc");
               
            });

            // Apply button in custom date range modal
        
         $.ajax({
                url: '/mpcs/fetch-pumps', // Laravel route
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Pumps data fetched successfully!");
                    console.log(response);

                    let html = "";

                    if (response.pumps.length > 0) {
                        // Group pumps by product name
                        let groupedPumps = {};

                        response.pumps.forEach(pump => {
                            if (!groupedPumps[pump.product_name]) {
                                groupedPumps[pump.product_name] = [];
                            }
                            groupedPumps[pump.product_name].push(pump);
                        });

                        // Loop through grouped products
                        for (let product_name in groupedPumps) {
                            html += `
                    <div class="pump-section" style="width:100%;">
                        <h4>${product_name}</h4>
                        <table class="table table-bordered " style="border: none; width: 100%;">
                            <tr>
                                <th class="column-50 no-wrap">Pump Name</th>
                                <th>Current Meter</th>
                                 
                            </tr>`;

                            // Add pump rows for this product
                            groupedPumps[product_name].forEach(pump => {
                                html += `
                            <tr>
                                <td>${pump.pump_name}</td>
                                <td ><input type="text" class="form-control" style="width:85px;"  value="${pump.last_meter_reading}"></td>
                               
                            </tr>`;
                            });

                            html += `</table></div>`;
                        }
                    } else {
                        html = `<p style="color: red;">No pumps found.</p>`; // Handle empty response
                    }

                    $(".pumps-container").html(html); // Insert HTML inside the container
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                    $(".pumps-container").html(
                        `<p style="color: red;">Failed to load pumps. Try again later.</p>`);
                }
            });




            $.ajax({
                url: '/mpcs/check-user-existence', // Adjust the route based on your setup
                type: 'GET',
                success: function(response) {
                   
                    console.log(response);
                    if (response.exists) {
                        $('#save_button').prop('Enabled', true); // Disable Save button
                    } else {
                       $('#save_button').prop('Disable', false); // Enable Save button
                    }
                },
                error: function() {
                    console.log('Error checking user existence');
                }
            });
     
        $(document).on('click', '.edit-button', function(e) {
            e.preventDefault(); // Prevent default action
            $('#save_button').prop('Disable', false); // Enable Save button
        });

        $('#f22_product_id').select2();
        $('#form_date_range').daterangepicker({
            ranges: ranges,
            autoUpdateInput: false,
            locale: {
                format: moment_date_format,
                cancelLabel: LANG.clear,
                applyLabel: LANG.apply,
                customRangeLabel: LANG.custom_range,
            },
        });
       



        $('#f22_location_id option:eq(1)').attr('selected', true);
        $(document).ready(function() {
            form_f22_list_table = $('#form_f22_list_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/mpcs/get-form-f22-list',
                    data: function(d) {
                        d.location_id = $('#f22_location_id').val();
                        d.product_id = $('#f22_product_id').val();
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'locations_name',
                        name: 'location'
                    },
                    {
                        data: 'form_no',
                        name: 'form_no'
                    },
                    {
                        data: 'total_stock_lose_purchase',
                        name: 'total_stock_lose_purchase'
                    },
                  
                    {
                        data: 'total_stock_lose_sale',
                        name: 'total_stock_lose_sale'
                    },
                  
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            form_f22_list_table_stock_taking = $('#form_f22_list_table_stock_taking').DataTable({
                processing: true,
                serverSide: false,
                order: [
                    [3, 'desc']
                ], // Ensure sorting by the first column (updated_at)
                ajax: {
                    url: '/mpcs/get-form-f22-list_gain_loss',
                    data: function(d) {
                        // d.location_id = $('#f22_location_id').val();
                        // d.product_id = $('#f22_product_id').val();
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'stock_loss_account',
                        name: 'stock_loss_account'
                    },
                    {
                        data: 'stock_gain_account',
                        name: 'stock_gain_account'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'added_user',
                        name: 'added_user'
                    },



                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            $('#f22_product_id, #f22_location_id').change(function() {
                form_22_table.ajax.reload();
                if ($('#f22_location_id').val() !== '' && $('#f22_location_id').val() !== undefined) {
                    $('.f22_location_name').text($('#f22_location_id :selected').text());
                    $('#f22_location_name').val($('#f22_location_id :selected').text());
                } else {
                    $('.f22_location_name').text('All');
                    $('#f22_location_name').val('All');
                }
            });


            var ppage_totals = [];
            var spage_totals = [];
            var pre_gppage_totals = [];
            var pre_gspage_totals = [];

            //form_22_table 
            form_22_table = $('#form_22_table').DataTable({
                processing: true,
            serverSide: true,
            paging: true,
            pagingType: "simple", // 👈 Only "Previous" and "Next"
    dom: 't<"bottom"p>',
                pageLength: newPageLength,
                columnDefs: [{
                        "targets": 0,
                        "orderable": false,
                    },

                ],
                ajax: {
                    url: '/mpcs/get-form-f22',
                    data: function(d) {
                        d.location_id = $('#f22_location_id').val();
                        d.product_id = $('#f22_product_id').val();
                    }
                },
                columns: [{
                        data: 'DT_Row_Index',
                        name: 'DT_Row_Index'
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'book_no',
                        name: 'book_no',
                        width: '50px !important'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock',
                        width: '64px'
                    },
                    {
                        data: 'stock_count',
                        name: 'stock_count'
                    },
                    {
                        data: 'unit_purchase_price',
                        name: 'unit_purchase_price',
                        width: '80px'
                    },
                    {
                        data: 'total_purchase_price',
                        name: 'total_purchase_price',
                        width: '80px'
                    },
                    {
                        data: 'unit_sale_price',
                        name: 'unit_sale_price',
                        width: '80px'
                    },
                    {
                        data: 'total_sale_price',
                        name: 'total_sale_price',
                        width: '80px'
                    },
                    {
                        data: 'qty_difference',
                        name: 'qty_difference',
                        width: '50px'
                    },
                ],
          drawCallback: function(settings) {
    var api = this.api();

    // Assign row indexes
    api.rows().every(function(rowIdx, tableLoop, rowLoop) {
        let row = $(this.node());
        let rowData = this.data();
        row.attr('data-row-index', rowData.DT_Row_Index); // Or use rowData.product_id if available

        // Check if this row has edited data
        let saved = form22EditedValues[rowData.DT_Row_Index];
        if (saved) {
            row.find('.stock_count').val(saved.stock_count);
            row.find('.total_purchase_price').text(__number_f(saved.total_purchase_price, false, false, __currency_precision));
            row.find('.total_purchase_price').data('orig-value', saved.total_purchase_price);
            row.find('.total_sale_price').text(__number_f(saved.total_sale_price, false, false, __currency_precision));
            row.find('.total_sale_price').data('orig-value', saved.total_sale_price);
            row.find('.qty_difference').val(saved.qty_difference);
        }
    });

    __currency_convert_recursively($('#form_22_table'));

    let total_purchase_price = sum_table_col($('#form_22_table'), 'total_purchase_price');
    let total_sale_price = sum_table_col($('#form_22_table'), 'total_sale_price');
    $('#footer_total_purchase_price').text(total_purchase_price);
    $('#footer_total_sale_price').text(total_sale_price);

    updatePaginationInfo();
}
,
    initComplete: function(settings, json) {
        // Initialize totals arrays if needed
        var table_info = this.api().page.info();
        ppage_totals = new Array(table_info.pages).fill(0.00);
        spage_totals = new Array(table_info.pages).fill(0.00);
        pre_gppage_totals = new Array(table_info.pages).fill(0.00);
        pre_gspage_totals = new Array(table_info.pages).fill(0.00);
        
        // Initial pagination info
        updatePaginationInfo();
        
    }
});

$('#custom_date_apply_button').on('click', function () {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStart = moment(startDate).format('YYYY-MM-DD');
                let formattedEnd = moment(endDate).format('YYYY-MM-DD');
                let fullRange = formattedStart + ' ~ ' + formattedEnd;

                $('#form_16a_date').val(fullRange);
                $('#form_16a_date').data('daterangepicker').setStartDate(moment(startDate));
                $('#form_16a_date').data('daterangepicker').setEndDate(moment(endDate));
                $("#report_date_range").text("Date Range: " + fullRange);

                form_22_table.ajax.reload();
                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        });

// Function to update pagination information
function updatePaginationInfo() {
    try {
        var pageInfo = form_22_table.page.info();
        var currentPage = pageInfo.page + 1;
        var totalPages = pageInfo.pages;
        
        var labelElement = $('#form_22_custom_pagination_label');
        if (labelElement.length) {
            if (totalPages > 0) {
                labelElement.html(`<strong>22 - ${currentPage}</strong>`);
               
            } else {
                labelElement.html('<strong>No data available</strong>');
               
            }
        } else {
            console.error('Pagination label element not found');
        }
    } catch (e) {
        console.error('Error updating pagination info:', e);
    }
   // form_22_table.page.len(newPageLength).draw();
}

// Add event listener for page changes
form_22_table.on('page.dt', function() {
    updatePaginationInfo();
});

          $(document).on('keyup', '.stock_count', function() {
    let tr = $(this).closest('tr');
    let rowIndex = tr.data('row-index'); // Use a reliable unique ID like product_id if available

    let unit_purchase_price = parseFloat(tr.find('.unit_purchase_price').data('orig-value'));
    let unit_sale_price = parseFloat(tr.find('.unit_sale_price').data('orig-value'));
    let current_stock = parseFloat(tr.find('.current_stock').data('orig-value'));
    let stock = parseFloat($(this).val());

    let total_purchase_value = unit_purchase_price * stock;
    let total_sale_value = unit_sale_price * stock;
    let qty_difference = current_stock - stock;

    tr.find('.total_purchase_price').text(__number_f(total_purchase_value, false, false, __currency_precision));
    tr.find('.total_purchase_price').data('orig-value', total_purchase_value);
    tr.find('.total_sale_price').text(__number_f(total_sale_value, false, false, __currency_precision));
    tr.find('.total_sale_price').data('orig-value', total_sale_value);
    tr.find('.qty_difference').val(qty_difference);

    // Store changes
    form22EditedValues[rowIndex] = {
        stock_count: stock,
        total_purchase_price: total_purchase_value,
        total_sale_price: total_sale_value,
        qty_difference: qty_difference
    };

    calculateTotals();
});


            function calculateTotals(page_change = null) {
                let pgrand = 0.00;
                let sgrand = 0.00;
                let total_purchase_amount = sum_table_col($('#form_22_table'), 'total_purchase_price');
                let total_sales_amount = sum_table_col($('#form_22_table'), 'total_sale_price');

                let info = form_22_table.page.info(); //get table info

                if (page_change == 1) {
                    if (info.page == 0) {
                        pgrand = ppage_totals[info.page];
                        sgrand = spage_totals[info.page];
                    } else {
                        pgrand = ppage_totals[info.page] + pre_gppage_totals[info.page - 1];
                        sgrand = spage_totals[info.page] + pre_gspage_totals[info.page - 1];
                    }

                    pre_gppage_totals[info.page] = pgrand;
                    pre_gspage_totals[info.page] = sgrand;
                } else {
                    ppage_totals[info.page] = total_purchase_amount;
                    spage_totals[info.page] = total_sales_amount;
                    if (info.page == 0) {
                        pgrand = ppage_totals[info.page];
                        sgrand = spage_totals[info.page];
                    } else {
                        pgrand = ppage_totals[info.page] + pre_gppage_totals[info.page - 1];
                        sgrand = spage_totals[info.page] + pre_gspage_totals[info.page - 1];
                    }

                    pre_gppage_totals[info.page] = pgrand;
                    pre_gspage_totals[info.page] = sgrand;
                }
                $('#purchase_price1').val(total_purchase_amount);
                $('#purchase_price3').val(total_purchase_amount);
                $('#sales_price1').val(total_sales_amount);
                $('#sales_price3').val(total_sales_amount);
                $('#footer_total_purchase_price').text(__number_f(ppage_totals[info.page], false, false,
                    __currency_precision));
                $('#footer_total_sale_price').text(__number_f(spage_totals[info.page], false, false,
                    __currency_precision));
                $('#pre_total_purchase_price').text(__number_f(pre_gppage_totals[info.page - 1], false, false,
                    __currency_precision));
                $('#pre_total_sale_price').text(__number_f(pre_gspage_totals[info.page - 1], false, false,
                    __currency_precision));
                $('#grand_total_purchase_price').text(__number_f(pgrand, false, false, __currency_precision));
                $('#grand_total_sale_price').text(__number_f(sgrand, false, false, __currency_precision));
            }

            $('#form_22_table').on('page.dt', function() {
                calculateTotals(1);
            });

            //form_22_table 
            var lf_ppage_totals = [];
            var lf_spage_totals = [];
            var lf_pre_gppage_totals = [];
            var lf_pre_gspage_totals = [];
            form_22_last_verified_table = $('#form_22_last_verified_table').DataTable({
                processing: true,
                serverSide: false,
                pageLength: newPageLength,
                ajax: {
                    url: '/mpcs/get-last-verified-form-f22',
                    data: function(d) {}
                },
                "columnDefs": [{
                    "width": "2%",
                    "targets": 2
                }],
                columns: [{
                        data: 'DT_Row_Index',
                        name: 'DT_Row_Index'
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'book_no',
                        name: 'book_no'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock'
                    },
                    {
                        data: 'stock_count',
                        name: 'stock_count'
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
                        data: 'qty_difference',
                        name: 'qty_difference'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
                "initComplete": function(settings, json) {
                    var table_info = form_22_last_verified_table.page.info(); //get table info
                    for (i = 0; i < table_info.pages; i++) {
                        lf_ppage_totals[i] = 0.00;
                        lf_spage_totals[i] = 0.00;
                        lf_pre_gppage_totals[i] = 0.00;
                        lf_pre_gspage_totals[i] = 0.00;

                    }
                }
            });

            $('#form_22_last_verified_table').on('page.dt', function() {
                lastFormCalculateTotals(1);
            });
            $('#form_22_last_verified_table').on('init.dt', function() {
                lastFormCalculateTotals();
            }).dataTable();

            function lastFormCalculateTotals(page_change = null) {
                let lf_pgrand = 0.00;
                let lf_sgrand = 0.00;
                let total_purchase_amount = sum_table_col($('#form_22_last_verified_table'),
                    'lf_total_purchase_price');
                let total_sales_amount = sum_table_col($('#form_22_last_verified_table'), 'lf_total_sale_price');

                let info = form_22_last_verified_table.page.info(); //get table info

                if (page_change == 1) {
                    lf_ppage_totals[info.page] = total_purchase_amount;
                    lf_spage_totals[info.page] = total_sales_amount;
                    if (info.page == 0) {
                        lf_pgrand = lf_ppage_totals[info.page];
                        lf_sgrand = lf_spage_totals[info.page];
                    } else {
                        lf_pgrand = lf_ppage_totals[info.page] + lf_pre_gppage_totals[info.page - 1];
                        lf_sgrand = lf_spage_totals[info.page] + lf_pre_gspage_totals[info.page - 1];
                    }

                    lf_pre_gppage_totals[info.page] = lf_pgrand;
                    lf_pre_gspage_totals[info.page] = lf_sgrand;

                } else {
                    lf_ppage_totals[info.page] = total_purchase_amount;
                    lf_spage_totals[info.page] = total_sales_amount;
                    if (info.page == 0) {
                        lf_pgrand = lf_ppage_totals[info.page];
                        lf_sgrand = lf_spage_totals[info.page];
                    } else {
                        lf_pgrand = lf_ppage_totals[info.page] + lf_pre_gppage_totals[info.page - 1];
                        lf_sgrand = lf_spage_totals[info.page] + lf_pre_gspage_totals[info.page - 1];
                    }

                    lf_pre_gppage_totals[info.page] = lf_pgrand;
                    lf_pre_gspage_totals[info.page] = lf_sgrand;
                }


                $('#lf_footer_total_purchase_price').text(__number_f(lf_ppage_totals[info.page], false, false,
                    __currency_precision));
                $('#lf_footer_total_sale_price').text(__number_f(lf_spage_totals[info.page], false, false,
                    __currency_precision));
                $('#lf_pre_total_purchase_price').text(__number_f(lf_pre_gppage_totals[info.page - 1], false, false,
                    __currency_precision));
                $('#lf_pre_total_sale_price').text(__number_f(lf_pre_gspage_totals[info.page - 1], false, false,
                    __currency_precision));
                $('#lf_grand_total_purchase_price').text(__number_f(lf_pgrand, false, false, __currency_precision));
                $('#lf_grand_total_sale_price').text(__number_f(lf_sgrand, false, false, __currency_precision));
            }


            $('#f22_save_and_print').click(function(e) {
                e.preventDefault();
                $(this).attr('disabled', 'disabled');
                let dateRange = $('#form_16a_date').val();
                 let firstDate = dateRange.split(' - ')[0]; 
                $.ajax({
                    method: 'post',
                    url: '/mpcs/save-form-f22',
                    data: { data: $('#f22_form').serialize() },
                    date_value:firstDate,
                    success: function(result) {
                        console.log(result);
                        if (result.success == 0) {
                            toastr.error(result.msg);

                            return false;
                        }

                        printPage(result);

                    },
                });
            })
            $('#f22_print').click(function(e) {
                e.preventDefault();
                $.ajax({
                    method: 'post',
                    url: '/mpcs/print-form-f22',
                    data: {
                        data: form_22_table.$('input, select').serialize() + $('#f22_form')
                            .serialize(),
                            date_value: $('#form_16a_date').val() // Make sure to replace 'date_input_id' with your actual date input field's ID
                    },
                    success: function(result) {
                        if (result.success == 0) {
                            toastr.error(result.msg);

                            return false;
                        }
                        onlyPrintPage(result);

                    },
                });
            });
            $('#lf_f22_print').click(function(e) {
                e.preventDefault();
                $.ajax({
                    method: 'post',
                    url: '/mpcs/print-form-f22',
                    data: {
                        data: form_22_last_verified_table.$('input, select').serialize() + $(
                            '#lf_f22_form').serialize()
                    },
                    success: function(result) {
                        if (result.success == 0) {
                            toastr.error(result.msg);

                            return false;
                        }
                        printPage(result);

                    },
                });
            });
            $(document).on('click', '.reprint_form', function(e) {
                e.preventDefault();
                href = $(this).data('href');
                console.log(href);

                $.ajax({
                    method: 'get',
                    url: href,
                    data: {},
                    success: function(result) {
                        if (result.success == 0) {
                            toastr.error(result.msg);

                            return false;
                        }
                        printPage(result);

                    },
                });
            });


        });

        
        function printPage(content) {
            var w = window.open('', '_self');
            $(w.document.body).html(content);
            w.print();
            w.close();
            window.location.href = "{{ URL::to('/') }}/mpcs/F22_stock_taking";
        }

        function onlyPrintPage(content) {
            var w = window.open('', '_blank');
            $(w.document.body).html(`@include('layouts.partials.css')` + content);
            w.print();
            w.close();
            return false;
        }
		   });
    </script>
@endsection
