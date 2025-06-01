@extends('layouts.app')
@section('title', __('contact.customer_statements'))
<!-- this style will hide the page title and print date -->
<style>
    @page{size:auto; margin:5mm ;}
    table#customer_statement_table > tbody > tr > td {
        padding: 2px !important;
    }
@media print{
    html,body,buttons,input,textarea,etc {
        font-family: Calibri !important;
        background: #357ca5 !important;
    }
    .dt-buttons,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none;
    }

    #print_header_div {
        display: inline !important;
    }

    .customer_details_div {
        display: none;
    }
    .margin-bottom-20 {
        margin-bottom: 0px !important;
    }
    table.dataTable {
        margin-top: 0px !important;
    }
}

 .buttons-pdf{
        display: none !important;
    }
    
    .buttons-print{
        display: none !important;
    }

</style>
@section('content')
<section class="content-header main-content-inner">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#customer_statements" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('contact.customer_statements')</strong>
                        </a>
                    </li>
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#list_customer_statements" class="" data-toggle="tab">
                            <i class="fa fa-list"></i>
                            <strong>@lang('contact.list_customer_statements')</strong>
                        </a>
                    </li>
                    
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#logos" class="" data-toggle="tab">
                            <i class="fa fa-list"></i>
                            <strong>@lang('lang_v1.statement_settings')</strong>
                        </a>
                    </li>
                    
                    
                    @if($enable_separate_customer_statement_no)
                    @can('enable_separate_customer_statement_no')
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#settings_customer_statements" class="" data-toggle="tab">
                            <i class="fa fa-cogs"></i>
                            <strong>@lang('contact.settings_customer_statements')</strong>
                        </a>
                    </li>
                    @endcan
                    @endif
                    
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#list_statement_payments" class="" data-toggle="tab">
                            <i class="fa fa-list"></i>
                            <strong>@lang('contact.list_statement_payments')</strong>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="customer_statements">
            @include('customer_statement.partials.customer_statements')
        </div>
        <div class="tab-pane" id="list_customer_statements">
            @include('customer_statement.partials.list_customer_statements')
        </div>
        
        <div class="tab-pane" id="logos">
            @include('customer_statement.logos.index')
        </div>
        
        @if($enable_separate_customer_statement_no)
        @can('enable_separate_customer_statement_no')
        <div class="tab-pane" id="settings_customer_statements">
            @include('customer_statement.partials.settings_customer_statements')
        </div>
        @endcan
        @endif
        
        <div class="tab-pane" id="list_statement_payments">
            @include('customer_statement.partials.list_statement_payments')
        </div>
    </div>

    <div class="modal fade customer_statement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


<div class="hide">
    <div id="report_print_div"></div>
</div>
<div class="modal fade" id="printableModal" tabindex="-1" role="dialog" aria-labelledby="printableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:100%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printableModalLabel">Printable Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- AJAX content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printButton">Print</button>
            </div>
        </div>
    </div>
</div>

</section>
@endsection
@section('javascript')
<script type="text/javascript">
$(document).on('click', '.btn-convert', function(){
            swal({
                title: '{{__("contact.convert_vat_statement")}}',
                text: '{{__("contact.convert_vat_statement_confirmation")}}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('href');
                     
                     console.log(url);

                     $.ajax({
                         method: "get",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                customer_statement_list_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });


    $(document).ready( function(){
        
        
        logos_table = $('#logos_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('CustomerStatementLogoController@index')}}',
                data: function (d) {
                    
                }
            },
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'logo', name: 'logo' },
                { data: 'image_name', name: 'image_name' },
                { data: 'alignment', name: 'alignment' },
                
                { data: 'text_position', name: 'text_position' },
                { data: 'statement_note', name: 'statement_note' },
                
                { data: 'username', name: 'username' },
             
               
            ],
            @include('layouts.partials.datatable_export_button')
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";

        var columns = [
            { data: 'customer_name', name: 'customer_name' },
            { data: 'starting_no', name: 'starting_no' },
            { data: 'action', searchable: false, orderable: false },
        ];

        statement_settings_table = $('#statement_settings_table').DataTable({
        processing: true,
        serverSide: false,
        aaSorting: [[0, 'desc']],
        ajax: '/customer-statement-settings',
        columns: columns,
        @include('layouts.partials.datatable_export_button')
        fnDrawCallback: function(oSettings) {

            },
        });


        customer_statement_table = $('#customer_statement_table').DataTable({
        processing: true,
        serverSide: false,
        aaSorting: [[0, 'desc']],
        pageLength: -1,
        ajax: {
            url: '/customer-statement',
            data: function(d) {
                d.location_id = $('select#customer_statement_location_id').val();
                d.customer_id = $('select#customer_statement_customer_id').val();
                d.reference = $('select#customer_statement_reference').val();
                var start = '';
                var end = '';
                if ($('input#customer_statement_date_range').val()) {
                    start = $('input#customer_statement_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#customer_statement_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            },
        },
        columnDefs: [
            { "width": "5%", "targets": 0 },
            { "width": "10%", "targets": 1 },
            { "width": "0%", "targets": 2 },
            { "width": "0%", "targets": 3 },
            { "width": "0%", "targets": 4 },
            { "width": "0%", "targets": 5 },
            { "width": "0%", "targets": 6 },
            { "width": "0%", "targets": 7 },
            { "width": "5%", "targets": 8 },
            { "width": "5%", "targets": 9 },
            { "width": "0%", "targets": 10 ,className: 'text-right'},
            { "width": "0%", "targets": 11 ,className: 'text-right'},
            // { "width": "8%", "targets":  11 ,className: 'text-right'},
             {
                "targets": 12,
                "visible": false,
            },
        ],
        columns:  [
            { data: 'action', searchable: false, orderable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'order_no', name: 'order_no' },
            // { data: 'location', name: 'location' },
            { data: 'invoice_no', name: 'invoice_no' },
            
            { data: 'route_name', name: 'route_name'},
            { data: 'ref_no', name: 'vehicle_number' },
            { data: 'order_date', name: 'order_date' },
            { data: 'product', name: 'product' },
            { data: 'quantity', name: 'quantity' },
            { data: 'unit_price', name: 'unit_price' },
            { data: 'final_total', name: 'final_total' },
            { data: 'due_amount', name: 'due_amount' },
            {data: 'final_due_amount', searchable: false, orderable: false, name: 'due_amount'},
        ],
        @include('layouts.partials.datatable_export_button')
        fnDrawCallback: function(oSettings) {
                var due_total = sum_table_col($('#customer_statement_table'), 'due');
                
                $('#footer_due').text(due_total);
                
                var total = sum_table_col($('#customer_statement_table'), 'total');
                $('#footer_total').text(due_total);
                
                __currency_convert_recursively($('#customer_statement_table'));
            },
        });
        
        
        $('#customer_statement_date_range, #customer_statement_location_id, #customer_statement_customer_id, #customer_statement_reference, #customer_statement_logos, #logo').on('change input', function(){
                loadStatements();
        });

        $('#customer_statement_customer_id').select2();

        // $(document).on('click', '.reprint_statement', function(e){
        //     console.log("he")
        //     e.preventDefault();
        //     href = $(this).data('href');
        //     console.log(href)
        //     $.ajax({
        //         method: 'get',
        //         contentType: 'html',
        //         url: href,
        //         data: {  },
        //         success: function(result) {
        //             $('#report_print_div').empty().append(result);
        //              setTimeout(function() { // Small delay to ensure content is fully loaded
        //                 $('#report_print_div').printThis();
        //             }, 300); // Adjust delay as necessary

        //         },
        //     });
        // });
        
$(document).on('click', '.reprint_statement', function(e) {
    e.preventDefault();
    let href = $(this).data('href');
    console.log("Loading content from:", href);

    // AJAX request to load the content
    $.ajax({
        method: 'get',
        url: href,
        dataType: 'html',
        success: function(result) {
            // Load the result into the modal body
            $('#modalContent').html(result);

            // Show the modal
            $('#printableModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error loading content:", error);
            alert("Failed to load content. Please try again.");
        }
    });
});

// Print the modal content when clicking the print button
$(document).on('click', '#printButton', function() {
    let printContent = document.getElementById('modalContent').innerHTML;
    let originalContent = document.body.innerHTML;

    // Set up the print layout
    document.body.innerHTML = printContent;

    // Trigger the print
    window.print();

    // Restore the original page content after printing
    document.body.innerHTML = originalContent;
    location.reload(); // Reload the page to reinitialize any JavaScript functionalities
});

        
        $(document).on('click', '.pdf_statement', function(e){
            e.preventDefault();
            href = $(this).data('href');
            $.ajax({
                method: 'get',
                contentType: 'html',
                url: href,
                data: {  },
                success: function(result) {
                    $('#report_print_div').empty().append(result);
                    generatePdf(result,'pdf');

                },
            });
        });
        
        $(document).on('click', '.email_statement', function(e){
            e.preventDefault();
            href = $(this).data('href');
            $.ajax({
                method: 'get',
                contentType: 'html',
                url: href,
                data: {  },
                success: function(result) {
                    $('#report_print_div').empty().append(result);
                    generatePdf(result,'email');

                },
            });
        });

});

$('#customer_id').select2();

$('#enable_separate_customer_statement_no').change(function(){
    console.log($(this).val());
    if($(this).val() == 1){
        console.log("value 1");
        $('.customer_separate_field').removeClass('hide');
        $('.customer_separate_field_no').addClass('hide');
    }else if ($(this).val() == 0){
        console.log("value 0");
        $('.customer_separate_field').addClass('hide');
        $('.customer_separate_field_no').removeClass('hide');
    }
});

$('#settings_statement_btn').click(function(){
    $.ajax({
        method: 'post',
        url: '/customer-statement-settings',
        data: {
            enable_separate_customer_statement_no : $('#enable_separate_customer_statement_no').val(),
            customer_id : $('#customer_id').val(),
            starting_no : $('#starting_no').val(),
         },
        success: function(result) {
            if(result.success == 1){
                toastr.success(result.msg);
            }else{
                toastr.error(result.msg);
            }
            statement_settings_table.ajax.reload();
        },
    });
})
$(document).on('click', '#edit_statement_settings', function(){
    url = $('#customer_statement_setting_add_form').attr('action');

    $.ajax({
        method: 'put',
        url: url,
        data: {
            enable_separate_customer_statement_no : $('#edit_enable_separate_customer_statement_no').val(),
            customer_id : $('#edit_customer_id').val(),
            starting_no : $('#edit_starting_no').val(),
         },
        success: function(result) {
            if(result.success == 1){
                toastr.success(result.msg);
            }else{
                toastr.error(result.msg);
            }

            $('.customer_statement_modal').modal('hide');

            statement_settings_table.ajax.reload();
        },
    });
})
        $('#customer_statement_date_range').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
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
                        return;
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#customer_statement_date_range').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        customer_statement_table.ajax.reload();
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
                    if ($('#customer_statement_date_range').length) {
                        $('#customer_statement_date_range').val(fullRange);
                        $('#customer_statement_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#customer_statement_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        customer_statement_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
    // if ($('#customer_statement_date_range').length == 1) {
        // $('#customer_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        //     $('#customer_statement_date_range').val(
        //         start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        //     );
        //     customer_statement_table.ajax.reload();
        //     loadStatements();
        // });
        // $('#customer_statement_date_range').on('cancel.daterangepicker', function(ev, picker) {
        //     $('#product_sr_date_filter').val('');
        // });
        // $('#customer_statement_date_range')
        //     .data('daterangepicker')
        //     .setStartDate(moment().startOf('month'));
        // $('#customer_statement_date_range')
        //     .data('daterangepicker')
        //     .setEndDate(moment().endOf('month'));
    // }
    
    
    
    function loadStatements()
        {
            var customer_id     = $('#customer_statement_customer_id').val();
            var reference       = $('#customer_statement_reference').val();
            var location_id     = $('#customer_statement_location_id').val();
            var customer_logo   = $('#customer_statement_logos').val();
            var date_range      = $('input#customer_statement_date_range').val();
            
            var start = '';
            var end = '';
            if ($('input#customer_statement_date_range').val()) {
                start = $('input#customer_statement_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end = $('input#customer_statement_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('.from_date').text($('input#customer_statement_date_range').data('daterangepicker').startDate.format('DD-MM-YYYY'));
                $('.to_date').text($('input#customer_statement_date_range').data('daterangepicker').endDate.format('DD-MM-YYYY'));
            } 

            var start_date = start;
            var end_date = end;


            $.ajax({
                method: 'get',
                url: '/customer-statement',
                data: { customer_id : customer_id, location : location_id, reference : reference, logo : customer_logo, start_date : start_date, end_date : end_date },
                success: function(result) {
                    if(result.date){
                        mindate = result.date;
                        
                        // Convert the startDate to a Unix timestamp
                        var startDateTimestamp = new Date(start).getTime() / 1000;
                        if(mindate){
                            // Convert the "2023-03-01" date to a Unix timestamp
                            var targetDateTimestamp = new Date(mindate).getTime() / 1000;
                        }else{
                            var targetDateTimestamp = 0;
                        }
                        
                        
                        // Compare the timestamps
                        if (startDateTimestamp <= targetDateTimestamp) {
                            toastr.error("You cannot regenerate statements older than " + mindate + " for "+$('#customer_statement_customer_id option:selected').text());
                        } else {
                            var statement_no = $('#statement_no').val();
                            $('#print_header_div').empty().append(result.header);
                        }
                    }else{
                        customer_statement_table.ajax.reload();
                        $.ajax({
                            method: 'get',
                            url: '/customer-statement',
                            data: { customer_id : customer_id,  start_date : start_date, end_date : end_date },
                            success: function(result) {
                                // $('.statement_no').text(result.statement_no);
                                $('#statement_no').val(result.statement_no);
                                $('#print_header_div').empty().append(result.header);
                            },
                        });
                    }
                }
            });
        }
    // end load statement

    let date = $('#customer_statement_date_range').val().split(' - ');

    $('.from_date').text(date[0]);
    $('.to_date').text(date[1]);

    if ($('#list_customer_statement_date_range').length == 1) {
        $('#list_customer_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#list_customer_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            customer_statement_list_table.ajax.reload();
        });
        $('#custom_date_apply_button').on('click', function() {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#list_customer_statement_date_range').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#list_customer_statement_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#list_customer_statement_date_range').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
                customer_statement_list_table.ajax.reload();
            } else {
                alert("Please select both start and end dates.");
            }
        });
        $('#list_customer_statement_date_range').on('apply.daterangepicker', function(ev, picker) {
            if (picker.chosenLabel === 'Custom Date Range') {
                $('.custom_date_typing_modal').modal('show');
            }
        });
        $('#list_customer_statement_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#list_customer_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#list_customer_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    if ($('#printed_list_customer_statement_date_range').length == 1) {
        $('#printed_list_customer_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#printed_list_customer_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            customer_statement_list_table.ajax.reload();
        });
        $('#printed_list_customer_statement_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#printed_list_customer_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#printed_list_customer_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    $(document).on('click', '.delete_customer_statement', function(e) {
        e.preventDefault();
      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_brand,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          customer_statement_list_table.ajax.reload();
                          $(".modal").modal('hide');

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
    
    $(document).ready( function(){
        
        customer_statement_list_table = $('#customer_statement_list_table').DataTable({
            processing: true,
            serverSide: false,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/customer-statement/get-statement-list',
                data: function(d) {
                    d.location_id = $('select#list_customer_statement_location_id').val();
                    d.customer_id = $('select#list_customer_statement_customer_id').val();
                    var start = '';
                    var end = '';
                    if ($('input#list_customer_statement_date_range').val()) {
                        start = $('input#list_customer_statement_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#list_customer_statement_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    
                    var printed_start = '';
                    var printed_end = '';
                    
                    if ($('input#printed_list_customer_statement_date_range').val()) {
                        printed_start = $('input#printed_list_customer_statement_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        printed_end = $('input#printed_list_customer_statement_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    
                    d.start_date = start;
                    d.end_date = end;
                    d.printed_start = printed_start; 
                    d.printed_end = printed_end;
                    
                    // console.log(d);
                },
            },
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'print_date', name: 'print_date' },
                { data: 'location', name: 'location' },
                { data: 'date_from', name: 'date_from' },
                { data: 'date_to', name: 'date_to' },
                { data: 'customer', name: 'customer' },
                { data: 'statement_no', name: 'statement_no' },
                { data: 'amount', name: 'amount' },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'username', name: 'username' },
                { data: 'description', name: 'description', searchable: false },
            ],
            @include('layouts.partials.datatable_export_button')
                fnDrawCallback: function(oSettings) {
                    var total = sum_table_col($('#customer_statement_list_table'), 'amount');
                    $('#grand_total').html(__number_f(total));
                },
        
            });

            $('#list_customer_statement_date_range, #list_customer_statement_customer_id, #list_customer_statement_location_id').change(function(){
                customer_statement_list_table.ajax.reload();
            });
        });
        
    $(document).ready( function(){
        
        // if ($('#list_statement_payment_date_range').length == 1) {
        //     $('#list_statement_payment_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        //         $('#list_statement_payment_date_range').val(
        //             start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        //         );
        //         list_statement_payment_table.ajax.reload();
        //     });
        //     $('#list_statement_payment_date_range').on('cancel.daterangepicker', function(ev, picker) {
        //         $('#product_sr_date_filter').val('');
        //     });
        //     $('#list_statement_payment_date_range')
        //         .data('daterangepicker')
        //         .setStartDate(moment().startOf('month'));
        //     $('#list_statement_payment_date_range')
        //         .data('daterangepicker')
        //         .setEndDate(moment().endOf('month'));
        // }

        $('#list_statement_payment_date_range').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
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
                        return;
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#list_statement_payment_date_range').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        list_statement_payment_table.ajax.reload();
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
                    if ($('#list_statement_payment_date_range').length) {
                        $('#list_statement_payment_date_range').val(fullRange);
                        $('#list_statement_payment_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#list_statement_payment_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        list_statement_payment_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
        
        list_statement_payment_table = $('#list_statement_payment_table').DataTable({
            processing: true,
            serverSide: false,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/customer-statement/list-payments',
                data: function(d) {
                    d.customer_id = $('select#list_statement_payment_customer_id').val();
                    var start = '';
                    var end = '';
                    if ($('input#list_statement_payment_date_range').val()) {
                        start = $('input#list_statement_payment_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#list_statement_payment_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    
                    d.start_date = start;
                    d.end_date = end;
                    
                    d.statement_no = $("#list_statement_payment_statement_no").val();
                    d.payment_method = $("#list_statement_payment_method").val();
                },
            },
            columns: [
                { data: 'paid_on', name: 'paid_on' },
                { data: 'customer_name', name: 'contacts.name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'statement_no', name: 'customer_statements.statement_no' },
                { data: 'statement_amount', name: 'statement_amount', searchable: false },
                { data: 'amount', name: 'amount' },
                { data: 'method', name: 'method' },
                { data: 'username', name: 'users.name' }
            ],
            @include('layouts.partials.datatable_export_button')
                fnDrawCallback: function(oSettings) {
                   
                },
        
            });

            $('#list_statement_payment_date_range, #list_statement_payment_customer_id,#list_statement_payment_statement_no,#list_statement_payment_method').change(function(){
                list_statement_payment_table.ajax.reload();
            });
        });
</script>

<script>
    function generatePdf(html,action) {
        $.ajax({
            url: '/download-pdf',
            method: 'POST',
            data: {
                html: html
            },
            success: function(data) {
                // Handle the success response, for example:
                var downloadUrl = data.path;
                if(action == "email"){
                    emailPdf(downloadUrl);
                }else if(action == "pdf"){
                    downloadPdf(downloadUrl);
                }
                
            },
            error: function(xhr, status, error) {
                // Handle the error response, for example:
                alert('An error occurred while generating the PDF.');
            }
        });
    }
    function emailPdf(file){
        console.log(file)
        const emailAddress = '';
        const emailSubject = 'Statement';
        const emailBody = 'Please find attached copy of your Customer Statement';

        let mailtoUrl = `mailto:${emailAddress}?subject=${encodeURIComponent(emailSubject)}&body=${encodeURIComponent(emailBody)}`;

        // Append the attachment header to the mailto URL
        mailtoUrl += `&attachment=${encodeURIComponent(file)}&Content-Type=application/pdf`;
        
        console.log(mailtoUrl);
        
        // Open the user's default email client with the pre-populated email
        window.open(mailtoUrl, '_blank');
    }
    function downloadPdf(file){
        var link = document.createElement('a');
        link.href = file;
        link.download = 'report.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    function saveDiv() {
        var location_id = $('select#customer_statement_location_id').val();
        var customer_id = $('select#customer_statement_customer_id').val();
        var logo = $('select#logo').val();
        
        if(!customer_id){
            toastr.error('Please select a customer');
            return false;
        }
        
        if(!logo){
            toastr.error('Please pick a logo');
            return false;
        }
        
        var start = '';
        var end = '';
        if ($('input#customer_statement_date_range').val()) {
            start = $('input#customer_statement_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            end = $('input#customer_statement_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
        }
        var start_date = start;
        var end_date = end;
        var statement_no = $('#statement_no').val();
        $.ajax({
            method: 'post',
            url: '/customer-statement',
            data: {
                location_id : location_id,
                customer_id : customer_id,
                start_date : start_date,
                end_date : end_date,
                statement_no : statement_no,
                logo : logo,
             },
            success: function(result) {
                if(result.success == 1){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
            },
        });

    }
    
    $(document).on('click', 'a.delete_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        logos_table.ajax.reload();
                    },
                });
            }
        });
    });
    
</script>

@endsection
