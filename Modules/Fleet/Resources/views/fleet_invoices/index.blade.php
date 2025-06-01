@extends('layouts.app')
@section('title', __('fleet::lang.fleet_invoices'))

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
                        <a style="font-size:13px;" href="#create_invoice" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('fleet::lang.create_invoice')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'list_invoices') active @endif">
                        <a style="font-size:13px;" href="#list_invoices" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>@lang('fleet::lang.list_invoices')</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="create_invoice">
            @include('fleet::fleet_invoices.create_invoice')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'list_invoices') active @endif" id="list_invoices">
            @include('fleet::fleet_invoices.list_invoices')
        </div>
    </div>
</section>

@endsection


@section('javascript')
<script>
    $('#location_id option:eq(1)').attr('selected', true);
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#list_date_range_filter').length == 1) {
        $('#list_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#list_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            list_route_operation_table.ajax.reload();
        });
        $('#list_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            
        });
        $('#list_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#list_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    // list_route_operation_table
    $(document).ready(function(){
        $(".original_location").show();
        $(".s_original_location").hide();
        
        $(".select2").select2();
        
        list_route_operation_table = $('#list_route_operation_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@list_invoices')}}",
                    data: function(d){
                        d.invoice_name = $('#list_invoice_name').val();
                        
                        d.start_date = $('#list_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#list_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'print_date', name: 'print_date'},
                    {data: 'customer', name: 'customer'},
                    {data: 'date_range', name: 'date_range'},
                    {data: 'invoice_nos', name: 'invoice_nos'},
                    {data: 'invoice_name', name: 'invoice_name'},
                    {data: 'action', name: 'action'}
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });

        $('#list_invoice_name').change(function () {
            list_route_operation_table.ajax.reload();
        })
        
        $('#original_type').change(function () {
            if($(this).val() == "dynamic"){
                $(".original_location").show();
                $(".s_original_location").hide();
            }else{
                $(".original_location").hide();
                $(".s_original_location").show();
            }
        })
        
        
</script>
<script>
    $('#location_id option:eq(1)').attr('selected', true);
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            route_operation_table.ajax.reload();
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    // route_operation_table
    $(document).ready(function(){
        route_operation_table = $('#route_operation_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@index_create')}}",
                    data: function(d){
                        d.location_id = $('#location_id').val();
                        d.contact_id = $('#contact_id').val();
                        d.vehicle_no = $('#vehicle_no').val();
                        d.trip_category = $('#trip_category').val();
                        
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'date_of_operation', name: 'date_of_operation'},
                    {data: 'contact_name', name: 'contacts.name'},
                    {data: 'category_name', name: 'trip_categories.name'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'order_number', name: 'order_number'},
                    {data: 'invoice_no', name: 'invoice_no'},
                    {data: 'product_name', name: 'route_products.name'},
                    {data: 'qty', name: 'qty'},
                    {data: 'distance', name: 'distance'},
                    {data: 'delivered_to_acc_no', name: 'fleet_account_numbers.delivered_to_acc_no'},
                    {data: 'amount', name: 'amount'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });

        $('#date_range_filter, #location_id, #contact_id, #route_id, #vehicle_no, #driver_id, #helper_id, #payment_status, #payment_method,#trip_category').change(function () {
            route_operation_table.ajax.reload();
        })
        
        $(document).on('click', '#add_create_invoice', function(){
            
            var location_id = $('#location_id').val();
            
            var type = $('#original_type').val();
            
            var customer_id = $('#contact_id').val();
            var invoice_name = $('#invoice_name').val();
            var logo = $('#logo').val();
            var vehicle_no = $('#vehicle_no').val();
            
            if(type== "dynamic"){
                var original_location = $('#original_location').val();
            }else{
                var original_location = $('#s_original_location').val();
            }
            
            if(invoice_name == ""){
                toastr.error("@lang('fleet::lang.please_select_invoice')");
                return false;
            }
            
            if(vehicle_no == ""){
                toastr.error("@lang('fleet::lang.please_select_vehicle')");
                return false;
            }
            
            if(location_id == ""){
                toastr.error("@lang('fleet::lang.please_select_location')");
                return false;
            }
            
            
            
            if(customer_id == ""){
                toastr.error("@lang('fleet::lang.please_select_customer')");
                return false;
            }
            
            if(logo == ""){
                toastr.error("@lang('fleet::lang.please_select_logo')");
                return false;
            }
            
            var start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
            var end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                            
            if(original_location == ""){
                toastr.error("@lang('fleet::lang.please_select_original_location')");
                return false;
            }
            
            $.ajax({
                    method: 'POST',
                    url: "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@insert_fleetInvoice')}}",
                    dataType: 'json',
                    data: {type,location_id,customer_id,invoice_name,start_date,end_date,logo,original_location,vehicle_no},
                    success: function(result) {
                        console.log(result);
                        if (result.success == true) {
                            toastr.success(result.msg);
                            list_route_operation_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },

                    error: function(xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                        toastr.error("@lang('messages.something_went_wrong')");
                    }
                });
            
            
        });
</script>
@endsection