@extends('layouts.app')
@section('title', __('fleet::lang.settings'))

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
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#routes" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('fleet::lang.routes')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'drivers') active @endif">
                        <a style="font-size:13px;" href="#drivers" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>@lang('fleet::lang.drivers')</strong>
                        </a>
                    </li>

                    <li class=" @if(session('status.tab') == 'helpers') active @endif">
                        <a style="font-size:13px;" href="#helpers" data-toggle="tab">
                            <i class="fa fa-user-secret"></i> <strong>@lang('fleet::lang.helpers')</strong>
                        </a>
                    </li>

                     <li class=" @if(session('status.tab') == 'route_invoice_number') active @endif">
                        <a style="font-size:13px;" href="#route_invoice_number" data-toggle="tab">
                            # <strong>@lang('fleet::lang.starting_invoice_number')</strong>
                        </a>
                    </li>
                     <li class=" @if(session('status.tab') == 'route_product') active @endif">
                        <a style="font-size:13px;" href="#route_product" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.product')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'account_nos') active @endif">
                        <a style="font-size:13px;" href="#account_nos" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.account_nos')</strong>
                        </a>
                    </li>
                    
                    <li class=" @if(session('status.tab') == 'original_locations') active @endif">
                        <a style="font-size:13px;" href="#original_locations" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.original_locations')</strong>
                        </a>
                    </li>
                    
                    <li class=" @if(session('status.tab') == 'fleet_logos') active @endif">
                        <a style="font-size:13px;" href="#fleet_logos" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.fleet_logos')</strong>
                        </a>
                    </li>
                    
                    <li class=" @if(session('status.tab') == 'fuel_types') active @endif">
                        <a style="font-size:13px;" href="#fuel_types" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.fuel_types')</strong>
                        </a>
                    </li>
                    
                    <li class=" @if(session('status.tab') == 'trip_categories') active @endif">
                        <a style="font-size:13px;" href="#trip_categories" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.trip_categories')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'vehicle_category') active @endif">
                        <a style="font-size:13px;" href="#vehicle_category" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.vehicle_category')</strong>
                        </a>
                    </li>
                      <li class=" @if(session('status.tab') == 'rates') active @endif">
                        <a style="font-size:13px;" href="#rates" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.rates')</strong>
                        </a>
                    </li>
                    
                  
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="routes">
            @include('fleet::settings.routes.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'drivers') active @endif" id="drivers">
            @include('fleet::settings.drivers.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'helpers') active @endif" id="helpers">
            @include('fleet::settings.helpers.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_invoice_number') active @endif" id="route_invoice_number">
            @include('fleet::settings.route_invoice_number.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_product') active @endif" id="route_product">
            @include('fleet::settings.route_product.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'account_nos') active @endif" id="account_nos">
            @include('fleet::settings.account_nos.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'original_locations') active @endif" id="original_locations">
            @include('fleet::settings.original_locations.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'fleet_logos') active @endif" id="fleet_logos">
            @include('fleet::settings.fleet_logos.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'fuel_types') active @endif" id="fuel_types">
            @include('fleet::settings.fuel_types.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'trip_categories') active @endif" id="trip_categories">
            @include('fleet::settings.trip_categories.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'vehicle_category') active @endif" id="vehicle_category">
            @include('fleet::settings.vehicle_category.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'rates') active @endif" id="rates">
            @include('fleet::settings.Rates.index')
        </div>
        

    </div>
</section>

@endsection


@section('javascript')
<script>
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
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
    $(document).ready(function () {
        routes_table = $('#routes_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteController@index')}}',
                data: function (d) {
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
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'route_name', name: 'route_name' },
                
                { data: 'category_name', name: 'trip_categories.name' },
                { data: 'delivered_accno', name: 'fleet_account_numbers.delivered_to_acc_no' },
                
                { data: 'orignal_location', name: 'orignal_location' },
                { data: 'destination', name: 'destination' },
                { data: 'distance', name: 'distance' },
                
                { data: 'actual_distance', name: 'actual_distance' },
                
                { data: 'rate', name: 'rate' },
                { data: 'route_amount', name: 'route_amount' },
                { data: 'driver_incentive', name: 'driver_incentive' },
                { data: 'helper_incentive', name: 'helper_incentive' },
                { data: 'created_by', name: 'created_by' },
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#date_range_filter, #route_names, #orignal_locations, #destinations, #users').change(function () {
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
                        vehicle_category_table.ajax.reload();
                        account_nos_table.ajax.reload();
                        helper_table.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                        fleet_fuel_types_table.ajax.reload();
                        trip_categories_table.ajax.reload();
                        
                        original_locations_table.ajax.reload();
                    },
                });
            }
        });
    });

    
    $(document).ready(function () {
        driver_table = $('#driver_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\DriverController@index')}}',
                data: function (d) {
                    d.driver_name = $('#driver_name').val();
                    d.nic_number = $('#nic_number').val();
                    d.employee_no = $('#employee_no').val();
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'driver_name', name: 'driver_name' },
                
                { data: 'sal_name', name: 'sal_cat.name' },
                { data: 'bata_name', name: 'bata_cat.name' },
                { data: 'adv_name', name: 'adv_cat.name' },
                
                { data: 'nic_number', name: 'nic_number' },
                { data: 'dl_number', name: 'dl_number' },
                { data: 'dl_type', name: 'dl_type' },
                { data: 'expiry_date', name: 'expiry_date' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });

        vehicle_category_table = $('#vehicle_category_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\VehicleCategoryController@index')}}',
                data: function (d) {
                    // 
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'category', name: 'category' },
            ],
            fnDrawCallback: function(oSettings) {
                // 
            },
        });
        
        account_nos_table = $('#account_nos_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\FleetAccountNumberController@index')}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'invoice_name', name: 'invoice_name' },
                { data: 'delivered_to_acc_no', name: 'delivered_to_acc_no' },
                { data: 'account_number', name: 'account_number' },
                { data: 'dealer_name', name: 'dealer_name' },
                { data: 'dealer_account_number', name: 'dealer_account_number' },
                { data: 'bank_name', name: 'bank_name' },
                { data: 'branch', name: 'branch' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        fleet_logos_table = $('#fleet_logos_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\FleetLogoController@index')}}',
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
                { data: 'username', name: 'username' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
    })

    $('#driver_date_range_filter, #employee_no, #driver_name, #nic_number').change(function () {
        driver_table.ajax.reload();
    })

    
    $(document).ready(function () {
        helper_table = $('#helper_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\HelperController@index')}}',
                data: function (d) {
                    d.helper_name = $('#helper_name').val();
                    d.nic_number = $('#helper_nic_number').val();
                    d.employee_no = $('#helper_employee_no').val();
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'helper_name', name: 'helper_name' },
                
                { data: 'sal_name', name: 'sal_cat.name' },
                { data: 'adv_name', name: 'adv_cat.name' },
                
                { data: 'nic_number', name: 'nic_number' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#helper_date_range_filter, #helper_employee_no, #helper_name, #helper_nic_number').change(function () {
        helper_table.ajax.reload();
    });

    //route_invoice_number_table tab script
    if ($('#route_invoice_number_date_range_filter').length == 1) {
        $('#route_invoice_number_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#route_invoice_number_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
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
    $(document).ready(function () {
        route_invoice_number_table = $('#route_invoice_number_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteInvoiceNumberController@index')}}',
                data: function (d) {
                    var start_date = $('input#route_invoice_number_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#route_invoice_number_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'prefix', name: 'prefix' },
                { data: 'starting_number', name: 'starting_number' },
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })
    $('#route_invoice_number_date_range_filter').change(function () {
        route_invoice_number_table.ajax.reload();
    });

   
    $(document).ready(function () {
        route_product_table = $('#route_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteProductController@index')}}',
                data: function (d) {
                   
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })
    $('#route_product_date_range_filter').change(function () {
        route_product_table.ajax.reload();
    });

    
    $(document).ready(function () {
        original_locations_table = $('#original_locations_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\OriginalLocationsController@index')}}', 
                data: function (d) {
                   
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })
    $(document).ready(function () {
        fleet_fuel_types_table = $('#fleet_fuel_types_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\FuelController@index')}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'type', name: 'type' },
                { data: 'price_per_litre', name: 'price_per_litre' },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        trip_categories_table = $('#trip_categories_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\TripCategoryController@index')}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'amount_method', name: 'amount_method' },
                { data: 'created_by', name: 'created_by' },
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
    })
    $('#original_locations_date_range_filter').change(function () {
        original_locations_table.ajax.reload();
    });
</script>
@endsection