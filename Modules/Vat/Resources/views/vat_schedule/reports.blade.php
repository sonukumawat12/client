@extends('layouts.app')

@section('title', __('vat::lang.vat_module'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs no-print">
                    <li class="active">
                        <a href="#tax_report" class="tax_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.customer_vat_schedule')</strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#supplier_tax_report" class="supplier_tax_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.supplier_vat_schedule')</strong>
                        </a>
                    </li>
                    
                </ul> 
                <div class="tab-content">
                    <div class="tab-pane active" id="tax_report">
                        @include('vat::vat_schedule.customer')
                    </div>
                    
                    <div class="tab-pane" id="supplier_tax_report">
                        @include('vat::vat_schedule.supplier')
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    
</section>
<!-- /.content -->

@endsection
@section('javascript')
@if(!empty(session('status')) && empty(session('status')['success']))
    <script>
        toastr.error('{{session('status')['msg']}}');
    </script>
    
@endif 
<script>
$(document).ready(function(){
    if ($('#tax_report_date_filter').length == 1) {
        $('#tax_report_date_filter').daterangepicker(dateRangeSettings, function (start, end) {
            $('#tax_report_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            taxes_details_table.ajax.reload();
        });
        $('#tax_report_date_filter').on('cancel.daterangepicker', function (ev, picker) {
            $('#tax_report_date_filter').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
        $('#tax_report_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#tax_report_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    
    taxes_details_table = $('#taxes_details_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/vat-module/customer-vat-schedule',
            data: function (d) {
                
                var start = $('#tax_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#tax_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var contact_id = $("#contact_id").val();
                
                
                d.contact_id = contact_id;
                d.start_date = start;
                d.end_date = end;
            },
        },
        columns: [
            { 
            data: null,
                render: function (data, type, row, meta) {
                    // Add 1 to meta.row to start serial number from 1
                    return meta.row + 1;
                }
            },
            { data: 'date', name: 'date' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'vat_number', name: 'vat_number' },
            { data: 'contact_name', name: 'contacts.name' },
            { data: 'product_name', name: 'products.name' },
            { data: 'tax_base', name: 'tax_base', className: 'text-right' },
            { data: 'tax_amount', name: 'tax_amount', className: 'text-right' }
        ],
        fnDrawCallback: function (oSettings) {
            // 
            var total_amount = sum_table_col($('#taxes_details_table'), 'final-total');
            $('#footer_total_amount').text((__number_uf(__number_f(total_amount))));

            var total_tax = sum_table_col(
                $('#taxes_details_table'),
                'tax-amount'
            );
            $('#footer_vat_total').text((__number_uf(__number_f(total_tax))));

            // __currency_convert_recursively($('#taxes_details_table'));
        },
    });
    
    $("#contact_id").on('change',function(){
        taxes_details_table.ajax.reload();
    })
})
    
</script>


<script>
$(document).ready(function(){
    if ($('#report_date_filter').length == 1) {
        $('#report_date_filter').daterangepicker(dateRangeSettings, function (start, end) {
            $('#report_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            sup_taxes_details_table.ajax.reload();
        });
        $('#report_date_filter').on('cancel.daterangepicker', function (ev, picker) {
            $('#report_date_filter').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
        $('#report_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#report_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    
    sup_taxes_details_table = $('#sup_taxes_details_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/vat-module/supplier-vat-schedule',
            data: function (d) {
                
                var start = $('#report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var contact_id = $("#sup_contact_id").val();
                
                
                d.contact_id = contact_id;
                d.start_date = start;
                d.end_date = end;
            },
        },
        columns: [
            { 
            data: null,
                render: function (data, type, row, meta) {
                    // Add 1 to meta.row to start serial number from 1
                    return meta.row + 1;
                }
            },
            { data: 'date', name: 'date' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'vat_number', name: 'vat_number' },
            { data: 'contact_name', name: 'contacts.name' },
            { data: 'product_name', name: 'products.name' },
            { data: 'tax_base', name: 'tax_base', className: 'text-right' },
            { data: 'tax_amount', name: 'tax_amount', className: 'text-right' }
        ],
        fnDrawCallback: function (oSettings) {
            // 
            var total_amount = sum_table_col($('#sup_taxes_details_table'), 'final-total');
            $('#sup_footer_total_amount').text((__number_uf(__number_f(total_amount))));

            var total_tax = sum_table_col(
                $('#sup_taxes_details_table'),
                'tax-amount'
            );
            $('#sup_footer_vat_total').text((__number_uf(__number_f(total_tax))));

            // __currency_convert_recursively($('#sup_taxes_details_table'));
        },
    });
    
    $("#sup_contact_id").on('change',function(){
        sup_taxes_details_table.ajax.reload();
    })
})
    
</script>

@endsection