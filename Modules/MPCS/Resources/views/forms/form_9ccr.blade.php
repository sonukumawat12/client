@extends('layouts.app')
@section('title', __('mpcs::lang.form_9_ccr_settings'))

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        @if (auth()->user()->can('f9a_form'))
                            <li class="active">
                                <a href="#9a_form_tab" class="9a_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_c_credit_form_detail')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can(abilities: 'f9a_settings_form'))
                            <li class="">
                                <a href="#9a_form_settings_tab" class="9a_form_settings_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_9_ccr_settings')</strong>
                                </a>
                            </li>
                        @endif

                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->can('f9a_form'))
                            <div class="tab-pane active" id="9a_form_tab">
                                @include('mpcs::forms.partials.9ccr_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('f9a_settings_form'))
                            <div class="tab-pane" id="9a_form_settings_tab">
                                @include('mpcs::forms.partials.9ccr_settings_form')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade form_9_ccr_settings_modal" id="form_9_ccr_settings_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade update_form_9_ccr_settings_modal" id="update_form_9_ccr_settings_modal" tabindex="-1"
            role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </section>
    <!-- /.content -->

@endsection
@section('javascript')

     
    <script>
         $(document).ready(function() {
            // Initialize the date picker
            $('#9ccr_date_range').daterangepicker({
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
                    $('#9ccr_date_range').val(start.format('YYYY-MM-DD'));

                    // Refresh DataTable with new date
                    form_9ccredit_table.ajax.reload();
                    }
                });

                // Reset the field when the cancel button is clicked
                $('#9ccr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#9ccr_date_range').val('');
                });

                // Set the default selected date range when initializing the date picker
                $('#9ccr_date_range').data('daterangepicker').setStartDate(moment().startOf('day'));
                $(
                    '#9ccr_date_range').data('daterangepicker').setEndDate(moment().endOf('day'));

                // Display the selected date range on the page
                let date = $('#9ccr_date_range').val().split(' - ');

                $('.to_date').text(date[1]);
            
            // $('#9ccr_date_range').change(function() {                
            //       console.log("eccce");
            //       form_9ccredit_table.ajax.reload();
                
            // });
            $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#9ccr_date_range').length) {
                        $('#9ccr_date_range').val(fullRange);
                        $('#9ccr_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#9ccr_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        form_9ccredit_table.ajax.reload();

                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
 //form 9a list
 form_9ccredit_table = $('#form_9ccredit_table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    "type": "get",
                    "url": "/mpcs/get-9ccredit-form",
                    data: function(d) {
                    const dateRange = $('#9ccr_date_range').val();
                
                    const singleDate = moment(dateRange, 'YYYY-MM-DD').format('YYYY-MM-DD');
                    d.start_date = singleDate;
                    d.end_date = singleDate;
                }
                },
                
                columns: [
                    {
                        data: 'billno',
                        name: 'billno'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'page',
                        name: 'page'
                    },
                    {
                        data: 'final_total_rs',
                        name: 'final_total_rs'
                    },
                    {
                        data: 'final_total_cent',
                        name: 'final_total_cent'
                    },
                    {
                        data: 'goods_rs',
                        name: 'goods_rs'
                    },
                    {
                        data: 'goods_cent',
                        name: 'goods_cent'
                    },
                    {
                        data: 'loading_rs',
                        name: 'loading_rs'
                    },
                    {
                        data: 'loading_rs',
                        name: 'loading_rs'
                    },
                    {
                        data: 'empty_rs',
                        name: 'empty_rs'
                    },
                    {
                        data: 'empty_cent',
                        name: 'empty_cent'
                    },
                    {
                        data: 'transport_rs',
                        name: 'transport_rs'
                    },
                    {
                        data: 'transport_cent',
                        name: 'transport_cent'
                    },
                    {
                        data: 'other_rs',
                        name: 'other_rs'
                    },
                    {
                        data: 'other_cent',
                        name: 'other_cent'
                    },
                ],
                fnDrawCallback: function(oSettings) {
    let api = this.api();

    let totalRs = 0;
    let totalCent = 0;

    // Sum values from the Rs and Cents columns
    api.column(4, { page: 'current' }).data().each(function(value) {
        totalRs += parseInt(value) || 0;
    });

    api.column(5, { page: 'current' }).data().each(function(value) {
        totalCent += parseInt(value) || 0;
    });

    // Carry over extra cents into Rs
    if (totalCent >= 100) {
        let carryRs = Math.floor(totalCent / 100);
        totalCent = totalCent % 100;
        totalRs += carryRs;
    }

    // Format and set today's total
    $('#footer_9c_total').html(totalRs);
    $('#footer_9c_total_cent').html(totalCent.toString().padStart(2, '0'));

    // Handle previous totals
    let response = oSettings.json || {};
    let previousRs = response.previous_total?.rs ?? 0;
    let previousCent = parseInt(response.previous_total?.cent ?? 0);

    // Carry over cents from previous
    let totalPreviousRs = parseInt(previousRs);
    let totalPreviousCent = previousCent;

    if (totalPreviousCent >= 100) {
        totalPreviousRs += Math.floor(totalPreviousCent / 100);
        totalPreviousCent = totalPreviousCent % 100;
    }

    $('#pre_9c_total').html(totalPreviousRs);
    $('#pre_9c_total_cent').html(totalPreviousCent.toString().padStart(2, '0'));

    // Calculate and set grand total
    let grandRs = totalRs + totalPreviousRs;
    let grandCent = totalCent + totalPreviousCent;

    if (grandCent >= 100) {
        grandRs += Math.floor(grandCent / 100);
        grandCent = grandCent % 100;
    }

    $('#grand_9c_total').html(grandRs);
    $('#grand_9c_total_cent').html(grandCent.toString().padStart(2, '0'));
}





            });

            //form 9a list
            form_9a_settings_table = $('#form_9a_settings_table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    "type": "get",
                    "url": "/mpcs/get-form-9c-settings",
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'date_time',
                        name: 'date_time'
                    },
                    {
                        data: 'starting_number',
                        name: 'starting_number'
                    },
                    {
                        data: 'ref_pre_form_number',
                        name: 'ref_pre_form_number'
                    },
                    {
                        data: 'added_user',
                        name: 'added_user'
                    },
                ]
            });

            //form 9a section
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

                            if ($('#form_9a_settings_table').length > 0) {
                                $(this).find('button[type="submit"]').attr('disabled', false);
                            }

                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });

            //update form 9a section
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

                            if ($('#form_9a_settings_table').length > 0) {
                                $(this).find('button[type="submit"]').attr('disabled', false);
                            }

                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });

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
                window.location.href = "{{ URL::to('/') }}/mpcs/form-9c";
            }

            function printDivs() {
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
                window.location.href = "{{ URL::to('/') }}/mpcs/form-9ccr";
            }
        });
    </script>
@endsection
