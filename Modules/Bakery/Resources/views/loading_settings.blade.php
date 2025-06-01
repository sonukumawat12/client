@extends('layouts.app')
@section('title','Loading')
<style>
    .select2 {
        width: 100% !important;
    }

    #product_modal_bakery {
            width: 500px;
            margin: auto;
    }
</style>
@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-12 dip_tab">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">

                            <li class=" @if(session('status.tab') == 'loading'  || empty(session('status.tab')))) active @endif">
                                <a style="font-size:13px;" href="#loading" data-toggle="tab">
                                    <i class="fa-solid fa-car"></i><strong>Loading</strong>
                                </a>
                            </li>

                            <li class=" @if(session('status.tab') == 'list_loading') active @endif">
                                <a style="font-size:13px;" href="#list_loading" data-toggle="tab">
                                     <strong>List Loading</strong>
                                </a>
                            </li>

                    </ul>
                </div>
            </div>
        </div>
    <div class="tab-content">

        
        <div class="tab-pane @if(session('status.tab') == 'loading' || empty(session('status.tab'))) active @endif" id="loading">
             @include('bakery::loading.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'list_loading') active @endif" id="list_loading">
            @include('bakery::loading.list_loading')
        </div>

    </div>

    
</section>

@endsection

@section('javascript')
<script>
//driver tab script
    if ($('#driver_date_range_filter').length == 1) {
        $('#driver_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#driver_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            driver_table_bakery.ajax.reload();
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
    $(document).ready(function () {
        driver_table_bakery = $('#driver_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\DriverController@index')}}',
                data: function (d) {
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
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'driver_name', name: 'driver_name' },
                { data: 'nic_number', name: 'nic_number' },
                { data: 'dl_number', name: 'dl_number' },
                { data: 'dl_type', name: 'dl_type' },
                { data: 'expiry_date', name: 'expiry_date' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
      
        
    })

    $('#driver_date_range_filter, #employee_no, #driver_name, #nic_number').change(function () {
        driver_table_bakery.ajax.reload();
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
                        driver_table_bakery.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                    },
                });
            }
        });
    });

</script>

<script>
    $('#location_id option:eq(1)').attr('selected', true);

    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            fleet_table.ajax.reload();
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
    
    $(document).on('click', '#add_fleet_btn', function(){
        $('.fleet_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })


    // fleet_table
    $(document).ready(function(){
        
        @if (session('status'))
            @if(session('status')['success'] == false)
                    var msg = "{{ session('status')['msg'] }}"
                    toastr.error(msg);
            @endif
        @endif
        
        
        fleet_table = $('#fleet_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Bakery\Http\Controllers\FleetController@index')}}",
                    data: function(d){
                        d.location_id = $('#location_id').val();
                        d.vehicle_model = $('#vehicle_model').val();
                        d.vehicle_brand = $('#vehicle_brand').val();
                        d.vehicle_type = $('#vehicle_type').val();
                        d.vehicle_number = $('#vehicle_number').val();
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
                    {data: 'action', name: 'action'},
                    {data: 'date', name: 'date'},
                    {data: 'location_name', name: 'location_name'},
                    {data: 'code_for_vehicle', name: 'code_for_vehicle'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'starting_meter', name: 'starting_meter'},
                    {data: 'ending_meter', name: 'ending_meter'},
                    {data: 'vehicle_type', name: 'vehicle_type'},
                    // {data: 'fuel_type', name: 'fuel_type'},
                    // {data: 'income', name: 'income'},
                    // {data: 'payment_received', name: 'payment_received'},
                    // {data: 'payment_due', name: 'payment_due'},
                    {data: 'opening_balance', name: 'opening_balance'}
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#fleet_table'));
                }
            });
        });

        $('#date_range_filter, #location_id, #vehicle_model, #vehicle_brand, #vehicle_type, #vehicle_number').change(function () {
            fleet_table.ajax.reload();
        })
        
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
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryInvoiceNumberController@index')}}',
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

    //route_product_table tab script
    if ($('#route_product_date_range_filter').length == 1) {
        $('#route_product_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#route_product_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
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
    $(document).ready(function () {
        route_product_table = $('#route_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryProductController@index')}}',
                data: function (d) {
                    var start_date = $('input#route_product_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#route_product_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
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

      // route table
    $(document).ready(function(){

        @if (session('status'))
        @if(session('status')['success'] == false)
        var msg = "{{ session('status')['msg'] }}"
        toastr.error(msg);
        @endif
                @endif


            route_table = $('#route_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Bakery\Http\Controllers\RouteController@index')}}",
                data: function(d){
                    d.route = $('#route').val();
                    d.vehicle_model = $('#vehicle_model').val();
                    d.added_by = $('#added_by').val();
                }
            },
            columnDefs:[{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'date', name: 'date'},
                {data: 'route', name: 'route'},
                {data: 'added_by', name: 'added_by'},

            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#route_table'));
            }
        });
    });

    $('#date, #route, #added_by').change(function () {
        route_table.ajax.reload();
    })
       
</script>

@endsection


