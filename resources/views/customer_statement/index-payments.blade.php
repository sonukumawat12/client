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
        @include('customer_statement.partials.list_customer_statements_payments')
    </div>

    <div class="modal fade customer_statement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


<div class="hide">
    <div id="report_print_div"></div>
</div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
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
  
        $('#customer_statement_date_range, #customer_statement_location_id, #customer_statement_customer_id').on('change input', function(){
                loadStatements();
        });

        $('#customer_statement_customer_id').select2();

        $(document).on('click', '.reprint_statement', function(e){
            e.preventDefault();
            href = $(this).data('href');
            $.ajax({
                method: 'get',
                contentType: 'html',
                url: href,
                data: {  },
                success: function(result) {
                    $('#report_print_div').empty().append(result);
                    $('#report_print_div').printThis();

                },
            });
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



    if ($('#customer_statement_date_range').length == 1) {
        $('#customer_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#customer_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            customer_statement_table.ajax.reload();
            loadStatements();
        });
        $('#customer_statement_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#customer_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#customer_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    
    
    function loadStatements()
    {
        if($('#customer_statement_customer_id').val() !== '' && $('#customer_statement_customer_id').val() !== undefined){
            
            // get customer minimum date
            var mindate = null;
            
            $.ajax({
                method: 'get',
                url: '/customer-date',
                data: { id : $('#customer_statement_customer_id').val() },
                success: function(result) {
                    
                    var location_id = $('select#customer_statement_location_id').val();
                        var customer_id = $('select#customer_statement_customer_id').val();
                        var start = '';
                        var end = '';
                        if ($('input#customer_statement_date_range').val()) {
                            start = $('input#customer_statement_date_range')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            end = $('input#customer_statement_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
            
                            $('.from_date').text($('input#customer_statement_date_range')
                                .data('daterangepicker')
                                .startDate.format('DD-MM-YYYY'));
                            $('.to_date').text($('input#customer_statement_date_range')
                                .data('daterangepicker')
                                .endDate.format('DD-MM-YYYY'));
                        }
                    
                    
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
                            
                            customer_statement_table.ajax.reload();
                            
                            var start_date = start;
                            var end_date = end;
                            var statement_no = $('#statement_no').val();
                
                            $.ajax({
                                method: 'get',
                                url: '/get-customer-statement-no',
                                data: { customer_id : $('#customer_statement_customer_id').val(),  start_date : start_date, end_date : end_date },
                                success: function(result) {
                                    console.log(result);
                                    // $('.statement_no').text(result.statement_no);
                                    $('#statement_no').val(result.statement_no);
                                    $('#print_header_div').empty().append(result.header);
                
                                },
                            });
                            
                        }
            
                        
        
                    }else{
                        customer_statement_table.ajax.reload();
                            
                            var start_date = start;
                            var end_date = end;
                            var statement_no = $('#statement_no').val();
                
                            $.ajax({
                                method: 'get',
                                url: '/get-customer-statement-no',
                                data: { customer_id : $('#customer_statement_customer_id').val(),  start_date : start_date, end_date : end_date },
                                success: function(result) {
                                    console.log(result);
                                    // $('.statement_no').text(result.statement_no);
                                    $('#statement_no').val(result.statement_no);
                                    $('#print_header_div').empty().append(result.header);
                
                                },
                            });
                            
                
                    }

                },
            });
            
            
            // $('.customer_name').text($('#customer_statement_customer_id :selected').text());
        }else{
            customer_statement_table.ajax.reload();
            
        }
    }

  

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
    
    $(document).ready( function(){
        
        customer_statement_list_table = $('#customer_statement_list_table').DataTable({
            processing: true,
            serverSide: false,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/customer-statement/get-statement-list-pmts',
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
                { data: 'username', name: 'username' },
            ],
                fnDrawCallback: function(oSettings) {
                    var total = sum_table_col($('#customer_statement_list_table'), 'amount');
                    $('#grand_total').html(__number_f(total));
                },
        
            });

        $('#list_customer_statement_date_range, #list_customer_statement_customer_id, #list_customer_statement_location_id').change(function(){
            customer_statement_list_table.ajax.reload();
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
</script>

@endsection
