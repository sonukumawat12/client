<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('driver_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('driver_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'driver_date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('employee_no', __( 'fleet::lang.employee_no' )) !!}
                {!! Form::select('employee_no', $employee_nos, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'employee_no']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('driver_name', __( 'fleet::lang.driver_name' )) !!}
                {!! Form::select('driver_name', $driver_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'driver_name']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('nic_number', __( 'fleet::lang.nic_number' )) !!}
                {!! Form::select('nic_number', $nic_numbers, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'nic_number']);
                !!}
            </div>
        </div>
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.all_your_drivers')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\DriverController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="driver_table_bakery" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('fleet::lang.joined_date')</th>
          <th>@lang('fleet::lang.employee_no')</th>
          <th>@lang('fleet::lang.driver_name')</th>
          <th>@lang('fleet::lang.nic_number')</th>
          <th>@lang('fleet::lang.dl_number')</th>
          <th>@lang('fleet::lang.dl_type')</th>
          <th>@lang('fleet::lang.expiry_date')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->

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
                 $('#show_product_modal_bakery').on('click', function(e){
         $('#product_modal_bakery').modal('show');
     })
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
                { data: 'orignal_location', name: 'orignal_location' },
                { data: 'destination', name: 'destination' },
                { data: 'distance', name: 'distance' },
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
                        driver_table_bakery.ajax.reload();
                        account_nos_table.ajax.reload();
                        helper_table.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                        fleet_fuel_types_table.ajax.reload();
                        
                        original_locations_table.ajax.reload();
                    },
                });
            }
        });
    });

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
        driver_table_bakery = $('#driver_table_bakery').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\DriverController@index')}}',
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
        driver_table_bakery.ajax.reload();
    })

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
                url: '{{action('\Modules\Fleet\Http\Controllers\HelperController@index')}}',
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
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteProductController@index')}}',
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

    //original_locations_table tab script
    if ($('#original_locations_date_range_filter').length == 1) {
        $('#original_locations_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#original_locations_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            original_locations_table.ajax.reload();
        });
        $('#original_locations_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#original_locations_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('year'));
        $('#original_locations_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('year'));
    }
    $(document).ready(function () {
        original_locations_table = $('#original_locations_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\OriginalLocationsController@index')}}', 
                data: function (d) {
                    var start_date = $('input#original_locations_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#original_locations_date_range_filter')
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
    })
    $('#original_locations_date_range_filter').change(function () {
        original_locations_table.ajax.reload();
    });
</script>
 <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready( function(){
            product_table = $('#product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[3, 'asc']],
                "ajax": {
                    "url": "/products",
                    "data": function ( d ) {
                        console.log('dadadadadadad: ', d);
                        d.type = $('#product_list_filter_type').val();
                        d.category_id = $('#product_list_filter_category_id').val();
                        d.sub_category_id = $('#product_list_filter_sub_category_id').val();
                        d.product_id = $('#product_list_filter_product_id').val();
                        d.semi_finished = $('#product_list_filter_semi_finished').val();
                        d.brand_id = $('#product_list_filter_brand_id').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.tax_id = $('#product_list_filter_tax_id').val();
                        d.active_state = $('#active_state').val();
                        d.not_for_selling = $('#not_for_selling').is(':checked');
                        d.location_id = $('#location_id').val();
                        if ($('#repair_model_id').length == 1) {
                            d.repair_model_id = $('#repair_model_id').val();
                        }

                        if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(':checked')) {
                            d.woocommerce_enabled = 1;
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                columnDefs: [ {
                    "targets": [0, 1, 2],
                    "orderable": false,
                    "searchable": false
                } ],
                columns: [
                        { data: 'mass_delete'  },
                        { data: 'image', name: 'products.image'  },
                        { data: 'action', name: 'action'},
                        { data: 'product', name: 'products.name'  },
                        { data: 'product_locations', name: 'product_locations'  },
                        @can('view_purchase_price')
                            { data: 'purchase_price', name: 'max_purchase_price', searchable: false},
                        @endcan
                        @can('access_default_selling_price')
                            { data: 'selling_price', name: 'max_price', searchable: false},
                        @endcan
                        { data: 'current_stock', searchable: false},
                        { data: 'type', name: 'products.type'},
                        { data: 'category', name: 'c1.name'},
                        { data: 'brand', name: 'brands.name'},
                        { data: 'tax', name: 'tax_rates.name', searchable: false},
                        { data: 'sku', name: 'products.sku'},
                        { data: 'semi_finished', name: 'products.semi_finished'},
                        { data: 'product_custom_field1', name: 'products.product_custom_field1', visible: $('#cf_1').text().length > 0  },
                        { data: 'product_custom_field2', name: 'products.product_custom_field2' , visible: $('#cf_2').text().length > 0},
                        { data: 'product_custom_field3', name: 'products.product_custom_field3', visible: $('#cf_3').text().length > 0},
                        { data: 'product_custom_field4', name: 'products.product_custom_field4', visible: $('#cf_4').text().length > 0 },
                    ],
                    createdRow: function( row, data, dataIndex ) {
                        if($('input#is_rack_enabled').val() == 1){
                            var target_col = 0;
                            @can('product.delete')
                                target_col = 1;
                            @endcan
                            $( row ).find('td:eq('+target_col+') div').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
                        }
                        $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                    },
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#product_table'));
                    },
            });
            // Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#product_table tbody').on( 'click', 'tr i.rack-details', function () {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row( tr );
                var idx = $.inArray( tr.attr('id'), detailRows );

                if ( row.child.isShown() ) {
                    i.addClass( 'fa-plus-circle text-success' );
                    i.removeClass( 'fa-minus-circle text-danger' );

                    row.child.hide();
         
                    // Remove from the 'open' array
                    detailRows.splice( idx, 1 );
                } else {
                    i.removeClass( 'fa-plus-circle text-success' );
                    i.addClass( 'fa-minus-circle text-danger' );

                    row.child( get_product_details( row.data() ) ).show();
         
                    // Add to the 'open' array
                    if ( idx === -1 ) {
                        detailRows.push( tr.attr('id') );
                    }
                }
            });

            $('#opening_stock_modal').on('hidden.bs.modal', function(e) {
                product_table.ajax.reload();
            });

            $('table#product_table tbody').on('click', 'a.delete-product', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    product_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#delete-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_rows').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('form#mass_delete_form').submit();
                        }
                    });
                } else{
                    $('input#selected_rows').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            });

            $(document).on('click', '#deactivate-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_products').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            var form = $('form#mass_deactivate_form')

                            var data = form.serialize();
                                $.ajax({
                                    method: form.attr('method'),
                                    url: form.attr('action'),
                                    dataType: 'json',
                                    data: data,
                                    success: function(result) {
                                        if (result.success == true) {
                                            toastr.success(result.msg);
                                            product_table.ajax.reload();
                                            form
                                            .find('#selected_products')
                                            .val('');
                                        } else {
                                            toastr.error(result.msg);
                                        }
                                    },
                                });
                        }
                    });
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            })

            $(document).on('click', '#edit-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_products_for_edit').val(selected_rows);
                    $('form#bulk_edit_form').submit();
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            })

            $('table#product_table tbody').on('click', 'a.activate-product', function(e){
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change', '#product_list_filter_product_id,#product_list_filter_semi_finished,#product_list_filter_type, #product_list_filter_category_id,#product_list_filter_sub_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id', 
                function() {
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }

                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
                    
                    if($('#product_list_filter_product_id').val() !== '' && $('#product_list_filter_product_id').val() !== undefined){
                      $('.product').text($('#product_list_filter_product_id :selected').text());
                    }else{
                      $('.product').text('All');
                    }
                    if($('#product_list_filter_category_id').val() !== '' && $('#product_list_filter_category_id').val() !== undefined){
                      $('.category').text($('#product_list_filter_category_id :selected').text());
                    }else{
                      $('.category').text('All');
                    }
                    if($('#product_list_filter_sub_category_id').val() !== '' && $('#product_list_filter_sub_category_id').val() !== undefined){
                      $('.sub_category').text($('#product_list_filter_sub_category_id :selected').text());
                    }else{
                      $('.sub_category').text('All');
                    }
            });

            $(document).on('ifChanged', '#not_for_selling, #woocommerce_enabled', function(){
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }

                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
            });

            $('#product_location').select2({dropdownParent: $('#product_location').closest('.modal')});

            @if($is_woocommerce)
                $(document).on('click', '.toggle_woocomerce_sync', function(e){
                    e.preventDefault();
                    var selected_rows = getSelectedRows();
                    if(selected_rows.length > 0){
                        $('#woocommerce_sync_modal').modal('show');
                        $("input#woocommerce_products_sync").val(selected_rows);
                    } else{
                        $('input#selected_products').val('');
                        swal('@lang("lang_v1.no_row_selected")');
                    }    
                });

                $(document).on('submit', 'form#toggle_woocommerce_sync_form', function(e){
                    e.preventDefault();
                    var url = $('form#toggle_woocommerce_sync_form').attr('action');
                    var method = $('form#toggle_woocommerce_sync_form').attr('method');
                    var data = $('form#toggle_woocommerce_sync_form').serialize();
                    var ladda = Ladda.create(document.querySelector('.ladda-button'));
                    ladda.start();
                    $.ajax({
                        method: method,
                        dataType: "json",
                        url: url,
                        data:data,
                        success: function(result){
                            ladda.stop();
                            if (result.success) {
                                $("input#woocommerce_products_sync").val('');
                                $('#woocommerce_sync_modal').modal('hide');
                                toastr.success(result.msg);
                                product_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            @endif
        });
        
        $('.category_id, .sub_category_id').change(function(){
          var cat = $('#product_list_filter_category_id').val();
          var sub_cat = $('#product_list_filter_sub_category_id').val();
          $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                console.log(result);
              if (result) {
                $('#product_list_filter_sub_category_id').html(result);
              }
            },
          });
          $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
              if (result) {
                $('#product_list_filter_product_id').html(result);
              }
            },
          });
        });

        $(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal, #view_product_modal', 
            function(){
                var div = $(this).find('#view_product_stock_details');
            if (div.length) {
                $.ajax({
                    url: "{{action([\App\Http\Controllers\ReportController::class, 'getStockReport'])}}"  + '?for=view_product&product_id=' + div.data('product_id'),
                    dataType: 'html',
                    success: function(result) {
                        div.html(result);
                        __currency_convert_recursively(div);
                    },
                });
            }
            __currency_convert_recursively($(this));
        });
        var data_table_initailized = false;
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') == '#product_stock_report') {
                if (!data_table_initailized) {
                    //Stock report table
                    var stock_report_cols = [
                        { data: 'action', name: 'action', searchable: false, orderable: false },
                        { data: 'sku', name: 'variations.sub_sku' },
                        { data: 'product', name: 'p.name' },
                        { data: 'variation', name: 'variation' },
                        { data: 'category_name', name: 'c.name' },
                        { data: 'location_name', name: 'l.name' },
                        { data: 'unit_price', name: 'variations.sell_price_inc_tax' },
                        { data: 'stock', name: 'stock', searchable: false },
                    ];
                    if ($('th.stock_price').length) {
                        stock_report_cols.push({ data: 'stock_price', name: 'stock_price', searchable: false });
                        stock_report_cols.push({ data: 'stock_value_by_sale_price', name: 'stock_value_by_sale_price', searchable: false, orderable: false });
                        stock_report_cols.push({ data: 'potential_profit', name: 'potential_profit', searchable: false, orderable: false });
                    }

                    stock_report_cols.push({ data: 'total_sold', name: 'total_sold', searchable: false });
                    stock_report_cols.push({ data: 'total_transfered', name: 'total_transfered', searchable: false });
                    stock_report_cols.push({ data: 'total_adjusted', name: 'total_adjusted', searchable: false });
                    stock_report_cols.push({ data: 'product_custom_field1', name: 'p.product_custom_field1'});
                    stock_report_cols.push({ data: 'product_custom_field2', name: 'p.product_custom_field2'});
                    stock_report_cols.push({ data: 'product_custom_field3', name: 'p.product_custom_field3'});
                    stock_report_cols.push({ data: 'product_custom_field4', name: 'p.product_custom_field4'});

                    if ($('th.current_stock_mfg').length) {
                        stock_report_cols.push({ data: 'total_mfg_stock', name: 'total_mfg_stock', searchable: false });
                    }
                    stock_report_table = $('#stock_report_table').DataTable({
                        order: [[1, 'asc']],
                        processing: true,
                        serverSide: true,
                        scrollY: "75vh",
                        scrollX:        true,
                        scrollCollapse: true,
                        ajax: {
                            url: '/reports/stock-report',
                            data: function(d) {
                                d.location_id = $('#location_id').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.type = $('#product_list_filter_type').val();
                                d.active_state = $('#active_state').val();
                                d.not_for_selling = $('#not_for_selling').is(':checked');
                                if ($('#repair_model_id').length == 1) {
                                    d.repair_model_id = $('#repair_model_id').val();
                                }
                            }
                        },
                        columns: stock_report_cols,
                        fnDrawCallback: function(oSettings) {
                            __currency_convert_recursively($('#stock_report_table'));
                        },
                        "footerCallback": function ( row, data, start, end, display ) {
                            var footer_total_stock = 0;
                            var footer_total_sold = 0;
                            var footer_total_transfered = 0;
                            var total_adjusted = 0;
                            var total_stock_price = 0;
                            var footer_stock_value_by_sale_price = 0;
                            var total_potential_profit = 0;
                            var footer_total_mfg_stock = 0;
                            for (var r in data){
                                footer_total_stock += $(data[r].stock).data('orig-value') ? 
                                parseFloat($(data[r].stock).data('orig-value')) : 0;

                                footer_total_sold += $(data[r].total_sold).data('orig-value') ? 
                                parseFloat($(data[r].total_sold).data('orig-value')) : 0;

                                footer_total_transfered += $(data[r].total_transfered).data('orig-value') ? 
                                parseFloat($(data[r].total_transfered).data('orig-value')) : 0;

                                total_adjusted += $(data[r].total_adjusted).data('orig-value') ? 
                                parseFloat($(data[r].total_adjusted).data('orig-value')) : 0;

                                total_stock_price += $(data[r].stock_price).data('orig-value') ? 
                                parseFloat($(data[r].stock_price).data('orig-value')) : 0;

                                footer_stock_value_by_sale_price += $(data[r].stock_value_by_sale_price).data('orig-value') ? 
                                parseFloat($(data[r].stock_value_by_sale_price).data('orig-value')) : 0;

                                total_potential_profit += $(data[r].potential_profit).data('orig-value') ? 
                                parseFloat($(data[r].potential_profit).data('orig-value')) : 0;

                                footer_total_mfg_stock += $(data[r].total_mfg_stock).data('orig-value') ? 
                                parseFloat($(data[r].total_mfg_stock).data('orig-value')) : 0;
                            }

                            $('.footer_total_stock').html(__currency_trans_from_en(footer_total_stock, false));
                            $('.footer_total_stock_price').html(__currency_trans_from_en(total_stock_price));
                            $('.footer_total_sold').html(__currency_trans_from_en(footer_total_sold, false));
                            $('.footer_total_transfered').html(__currency_trans_from_en(footer_total_transfered, false));
                            $('.footer_total_adjusted').html(__currency_trans_from_en(total_adjusted, false));
                            $('.footer_stock_value_by_sale_price').html(__currency_trans_from_en(footer_stock_value_by_sale_price));
                            $('.footer_potential_profit').html(__currency_trans_from_en(total_potential_profit));
                            if ($('th.current_stock_mfg').length) {
                                $('.footer_total_mfg_stock').html(__currency_trans_from_en(footer_total_mfg_stock, false));
                            }
                        },
                                    });
                    data_table_initailized = true;
                } else {
                    stock_report_table.ajax.reload();
                }
            } else {
                product_table.ajax.reload();
            }
        });

        $(document).on('click', '.update_product_location', function(e){
            e.preventDefault();
            var selected_rows = getSelectedRows();
            
            if(selected_rows.length > 0){
                $('input#selected_products').val(selected_rows);
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if(type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if(type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.modal('show');
                modal.find('#product_location').select2({ dropdownParent: modal });
                modal.find('#product_location').val('').change();
                modal.find('#update_type').val(type);
                modal.find('#products_to_update_location').val(selected_rows);
            } else{
                $('input#selected_products').val('');
                swal('@lang("lang_v1.no_row_selected")');
            }    
        });

    $(document).on('submit', 'form#edit_product_location_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success == true) {
                    $('div#edit_product_location_modal').modal('hide');
                    toastr.success(result.msg);
                    product_table.ajax.reload();
                    $('form#edit_product_location_form')
                    .find('button[type="submit"]')
                    .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    </script>
@endsection