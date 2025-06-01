@extends('layouts.app')
@section('title', __('contact_credit_sales.credit_sales'))

@section('content')

    <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{__('contact_credit_sales.credit_sales')}}</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><span>{{__('contact_credit_sales.credit_sales')}}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <!-- Main content -->
    <section class="content main-content-inner">

        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('contact_credit_sales.filters')])
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location', __('contact_credit_sales.location') . ':') !!}
                                {!! Form::select('location', $business_locations, null, ['class' => 'form-control select2',
                                'placeholder' => __('contact_credit_sales.all'), 'id' => 'location', 'style' => 'width:
                                100%;']); !!}
                            
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ir_customer_id', __('contact_credit_sales.customer') . ':') !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                                {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2',
                                'placeholder' => __('contact_credit_sales.all'), 'id' => 'customer_id', 'style' => 'width:
                                100%;']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('invoice_no', __('contact_credit_sales.invoice_no') . ':') !!}
                            
                            
                                {!! Form::select('invoice_no', $invoices, null, ['class' => 'form-control select2',
                                'placeholder' => __('contact_credit_sales.all'), 'id' => 'invoice_no', 'style' => 'width:
                                100%; !important']); !!}
                            
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('date_filter', __('contact_credit_sales.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('contact_credit_sales.select_a_date_range'), 'class' =>
                            'form-control', 'id' => 'date_filter', 'readonly']); !!}
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
                            <table class="table table-bordered table-striped" id="credit_sales_table"
                                   style="width: 100%">
                                <thead>
                                <tr>
                                    <th>@lang('contact_credit_sales.date')</th>
                                    <th>@lang('contact_credit_sales.customer')</th>
                                    <th>@lang('contact_credit_sales.invoice_no')</th>
                                    <th>@lang('contact_credit_sales.amount')</th>
                                    <th>@lang('contact_credit_sales.payment_status')</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="3"><strong>@lang('contact_credit_sales.total'):</strong></td>
                                    <td><span id="total">0.00</span></td>
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
    
    <script>
        if ($('#date_filter').length == 1) {
            $('#date_filter').daterangepicker(dateRangeSettings, function (start, end) {
                $('#date_filter span').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                credit_sales_table.ajax.reload();
            });
            $('#date_filter').on('cancel.daterangepicker', function (ev, picker) {
                $('#date_filter').val('');
                credit_sales_table.ajax.reload();
            });
            
             $('#date_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
            
        }

        $(document).ready(function () {
            credit_sales_table = $('#credit_sales_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'desc']],
                "ajax": {
                    "url": "{{action('ContactCreditSales@index')}}",
                    "data": function (d) {
                        if ($('#date_filter').val()) {
                            var start = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.invoice_no = $('#invoice_no').val();
                        d.customer_id = $('#customer_id').val();
                        d.location_id = $('#location').val();
                    }
                },
                columns: [
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'customer_name',name: 'contacts.name'},
                    {data: 'invoice_no',name: 'invoice_no'},
                    { data: 'amount', name: 'amount'},
                    { data: 'payment_status', name: 'payment_status'},
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

                    $('#total').text(__number_f(sum_table_col($('#credit_sales_table'), 'final-total')));
                    __currency_convert_recursively($('#credit_sales_table'));
                },

            });
        });

        $(document).on('change', '#date_filter, #customer_id, #invoice_no', function () {
            credit_sales_table.ajax.reload();
        });
    </script>
@endsection
