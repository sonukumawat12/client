<?php
if(!isset($is_ajax)){
    ?>
{{-- @extends('layouts.app') --}}
@extends($layout)
@section('title', __('mpcs::lang.F21_form'))

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
                        <h4 class="page-title pull-left">FORM F21C</h4>
                        <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                            <li><a href="#">F21C</a></li>
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
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.21_c_form_details')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f21c_form'))
                            <li class="">
                                <a href="#21c_form_list_tab" class="21c_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.21_c_form_settings')</strong>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->can('F21_form'))
                            <div class="tab-pane active" id="21c_form_tab">
                                @include('mpcs::forms.21CForm.21c_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('F21_form'))
                            <div class="tab-pane" id="21c_form_list_tab">
                                @include('mpcs::forms.21CForm.list_f21c')
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
        $(document).ready(function() {

//             //get setting list
            
//  form_21c_settings_tables = $('#form_21c_settings_tabless').DataTable({
//     processing: true,
//     serverSide: true,
//     ajax: {
//         url: '/mpcs/21cformsettings',
//         type: 'GET',
//         dataSrc: function(json) {
//             var newData = [];

//             json.data.forEach(function(item) {
//                 // First row (General details, empty Pump columns)
//                 newData.push({
//                     action: item.action,
//                     date: item.date,
//                     starting_number: item.starting_number,
//                     ref_pre_form_number: item.ref_pre_form_number,
//                     rec_sec_prev_day_amt: item.rec_sec_prev_day_amt,
//                     rec_sec_opn_stock_amt: item.rec_sec_opn_stock_amt,
//                     issue_section_previous_day_amount: item.issue_section_previous_day_amount,
//                     pump_name: "",  // Empty for first row
//                     last_meter_value: "" // Empty for first row
//                 });

//                 // Additional rows for each pump
//                 if (item.pumps_data && item.pumps_data.length > 0) {
//                     item.pumps_data.forEach(function(pump) {
//                         newData.push({
//                             action: "",  // Empty for pump rows
//                             date: "",
//                             starting_number: "",
//                             ref_pre_form_number: "",
//                             rec_sec_prev_day_amt: "",
//                             rec_sec_opn_stock_amt: "",
//                             issue_section_previous_day_amount: "",
//                             pump_name: pump.pump_name,
//                             last_meter_value: pump.last_meter_value
//                         });
//                     });
//                 }
//             });

//             return newData;
//         }
//     },
//     columns: [
//         { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '' },
//         { data: 'date', name: 'date' },
//         { data: 'starting_number', name: 'starting_number' },
//         { data: 'ref_pre_form_number', name: 'ref_pre_form_number' },
//         { data: 'rec_sec_prev_day_amt', name: 'rec_sec_prev_day_amt' },
//         { data: 'rec_sec_opn_stock_amt', name: 'rec_sec_opn_stock_amt' },
//         { data: 'issue_section_previous_day_amount', name: 'issue_section_previous_day_amount' },
//         { data: 'pump_name', name: 'pump_name', defaultContent: '' },
//         { data: 'last_meter_value', name: 'last_meter_value', defaultContent: '' }
//     ]
//     });
           
           // get_21_c_form_all_query(); // Optional on page load
            $('#f21c_print').click(function(e) {
                e.preventDefault();
                $.ajax({
                    method: 'post',
                    url: '/mpcs/print-form-f21c',
                    data: {
                        data: $('#f21c_form').serialize()
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

            function onlyPrintPage(content) {
                var w = window.open('', '_blank');
                $(w.document.body).html(`@include('layouts.partials.css')` + content);
                w.print();
                w.close();
                return false;
            }



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
            $('#form_date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format(moment_date_format) +
                    ' - ' +
                    picker.endDate.format(moment_date_format)
                );
            });

            $('#form_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });



            if ($('#form_21c_date_range').length == 1) {
    // Initialize the date range picker
    $('#form_21c_date_range').daterangepicker(
        {
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
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
                'Custom Date Range': [moment().startOf('month'), moment().endOf('month')]
            },
            startDate: moment(), // Default start date is today
            endDate: moment(),   // Default end date is today
            alwaysShowCalendars: true,
            showCustomRangeLabel: true,
            locale: {
                customRangeLabel: 'Date Range'
            }
        },
        function (start, end, label) {
            if (label === 'Custom Date Range') {
                // Show custom modal for manual input
                $('.custom_date_typing_modal').modal('show');
                return;
            }
            // Clear table data before updating
            clearTableData();
            clearTableDataAll();
            // Update the displayed date range
            $('#form_21c_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            // Fetch and update the table data
            get_21_c_form_all_query();
        }
    );

    // Apply button in custom date range modal
    $('#custom_date_apply_button').on('click', function () {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStart = moment(startDate).format('YYYY-MM-DD');
                let formattedEnd = moment(endDate).format('YYYY-MM-DD');
                let fullRange = formattedStart + ' ~ ' + formattedEnd;

                $('#form_21c_date_range').val(fullRange);
                $('#form_21c_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#form_21c_date_range').data('daterangepicker').setEndDate(moment(endDate));
                $("#report_date_range").text("Date Range: " + fullRange);

                get_21_c_form_all_query();
                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        });


    // Pre-fill the input field with today's date
    const today = moment().format(moment_date_format);
    $('#form_21c_date_range').val(today + ' - ' + today);

    // Handle cancel event
    $('#form_21c_date_range').on('cancel.daterangepicker', function (ev, picker) {
        // Clear table data if the user cancels the date picker
        clearTableData();
        clearTableDataAll();
    });

    // Initial query with today's date
    get_21_c_form_all_query();
}

function clearTableDataAll() {
    // Reset all input fields to empty or default values
    $('.rows input[type="number"], .rows input[type="text"]').val('');

    // Clear pump names
    $('[id^=pump_name_]').text('');

    // Reset total fields
    $('[id$=_total]').val('');
}
// Function to clear table data
function clearTableData() {
    // Reset all input fields to 0.00
    $('.rows input[type="number"]').not('[name$="[no]"]').val('');
   // $('.rows input[type="number"]').val('');

    // Reset total fields
    $('#cash_for_today_qty_total').val('0.00');
    $('#cash_for_today_val_total').val('0.00');
    $('#credit_for_today_qty_total').val('0.00');
    $('#credit_for_today_val_total').val('0.00');
    $('#issues_up_to_last_day_qty_total').val('0.00');
    $('#issues_up_to_last_day_val_total').val('0.00');
    $('#price_discounts_for_today_qty_total').val('0.00');
    $('#price_discounts_for_today_val_total').val('0.00');
    $('#pre_date_qty_total').val('0.00');
    $('#pre_date_val_total').val('0.00');
    $('#pump_meter_opening_qty_total').val('0.00');
    $('#pump_meter_closing_qty_total').val('0.00');
    $('#issued_qty_for_today_qty_total').val('0.00');
    $('#total_issues_one_qty_total').val('0.00');
    $('#total_issues_one_val_total').val('0.00');
    $('#total_discounts_qty_total').val('0.00');
    $('#total_discounts_val_total').val('0.00');
    $('#total_receipts_today_qty_total').val('0.00');
    $('#total_receipts_today_val_total').val('0.00');
    $('#total_for_today_one_plus_two_qty_total').val('0.00');
    $('#total_for_today_one_plus_two_val_total').val('0.00');
    $('#balances_qty_total').val('0.00');
    $('#balances_val_total').val('0.00');
    $('#total_receipts_qty_total').val('0.00');
    $('#total_receipts_val_total').val('0.00');

    // Clear pump names
    $('[id^=pump_name_]').text('');
}


            //21c list
            //form 21C

            $('#form_21c_date_range_list').daterangepicker();
            $('#form_21c_date_range_list').daterangepicker({
                onSelect: function() {
                    $(this).change();
                }
            });

            if ($('#form_21c_date_range_list').length == 1) {
                $('#form_21c_date_range_list').daterangepicker(dateRangeSettings, function(start, end) {
                    $('#form_21c_date_range_list').val(
                        start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                    );
                    get_21_c_form_all_query_list(); // Call the function when the date range changes
                });

                $('#form_21c_date_range_list').on('cancel.daterangepicker', function(ev, picker) {
                    $('#product_sr_date_filter').val('');
                    get_21_c_form_all_query_list(); // Call the function when the date range is cancelled
                });

                $('#form_21c_date_range_list')
                    .data('daterangepicker')
                    .setStartDate(moment().startOf('month'));
                $('#form_21c_date_range_list')
                    .data('daterangepicker')
                    .setEndDate(moment().endOf('month'));
            }

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
    const start_date = $('input#form_21c_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const end_date = $('input#form_21c_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
    const location_id = $('#f21c_location_id').val();

    $.ajax({
        method: 'get',
        url: '/mpcs/get_21_c_form_all_query',
        data: { start_date, end_date, location_id },
        success: function (result) {
            console.log('API Response:', result);
            
            // Handle form header information
            handleFormHeader(result, start_date);
            
            // Initialize data structures
            const categoryData = {};
            const pumpData = {};
            
            // Process all data rows and populate categoryData
            processAllDataRows(result, categoryData);
            
            // Process pump operator data
            processPumpOperatorData(result, pumpData);
            
            // Calculate derived values and update UI
            calculateAndUpdateAllValues(categoryData, pumpData);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching form 21C data:", error);
            alert("Error fetching data. Please check console for details.");
        }
    });
}

// Handle form header information
function handleFormHeader(result, start_date) {
    if (Array.isArray(result.header) && result.header.length > 0) {
        const current_date = moment().format('YYYY-MM-DD');
        console.log('Current date:', current_date);
        
        result.header.forEach(item => {
            let adjustedFormNumber = '';
            
            if (start_date === current_date) {
                const today = new Date();
                const itemDate = new Date(item.date);
                today.setHours(0, 0, 0, 0);
                itemDate.setHours(0, 0, 0, 0);
                
                const timeDiff = today - itemDate;
                const daysPassed = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                adjustedFormNumber = parseInt(item?.starting_number) + daysPassed;
                console.log('Adjusted form number:', adjustedFormNumber);
            }
            
            $('#formno').text("Form No: " + adjustedFormNumber);
            $('input[name="formnovalue"]').val(adjustedFormNumber);
            $('#openingdate').text("Date: " + start_date);
            $('#name').text("Manager Name: ");
        });
    } else {
        $('#formno').text("Form No:");
        $('input[name="formnovalue"]').val('');
        $('#openingdate').text("Date:");
        $('#name').text("Manager Name:");
    }
}

// Process all data rows and populate categoryData
function processAllDataRows(result, categoryData) {
    // Define all data types we need to process
    const dataTypes = [
        { key: 'today_sales', type: 'today' },
        { key: 'previous_day', type: 'previous_day' },
        { key: 'opening_stock', type: 'opening_stock' },
        { key: 'cash_sales_today', type: 'cash_for_today' },
        { key: 'credit_sales_today', type: 'credit_for_today' },
        { key: 'total_receipts_last', type: 'issues_up_to_last_day' },
        { key: 'discount_todays', type: 'price_discounts_for_today' },
        { key: 'discount_previous', type: 'pre_date' }
    ];

    // Process each data type
    dataTypes.forEach(({key, type}) => {
        if (result[key] && Array.isArray(result[key])) {
            result[key].forEach(item => {
                const catId = item.category_id;
                
                // Initialize category if not exists
                if (!categoryData[catId]) {
                    categoryData[catId] = {
                        today_qty: 0, today_val: 0,
                        previous_day_qty: 0, previous_day_val: 0,
                        opening_stock_qty: 0, opening_stock_val: 0,
                        cash_qty: 0, cash_val: 0,
                        credit_qty: 0, credit_val: 0,
                        issues_up_to_last_day_qty: 0, issues_up_to_last_day_val: 0,
                        price_discounts_qty: 0, price_discounts_val: 0,
                        pre_date_qty: 0, pre_date_val: 0
                    };
                }
                
                // Set values based on type
                const qty = parseFloat(item.total_quantity) || 0;
                const val = parseFloat(item.total_sales) || 0;
                
                switch (type) {
                    case 'today':
                        categoryData[catId].today_qty = qty;
                        categoryData[catId].today_val = val;
                        break;
                    case 'previous_day':
                        categoryData[catId].previous_day_qty = qty;
                        categoryData[catId].previous_day_val = val;
                        break;
                    case 'opening_stock':
                        categoryData[catId].opening_stock_qty = qty;
                        categoryData[catId].opening_stock_val = val;
                        break;
                    case 'cash_for_today':
                        categoryData[catId].cash_qty = qty;
                        categoryData[catId].cash_val = val;
                        break;
                    case 'credit_for_today':
                        categoryData[catId].credit_qty = qty;
                        categoryData[catId].credit_val = val;
                        break;
                    case 'issues_up_to_last_day':
                        categoryData[catId].issues_up_to_last_day_qty = qty;
                        categoryData[catId].issues_up_to_last_day_val = val;
                        break;
                    case 'price_discounts_for_today':
                        categoryData[catId].price_discounts_qty = qty;
                        categoryData[catId].price_discounts_val = val;
                        break;
                    case 'pre_date':
                        categoryData[catId].pre_date_qty = qty;
                        categoryData[catId].pre_date_val = val;
                        break;
                }
                
                // Update UI for basic fields
                $(`#${type}_qty_${catId}`).val(qty === 0 ? '' : qty.toFixed(2));
                $(`#${type}_val_${catId}`).val(val === 0 ? '' : formatCurrency(val));
            });
        }
    });
}
// Process pump operator data with date validation
function processPumpOperatorData(result, pumpData) {
    const start_date = $('input#form_21c_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');

    // Exit if no pump data is available
    if (!result.pump_operator || !Array.isArray(result.pump_operator)) {
        return;
    }

    let totalOpeningMeter = 0, totalClosingMeter = 0, totalIssuedQty = 0;

    // Determine the latest date from header_latest (if available)
    const latestDate = result.header_latest || null;

    console.log("Date comparison:", {
        start_date,
        latestDate,
        shouldUseActualValues: !latestDate || start_date >= latestDate
    });

    // Iterate through each pump operator item
    result.pump_operator.forEach(item => {
        const pumpId = item.pump_id;
        const catId = item.category_id;
        const pumpName = item.pump_no || '-';

        // Parse actual values from the server response
        const actualMinMeter = parseFloat(item.min_starting_meter) || 0;
        const actualMaxMeter = parseFloat(item.max_closing_meter) || 0;
        const actualDiff = actualMaxMeter - actualMinMeter;

        // Determine whether to use actual values or zero them out
        const useActualValues = !latestDate || start_date >= latestDate;

        // Values to display (either actual or zero)
        const displayMinMeter = useActualValues ? actualMinMeter : 0;
        const displayMaxMeter = useActualValues ? actualMaxMeter : 0;
        const displayDiff = useActualValues ? actualDiff : 0;

        // Store pump data (always store actual values for reference)
        pumpData[pumpId] = {
            pump_no: pumpName,
            opening_meter: actualMinMeter,
            closing_meter: actualMaxMeter,
            issued_qty: actualDiff,
            category_id: catId,
            is_zeroed: !useActualValues // Flag if values were zeroed out
        };

        // Helper function to update input fields
        const updateField = (selector, value) => {
            const $field = $(`input[name="${selector}"][data-pump-id="${pumpId}"]`);
            $field.val(value === 0 ? '' : value.toFixed(2));
        };

        // Update UI fields with display values
        updateField(`pump_meter_opening[${catId}][val][]`, displayMinMeter);
        updateField(`pump_meter_closing[${catId}][val][]`, displayMaxMeter);
        updateField(`issued_qty_for_today[${catId}][val][]`, displayDiff);

        // Accumulate totals for display values
        totalOpeningMeter += displayMinMeter;
        totalClosingMeter += displayMaxMeter;
        totalIssuedQty += displayDiff;
    });

    // Helper function to format and set total values
    const setTotalValue = (selector, value) => {
        $(selector).val(value === 0 ? '' : value.toFixed(2));
    };

    // Update total values in the UI
    setTotalValue('#pump_meter_opening_qty_total', totalOpeningMeter);
    setTotalValue('#pump_meter_closing_qty_total', totalClosingMeter);
    setTotalValue('#issued_qty_for_today_qty_total', totalIssuedQty);
}

// Calculate derived values and update UI
function calculateAndUpdateAllValues(categoryData, pumpData) {
    const totals = {
        today_qty: 0, today_val: 0,
        previous_day_qty: 0, previous_day_val: 0,
        opening_stock_qty: 0, opening_stock_val: 0,
        cash_qty: 0, cash_val: 0,
        credit_qty: 0, credit_val: 0,
        issues_up_to_last_day_qty: 0, issues_up_to_last_day_val: 0,
        price_discounts_qty: 0, price_discounts_val: 0,
        pre_date_qty: 0, pre_date_val: 0,
        total_issues_qty: 0, total_issues_val: 0,
        total_issues_one_qty: 0, total_issues_one_val: 0,
        total_discounts_qty: 0, total_discounts_val: 0,
        total_receipts_qty: 0, total_receipts_val: 0,
        total_receipts_today_qty: 0, total_receipts_today_val: 0,
        total_for_today_one_plus_two_qty: 0, total_for_today_one_plus_two_val: 0,
        balances_qty: 0, balances_val: 0
    };

    // Process each category
    Object.keys(categoryData).forEach(catId => {
        const data = categoryData[catId];
        
        // Calculate derived values
        const total_issues_qty = data.cash_qty + data.credit_qty;
        const total_issues_val = data.cash_val + data.credit_val;
        
        const total_issues_one_qty = total_issues_qty + data.issues_up_to_last_day_qty;
        const total_issues_one_val = total_issues_val + data.issues_up_to_last_day_val;
        
        const total_discounts_qty = data.price_discounts_qty + data.pre_date_qty;
        const total_discounts_val = data.price_discounts_val + data.pre_date_val;
        
        const total_receipts_qty = data.today_qty + data.previous_day_qty;
        const total_receipts_val = data.today_val + data.previous_day_val;
        
        const total_receipts_today_qty = total_receipts_qty + data.opening_stock_qty;
        const total_receipts_today_val = total_receipts_val + data.opening_stock_val;
        
        const total_for_today_one_plus_two_qty = total_issues_one_qty + total_discounts_qty;
        const total_for_today_one_plus_two_val = total_issues_one_val + total_discounts_val;
        
        const balances_qty = total_receipts_today_qty - total_for_today_one_plus_two_qty;
        const balances_val = total_receipts_today_val - total_for_today_one_plus_two_val;

        // Update UI for derived fields
        $(`#total_issues_qty_${catId}`).val(total_issues_qty === 0 ? '' : total_issues_qty.toFixed(2));
        $(`#total_issues_val_${catId}`).val(total_issues_val === 0 ? '' : formatCurrency(total_issues_val));
        
        $(`#total_issues_one_qty_${catId}`).val(total_issues_one_qty === 0 ? '' : total_issues_one_qty.toFixed(2));
        $(`#total_issues_one_val_${catId}`).val(total_issues_one_val === 0 ? '' : formatCurrency(total_issues_one_val));
        
        $(`#total_discounts_qty_${catId}`).val(total_discounts_qty === 0 ? '' : total_discounts_qty.toFixed(2));
        $(`#total_discounts_val_${catId}`).val(total_discounts_val === 0 ? '' : formatCurrency(total_discounts_val));
        
        $(`#total_receipts_qty_${catId}`).val(total_receipts_qty === 0 ? '' : total_receipts_qty.toFixed(2));
        $(`#total_receipts_val_${catId}`).val(total_receipts_val === 0 ? '' : formatCurrency(total_receipts_val));
        
        $(`#total_receipts_today_qty_${catId}`).val(total_receipts_today_qty === 0 ? '' : total_receipts_today_qty.toFixed(2));
        $(`#total_receipts_today_val_${catId}`).val(total_receipts_today_val === 0 ? '' : formatCurrency(total_receipts_today_val));
        
        $(`#total_for_today_one_plus_two_qty_${catId}`).val(total_for_today_one_plus_two_qty === 0 ? '' : total_for_today_one_plus_two_qty.toFixed(2));
        $(`#total_for_today_one_plus_two_val_${catId}`).val(total_for_today_one_plus_two_val === 0 ? '' : formatCurrency(total_for_today_one_plus_two_val));
        
        $(`#balances_qty_${catId}`).val(balances_qty === 0 ? '' : balances_qty.toFixed(2));
        $(`#balances_val_${catId}`).val(balances_val === 0 ? '' : formatCurrency(balances_val));

        // Accumulate totals
        totals.today_qty += data.today_qty;
        totals.today_val += data.today_val;
        totals.previous_day_qty += data.previous_day_qty;
        totals.previous_day_val += data.previous_day_val;
        totals.opening_stock_qty += data.opening_stock_qty;
        totals.opening_stock_val += data.opening_stock_val;
        totals.cash_qty += data.cash_qty;
        totals.cash_val += data.cash_val;
        totals.credit_qty += data.credit_qty;
        totals.credit_val += data.credit_val;
        totals.issues_up_to_last_day_qty += data.issues_up_to_last_day_qty;
        totals.issues_up_to_last_day_val += data.issues_up_to_last_day_val;
        totals.price_discounts_qty += data.price_discounts_qty;
        totals.price_discounts_val += data.price_discounts_val;
        totals.pre_date_qty += data.pre_date_qty;
        totals.pre_date_val += data.pre_date_val;
        totals.total_issues_qty += total_issues_qty;
        totals.total_issues_val += total_issues_val;
        totals.total_issues_one_qty += total_issues_one_qty;
        totals.total_issues_one_val += total_issues_one_val;
        totals.total_discounts_qty += total_discounts_qty;
        totals.total_discounts_val += total_discounts_val;
        totals.total_receipts_qty += total_receipts_qty;
        totals.total_receipts_val += total_receipts_val;
        totals.total_receipts_today_qty += total_receipts_today_qty;
        totals.total_receipts_today_val += total_receipts_today_val;
        totals.total_for_today_one_plus_two_qty += total_for_today_one_plus_two_qty;
        totals.total_for_today_one_plus_two_val += total_for_today_one_plus_two_val;
        totals.balances_qty += balances_qty;
        totals.balances_val += balances_val;
    });

    // Update all total fields
    $('#today_qty_total').val(totals.today_qty === 0 ? '' : totals.today_qty.toFixed(2));
    $('#today_val_total').val(totals.today_val === 0 ? '' : formatCurrency(totals.today_val));
    $('#previous_day_qty_total').val(totals.previous_day_qty === 0 ? '' : totals.previous_day_qty.toFixed(2));
    $('#previous_day_val_total').val(totals.previous_day_val === 0 ? '' : formatCurrency(totals.previous_day_val));
    $('#opening_stock_qty_total').val(totals.opening_stock_qty === 0 ? '' : totals.opening_stock_qty.toFixed(2));
    $('#opening_stock_val_total').val(totals.opening_stock_val === 0 ? '' : formatCurrency(totals.opening_stock_val));
    $('#cash_for_today_qty_total').val(totals.cash_qty === 0 ? '' : totals.cash_qty.toFixed(2));
    $('#cash_for_today_val_total').val(totals.cash_val === 0 ? '' : formatCurrency(totals.cash_val));
    $('#credit_for_today_qty_total').val(totals.credit_qty === 0 ? '' : totals.credit_qty.toFixed(2));
    $('#credit_for_today_val_total').val(totals.credit_val === 0 ? '' : formatCurrency(totals.credit_val));
    $('#issues_up_to_last_day_qty_total').val(totals.issues_up_to_last_day_qty === 0 ? '' : totals.issues_up_to_last_day_qty.toFixed(2));
    $('#issues_up_to_last_day_val_total').val(totals.issues_up_to_last_day_val === 0 ? '' : formatCurrency(totals.issues_up_to_last_day_val));
    $('#price_discounts_for_today_qty_total').val(totals.price_discounts_qty === 0 ? '' : totals.price_discounts_qty.toFixed(2));
    $('#price_discounts_for_today_val_total').val(totals.price_discounts_val === 0 ? '' : formatCurrency(totals.price_discounts_val));
    $('#pre_date_qty_total').val(totals.pre_date_qty === 0 ? '' : totals.pre_date_qty.toFixed(2));
    $('#pre_date_val_total').val(totals.pre_date_val === 0 ? '' : formatCurrency(totals.pre_date_val));
    $('#total_issues_qty_total').val(totals.total_issues_qty === 0 ? '' : totals.total_issues_qty.toFixed(2));
    $('#total_issues_val_total').val(totals.total_issues_val === 0 ? '' : formatCurrency(totals.total_issues_val));
    $('#total_issues_one_qty_total').val(totals.total_issues_one_qty === 0 ? '' : totals.total_issues_one_qty.toFixed(2));
    $('#total_issues_one_val_total').val(totals.total_issues_one_val === 0 ? '' : formatCurrency(totals.total_issues_one_val));
    $('#total_discounts_qty_total').val(totals.total_discounts_qty === 0 ? '' : totals.total_discounts_qty.toFixed(2));
    $('#total_discounts_val_total').val(totals.total_discounts_val === 0 ? '' : formatCurrency(totals.total_discounts_val));
    $('#total_receipts_qty_total').val(totals.total_receipts_qty === 0 ? '' : totals.total_receipts_qty.toFixed(2));
    $('#total_receipts_val_total').val(totals.total_receipts_val === 0 ? '' : formatCurrency(totals.total_receipts_val));
    $('#total_receipts_today_qty_total').val(totals.total_receipts_today_qty === 0 ? '' : totals.total_receipts_today_qty.toFixed(2));
    $('#total_receipts_today_val_total').val(totals.total_receipts_today_val === 0 ? '' : formatCurrency(totals.total_receipts_today_val));
    $('#total_for_today_one_plus_two_qty_total').val(totals.total_for_today_one_plus_two_qty === 0 ? '' : totals.total_for_today_one_plus_two_qty.toFixed(2));
    $('#total_for_today_one_plus_two_val_total').val(totals.total_for_today_one_plus_two_val === 0 ? '' : formatCurrency(totals.total_for_today_one_plus_two_val));
    $('#balances_qty_total').val(totals.balances_qty === 0 ? '' : totals.balances_qty.toFixed(2));
    $('#balances_val_total').val(totals.balances_val === 0 ? '' : formatCurrency(totals.balances_val));
}

// Helper function to format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
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
            // all
            function get_21_c_form_all_query_list() {
                var start_date = $('input#form_21c_date_range_list')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#form_21c_date_range_list')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                var location_id = $('#f21c_location_id').val();

                $.ajax({
                    method: 'get',
                    url: '/mpcs/get-9c-forms',
                    data: {
                        start_date,
                        end_date,
                        location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        console.log("result_list_ss");
                        $('#21c_details_section').empty().append(result);


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
        });

        $("#print_div").click(function() {
                printDiv();
            });

            function printDiv() {
    // Clone the content so we don't mess with the original DOM
    var content = document.getElementById("print_content").cloneNode(true);

    // Replace all input fields with their values
    var inputs = content.querySelectorAll('input');
    inputs.forEach(function(input) {
        var span = document.createElement('span');
        span.textContent = input.value;
        span.style.display = 'inline-block';
        span.style.minWidth = input.offsetWidth + 'px'; // optional: retain width
        input.parentNode.replaceChild(span, input);
    });

    var w = window.open('', '_self'); // '_self' might overwrite your current page
    var html = `
        <html>
            <head>
             <title>21C Form Settings</title>
                <style>
                    @page {
                        size: landscape;
                         margin: 2mm;
                    }
                    body {
                        width: 100%;
                        margin: 0px;
                        padding: 0px;
                    }
                        .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .pull-left { float: left; }

                   

                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin-bottom: 1px;
                    }
                h4 {
                        margin-left: 450px;
                        font-weight: bold;
                    }
                 h3 {
                        margin-left: 850px;
                        font-weight: bold;
                    }
                h5 {
                        margin-left: 70px;
                        font-weight: bold;
                    }
                    @media print {
                        html, body {
                            width: 100%;
                            overflow: visible !important;
                        }
                        * {
                            font-size: 8pt;
                        }
                    }
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    table, th, td {
                        border: 1px solid black;
                        padding: 2px;
                    }
                </style>
            </head>
            <body>
                ${content.innerHTML}
            </body>
        </html>
    `;

    w.document.write(html);
    w.document.close();
    w.focus();
    w.print();
    w.close();

    // Optional: redirect after printing
    window.location.href = "{{ URL::to('/') }}/mpcs/21CForm";
}  





    
   
    </script>
    @if (empty($is_ajax))
    @endsection
@endif