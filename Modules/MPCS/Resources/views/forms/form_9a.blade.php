@extends('layouts.app')
@section('title', __('mpcs::lang.form_9_a'))

@section('content')
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f9a_form'))
                    <li class="active">
                        <a href="#9a_form_tab" class="9a_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_a')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(abilities: 'f9a_settings_form'))
                    <li class="">
                        <a href="#9a_form_settings_tab" class="9a_form_settings_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_a_settings')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(auth()->user()->can('f9a_form'))
                    <div class="tab-pane active" id="9a_form_tab">
                        @include('mpcs::forms.partials.9a_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f9a_settings_form'))
                    <div class="tab-pane" id="9a_form_settings_tab">
                        @include('mpcs::forms.partials.9a_settings_form')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade form_9_a_settings_modal" id="form_9_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade update_form_9_a_settings_modal" id="update_form_9_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>

<!-- /.content -->

@endsection
@section('javascript')<script type="text/javascript">
    $(document).ready(function () {
    // Function to enable or disable the button based on ref_pre_form_number
    function toggleButtonState() {
        const refPreFormNumber = $('#ref_pre_form_number').val().trim(); // Get the value of ref_pre_form_number
        const addButton = $('#add_form_9_a_settings_button'); // Select the button

        if (refPreFormNumber !== '') {
            // If ref_pre_form_number is not empty, disable the button
            addButton.prop('disabled', true);
        } else {
            // If ref_pre_form_number is empty, enable the button
            addButton.prop('disabled', false);
        }
    }

    // Call the function on page load
    toggleButtonState();
    // Optionally, recheck the state if ref_pre_form_number changes dynamically
    $(document).on('change', '#ref_pre_form_number', function () {
        toggleButtonState();
    });
});
    $(document).ready(function() {
        // Fetch and display Form 9A data
        $('#9a_date_ranges').daterangepicker({
                    singleDatePicker: true, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#9a_date_ranges').val(start.format('YYYY-MM-DD'));
                        get9AForm();
                    }
                    // Refresh DataTable with new date
                    //form_9a_tables.ajax.reload();
                });

                $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#9a_date_ranges').length) {
                        $('#9a_date_ranges').val(fullRange);
                        $('#9a_date_ranges').data('daterangepicker').setStartDate(moment(startDate));
                        $('#9a_date_ranges').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        if (typeof get9AForm === 'function') get9AForm();
                    }

                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });

                // Reset the field when the cancel button is clicked
                $('#9a_date_ranges').on('cancel.daterangepicker', function(ev, picker) {
                    $('#9a_date_ranges').val('');
                });

                // Set the default selected date range when initializing the date picker
                $('#9a_date_ranges').data('daterangepicker').setStartDate(moment().startOf('day'));
                $(
                    '#9a_date_ranges').data('daterangepicker').setEndDate(moment().endOf('day'));

                // Display the selected date range on the page
                let date = $('#9a_date_ranges').val().split(' - ');

                $('.to_date').text(date[1]);
            
            $('#9a_date_ranges').change(function() {                
                  console.log("eccce");
                 // form_9a_tables.ajax.reload();
                 get9AForm();
            });
 //form 9a list ;
        get9AForm();

        function get9AForm() {
            const start_date = $('input#9a_date_ranges').data('daterangepicker').startDate.format('YYYY-MM-DD');
            const end_date = $('input#9a_date_ranges').data('daterangepicker').endDate.format('YYYY-MM-DD');

            $.ajax({
                method: 'get',
                url: '/mpcs/get-9a-form_value',
                data: { start_date, end_date },
                success: function(result) {
                    console.log(result);
                       
                    if (result) {
                        $('#openingdate').text("Date: " + start_date);
                        $('#cash_sales_rup').text(Number(result.cash_sales_rup) === 0 ? '' : Number(result.cash_sales_rup));
                        $('#cash_sales_cent').text(Number(result.cash_sales_cent) === 0 ? '' : Number(result.cash_sales_cent));
                        
                        $('#card_sales_rup').text(Number(result.card_sales_rup) === 0 ? '' : Number(result.card_sales_rup));
                        $('#card_sales_cent').text(Number(result.card_sales_cent) === 0 ? '' : Number(result.card_sales_cent));
                        $('#total_cash_sale_rup').text(Number(result.card_sales_rup) + Number(result.cash_sales_rup));
                        $('#total_cash_sale_cent').text(Number(result.card_sales_cent) + Number(result.cash_sales_cent));
                        $('#total_credit_sale_rup').text(Number(result.credit_sales_rup) === 0 ? '' : Number(result.credit_sales_rup));
                        $('#total_credit_sale_cent').text(Number(result.credit_sales_cent) === 0 ? '' : Number(result.credit_sales_cent));
                        $('#total_sale_rup').text(Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.credit_sales_rup));
                        $('#total_sale_cent').text(Number(result.card_sales_cent) + Number(result.cash_sales_cent) + Number(result.credit_sales_cent));
                        $('#total_sale_pre_day_rup').text(Number(result.previous_sales_rup) === 0 ? '' : Number(result.previous_sales_rup));
                        $('#total_sale_pre_day_cent').text(Number(result.previous_sales_cent) === 0 ? '' : Number(result.previous_sales_cent));
                        let rup = Number(result.card_sales_rup) + Number(result.cash_sales_rup) + Number(result.credit_sales_rup) + Number(result.previous_sales_rup);
                        let cent = Number(result.card_sales_cent) + Number(result.cash_sales_cent) + Number(result.credit_sales_cent) + Number(result.previous_sales_cent);

                        // Handle cent overflow
                        if (cent >= 100) {
                            rup += Math.floor(cent / 100);
                            cent = cent % 100;
                        }
                        $('#total_sale_today_rup').text(rup);
                        $('#total_sale_today_cent').text(cent);

                    } else {

                        $('#cash_sales').text("");
                        $('#card_sales').text("");
                        $('#total_cash_sale').text("");
                        $('#total_credit_sale').text("");
                        $('#total_sale').text("");
                        $('#total_sale_pre_day').text("");
                        $('#total_sale_today').text("");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching Form 9A data:", error);
                }
            });
        }

        // Reload Form 9A data when the date changes
        $('#9a_date_ranges').change(function() {
            get9AForm();
        });

        // Initialize DataTable for Form 9A settings
        var form_9a_settings_table = $('#form_9a_settings_table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                type: "get",
                url: "/mpcs/get-form-9a-settings",
                dataSrc: "data", // Ensure this matches the key in your JSON response
                error: function(xhr, error, thrown) {
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            columns: [
                { data: 'action', name: 'action', searchable: false, orderable: false },
                { data: 'starting_number', name: 'starting_number' },
                { data: 'total_sale_to_pre', name: 'total_sale_to_pre' },
                { data: 'pre_day_cash_sale', name: 'pre_day_cash_sale' },
                { data: 'pre_day_card_sale', name: 'pre_day_card_sale' },
                { data: 'pre_day_credit_sale', name: 'pre_day_credit_sale' },
                { data: 'pre_day_cash', name: 'pre_day_cash' },
                { data: 'pre_day_cheques', name: 'pre_day_cheques' },
                { data: 'pre_day_total', name: 'pre_day_total' },
                { data: 'pre_day_balance', name: 'pre_day_balance' },
                { data: 'pre_day_grand_total', name: 'pre_day_grand_total' }
            ]
        });

        // Handle Form 9A settings submission
        $(document).on('submit', 'form#add_9a_form_settings', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#form_9_a_settings_modal').modal('hide');
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error submitting Form 9A settings:", error);
                },
                complete: function() {
                    $(this).find('button[type="submit"]').attr('disabled', false);
                }
            });
        });

        // Handle Form 9A settings update
        $(document).on('submit', 'form#update_9a_form_settings', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#update_form_9_a_settings_modal').modal('hide');
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating Form 9A settings:", error);
                },
                complete: function() {
                    $(this).find('button[type="submit"]').attr('disabled', false);
                }
            });
        });

        // Print Form 9A
        $("#print_div").click(function() {
            printDiv();
        });

        function printDiv() {
            var w = window.open('', '_self');
            var html = `
                <html>
                    <head>
                        <style>
                            @page {
                                size: landscape;
                            }
                            body {
                                width: 100%;
                                margin: 0;
                                padding: 0;

                            }
                              h5 {
                margin: 0px 0;
                font-weight: bold;
                text-align: center;
            }   
                dropdown
                {
 text-align: center;
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
                        </style>
                    </head>
                    <body>
                        ${document.getElementById("print_content").innerHTML}
                    </body>
                </html>
            `;
            $(w.document.body).html(html);
            w.print();
            w.close();
            window.location.href = "{{URL::to('/')}}/mpcs/form-9a";
        }
    });
</script>
@endsection