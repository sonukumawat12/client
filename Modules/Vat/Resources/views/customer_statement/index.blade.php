@extends('layouts.app')
@section('title', __('vat::lang.vat_statements'))
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
    .dropdown-toggle{
        margin:8px;
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
                            <i class="fa fa-superpowers"></i> <strong>@lang('vat::lang.vat_statements')</strong>
                        </a>
                    </li>
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#list_customer_statements" class="" data-toggle="tab">
                            <i class="fa fa-list"></i>
                            <strong>@lang('vat::lang.list_vat_statements')</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#vat_prefixes" class="vat_prefixes" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.prefix_and_starting_nos')</strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#vat_logos" class="vat_logos" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.statement_settings')</strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#font-setting" class="font-setting" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.customer_statement_font_setting')</strong>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="customer_statements">
            @include('vat::customer_statement.partials.customer_statements')
        </div>
        <div class="tab-pane" id="list_customer_statements">
            @include('vat::customer_statement.partials.list_customer_statements')
        </div>
        <div class="tab-pane" id="vat_prefixes">
            @include('vat::vat_statement_prefixes.index')
        </div>
        <div class="tab-pane" id="vat_logos">
            @include('vat::customer_statement.logos.index')
        </div>
        
        <div class="tab-pane" id="font-setting">
            @include('vat::customer_statement.font-setting')
        </div>
        
    </div>

    <div class="modal fade customer_statement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


<div class="hide">
    <div id="report_print_div"></div>
</div>
    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        
        
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

        // $(document).on('click', '.reprint_statement', function(e){
        //     e.preventDefault();
        //     href = $(this).data('href');
        //     $.ajax({
        //         method: 'get',
        //         contentType: 'html',
        //         url: href,
        //         data: {  },
        //         success: function(result) {
        //             $('#report_print_div').empty().append(result);
        //             $('#report_print_div').printThis();

        //         },
        //     });
        // });
        
        $(document).on('click', '.reprint_statement', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    setTimeout(function() {
                location.reload();
            }, 50000);
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
        
        logos_table = $('#logos_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Vat\Http\Controllers\VatStatementLogoController@index')}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
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
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        customer_statement_table = $('#customer_statement_table').DataTable({
        processing: true,
        serverSide: false,
        aaSorting: [[0, 'desc']],
        pageLength: -1,
        ajax: {
            url: '/vat-module/customer-statement',
            data: function(d) {
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
        @include('layouts.partials.datatable_export_button')
        
        columns:  [
            { data: 'action', searchable: false, orderable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'order_no', name: 'order_no' },
            { data: 'invoice_no', name: 'invoice_no' },
            
            { data: 'vehicle_number', name: 'invoice_no' },
            
            { data: 'quantity', name: 'quantity' },
            
            { data: 'product', name: 'product' },
            { data: 'unit_price', name: 'unit_price' },
            { data: 'final_total', name: 'final_total' },
        ],
        fnDrawCallback: function(oSettings) {
            due_total = sum_table_col($('#customer_statement_table'), 'due');
            $('#due_total').val(due_total);
            },
        });
        
        
        $('#customer_statement_date_range, #customer_statement_location_id, #customer_statement_customer_id,#price_adjustment').on('change input', function(){
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
                    url: '/vat-module/customer-date',
                    data: { id : $('#customer_statement_customer_id').val() },
                    success: function(result) {
                        
                        var location_id = $('select#customer_statement_location_id').val();
                            var customer_id = $('select#customer_statement_customer_id').val();
                            var reference = $('select#customer_statement_reference').val();
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
                                    url: '/vat-module/get-customer-statement-no',
                                    data: { customer_id : $('#customer_statement_customer_id').val(),  start_date : start_date, end_date : end_date ,price_adjustment: __read_number($("#price_adjustment"))},
                                    success: function(result) {
                                        debugger;
                                        $('#print_footer_div').empty().append(result.footer);
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
                                    url: '/vat-module/get-customer-statement-no',
                                    data: { customer_id : $('#customer_statement_customer_id').val(),  start_date : start_date, end_date : end_date ,price_adjustment: __read_number($("#price_adjustment"))},
                                    success: function(result) {
                                        // document.getElementById('price_adjustment').value = result.priceAdjustmentAmt;
                                        $('#print_footer_div').empty().append(result.footer);
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
                customer_statement_table.ajax.reload();
                                
                var start_date = start;
                var end_date = end;
                var statement_no = $('#statement_no').val();
    
                $.ajax({
                    method: 'get',
                    url: '/vat-module/get-customer-statement-no',
                    data: { customer_id : $('#customer_statement_customer_id').val(),  start_date : start_date, end_date : end_date ,price_adjustment: __read_number($("#price_adjustment"))},
                    success: function(result) {
                        $('#print_footer_div').empty().append(result.footer);
                        // $('.statement_no').text(result.statement_no);
                        $('#statement_no').val(result.statement_no);
                        $('#print_header_div').empty().append(result.header);
    
                    },
                });
            }
        }

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
                url: '/vat-module/customer-statement/get-statement-list',
                data: function(d) {
                    d.location_id = $('select#list_customer_statement_location_id').val();
                    d.customer_id = $('select#list_customer_statement_customer_id').val();
                    d.reference = $('select#list_customer_statement_reference').val();
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
                    
                  
                    d.printed_start = start; 
                    d.printed_end = end;
                    
                    // console.log(d);
                },
            },
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'print_date', name: 'print_date' },
                { data: 'date_from', name: 'date_from' },
                { data: 'date_to', name: 'date_to' },
                { data: 'customer', name: 'customer' },
                { data: 'statement_no', name: 'statement_no' },
                { data: 'amount', name: 'amount' },
                { data: 'payment_status', name: 'payment_status'},
                { data: 'username', name: 'username' },
                { data: 'description', name: 'description', searchable: false },
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
    function downloadPdf(file){
        var link = document.createElement('a');
        link.href = file;
        link.download = 'report.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    function saveDiv() {
        var customer_id = $('select#customer_statement_customer_id').val();
        
        if(!customer_id){
            toastr.error('Please select a customer');
            return false;
        }
        
        var logo = $('select#logo').val();
         
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
            url: '/vat-module/customer-statement',
            data: {
                customer_id : customer_id,
                start_date : start_date,
                end_date : end_date,
                statement_no : statement_no,
                logo : logo,
                price_adjustment: __read_number($("#price_adjustment"))
             },
            success: function(result) {
                if(result.success == 1){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
                
                window.location.reload();
            },
        });

    }
        var columns = [

            { data: 'prefix', name: 'prefix' },
            
            { data: 'starting_no', name: 'starting_no' },

            { data: 'user_created', name: 'users.username' },
            
            { data: 'action', name: 'action' }
        ];

    prefixes_table = $('#prefixes_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatStatementPrefixController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    $(document).on('click', 'a.delete_task', function(e) {
		e.preventDefault();
		var href = $(this).data('href');
        var data = $(this).serialize();
        console.log(href);
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
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
                        prefixes_table.ajax.reload();
                        
                        tank_transfers_table.ajax.reload();
                        userinvoice_prefixes_table.ajax.reload();
                        userinvoice_smstypes_table.ajax.reload();
                        vat_payable_to_table.ajax.reload();
                    },
                });
            }
        });
    });
    
</script>

<script>
    document.getElementById('header').style.fontSize =  document.getElementById('header_size').value + 'px';
    document.getElementById('company').style.fontSize =  document.getElementById('company_size').value + 'px';
    document.getElementById('address').style.fontSize =  document.getElementById('address_size').value + 'px';
    
    document.getElementById('left_header').style.fontSize =  document.getElementById('left_header_size').value + 'px';
    document.getElementById('right_header').style.fontSize =  document.getElementById('right_header_size').value + 'px';
    document.getElementById('system_footer').style.fontSize =  document.getElementById('system_footer_size').value + 'px';
    
    
    document.getElementById('thead').style.fontSize =  document.getElementById('thead_size').value + 'px';
    document.getElementById('tbody').style.fontSize =  document.getElementById('tbody_size').value + 'px';
    document.getElementById('sub').style.fontSize =  document.getElementById('sub_size').value + 'px';
    document.getElementById('footer1').style.fontSize =  document.getElementById('footer_size').value + 'px';
    
    
    
    // Function to update font size
    function updateFontSize(elementId, fontSize) {
        
        var element = document.getElementById(elementId);
        if (element) {
            element.style.fontSize = fontSize + 'px';
        }
    }
    
    // Event listener for invoice font size input
    document.getElementById('header_size').addEventListener('input', function() {
        updateFontSize('header', this.value);
    });

    // Event listener for header font size input
    document.getElementById('company_size').addEventListener('input', function() {
        updateFontSize('company', this.value);
    });

    document.getElementById('address_size').addEventListener('input', function() {
        updateFontSize('address', this.value);
    });
    
    document.getElementById('left_header_size').addEventListener('input', function() {
        updateFontSize('left_header', this.value);
    });
    
    document.getElementById('right_header_size').addEventListener('input', function() {
        updateFontSize('right_header', this.value);
    });
    
    document.getElementById('system_footer_size').addEventListener('input', function() {
        updateFontSize('system_footer', this.value);
    });

   
    document.getElementById('footer_size').addEventListener('input', function() {
        updateFontSize('footer1', this.value);
    });
    document.getElementById('sub_size').addEventListener('input', function() {
        updateFontSize('sub', this.value);
    });
    document.getElementById('thead_size').addEventListener('input', function() {
        updateFontSize('thead', this.value);
    });
    document.getElementById('tbody_size').addEventListener('input', function() {
        updateFontSize('tbody', this.value);
    });
</script>
<script>
    function roundingOnclick(){
        var total = document.getElementById('total-invoice-amount').innerText;
        var amountBeforeRoundingOff = document.getElementById('amount-before-rounding-off').innerText;

        total = parseFloat(total.replace(/,/g, ''));
        amountBeforeRoundingOff = parseFloat(amountBeforeRoundingOff.replace(/,/g, ''));

        // Calculate the difference
        var deff = total - amountBeforeRoundingOff;
        // Display the difference
        document.getElementById('price_adjustment').value= deff.toFixed(8);
        document.getElementById('price_adjustment').focus();
    };
</script>
@endsection
