@extends('layouts.app')
@section('title', __('petro::lang.settlement'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
$meeter_precision = 3;
@endphp

s

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.petro')</a></li>
                    <li><span>@lang( 'petro::lang.settlement')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<style>
    body.modal-open {
        height: 100vh;pum
        overflow-y: hidden;
    }
    
    .col-md-1-7 {
        flex: 0 0 14.2857% !important;
        max-width: 14.2857% !important;
        padding: 6px 12px !important;
    }
    
    .select2-selection{
        line-height: 21px !important
    }
</style>
<!-- Main content -->
<section class="content main-content-inner">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row d-flex">
                <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
                        {!! Form::text('settlement_no', !empty($active_settlement) ? $active_settlement->settlement_no :
                        $settlement_no, ['class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, !empty($active_settlement) ?
                        $active_settlement->location_id : (!empty($default_location) ? $default_location : null), ['class'
                        => 'form-control select2', 'id' => 'location_id',
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-1-7">
                    <div class="form-group">                                        
                        {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                        {!! Form::select('pump_operator_id', $pump_operators, !empty($active_settlement) ?
                        $active_settlement->pump_operator_id : null, ['class' => 'form-control select2', 'id' =>
                        'pump_operator_id', 'disabled' => !empty($select_pump_operator_in_settlement) ? false : true,
                        'placeholder' => __('petro::lang.please_select')]); !!}
                    </div>
                </div>
    
                <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                        {!! Form::text('transaction_date', null, ['class' =>
                        'form-control transaction_date', 'required',
                        'placeholder' => __(
                        'petro::lang.transaction_date' ) ]); !!}
                    </div>
                </div>
    
                <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('work_shift', __('petro::lang.work_shift').':') !!}
                        {!! Form::select('work_shift[]', $wrok_shifts, !empty($active_settlement) ?
                        $active_settlement->work_shift : [], ['class' => 'form-control select2', 'id' => 'work_shift',
                        'multiple']); !!}
                    </div>
                </div>
                
                 <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('work_shift', __('petro::lang.shift_number').':') !!}
                        <select id="shift_number", class="form-control select2" multiple></select>
                    </div>
                </div>
                
    
                <div class="col-md-1-7">
                    <div class="form-group">
                        {!! Form::label('note', __('petro::lang.note') . ':') !!}
                        {!! Form::text('note', !empty($active_settlement) ? $active_settlement->note : null, ['class' =>
                        'form-control note',
                        'placeholder' => __(
                        'petro::lang.note' ) ]); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary below_box', 'id' => 'below_box'])
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#meter_sale_tab" class="meter_sale_tab" data-toggle="tab">
                            <i class="fa fa-tachometer"></i> <strong>@lang('petro::lang.meter_sale')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#other_sale_tab" class="other_sale_tab" style="" data-toggle="tab">
                            <i class="fa fa-balance-scale"></i> <strong>
                                @lang('petro::lang.other_sale') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#other_income_tab" class="other_income_tab" style="" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>
                                @lang('petro::lang.other_income') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#customer_payment_tab" class="customer_payment_tab" style="" data-toggle="tab">
                            <i class="fa fa-money"></i> <strong>
                                @lang('petro::lang.customer_payment') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#payment_tab" class="payment_tab" style="" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>
                                @lang('petro::lang.payment') </strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="meter_sale_tab">
                        @include('petro::settlement.partials.meter_sale')
                    </div>

                    <div class="tab-pane" id="other_sale_tab">
                        @include('petro::settlement.partials.other_sale')
                        <input type="hidden" value="{{$check_qty}}" id="allowoverselling">
                    </div>

                    <div class="tab-pane" id="other_income_tab">
                        @include('petro::settlement.partials.other_income')
                    </div>

                    <div class="tab-pane" id="customer_payment_tab">
                        @include('petro::settlement.partials.customer_payment')
                    </div>

                    <div class="tab-pane" id="payment_tab">
                        @include('petro::settlement.partials.payment')
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade add_payment" role="dialog" aria-labelledby="gridSystemModalLabel" style="overflow-y: auto;">
    </div>
    <div class="modal fade preview_settlement" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print"></div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{url('Modules/Petro/Resources/assets/js/app.js?v=20')}}"></script>
<script src="{{url('Modules/Petro/Resources/assets/js/payment.js?v=20')}}"></script>
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


@if(!empty($active_settlement))
    $('#note, #work_shift, #transaction_date, #pump_operator_id, #location_id').change(function() {
        console.log("jj")
        
        $('#shift_number').html('');
        $('.shift_number').html('');
        
        $.ajax({
            method: 'put',
            url: "{{action('\Modules\Petro\Http\Controllers\SettlementController@update', $active_settlement->id)}}",
            data: {
                note: $('#note').val(),
                work_shift: $('#work_shift').val(),
                transaction_date: $('#transaction_date').val(),
                pump_operator_id: $('#pump_operator_id').val(),
                location_id: $('#location_id').val()
            },
            success: function(result) {
                console.log("result =>", result);
                if (result.success == 1) {
                    toastr.success(result.msg);
                    $("#shift_number").html(result.optionHtml);
                    $("#work_shift").html(result.optionHtml);
                    
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    
    $(document).on('change', '#shift_number', function() {
        var shift_numbers = $(this).val(); // assuming this is an array
        var string = '';
        shift_numbers.forEach(function(item, index) {
            if(index !== shift_numbers.length - 1) {
                string += item + ",";
            } else {
                string += item;
            }
        });
        
        $('.shift_number').html(string);

        if(shift_numbers.length >= 0){
            
            
            //Other Sales
             $('#outside_other_sale_table').show();
             $('#other_sale_table').hide();
            if ( $.fn.DataTable.isDataTable('#pump_operator_other_sale_table') ) {
                // DataTable is already initialized
                $('#pump_operator_other_sale_table').DataTable().ajax.reload(null, false);  // `false` prevents page reset
            } else {
                pump_operator_other_sale_table = $('#pump_operator_other_sale_table').DataTable({
                    processing: true,
                    serverSide: true,
                    aaSorting: [[0, 'desc']],
                    ajax: {
                        url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@otherSalesList')}}",
                        data: function(d) {
                            console.log($('#shift_number').val());
                            d.shift_ids = $('#shift_number').val();
                        },
                    },
                    columnDefs: [ {
                        "targets": 0,
                        "orderable": false,
                        "searchable": false
                    }],
                    columns: [
                        { 
                            data: 'product_sku',
                            name: 'products.sku'
                        },
                        { 
                            data: 'product_name',
                            name: 'products.name'
                        },
                        { 
                            data: 'balance_stock',
                            name: 'balance_stock',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        },
                        { 
                            data: 'price',
                            name: 'price',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        },
                        { 
                            data: 'quantity',
                            name: 'quantity',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        },
                        { 
                            data: 'discount_type',
                            name: 'discount_type'
                        },
                        { 
                            data: 'discount',
                            name: 'discount',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        },
                        { 
                            data: 'sub_total',
                            name: 'sub_total',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        },
                        { 
                            data: 'sub_total',
                            name: 'sub_total',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        }
                    ],
                    fnDrawCallback: function(oSettings) {
                        var footer_list_other_sales_amount = sum_table_col($('#pump_operator_other_sale_table'), 'sub_total');
                        $('#footer_list_other_sales_amount').val(footer_list_other_sales_amount);
                        $('#footer_list_other_sales_amount').text(footer_list_other_sales_amount);
                    
                        __currency_convert_recursively($('#pump_operator_other_sale_table'));
                    },
                });
            }
            
            
            
            //Meter Sales
             $('#outside_meter_sale_table').show();
             $('#meter_sale_table').hide();
            if ( $.fn.DataTable.isDataTable('#pump_operator_meter_sale_table') ) {
                // DataTable is already initialized
                $('#pump_operator_meter_sale_table').DataTable().ajax.reload(null, false);  // `false` prevents page reset
            } else {
                pump_operator_meter_sale_table = $('#pump_operator_meter_sale_table').DataTable({
                    processing: true,
                    serverSide: true,
                    aaSorting: [[0, 'desc']],
                    ajax: {
                        url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@meterSalesList')}}",
                        data: function(d) {
                            console.log($('#shift_number').val());
                            d.shift_ids = $('#shift_number').val();
                        },
                    },
                    columnDefs: [ {
                        "targets": 0,
                        "orderable": false,
                        "searchable": false
                    }],
                    columns: [
                        { 
                            data: 'product_sku',
                            name: 'products.sku'
                        },
                        { 
                            data: 'product_name',
                            name: 'products.name'
                        },
                        { 
                            data: 'pump_id', // Assuming pump_no corresponds to the pump column
                            name: 'pump_id'
                        },
                        { 
                            data: 'starting_meter',
                            name: 'starting_meter'
                        },
                        { 
                            data: 'closing_meter',
                            name: 'closing_meter'
                        },
                        { 
                            data: 'price',
                            name: 'price',
                        },
                        { 
                            data: 'quantity', // This should be the sold quantity
                            name: 'sold_qty' // Adjust as necessary if you have a specific name in your data
                        },
                        { 
                            data: 'discount_type',
                            name: 'discount_type'
                        },
                        { 
                            data: 'discount',
                            name: 'discount_value'
                        },
                        { 
                            data: 'testing_qty',
                            name: 'testing_qty'
                        },
                        { 
                            data: null, // Set data to null because we will calculate the value
                            name: 'testing_qty_quantity', // You can name this whatever you like for sorting/filtering
                            render: function(data, type, row) {
                                return row.testing_qty + row.quantity; // Perform the addition and return the result
                            }
                        },
                        { 
                            data: 'sub_total',
                            name: 'sub_total',
                        },
                        { 
                            data: 'discount_amount',
                            name: 'discount_amount',
                        },
                        {
                            data: 'action',
                            name: 'action', // You can also use a specific name if you want to refer to a database column
                            render: function(data, type, row) {
                                var editButton = '<button class="btn btn-xs btn-primary get_meter_sale_from" data-type="edit" data-href="/petro/settlement/get-meter-sale-form/' + row.id + '"><i class="fa fa-edit"></i></button>';
                                var deleteButton = '';
                                
                                if (row.later_settlements < 1 || !row.transaction_id || row.bulk_tank == 1) {
                                    deleteButton = '<button class="btn btn-xs btn-danger delete_meter_sale" data-href="/petro/settlement/delete-meter-sale/' + row.id + '"><i class="fa fa-times"></i></button>';
                                }
                                
                                return editButton + ' ' + deleteButton; // Combine both buttons into a single string
                            }
                        }

                    ]

                    // fnDrawCallback: function(oSettings) {
                    //     var footer_list_other_meter_amount = sum_table_col($('#pump_operator_meter_sale_table'), 'sub_total');
                    //     $('#footer_list_meter_sales_amount').val(footer_list_meter_sales_amount);
                    //     $('#footer_list_meter_sales_amount').text(footer_list_meter_sales_amount);
                    
                    //     __currency_convert_recursively($('#pump_operator_meter_sale_table'));
                    // },
                });
            }
        }

        // Now 'string' contains the comma-separated values of shift_numbers
        console.log(string); // or you can set this value to some element if needed
    });
    
@endif
    
    $('.transaction_date').datepicker("setDate", @if(!empty($active_settlement)) "{{\Carbon::parse($active_settlement->transaction_date)->format('m/d/Y') }}" @else new Date() @endif);
   
    $('#customer_payment_cheque_date').datepicker("setDate", new Date());
    $('#location_id').select2();
    $('#shif_time_in').datetimepicker({
        format: 'LT'
    });
    $('#shif_time_out').datetimepicker({
        format: 'LT'
    });
    $('#item').select2();
    $('#store_id').select2();
    $('#bulk_tank').select2();


$('#add_payment').click(function(){
    /**
    * @ChangedBy Afes
    * @Date 25-05-2021
    * @Date 02-06-2021
    * @Task 12700
    * @Task 127004
    */
    url = $(this).data('href')+'&operator_id='+$('#pump_operator_id').val();
    $('.add_payment').load(url,function(){
        $('.add_payment').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
$(document).on('click', '#payment_review_btn',function(){
    url = $(this).data('href');
    $('.preview_settlement').load(url,function(){
        $('.preview_settlement').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
$(document).on('click', '#product_preview_btn',function(){
    url = $(this).data('href');
    $('.preview_settlement').load(url,function(){
        $('.preview_settlement').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
$(document).ready(function () {
    $('#show_bulk_tank').on('ifChecked', function(event){
        $('.store_field').addClass('hide');
        $('.bulk_tank_field').removeClass('hide');
    });

    $('#show_bulk_tank').on('ifUnchecked', function(event){
        $('.store_field').removeClass('hide');
        $('.bulk_tank_field').addClass('hide');
    });
});


$('#bulk_tank').change(function(){
    tank_id = $(this).val();
    
    $.ajax({
        method: 'get',
        url: "{{action('\Modules\Petro\Http\Controllers\FuelTankController@getTankProduct')}}/"+tank_id,
        data: {  },
        success: function(result) {
            html = `<option>Please Select</option><option value=""${result.id}>${result.name}</option>`;
            $('#item').empty().append(html);
        },
    });
})
$('#card_customer_id').select2();
$('#work_shift').select2();
$('#customer_payment_customer_id').select2();
$('#settlement_print').css('visibility', 'hidden');




</script>


<script>
    $(document).on('click', '#save_edit_price_other_income_btn', function(){
        var edit_price = $('#other_income_edit_price').val();

        $('#other_income_price').val(edit_price);
        $('#other_income_edit_price').val('0');
        $('#edit_price_other_income').modal('hide');
    });

</script>
@endsection