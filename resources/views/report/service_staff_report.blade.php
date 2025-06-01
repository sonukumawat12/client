@extends('layouts.app')
@section('title', __('restaurant.service_staff_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('restaurant.service_staff_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ssr_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('ssr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('service_staff_id',  __('restaurant.service_staff') . ':') !!}
                        {!! Form::select('service_staff_id', $waiters, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ssr_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'ssr_date_range', 'readonly']); !!}
                    </div>
                </div>

                <!-- Modal for Custom Date Range -->
                <div class="modal fade" id="ssr_customDateRangeModal" tabindex="-1" aria-labelledby="ssr_customDateRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ssr_customDateRangeModalLabel">Select Custom Date Range</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-6">
                                <label for="ssr_start_date">From:</label>
                                <input type="date" id="ssr_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                                
                                <label for="ssr_end_date" class="mt-2">To:</label>
                                <input type="date" id="ssr_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="ssr_applyCustomRange">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#ss_orders_tab" data-toggle="tab" aria-expanded="true">@lang('restaurant.orders')</a>
                    </li>

                    <li>
                        <a href="#ss_line_orders_tab" data-toggle="tab" aria-expanded="true">@lang('lang_v1.line_orders')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="ss_orders_tab">
                        @include('report.partials.service_staff_orders_table')
                    </div>

                    <div class="tab-pane" id="ss_line_orders_tab">
                        @include('report.partials.service_staff_line_orders_table')
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
        $(document).ready(function(){
            if($('#ssr_date_range').length == 1){
                var my_ranges = Object.assign({}, ranges);
                my_ranges['Custom Date Range'] = [moment(), moment()];
                $('#ssr_date_range').daterangepicker({
                    ranges: my_ranges,
                    autoUpdateInput: false,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                    locale: {
                        format: moment_date_format
                    }
                });
                $('#custom_date_apply_button').on('click', function() {
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);

                        $('#ssr_date_range').val(formattedStartDate + ' ~ ' + formattedEndDate);

                        $('#ssr_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#ssr_date_range').data('daterangepicker').setEndDate(moment(endDate));

                        $('.custom_date_typing_modal').modal('hide');
                        service_staff_report.ajax.reload();
                        service_staff_line_orders.ajax.reload();
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });
                $('#ssr_date_range').on('apply.daterangepicker', function(ev, picker) {
                    if (picker.chosenLabel === 'Custom Date Range') {
                        $('.custom_date_typing_modal').modal('show');
                    } else {
                        $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                        service_staff_report.ajax.reload();
                        service_staff_line_orders.ajax.reload();
                    }
                });

                $('#ssr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    service_staff_report.ajax.reload();
                    service_staff_line_orders.ajax.reload();
                });
            }

        service_staff_report = $('table#service_staff_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/reports/service-staff-line-orders?partial=1",
                "data": function ( d ) {
                    var start = $('input#ssr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#ssr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.list_for = 'service_staff_report';
                    d.location_id = $('select#ssr_location_id').val();
                    d.start_date = start;
                    d.end_date = end;
                    d.res_waiter_id = $('select#service_staff_id').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'service_staff', name: 'ss.first_name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'total_before_tax', name: 'transactions.total_before_tax'},
                { data: 'discount_amount', name: 'transactions.discount_amount'},
                { data: 'tax_amount', name: 'transactions.tax_amount'},
                { data: 'final_total', name: 'final_total'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [4, 5, 6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                $('#footer_total_amount').text(sum_table_col($('#service_staff_report'), 'final_total'));
                $('#footer_subtotal').text(sum_table_col($('#service_staff_report'), 'total_before_tax'));
                $('#footer_total_tax').text(sum_table_col($('#service_staff_report'), 'tax_amount'));

                $('#footer_total_discount').text(sum_table_col($('#service_staff_report'), 'discount_amount'));
                
                __currency_convert_recursively($('#service_staff_report'));
            }
        });

        service_staff_line_orders = $('table#service_staff_line_orders').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/reports/service-staff-line-orders",
                "data": function ( d ) {
                    var start = $('input#ssr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#ssr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.location_id = $('select#ssr_location_id').val();
                    d.start_date = start;
                    d.end_date = end;
                    d.service_staff_id = $('select#service_staff_id').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 't.transaction_date'  },
                { data: 'invoice_no', name: 't.invoice_no'},
                { data: 'service_staff', name: 'ss.first_name'},
                { data: 'product_name', name: 'p.name'},
                { data: 'quantity', name: 'quantity'},
                { data: 'unit_price_before_discount', name: 'unit_price_before_discount'},
                { data: 'line_discount_amount', name: 'line_discount_amount'},
                { data: 'item_tax', name: 'item_tax'},
                { data: 'unit_price_inc_tax', name: 'unit_price_inc_tax'},
                { data: 'total', searchable: false}
            ],
            "fnDrawCallback": function (oSettings) {
                $('#sslo_quantity').html(__sum_stock($('#service_staff_line_orders'), 'quantity'));
                $('#sslo_total_tax').text(sum_table_col($('#service_staff_line_orders'), 'item_tax'));
                $('#sslo_unit_price').text(sum_table_col($('#service_staff_line_orders'), 'unit_price_before_discount'));
                $('#sslo_total_discount').text(sum_table_col($('#service_staff_line_orders'), 'total-discount'));

                $('#sslo_subtotal').text(sum_table_col($('#service_staff_line_orders'), 'unit_price_inc_tax'));
                $('#sslo_total').text(sum_table_col($('#service_staff_line_orders'), 'total'));
                
                __currency_convert_recursively($('#service_staff_line_orders'));
            }
        });

            
        //Customer Group report filter
        $('select#ssr_location_id, #ssr_date_range, #service_staff_id').change( function(){
            service_staff_report.ajax.reload();
            service_staff_line_orders.ajax.reload();
        });
    })
    </script>
@endsection