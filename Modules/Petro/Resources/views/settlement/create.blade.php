
@extends('layouts.app')
@section('title', __('petro::lang.settlement'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
$meeter_precision = 3;
@endphp

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
        height: 100vh;
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
            <div class="col-md-12">
                <div class="col-md-1">
                    <div class="form-group">
                        {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
                        {!! Form::text('settlement_no', !empty($active_settlement) ? $active_settlement->settlement_no :
                        $settlement_no, ['class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, !empty($active_settlement) ?
                        $active_settlement->location_id : (!empty($default_location) ? $default_location : null), ['class'
                        => 'form-control select2', 'id' => 'location_id',
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">                                        
                        {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                        {!! Form::select('pump_operator_id', $pump_operators, !empty($active_settlement) ?
                        $active_settlement->pump_operator_id : null, ['class' => 'form-control select2', 'id' =>
                        'pump_operator_id', 'disabled' => !empty($select_pump_operator_in_settlement) ? false : true,
                        'placeholder' => __('petro::lang.please_select')]); !!}
                    </div>
                </div>
    
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                        {!! Form::text('transaction_date', null, ['class' =>
                        'form-control transaction_date', 'required',
                        'placeholder' => __(
                        'petro::lang.transaction_date' ) ]); !!}
                    </div>
                </div>
    
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('work_shift', __('petro::lang.work_shift').':') !!}
                        {!! Form::select('work_shift[]', $wrok_shifts, !empty($active_settlement) ?
                        $active_settlement->work_shift : [], ['class' => 'form-control select2', 'id' => 'work_shift',
                        'multiple']); !!}
                    </div>
                </div>
                
                 <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('shift_number', __('petro::lang.shift_number').':') !!}
                        <select id="shift_number", class="form-control select2" multiple></select>
                    </div>
                </div>
                
    
                <div class="col-md-1">
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
                        <a href="#meter_sale_tab" class="meter_sale_tab" data-toggle="tab" onclick="return toggle_check_operator_shift_status(event);">
                            <i class="fa fa-tachometer"></i> <strong>@lang('petro::lang.meter_sale')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#other_sale_tab" class="other_sale_tab" style="" data-toggle="tab" onclick="return toggle_check_operator_shift_status(event);">
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
<script src="{{url('Modules/Petro/Resources/assets/js/app.js?v=30')}}"></script>
<script src="{{url('Modules/Petro/Resources/assets/js/payment.js?v=22')}}"></script> 

<!--<script src="https://220.kiria.online/Modules/Petro/Resources/assets/js/app.js?v=37"></script>-->
<!--<script src="https://220.kiria.online/Modules/Petro/Resources/assets/js/payment.js?v=22"></script>-->

<input type="hidden" value="{{ $active_settlement->id ?? "0" }}" id="active_settlement_id">
<input type="hidden" value="yes" id="shift_closed">
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function toggle_check_operator_shift_status(event){
    if($('#shift_closed').val() != "yes"){
        // Prevent default tab switch
        event.preventDefault();

        toastr.error("Operator shift not closed.");
        // Use a timeout to delay the execution
        setTimeout(function() {
            $('#below_box').addClass('hide');
            // Remove active class from current active tab and pane
            $('.settlement_tabs .nav-tabs li.active').removeClass('active');
            $('.settlement_tabs .tab-pane.active').removeClass('active');
            $('.settlement_tabs .nav-tabs li.show').removeClass('show');
            $('.settlement_tabs .tab-pane.show').removeClass('show');
            $('#meter_sale_tab').removeClass('active');
            $('#other_sale_tab').removeClass('active');
            $('#meter_sale_tab').removeClass('show');
            $('#other_sale_tab').removeClass('show');

            // Add active class to the Other Income tab and pane
            $('.settlement_tabs .other_income_tab').closest('li').addClass('active');
            $('.settlement_tabs .other_income_tab').closest('li').addClass('show');
            $('#other_income_tab').addClass('active');
            $('#other_income_tab').addClass('show');
        }, 1000);
        
        return false; // Ensure the tab switch doesn't occur
    }
    $('#below_box').removeClass('hide');
    return true; // Allow the tab switch if shift is closed
}

    function updatePumpDropdown(pump_nos) {
        const select = $('#pump_no');
        select.empty();
        select.append(`<option value="">Please Select</option>`);

        $.each(pump_nos, function (id, name) {
            select.append(`<option value="${id}">${name}</option>`);
        });

        select.trigger('change.select2');
    }

// Bind the function to the click event of the Meter Sale and Other Sale tabs
$('.settlement_tabs .nav-tabs a.meter_sale_tab, .settlement_tabs .nav-tabs a.other_sale_tab').on('click', function(event) {
    // Only call toggle_check_operator_shift_status if shift_closed is "no"
    if ($('#shift_closed').val() != "yes") {
        return toggle_check_operator_shift_status(event);
    }
});
    function checkPreviousUnsettled(shift_id) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: "{{ url('petro/settlement/check_prev_settlement') }}",
                type: "get",
                data: {
                    shift_id: shift_id,
                },
                success: function(res) {
                    if (res.status) {
                        resolve(true);   // ✅ settlement clear
                    } else {
                        toastr.error(res.msg);
                        resolve(false);  // ❌ unsettled
                    }
                },
                error: function(xhr) {
                    toastr.error("Something went wrong.");
                    reject(false); // ❌ network/server error
                }
            });
        });
    }



@if(!empty($active_settlement))

    //alert('IF Condition');

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
                    // $("#work_shift").html(result.optionHtml);
                    $('#shift_closed').val("yes");
                    $('#below_box').toggleClass('hide', result.optionHtml !== '');
                } else {
                    toastr.error(result.msg);
                    if (result.msg === "Operator shift not closed.") {
                        $('#below_box').addClass('hide');
                        // Remove active class from current active tab and pane
                        $('.settlement_tabs .nav-tabs li.active').removeClass('active');
                        $('.settlement_tabs .tab-pane.active').removeClass('active');
                        $('.settlement_tabs .nav-tabs li.show').removeClass('show');
                        $('.settlement_tabs .tab-pane.show').removeClass('show');
                        $('#meter_sale_tab').removeClass('active');
                        $('#other_sale_tab').removeClass('active');
                        $('#meter_sale_tab').removeClass('show');
                        $('#other_sale_tab').removeClass('show');

                        // Add active class to the Other Income tab and pane
                        $('.settlement_tabs .other_income_tab').closest('li').addClass('active');
                        $('.settlement_tabs .other_income_tab').closest('li').addClass('show');
                        $('#other_income_tab').addClass('active');
                        $('#other_income_tab').addClass('show');
                        $('#shift_closed').val("no");
                    }
                }
            },
        });
    });
    
    $(document).on('change', '#shift_number',async function() {

        var shift_numbers = $(this).val(); // assuming this is an array
        var shift_id = $('#shift_number').val();
        if (Array.isArray(shift_id)) {
            shift_id = shift_id[0]; // take first if multiple
        }
        if (!shift_id) return
        var prevUnselected = await checkPreviousUnsettled(shift_id)
        if (!prevUnselected) {
            return
        }

        if (shift_numbers && shift_numbers.length > 0) {
            $('#below_box').removeClass('hide'); // Show if value exists
        } else {
            $('#below_box').addClass('hide'); // Hide if empty
        }

        let shift_number_input = document.getElementById('shift_number');
        var string = '';
        shift_numbers.forEach(function(item, index) {
            let selectedOption = Array.from(shift_number_input.options).find(option => option.value === item);
            if(index !== shift_numbers.length - 1) {
                string += selectedOption.textContent + ",";
            } else {
                string += selectedOption.textContent;
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
                            data: 'qty_available',
                            name: 'qty_available',
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
                            data: 'with_discount',
                            name: 'with_discount',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        }
                    ],
                    fnDrawCallback: function(oSettings) {
                        var footer_list_other_sales_amount = sum_table_col($('#pump_operator_other_sale_table'), 'with_discount');

                        $('#footer_list_other_sales_amount').val(footer_list_other_sales_amount);
                        $('#footer_list_other_sales_amount').text(footer_list_other_sales_amount);
                    
                        __currency_convert_recursively($('#pump_operator_other_sale_table'));
                    },
                });
            }

            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@otherSalesList')}}",
                data: {
                    shift_ids: $('#shift_number').val(),
                    get_total: true
                },
                success: function(result) {
                    if (result.success == 1) {
                        updatePumpDropdown(result.pump_nos)
                        $('#shift_operator_other_sale_total').val(parseFloat(result.total));
                        $('#other_sale_total').val(0);
                        calculate_payment_tab_total();
                    } else {
                        toastr.error("Error fetching other sale total");
                    }
                },
            });
            
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
                            d.active_settlement_id = $('#active_settlement_id').val();
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
                            data: 'pump_name',
                            name: 'pump_name'
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
                            data: 'total_qty',
                            name: 'total_qty',
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

                    ],

                    fnDrawCallback: function(oSettings) {
                        var footer_list_meter_sales_amount = sum_table_col($('#pump_operator_meter_sale_table'), 'sub_total');
                        $('#footer_list_meter_sales_amount').val(footer_list_meter_sales_amount);
                        $('#footer_list_meter_sales_amount').text(footer_list_meter_sales_amount);
                    
                        __currency_convert_recursively($('#pump_operator_meter_sale_table'));
                    },
                });
            }
        }

        // Now 'string' contains the comma-separated values of shift_numbers
        //console.log(string); // or you can set this value to some element if needed
    });
@else

    //alert('Else Block');

    $('#note, #work_shift, #transaction_date, #pump_operator_id, #location_id').change(function() {
        $('#shift_number').html('');
        $('.shift_number').html('');
        
        $.ajax({
            method: 'put',
            url: "/petro/settlement/" + $('#active_settlement_id').val(),
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
                    // $("#work_shift").html(result.optionHtml);
                    $('#shift_closed').val("yes");
                    //$('#below_box').addClass('hide');
                    $('#below_box').toggleClass('hide', result.optionHtml !== '');
                } else {
                    toastr.error(result.msg);
                    if (result.msg === "Operator shift not closed.") {
                        $('#below_box').addClass('hide');
                        // Remove active class from current active tab and pane
                        $('.settlement_tabs .nav-tabs li.active').removeClass('active');
                        $('.settlement_tabs .tab-pane.active').removeClass('active');
                        $('.settlement_tabs .nav-tabs li.show').removeClass('show');
                        $('.settlement_tabs .tab-pane.show').removeClass('show');
                        $('#meter_sale_tab').removeClass('active');
                        $('#other_sale_tab').removeClass('active');
                        $('#meter_sale_tab').removeClass('show');
                        $('#other_sale_tab').removeClass('show');

                        // Add active class to the Other Income tab and pane
                        $('.settlement_tabs .other_income_tab').closest('li').addClass('active');
                        $('.settlement_tabs .other_income_tab').closest('li').addClass('show');
                        $('#other_income_tab').addClass('active');
                        $('#other_income_tab').addClass('show');
                        $('#shift_closed').val("no");
                    }
                }
            },
        });
    });
    
    $(document).on('change', '#shift_number',async function() {

        var shift_numbers = $(this).val(); // assuming this is an array
        var shift_id = $('#shift_number').val();
        if (Array.isArray(shift_id)) {
            shift_id = shift_id[0]; // take first if multiple
        }
        if (!shift_id) return

        var prevUnsettled = await checkPreviousUnsettled(shift_id)
        if (!prevUnsettled) {
            return
        }

        if (shift_numbers && shift_numbers.length > 0) {
            $('#below_box').removeClass('hide'); // Show if value exists
        } else {
            $('#below_box').addClass('hide'); // Hide if empty
}

        let shift_number_input = document.getElementById('shift_number');
        var string = '';
        shift_numbers.forEach(function(item, index) {
            let selectedOption = Array.from(shift_number_input.options).find(option => option.value === item);
            if(index !== shift_numbers.length - 1) {
                string += selectedOption.textContent + ",";
            } else {
                string += selectedOption.textContent;
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
                            data: 'qty_available',
                            name: 'qty_available',
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
                            data: 'with_discount',
                            name: 'with_discount',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-right');
                            }
                        }
                    ],
                    fnDrawCallback: function(oSettings) {
                        var footer_list_other_sales_amount = sum_table_col($('#pump_operator_other_sale_table'), 'with_discount');
                        $('#footer_list_other_sales_amount').val(footer_list_other_sales_amount);
                        $('#footer_list_other_sales_amount').text(footer_list_other_sales_amount);
                    
                        __currency_convert_recursively($('#pump_operator_other_sale_table'));
                    },
                });
            }

            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@otherSalesList')}}",
                data: {
                    shift_ids: $('#shift_number').val(),
                    get_total: true
                },
                success: function(result) {
                    if (result.success == 1) {
                        updatePumpDropdown(result.pump_nos)
                        $('#shift_operator_other_sale_total').val(parseFloat(result.total));
                        $('#other_sale_total').val(0);
                        calculate_payment_tab_total();
                    } else {
                        toastr.error("Error fetching other sale total");
                    }
                },
            });
            
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
                            d.active_settlement_id = $('#active_settlement_id').val();
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
                            data: 'pump_name',
                            name: 'pump_name'
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
                            data: 'total_qty',
                            name: 'total_qty',
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

                    ],

                    fnDrawCallback: function(oSettings) {
                        var footer_list_meter_sales_amount = sum_table_col($('#pump_operator_meter_sale_table'), 'sub_total');
                        $('#footer_list_meter_sales_amount').val(footer_list_meter_sales_amount);
                        $('#footer_list_meter_sales_amount').text(footer_list_meter_sales_amount);
                    
                        __currency_convert_recursively($('#pump_operator_meter_sale_table'));
                    },
                });
            }
        }

        // Now 'string' contains the comma-separated values of shift_numbers
        //console.log(string); // or you can set this value to some element if needed
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
    url = $(this).data('href')+'&operator_id='+$('#pump_operator_id').val()+'&shift_ids='+$('#shift_number').val();
    $('.add_payment').load(url,function(){
        $('.add_payment').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
$(document).on("click", ".cash_add_updated", function () {
    console.log('123');
    if ($("#cash_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cash_customer_id = $("#cash_customer_id").val();
    var cash_amount = $("#cash_amount").val();
    var settlement_no = $("#settlement_no").val();
    var customer_name = $("#cash_customer_id :selected").text();
    var cash_note = $("#cash_note").val();
    var is_edit = $("#is_edit").val() ?? 0;

    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cash-payment",
        data: {
        customer_id: cash_customer_id,
        amount: cash_amount,
        settlement_no: settlement_no,
        note: cash_note,
        is_edit: is_edit,
        },
        success: function (result) {
        if (!result.success) {
            toastr.error(result.msg);
        } else {
            if ($("#calculate_cash").is(":checked")) {
            $(".denoms_totals").hide();
            $(".cash_to_disable").hide();
            $("#cash_amount").prop("readonly", true);
            } else {
            $(".denoms_totals").show();
            $(".cash_to_disable").show();
            $("#cash_amount").prop("readonly", false);
            }

            console.log("here is cash add data ==>", result);
            settlement_cash_payment_id = result.settlement_cash_payment_id;
            add_payment(cash_amount);
            $("#cash_table tbody").append(
            `
                        <tr> 
                            <td>` +
                customer_name +
                `</td>
                            <td class="cash_amount">` +
                __number_f(cash_amount, false, false, __currency_precision) +
                `</td>
                            <td>` +
                cash_note +
                `</td>
                            <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-payment/` +
                settlement_cash_payment_id +
                `"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    `
            );
            $(".cash_fields").val("");
            calculateTotal("#cash_table", ".cash_amount", ".cash_total");
        }
        },
    });
});
$(document).on("click", ".excess_add_btn", function () {
    console.log('function called')
    var excess_amount_input = $("#excess_amount").val();
    var excess_note = $("#excess_note").val();
    if (excess_amount_input == "") {
        toastr.error("Please enter amount");
        return false;
    } else {
        if (excess_amount_input > 0) {
            toastr.error("Please enter the amount with a negative symbol");
            return false;
        }
    }
    var settlement_no = $("#settlement_no").val();
    var excess_amount = $("#excess_amount").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-excess-payment",
        data: {
            settlement_no: settlement_no,
            amount: excess_amount,
            note: excess_note,
            is_edit: is_edit
        },
        success: function (result) {
            console.log(result.success)
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                
                settlement_excess_payment_id = result.settlement_excess_payment_id;
                $("#excess_table tbody").append(
                    `
                    <tr> 
                        <td></td>
                        <td class="excess_amount">` +
                        __number_f(excess_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        excess_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_excess_payment" data-href="/petro/settlement/payment/delete-excess-payment/` +
                        settlement_excess_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                console.log('working');
                $(".excess_fields").val("");
                $(".cash_fields").val("");
                
                $("#excess_number").val(result.excess_number);
                calculateTotal("#excess_table", ".excess_amount", ".excess_total");
                add_payment(excess_amount);
            }
            console.log('result',result)
        },
    });
});
$(document).on("click", ".credit_sale_add", function () {
        if ($("#credit_sale_amount").val() == "") {
            toastr.error("Please enter amount");
            return false;
        }
        var credit_sale_customer_id = $("#credit_sale_customer_id").val();
        var customer_name = $("#credit_sale_customer_id :selected").text();
        var credit_sale_product_id = $("#credit_sale_product_id").val();
        var credit_sale_product_name = $("#credit_sale_product_id :selected").text();
        if ($("#customer_reference_one_time").val() !== "" && $("#customer_reference_one_time").val() !== null && $("#customer_reference_one_time").val() !== undefined) {
            var customer_reference = $("#customer_reference_one_time").val();
        } else {
            var customer_reference = $("#customer_reference").val();
        }
        var settlement_no = $("#settlement_no").val();
        var order_date = $("#order_date").val();
        var order_number = $("#order_number").val();
        
        var credit_sale_price = __read_number($("#unit_price"));
        var credit_unit_discount = __read_number($("#unit_discount")) ?? 0;
        var credit_sale_qty = __read_number($("#credit_sale_qty")) ?? 0;
        var credit_total_amount = __read_number($("#credit_total_amount")) ?? 0;
        var credit_total_discount = __read_number($("#credit_discount_amount")) ?? 0;
        var credit_sub_total = __read_number($("#credit_sale_amount")) ?? 0;
        
        var outstanding = $(".current_outstanding").text();
        var credit_limit = $(".credit_limit").text();
        var credit_note = $("#credit_note").val();
        var is_edit = $("#is_edit").val() ?? 0;
        
        $.ajax({
            method: "post",
            url: "/petro/settlement/payment/save-credit-sale-payment",
            data: {
                settlement_no: settlement_no,
                customer_id: credit_sale_customer_id,
                product_id: credit_sale_product_id,
                order_number: order_number,
                order_date: order_date,
                
                price: credit_sale_price,
                unit_discount: credit_unit_discount,
                qty: credit_sale_qty,
                amount: credit_total_amount,
                sub_total: credit_sub_total,
                total_discount: credit_total_discount,
                outstanding: outstanding,
                credit_limit: credit_limit,
                customer_reference: customer_reference,
                note: credit_note,
                is_edit: is_edit
            },
            success: function (result) {
                if (!result.success) {
                    toastr.error(result.msg);
                } else {
                    settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;
                    add_payment(credit_total_amount-credit_total_discount);
                    $("#credit_sale_table tbody").prepend(
                        `
                        <tr> 
                            <td>` +
                            customer_name +
                            `</td>
                            <td>` +
                            outstanding +
                            `</td>
                            <td>` +
                            credit_limit +
                            `</td>
                            <td>` +
                            order_number +
                            `</td>
                            <td>` +
                            order_date +
                            `</td>
                            <td>` +
                            customer_reference +
                            `</td>
                            <td>` +
                            credit_sale_product_name +
                            `</td>
                            <td>` +
                            __number_f(credit_sale_price, false, false, __currency_precision) +
                            `</td>
                            <td>` +
                            __number_f(credit_sale_qty, false, false, __currency_precision) +
                            `</td>
                            <td class="credit_sale_amount">` +
                            __number_f(credit_total_amount, false, false, __currency_precision) +
                            `</td>
                            
                            <td class="credit_tbl_discount_amount">` +
                            __number_f(credit_total_discount, false, false, __currency_precision) +
                            `</td>
                            <td class="credit_tbl_total_amount">` +
                            __number_f(credit_sub_total, false, false, __currency_precision) +
                            `</td>
                            
                            
                            <td>` +
                           credit_note +
                            `</td>
                            <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/petro/settlement/payment/delete-credit-sale-payment/` +
                            settlement_credit_sale_payment_id +
                            `"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    `
                    );
                    $("#customer_reference_one_time").val("").trigger("change");
                    $(".credit_sale_fields").val("");
                    $(".cash_fields").val("");
                    $("#credit_sale_product_id").trigger('change');
                    $("#order_number").val(order_number);
                    calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
                    calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
                    calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
                    
                }
            },
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