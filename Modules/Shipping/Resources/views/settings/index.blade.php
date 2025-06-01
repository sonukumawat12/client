@extends('layouts.app')
@section('title', __('Shipping Settings'))

<style>
    .select2 {
        width: 100% !important;
    }
    

</style>
@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-12 dip_tab">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs ">
                        <li class=" @if (empty(session('status.tab'))) == 'drivers') active @endif @if (session('status.tab') == 'drivers') active @endif">
                            <a style="font-size:13px;" href="#drivers" data-toggle="tab">
                                <i class="fa fa-user"></i> <strong>@lang('shipping::lang.drivers')</strong>
                            </a>
                        </li>

                        <li class=" @if (session('status.tab') == 'officer') active @endif">
                            <a style="font-size:13px;" href="#officer" data-toggle="tab">
                                <strong>@lang(' Collection Officer ')</strong>
                            </a>
                        </li>

                        <li class=" @if (session('status.tab') == 'types') active @endif">
                            <a style="font-size:13px;" href="#types" data-toggle="tab">
                                <strong>@lang(' Shipping Types ')</strong>
                            </a>
                        </li>
                        <li class=" @if (session('status.tab') == 'status') active @endif">
                            <a style="font-size:13px;" href="#status" data-toggle="tab">
                                <strong>@lang(' Shipping Status ')</strong>
                            </a>
                        </li>
                        <li class=" @if (session('status.tab') == 'mode') active @endif">
                            <a style="font-size:13px;" href="#mode" data-toggle="tab">
                                <strong>@lang(' Shipping Mode ')</strong>
                            </a>
                        </li>
                        
                        <li class=" @if (session('status.tab') == 'delivery') active @endif">
                            <a style="font-size:13px;" href="#delivery" data-toggle="tab">
                                <strong>@lang('shipping::lang.shipping_delivery')</strong>
                            </a>
                        </li>
                        
                        <li class=" @if (session('status.tab') == 'delivery_days') active @endif">
                            <a style="font-size:13px;" href="#delivery_days" data-toggle="tab">
                                <strong>@lang('shipping::lang.shipping_delivery_days')</strong>
                            </a>
                        </li>
                        
                        <li class=" @if (session('status.tab') == 'prefix') active @endif">
                            <a style="font-size:13px;" href="#prefix" data-toggle="tab">
                                <strong>@lang('shipping::lang.prefix')</strong>
                            </a>
                        </li>
                        
                       <li class=" @if (session('status.tab') == 'package') active @endif">
                            <a style="font-size:13px;" href="#package" data-toggle="tab">
                                <strong>@lang('shipping::lang.package')</strong>
                            </a>
                        </li>
                        
                        <li class=" @if (session('status.tab') == 'price') active @endif">
                            <a style="font-size:13px;" href="#price" data-toggle="tab">
                                <strong>@lang('shipping::lang.price')</strong>
                            </a>
                        </li>
                        
                        
                        <li class=" @if (session('status.tab') == 'credit_days') active @endif">
                            <a style="font-size:13px;" href="#credit_days" data-toggle="tab">
                                <strong>@lang('shipping::lang.credit_days')</strong>
                            </a>
                        </li>
                        
                         <li class=" @if (session('status.tab') == 'account') active @endif">
                            <a style="font-size:13px;" href="#account" data-toggle="tab">
                                <strong>@lang('shipping::lang.shipping_account')</strong>
                            </a>
                        </li>
                        
                         <li class=" @if (session('status.tab') == 'dimension') active @endif">
                            <a style="font-size:13px;" href="#dimension" data-toggle="tab">
                                <strong>@lang('shipping::lang.shipping_dimensions')</strong>
                            </a>
                        </li>

                        <li class=" @if (session('status.tab') == 'fleet_logos') active @endif">
                            <a style="font-size:13px;" href="#fleet_logos" data-toggle="tab">
                                <i class="fa fa-cubes"></i> <strong>@lang('shipping::lang.fleet_logos')</strong>
                            </a>
                        </li>
                        <li class=" @if (session('status.tab') == 'bar_qr_code_print') active @endif">
                            <a style="font-size:13px;" href="#bar_qr_code_print" data-toggle="tab">
                                <i class="fa fa-qrcode"></i> <strong>@lang('shipping::lang.bar_qr_code_print')</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane  @if (empty(session('status.tab'))) active @endif @if (session('status.tab') == 'drivers') active @endif" id="drivers">
                @include('shipping::settings.drivers.index')
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'officer') active @endif" id="officer">
                @include('shipping::settings.officer.index')
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'types') active @endif" id="types">
                @include('shipping::settings.types.index')
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'status') active @endif" id="status">
                @include('shipping::settings.status.index')
            </div>

            <div class="tab-pane  @if (session('status.tab') == 'mode') active @endif" id="mode">
                @include('shipping::settings.mode.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'delivery') active @endif" id="delivery">
                @include('shipping::settings.delivery.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'delivery_days') active @endif" id="delivery_days">
                @include('shipping::settings.delivery_days.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'prefix') active @endif" id="prefix">
                @include('shipping::settings.prefix.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'package') active @endif" id="package">
                @include('shipping::settings.package.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'price') active @endif" id="price">
                @include('shipping::settings.price.index')
            </div>
            
            
            <div class="tab-pane  @if (session('status.tab') == 'credit_days') active @endif" id="credit_days">
                @include('shipping::settings.credit_days.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'account') active @endif" id="account">
                @include('shipping::settings.account.index')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'dimension') active @endif" id="dimension">
                @include('shipping::settings.dimension.index')
            </div>

            <div class="tab-pane  @if (session('status.tab') == 'fleet_logos') active @endif" id="fleet_logos">
                @include('shipping::settings.fleet_logos.index')
            </div>

            <div class="tab-pane  @if (session('status.tab') == 'bar_qr_code_print') active @endif" id="bar_qr_code_print">
                @include('shipping::settings.bar_qr_code_print.index')
            </div>


        </div>
    </section>

@endsection


@section('javascript')
    <script>
        if ($('#date_range_filter').length == 1) {
            $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                routes_table.ajax.reload();
            });
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            routes_table = $('#routes_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Fleet\Http\Controllers\RouteController@index') }}',
                    data: function(d) {
                        d.route_name = $('#route_names').val();
                        d.orignal_location = $('#orignal_locations').val();
                        d.destination = $('#destinations').val();
                        d.user_id = $('#users').val();
                        var start_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'route_name',
                        name: 'route_name'
                    },
                    {
                        data: 'orignal_location',
                        name: 'orignal_location'
                    },
                    {
                        data: 'destination',
                        name: 'destination'
                    },
                    {
                        data: 'distance',
                        name: 'distance'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'route_amount',
                        name: 'route_amount'
                    },
                    {
                        data: 'driver_incentive',
                        name: 'driver_incentive'
                    },
                    {
                        data: 'types_incentive',
                        name: 'types_incentive'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#date_range_filter, #route_names, #orignal_locations, #destinations, #users').change(function() {
            routes_table.ajax.reload();
        })

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
                            routes_table.ajax.reload();
                            driver_table.ajax.reload();
                            account_nos_table.ajax.reload();
                            types_table.ajax.reload();
                            status_table.ajax.reload();
                            
                            mode_table.ajax.reload();
                            delivery_table.ajax.reload();
                            delivery_days_table.ajax.reload();
                            
                            helper_table.ajax.reload();
                            route_invoice_number_table.ajax.reload();
                            route_product_table.ajax.reload();
                            
                            prefix_table.ajax.reload();
                            price_table.ajax.reload();
                            package_table.ajax.reload();
                            credit_days_table.ajax.reload();
                            dimension_table.ajax.reload();
                            
                            accounts_table.ajax.reload();
                        },
                    });
                }
            });
        });

        //driver tab script
        if ($('#driver_date_range_filter').length == 1) {
            $('#driver_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#driver_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                driver_table.ajax.reload();
            });
            $('#driver_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#driver_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#driver_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            driver_table = $('#driver_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\DriverController@index') }}',
                    data: function(d) {
                        d.driver_name = $('#driver_name').val();
                        d.nic_number = $('#nic_number').val();
                        d.employee_no = $('#employee_no').val();
                        var start_date = $('input#driver_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#driver_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    {
                        data: 'employee_no',
                        name: 'employee_no'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver_name'
                    },
                    {
                        data: 'nic_number',
                        name: 'nic_number'
                    },
                    {
                        data: 'dl_number',
                        name: 'dl_number'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            account_nos_table = $('#account_nos_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Fleet\Http\Controllers\FleetAccountNumberController@index') }}',
                    data: function(d) {

                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'invoice_name',
                        name: 'invoice_name'
                    },
                    {
                        data: 'account_number',
                        name: 'account_number'
                    },
                    {
                        data: 'dealer_name',
                        name: 'dealer_name'
                    },
                    {
                        data: 'dealer_account_number',
                        name: 'dealer_account_number'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'branch',
                        name: 'branch'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });


            fleet_logos_table = $('#fleet_logos_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Fleet\Http\Controllers\FleetLogoController@index') }}',
                    data: function(d) {

                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'logo',
                        name: 'logo'
                    },
                    {
                        data: 'image_name',
                        name: 'image_name'
                    },
                    {
                        data: 'alignment',
                        name: 'alignment'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });

        })

        $('#driver_date_range_filter, #employee_no, #driver_name, #nic_number').change(function() {
            driver_table.ajax.reload();
        })

        //types tab script
        if ($('#types_date_range_filter').length == 1) {
            $('#types_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#types_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                types_table.ajax.reload();
            });
            $('#types_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#types_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#types_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            types_table = $('#types_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\TypeController@index') }}',
                    data: function(d) {
                        d.shipping_types = $('#shipping_types').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#types_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#types_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    // {
                    //     data: 'employee_no',
                    //     name: 'employee_no'
                    // },
                    {
                        data: 'shipping_types',
                        name: 'shipping_types'
                    },
                    // {
                    //     data: 'nic_number',
                    //     name: 'nic_number'
                    // },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#types_date_range_filter, #types_employee_no, #shipping_types').change(function() {
            types_table.ajax.reload();
        });
        
        //status tab script
        if ($('#status_date_range_filter').length == 1) {
            $('#status_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#status_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                status_table.ajax.reload();
            });
            $('#status_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#status_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#status_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            status_table = $('#status_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\StatusController@index') }}',
                    data: function(d) {
                        d.shipping_status = $('#shipping_status').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#status_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#status_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    // {
                    //     data: 'employee_no',
                    //     name: 'employee_no'
                    // },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#status_date_range_filter, #status_employee_no, #shipping_status').change(function() {
            status_table.ajax.reload();
        });
        
        
         //mode tab script
        if ($('#mode_date_range_filter').length == 1) {
            $('#mode_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#mode_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                mode_table.ajax.reload();
            });
            $('#mode_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#mode_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#mode_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            mode_table = $('#mode_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\ModeController@index') }}',
                    data: function(d) {
                        d.shipping_mode = $('#shipping_mode').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#mode_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#mode_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    // {
                    //     data: 'employee_no',
                    //     name: 'employee_no'
                    // },
                    {
                        data: 'shipping_mode',
                        name: 'shipping_mode'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#mode_date_range_filter, #mode_employee_no, #shipping_mode').change(function() {
            mode_table.ajax.reload();
        });
        
        
         //delivery tab script
        if ($('#delivery_date_range_filter').length == 1) {
            $('#delivery_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#delivery_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                delivery_table.ajax.reload();
            });
            $('#delivery_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#delivery_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#delivery_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            delivery_table = $('#delivery_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\DeliveryController@index') }}',
                    data: function(d) {
                        d.shipping_delivery = $('#shipping_delivery').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#delivery_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#delivery_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    // {
                    //     data: 'employee_no',
                    //     name: 'employee_no'
                    // },
                    {
                        data: 'shipping_delivery',
                        name: 'shipping_delivery'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#delivery_date_range_filter, #delivery_employee_no, #shipping_delivery').change(function() {
            delivery_table.ajax.reload();
        });
        
        
        //delivery days tab script
        if ($('#delivery_days_date_range_filter').length == 1) {
            $('#delivery_days_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#delivery_days_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                delivery_days_table.ajax.reload();
            });
            $('#delivery_days_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#delivery_days_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#delivery_days_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            delivery_days_table = $('#delivery_days_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\DeliveryDaysController@index') }}',
                    data: function(d) {
                        d.days = $('#days').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#delivery_days_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#delivery_days_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    
                    {
                        data: 'days',
                        name: 'days'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#delivery_days_date_range_filter, #days').change(function() {
            delivery_days_table.ajax.reload();
        });
        
        
        //prefix tab script
        if ($('#prefix_date_range_filter').length == 1) {
            $('#prefix_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#prefix_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                prefix_table.ajax.reload();
            });
            $('#prefix_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#prefix_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#prefix_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            prefix_table = $('#prefix_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\PrefixController@index') }}',
                    data: function(d) {
                        d.prefix_ = $('#add_pr').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#prefix_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#prefix_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    
                    {
                        data: 'prefix',
                        name: 'prefix'
                    },
                    
                    {
                        data: 'starting_no',
                        name: 'starting_no'
                    },
                    
                    {
                        data: 'shipping_mode',
                        name: 'shipping_mode'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },                     
                    {                         
                        data: 'created_by',                         
                        name: 'created_by'                     
                        
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#prefix_date_range_filter, #add_pr').change(function() {
            delivery_days_table.ajax.reload();
        });
        
        
        //dimension tab script
        if ($('#dimension_date_range_filter').length == 1) {
            $('#dimension_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#dimension_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                dimension_table.ajax.reload();
            });
            $('#dimension_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#dimension_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#dimension_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            dimension_table = $('#dimension_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\DimensionController@index') }}',
                    data: function(d) {
                        
                        var start_date = $('input#dimension_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#dimension_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    
                    {
                        data: 'dimension_no',
                        name: 'dimension_no'
                    },
                    
                    {
                        data: 'weight',
                        name: 'weight'
                    },
                    
                    {
                        data: 'length',
                        name: 'length'
                    },
                    {
                        data: 'width',
                        name: 'width'
                    },
                    {
                        data: 'height',
                        name: 'height'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    }


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#dimension_date_range_filter, #dimension').change(function() {
            dimension_table.ajax.reload();
        });
        
        //package tab script
        if ($('#package_date_range_filter').length == 1) {
            $('#package_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#package_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                package_table.ajax.reload();
            });
            $('#package_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#package_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#package_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            package_table = $('#package_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\PackageController@index') }}',
                    data: function(d) {
                        d.package_name = $('#package_name').val();
                        
                        var start_date = $('input#package_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#package_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    
                    {
                        data: 'package_name',
                        name: 'package_name'
                    },
                    
                    {
                        data: 'package_details',
                        name: 'package_details'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    }, 

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#package_date_range_filter, #package').change(function() {
            package_table.ajax.reload();
        });

        $(document).ready(function() {
            bar_qr_code_table = $('#bar_qr_code_table').DataTable({
                processing: true,
                serverSide: true,
                "paging" : false,
                "info" : false,
                "searching": false,
                "ordering": false,
                "buttons": [
                   
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@index') }}',
                },
                columns: [
                    {
                        data: 'details',
                        name: 'details'
                    },
                    
                    {
                        data: 'bar_code',
                        name: 'bar_code'
                    },
                    
                    {
                        data: 'qr_code',
                        name: 'qr_code'
                    }

                ],
                "createdRow": function(row, data) {
                    if (data.bar_code === '') { // Check the condition for the extra row
                        $(row).addClass('extrarow'); // Add your class to the extra row
                    }
                },
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $(document).on('click', '.bar_qr_save_btn', function(e) {
            var page_details = $(this).closest('div.page_details')
            e.preventDefault();
            const tableRows = document.querySelectorAll('#bar_qr_code_table tbody tr');

            const secondColumnCheckBoxValues = [];const thirdColumnCheckBoxValues = [];const firstColumnCheckBoxValues = [];

            tableRows.forEach(row => {
                const firstColumnCheckBox = row.querySelectorAll('td:nth-child(1)');

                firstColumnCheckBox.forEach(checkbox => {
                    firstColumnCheckBoxValues.push(checkbox.innerText);
                });

                const secondColumnCheckBox = row.querySelectorAll('td:nth-child(2) input[type="checkbox"]');

                secondColumnCheckBox.forEach(checkbox => {
                    secondColumnCheckBoxValues.push(checkbox.checked);
                });

                const thirdColumnCheckBox = row.querySelectorAll('td:nth-child(3) input[type="checkbox"]');

                thirdColumnCheckBox.forEach(checkbox => {
                    thirdColumnCheckBoxValues.push(checkbox.checked);
                });

            });



            $.ajax({
                method: 'post',
                url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@store') }}',
                 dataType: 'json',
                data: {
                    'bar_code' : secondColumnCheckBoxValues,
                    'qr_code' : thirdColumnCheckBoxValues,
                    'details' : firstColumnCheckBoxValues
                },
                success: function(result) {
                    alert(@lang('lang_v1.success'));
                    toastr.success(@lang('lang_v1.success'));                  
                },
            });
        });       
        

        
        //credit days tab script
        if ($('#credit_date_range_filter').length == 1) {
            $('#credit_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#credit_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                credit_days_table.ajax.reload();
            });
            $('#credit_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#credit_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#credit_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            credit_days_table = $('#credit_days_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\CreditDaysController@index') }}',
                    data: function(d) {
                        d.credit_days = $('#filter_credit_days').val();
                        // d.nic_number = $('#types_nic_number').val();
                        // d.employee_no = $('#types_employee_no').val();
                        var start_date = $('input#credit_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#credit_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                       
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    
                    {
                        data: 'credit_days',
                        name: 'credit_days'
                    },
                    
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                      data: 'created_by',
                      name: 'created_by'
                    },
                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#credit_date_range_filter, #filter_credit_days').change(function() {
            credit_days_table.ajax.reload();
        });
        
        //prices tab script
        if ($('#price_date_range_filter').length == 1) {
            $('#price_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#price_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                price_table.ajax.reload();
            });
            $('#price_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#price_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#price_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            price_table = $('#price_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\PriceController@index') }}',
                    data: function(d) {
                        d.shipping_mode_price_ = $('#shipping_mode_price').val();
                        
                        var start_date = $('input#price_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#price_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'added_date',
                        name: 'added_date'
                    },
                    {
                       data: 'package_name',
                       name: 'shipping_packages.package_name'
                    },
                    {
                        data: 'per_kg',
                        name: 'per_kg'
                    },
                    {
                        data: 'fixed_price',
                        name: 'fixed_price'
                    },
                    
                    {
                        data: 'constant_value',
                        name: 'constant_value'
                    },
                    
                    {
                        data: 'shipping_partner',
                        name: 'shipping_partner'
                    },
                    
                    {
                        data: 'shipping_mode',
                        name: 'shipping_mode'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                      data: 'created_by',
                      name: 'created_by'
                    },

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
            
            accounts_table = $('#accounts_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingAccountController@index') }}',
                    data: function(d) {
                        
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                       data: 'incomeAccount',
                       name: 'income.name'
                    },
                    {
                        data: 'expenseAccount',
                        name: 'expense.name'
                    },
                    
                    
                    {
                        data: 'shippingPartner',
                        name: 'shipping_partners.shipping_partner'
                    },
                    
                    {
                        data: 'shippingMode',
                        name: 'shipping_mode.shipping_mode'
                    },
                    
                    {
                      data: 'created_by',
                      name: 'added_by'
                    },

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })

        $('#price_date_range_filter, #price').change(function() {
            price_table.ajax.reload();
        });

        //route_invoice_number_table tab script
        if ($('#route_invoice_number_date_range_filter').length == 1) {
            $('#route_invoice_number_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#route_invoice_number_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                route_invoice_number_table.ajax.reload();
            });
            $('#route_invoice_number_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#route_invoice_number_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#route_invoice_number_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            route_invoice_number_table = $('#route_invoice_number_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Fleet\Http\Controllers\RouteInvoiceNumberController@index') }}',
                    data: function(d) {
                        var start_date = $('input#route_invoice_number_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#route_invoice_number_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'prefix',
                        name: 'prefix'
                    },
                    {
                        data: 'starting_number',
                        name: 'starting_number'
                    },
                    {
                        data: 'created_by',
                        name: 'users.username'
                    },
                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })
        $('#route_invoice_number_date_range_filter').change(function() {
            route_invoice_number_table.ajax.reload();
        });

        //route_product_table tab script
        if ($('#route_product_date_range_filter').length == 1) {
            $('#route_product_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#route_product_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                route_product_table.ajax.reload();
            });
            $('#route_product_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#route_product_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#route_product_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        $(document).ready(function() {
            route_product_table = $('#route_product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Fleet\Http\Controllers\RouteProductController@index') }}',
                    data: function(d) {
                        var start_date = $('input#route_product_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#route_product_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_by',
                        name: 'users.username'
                    },
                ],
                fnDrawCallback: function(oSettings) {

                },
            });
        })
        $('#route_product_date_range_filter').change(function() {
            route_product_table.ajax.reload();
        });
        
        
         //helper tab script
    if ($('#helper_date_range_filter').length == 1) {
        $('#helper_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#helper_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            helper_table.ajax.reload();
        });
        $('#helper_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#helper_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('year'));
        $('#helper_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('year'));
    }
    $(document).ready(function () {
        helper_table = $('#helper_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Shipping\Http\Controllers\CollectionOfficerController@index')}}',
                data: function (d) {
                    d.helper_name = $('#helper_name').val();
                    d.nic_number = $('#helper_nic_number').val();
                    d.employee_no = $('#helper_employee_no').val();
                    var start_date = $('input#helper_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#helper_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'helper_name', name: 'helper_name' },
                { data: 'nic_number', name: 'nic_number' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#helper_date_range_filter, #helper_employee_no, #helper_name, #helper_nic_number').change(function () {
        helper_table.ajax.reload();
    });

        
        
    </script>
@endsection

