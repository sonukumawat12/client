@extends('layouts.app')

@section('title', __('petro::lang.tank_management'))



@section('content')

@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp

<section class="content-header main-content-inner">

    <div class="row">

        <div class="col-md-12 dip_tab">

            <div class="settlement_tabs">

                <ul class="nav nav-tabs">

                    <li class="active" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#fuel_tanks" class="" data-toggle="tab">

                            <i class="fa fa-superpowers"></i> <strong>@lang('petro::lang.fuel_tanks')</strong>

                        </a>

                    </li>

                    <li class="" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#tank_transactions_details" class="" data-toggle="tab">

                            <i class="fa fa-info-circle"></i>

                            <strong>@lang('petro::lang.tank_transactions_details')</strong>

                        </a>

                    </li>

                    <li class="" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#tank_transactions_summary" class="" data-toggle="tab">

                            <i class="fa fa-exchange"></i>

                            <strong>@lang('petro::lang.tank_transactions_summary')</strong>

                        </a>

                    </li>
                    
                    @if(!empty($pacakge_details['edit_settlement_date']))
                    
                     <li class="" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#edit_settlement_date" class="" data-toggle="tab">

                            <i class="fa fa-exchange"></i>

                            <strong>@lang('superadmin::lang.edit_settlement_date')</strong>

                        </a>

                    </li>
                    
                    @endif

                </ul>

            </div>

        </div>

    </div>

    <div class="tab-content">

        <div class="tab-pane active" id="fuel_tanks">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::fuel_tanks.fuel_tanks')

        </div>

        <div class="tab-pane" id="tank_transactions_details">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::tanks_transaction_details.tank_transactions_details')

        </div>

        <div class="tab-pane" id="tank_transactions_summary">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::tanks_transaction_details.tank_transactions_summary')

        </div>
        
        @if(!empty($pacakge_details['edit_settlement_date']))
        
        <div class="tab-pane" id="edit_settlement_date">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::edit_settlement_date.index')

        </div>
        
        @endif

    </div>



    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){

    var columns = [

            { data: 'transaction_date', name: 'transaction_date' },
            
            { data: 'location_name', name: 'business_locations.name' },

            { data: 'fuel_tank_number', name: 'fuel_tank_number' },

            { data: 'product_name', name: 'products.name' },

            { data: 'storage_volume', name: 'storage_volume' },

            { data: 'new_balance', name: 'new_balance' },

            { data: 'bulk_tank', name: 'bulk_tank' },

            { data: 'action', searchable: false, orderable: false },

        ];

  

    fuel_tanks_table = $('#fuel_tanks_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],
        
        ajax: {

                url: "{{action('\Modules\Petro\Http\Controllers\FuelTankController@index')}}",

                data: function(d) {

                    d.fuel_tank_number =  $('#fueltanks_tank_number').val();
                    d.location_id =  $('#fueltanks_location_id').val();

                },

            },

        columnDefs: [ {

            "targets": 7,

            "orderable": false,

            "searchable": false

        },
        {

            "targets": 1,

            "visible": false

        } 
        ],

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    $('#fueltanks_tank_number').change(function(){

        fuel_tanks_table.ajax.reload();

    });



    $(document).on('click', 'a.delete_tank_button', function(e) {

		var page_details = $(this).closest('div.page_details')

		e.preventDefault();

        swal({

            title: LANG.sure,

            icon: 'warning',

            buttons: true,

            dangerMode: true,

        }).then(willDelete => {

            if (willDelete) {

                var href = $(this).attr('href');

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

                        fuel_tanks_table.ajax.reload();

                    },

                });

            }

        });

    });

});

</script>



<script type="text/javascript">

    if ($('#transaction_details_date_range').length == 1) {

        $('#transaction_details_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#transaction_details_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );
            
            tank_transaction_details_table.ajax.reload();

        });

        $('#transaction_details_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#transaction_details_date_range').val('');

        });

        $('#transaction_details_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#transaction_details_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }



  



    $(document).ready( function(){

        var tank_transaction_details_columns = [

            { data: 'created_at', name: 'created_at', searchable: false, },
            
            { data: 'location_name', name: 'business_locations.name'},

            { data: 'transaction_date', name: 'transaction_date'},

            { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number'},

            { data: 'product_name', name: 'products.name'},

            { data: 'ref_no', name: 'ref_no'},

            { data: 'purchase_order_no', name: 'purchase_order_no'},

            { data: 'opening_balance_qty', name: 'opening_balance_qty'},

            { data: 'purchase_qty', name: 'tank_purchase_lines.quantity', searchable: 'false'},

            { data: 'sold_qty', name: 'tank_sell_lines.quantity', searchable: 'false'},

            { data: 'balance_qty', name: 'balance_qty', searchable: false, sortable: false},

        ];

    

        tank_transaction_details_table = $('#tank_transaction_details_table').DataTable({

            processing: true,

            serverSide: true,

            pageLength: 25, 

            deferRender: true,

            order: [[0, 'desc']],

            ajax: {

                url: '/petro/tanks-transaction-details',

                data: function(d) {

                    d.start_date = $('input#transaction_details_date_range')

                        .data('daterangepicker')

                        .startDate.format('YYYY-MM-DD');

                    d.end_date = $('input#transaction_details_date_range')

                        .data('daterangepicker')

                        .endDate.format('YYYY-MM-DD');

                    d.location_id =  $('#transaction_details_location_id').val();

                    d.fuel_tank_number =  $('#transaction_details_tank_number').val();

                    d.product_id =  $('#transaction_details_product_id').val();

                    d.settlement_id =  $('#transaction_details_settlement_id').val();

                    d.purchase_no =  $('#transaction_details_purhcase_no').val();

                },

            },
            
            columnDefs: [ 
                {
        
                    "targets": 1,
        
                    "visible": false
        
                } 
            ],

            columns: tank_transaction_details_columns,

            

        });
        
        tank_transaction_details_table.column(1).visible(false);

    });



    $('#transaction_details_date_range, #transaction_details_location_id, #transaction_details_tank_number, #transaction_details_product_id, #transaction_details_settlement_id, #transaction_details_purhcase_no').change(function(){

        tank_transaction_details_table.ajax.reload();

    });

</script>

<script type="text/javascript">

    if ($('#transaction_summary_date_range').length == 1) {

        $('#transaction_summary_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#transaction_summary_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );
            
            tank_transaction_summary_table.ajax.reload();

        });

        $('#transaction_summary_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#transaction_summary_date_range').val('');

        });

        $('#transaction_summary_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#transaction_summary_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }



    $('#transaction_summary_date_range, #transaction_summary_location_id, #transaction_summary_tank_number, #transaction_summary_product_id').change(function(){

        tank_transaction_summary_table.ajax.reload();

    });





    $(document).ready( function(){

        var tank_transaction_summary_columns = [

                { data: 'transaction_date', name: 'transaction_date' },
                
                { data: 'location_name', name: 'business_locations.name'},

                { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number' },

                { data: 'product_name', name: 'products.name' },

                { data: 'starting_qty', name: 'starting_qty', searchable: false, sortable: false},

                { data: 'purchase_qty', name: 'purchase_qty', searchable: false, sortable: false},

                { data: 'sold_qty', name: 'sold_qty', searchable: false, sortable: false},

                { data: 'balance_qty', name: 'balance_qty', searchable: false, sortable: false},

            ];

    

        tank_transaction_summary_table = $('#tank_transaction_summary_table').DataTable({

             processing: true,

            serverSide: true,

            pageLength: 25, 

            deferRender: true,

            order: [[0, 'asc']],

            ajax: {

                url: '/petro/tanks-transaction-summary',

                data: function(d) {

                    d.start_date = $('input#transaction_summary_date_range')

                        .data('daterangepicker')

                        .startDate.format('YYYY-MM-DD');

                    d.end_date = $('input#transaction_summary_date_range')

                        .data('daterangepicker')

                        .endDate.format('YYYY-MM-DD');

                    d.location_id =  $('#transaction_summary_location_id').val();

                    d.fuel_tank_number =  $('#transaction_summary_tank_number').val();

                    d.product_id =  $('#transaction_summary_product_id').val();

                },

            },
            
            columnDefs: [ 
                {
        
                    "targets": 1,
        
                    "visible": false
        
                } 
            ],

            columns: tank_transaction_summary_columns,

            rowCallback: function( row, data, index ) {

                // if (data['balance_qty'] == 0) {

                //     $(row).hide();

                // }

            },

        });
        
        tank_transaction_summary_table.column(1).visible(false);



        $('.add_fuel_tank').click(function(){

            $('.fuel_tank_modal').modal({

                backdrop : 'static',

                keyboard: false

            })

        })

    });

</script>

<script type="text/javascript">

    if ($('#edit_settlement_date_range').length == 1) {

        $('#edit_settlement_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#edit_settlement_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );
            
            edit_settlement_date_table.ajax.reload();

        });

        $('#edit_settlement_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#edit_settlement_date_range').val('');

        });

        $('#edit_settlement_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#edit_settlement_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }



    $('#edit_settlement_date_range, #edit_settlement_id').change(function(){

        edit_settlement_date_table.ajax.reload();

    });





    $(document).ready( function(){

        var edit_settlement_date_columns = [

                { data: 'transaction_date', name: 'transactions.transaction_date' },

                { data: 'settlement_no', name: 'settlements.settlement_no' },

                { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number'},

                { data: 'product_name', name: 'products.name' },

                { data: 'created_at', name: 'created_at'},

                { data: 'action', name: 'action'},

            ];

    

        edit_settlement_date_table = $('#edit_settlement_date_table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: '/petro/settlement/get-meter-sales',

                data: function(d) {

                    d.start_date = $('input#edit_settlement_date_range')

                        .data('daterangepicker')

                        .startDate.format('YYYY-MM-DD');

                    d.end_date = $('input#edit_settlement_date_range')

                        .data('daterangepicker')

                        .endDate.format('YYYY-MM-DD');

                    d.settlement_no =  $('#edit_settlement_id').val();

                },

            },

            columns: edit_settlement_date_columns,

            rowCallback: function( row, data, index ) {

                // if (data['balance_qty'] == 0) {

                //     $(row).hide();

                // }

            },

        });



    });

</script>

@endsection