@extends('layouts.app')
@section('title', __('lang_v1.outstanding_received_report'))

@section('content')

    <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('lang_v1.outstanding_received_report')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('lang_v1.contacts')</a></li>
                    <li><span>@lang('lang_v1.outstanding_received_report')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <!-- Main content -->
    <section class="content main-content-inner">

        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('outstanding_report_date_filter', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                            'form-control', 'id' => 'outstanding_report_date_filter', 'readonly']); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                                {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control select2',
                                'placeholder' => __('lang_v1.all'), 'id' => 'outstanding_customer_id', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('payment_ref_no', __('lang_v1.payment_ref_no') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                                {!! Form::select('payment_ref_no', [], null, ['class' => 'form-control select2',
                                'placeholder' => __('lang_v1.all'), 'id' => 'payment_ref_no', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('cheque_number', __('lang_v1.cheque_number') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                                {!! Form::select('cheque_number', [], null, ['class' => 'form-control select2',
                                'placeholder' => __('lang_v1.all'), 'id' => 'cheque_number', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('payment_type', __('lang_v1.payment_method') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                                {!! Form::select('payment_type', $payment_types, null, ['class' => 'form-control select2',
                                'placeholder' => __('lang_v1.all'), 'id' => 'payment_type', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('payment_page', __('lang_v1.payment_page') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                                {!! Form::select('payment_page', $payment_pages, null, ['class' => 'form-control select2',
                                'placeholder' => __('lang_v1.all'), 'id' => 'payment_page', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>


        <div class="table-responsive">
            <div class="row">
                <div class="col-md-12">
                    @component('components.widget', ['class' => 'box-primary'])
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="outstanding_report_table"
                                   style="width: 100%">
                                <thead>
                                <tr>
                                    <th>@lang('report.payment_received_date')</th>
                                    <th>@lang('lang_v1.system_date')</th>
                                    <th>@lang('lang_v1.payment_type')</th>
                                    <th>@lang('report.customer')</th>
                                    <th>@lang('lang_v1.payment_ref_no')</th>
                                    <th>@lang('lang_v1.bill_no')</th>
                                    <th>@lang('lang_v1.paid_for_bill_no')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    <th>@lang('lang_v1.payment_amount')</th>
                                    <th>@lang('lang_v1.cheque_card_no')</th>
                                    <th>Bank</th>
                                    <th>User</th>
                                    @if(auth()->user()->can('edit_received_outstanding') || auth()->user()->can('add_received_outstanding'))
                                        <th>Action</th>
                                    @endif
                                    <td></td>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td><strong>@lang('sale.total'):</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <span class="display_currency" id="footer_sale_total" data-currency_symbol="true"></span>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @if(auth()->user()->can('edit_received_outstanding') || auth()->user()->can('add_received_outstanding'))
                                        <td></td>
                                    @endif
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
    </script>
    <script>
        // if ($('#outstanding_report_date_filter').length == 1) {
        //     $('#outstanding_report_date_filter').daterangepicker(dateRangeSettings, function (start, end) {
        //         $('#outstanding_report_date_filter span').val(
        //             start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
        //         );
        //         outstanding_report_table.ajax.reload();
        //         updateFilters();
        //     });
        //     $('#outstanding_report_date_filter').on('cancel.daterangepicker', function (ev, picker) {
        //         $('#outstanding_report_date_filter').val('');
        //         outstanding_report_table.ajax.reload();
        //     });
            
        //      $('#outstanding_report_date_filter')
        //         .data('daterangepicker')
        //         .setStartDate(moment().startOf('month'));
        //     $('#outstanding_report_date_filter')
        //         .data('daterangepicker')
        //         .setEndDate(moment().endOf('month'));
            
        // }

        $('#outstanding_report_date_filter').daterangepicker({
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
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#outstanding_report_date_filter').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        outstanding_report_table.ajax.reload();
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
                    if ($('#outstanding_report_date_filter').length) {
                        $('#outstanding_report_date_filter').val(fullRange);
                        $('#outstanding_report_date_filter').data('daterangepicker').setStartDate(moment(startDate));
                        $('#outstanding_report_date_filter').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        outstanding_report_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
        
        function updateFilters(){
            var start_date = $('#outstanding_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end_date = $('#outstanding_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            
            $.ajax({
                method: 'get',
                url: "{{action('ContactController@getOutstandingFilters')}}",
                data: {start_date, end_date},
                success: function (result) {
                    var default_option = '<option value="">All</option>';
                    $("#payment_ref_no").empty().append(default_option);
                    $("#cheque_number").empty().append(default_option);
                    
                    $.each(result.payment_ref_nos,function(index,value){
                        $("#payment_ref_no").append('<option value="'+ index +'">'+  value +'</option>');
                    })
                    
                    $.each(result.cheque_numbers,function(index,value){
                        $("#cheque_number").append('<option value="'+ index +'">'+  value +'</option>');
                    })
                    
                },
            });
        }

        $(document).ready(function () {
            outstanding_report_table = $('#outstanding_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'desc']],
                "ajax": {
                    "url": "{{action('ReportController@getOutstandingReport')}}",
                    "data": function (d) {
                        if ($('#outstanding_report_date_filter').val()) {
                            var start = $('#outstanding_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#outstanding_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        
                        d.bill_no = $('#bill_no').val();
                        d.payment_ref_no = $('#payment_ref_no').val();
                        d.cheque_number = $('#cheque_number').val();
                        d.payment_type = $('#payment_type').val();
                        d.payment_page = $('#payment_page').val();
                        d.customer_id = $('#outstanding_customer_id').val();
                    }
                },
                columns: [
                    {data: 'paid_on', name: 'tp.paid_on'},
                    {data: 'created_at', name: 'tp.created_at'},
                    {data: 'ttype', name: 'transactions.type'},
                    {data: 'name', name: 'contacts.name'},
                    {data: 'payment_ref_no', name: 'tp.payment_ref_no'},
                    {data: 'invoice_no',name: 'invoice_no'},
                    {data: 'paid_for',name: 'paid_for',searchable: false},
                    { data: 'method', name: 'tp.method'},
                    {data: 'payment_amount', name: 'payment_amount'},
                    {data: 'cheque_number', name: 'tp.cheque_number'},
                    {data: 'bank_name', name: 'tp.bank_name'},
                    {data: 'user_name', name: 'user.username'},
                    @if(auth()->user()->can('edit_received_outstanding') || auth()->user()->can('add_received_outstanding'))
                        {data: 'action', name: 'action'},
                    @endif
                    {data: 'total_paid', name: 'tp.amount', searchable: true, visible: false},
                ],
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file"></i> Export to CSV',
                        className: 'btn btn-default btn-sm',
                        title: 'Outstanding Received Report',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                    true : false;
                            }
                        },
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                        className: 'btn btn-default btn-sm',
                        title: 'Outstanding Received Report',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                    true : false;
                            }
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i> Column Visibility',
                        className: 'btn btn-default btn-sm',
                        title: 'Outstanding Received Report',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                    true : false;
                            }
                        },
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                        className: 'btn btn-default btn-sm',
                        title: 'Outstanding Received Report',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                    true : false;
                            }
                        },
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-default btn-sm',
                        title: 'Outstanding Received Report',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                    true : false;
                            }
                        },
                        customize: function (win) {
                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('h1').css('font-size', '25px');
                        },
                    },
                ],
                "fnDrawCallback": function (oSettings) {

                    $('#footer_sale_total').text(sum_table_col($('#outstanding_report_table'), 'final-total'));

                    $('#footer_total_paid').text(sum_table_col($('#outstanding_report_table'), 'total-paid'));

                    $('#footer_total_paid1').text(sum_table_col($('#outstanding_report_table'), 'total-paid'));

                    $('#footer_total_remaining').text(sum_table_col($('#outstanding_report_table'), 'payment_due'));

                    $('#footer_total_sell_return_due').text(sum_table_col($('#outstanding_report_table'), 'sell_return_due'));

                    $('#footer_payment_status_count').html(__sum_status_html($('#outstanding_report_table'), 'payment-status-label'));
                    __currency_convert_recursively($('#outstanding_report_table'));
                },

            });
        });

        $(document).on('change', '#outstanding_report_date_filter, #outstanding_customer_id, #payment_type, #cheque_number, #payment_ref_no, #bill_no,#payment_page', function () {
            outstanding_report_table.ajax.reload();
        });

        $(document).on('click', '.btn-modal-edit', function (e) {
            e.preventDefault();
            var container = $(this).data('container');
            $.ajax({
                url: $(this).data('href'),
                dataType: 'html',
                success: function (result) {
                    $(container).html(result).modal('show');
                },
            });
        });
    </script>
@endsection
