@extends('layouts.app')
@section('title', __('petro::lang.daily_collection'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
@endphp

<section class="content-header main-content-inner">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#daily_collection" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('petro::lang.daily_cash')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#daily_voucher" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_credit_sales')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#daily_card" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_cards')</strong>
                        </a>
                    </li>
                    
                    <li class="">
                        <a style="font-size:13px;" href="#daily_shortage_excess" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_shortage_excess')</strong>
                        </a>
                    </li>
                    
                    <li class="">
                        <a style="font-size:13px;" href="#daily_cheques" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_cheques')</strong>
                        </a>
                    </li>
                    
                    <li class="">
                        <a style="font-size:13px;" href="#daily_others" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_other_payments')</strong>
                        </a>
                    </li>
                    
                    <li class="">
                        <a style="font-size:13px;" href="#collection_summary" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.collection_summary')</strong>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="daily_collection">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_collection')
        </div>
        <div class="tab-pane" id="daily_voucher">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_voucher')
        </div>
        
        <div class="tab-pane" id="daily_card">
            
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_cards')
        </div>
        
        <div class="tab-pane" id="daily_shortage_excess">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_shortage_excess')
        </div>
        
        <div class="tab-pane" id="daily_cheques">
            
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_cheque')
        </div>
        
        <div class="tab-pane" id="daily_others">
            
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_others')
        </div>
        
        
        <div class="tab-pane" id="collection_summary">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.collecion_summary')
        </div>

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection
@section('javascript')
<script>
    $(".select2").select2();
    
    @if(Session::has('status'))
        
        var status = "{{ Session::get('status')['success'] }}";
        var msg = "{{ Session::get('status')['msg'] }}";
        
        console.log(status);
        
        if(status == false) {
            toastr.error(msg);
        } else if(status === true) {
            toastr.success(msg);
        }
    @endif
    
    $(document).on('click','#add_row',function(){
        var add_card_type = $("#add_card_type").val();
        var add_card_type_text = $("#add_card_type option:selected").text();
        
        var add_customer_id = $("#add_customer_id").val();
        var add_customer_id_text = $("#add_customer_id option:selected").text();
        
        var add_card_number = $("#add_card_number").val();
        var add_amount = $("#add_amount").val();
        var add_slip_no = $("#add_slip_no").val();
        var add_card_note = $("#add_card_note").val();
        
        if (!add_customer_id || !add_card_type || !add_card_number || !add_amount || !add_slip_no) {
            toastr.error("{{__('petro::lang.please_fill_all_details')}}");
            return false;
        }
        
        
        var tr = `<tr class="single_row">
                <td>${add_customer_id_text}</td>
                <td>${add_card_type_text}</td>
                <td>${add_card_number}</td>
                <td>${add_amount}</td>
                <td>${add_slip_no}</td>
                <td>
                <input type="hidden" name="dynamic_customer_id[]" value="${add_customer_id}">
                <input type="hidden" name="dynamic_card_type[]" value="${add_card_type}">
                <input type="hidden" name="dynamic_card_number[]" value="${add_card_number}">
                <input type="hidden" name="dynamic_slip_no[]" value="${add_slip_no}">
                <input type="hidden" name="dynamic_card_note[]" value="${add_card_note}">
                <input type="hidden" name="dynamic_amount[]" value="${add_amount}">
                
                <button type="button" class="btn btn-danger remove_row"> - </button></td>
        </tr>`;
        
        $("#added_details").append(tr);
        
        $("#add_card_number").val("");
        $("#add_amount").val("");
        $("#add_slip_no").val("");
        $("#add_card_note").val("");
        
        deactivateDropdowns();
    })
    
    $(document).on("click", ".remove_row", function () {
        tr = $(this).closest("tr");
        tr.remove();
        deactivateDropdowns();
    });
    
    $(document).on('change', '#add_pump_operator_id', function() {
        var add_pump_operator_id = $("#add_pump_operator_id").val();
        
        if (add_pump_operator_id) {
            $("#add_pump_operator_id_text").val($("#add_pump_operator_id option:selected").text());
        }
       
        
        deactivateDropdowns();
    });

    
    function deactivateDropdowns(){
        if ($('.single_row').length) {
            var add_pump_operator_id = $("#add_pump_operator_id").val();
            
            if(add_pump_operator_id){
                $("#add_pump_operator_id_text").removeClass('hide');
                $("#add_pump_operator_id_div").hide();
            }else{
                $("#add_pump_operator_id_div").show();
                $("#add_pump_operator_id_text").addClass('hide');
            }
            
            $(".add_card_collection_btn").removeClass('hide');
           
            
        }else{
            $("#add_pump_operator_id_div").show();
            $("#add_pump_operator_id_text").addClass('hide');
            
            $(".add_card_collection_btn").addClass('hide');
        }
    }

    $(document).on('change', '#customer_reference_one_time', function(){
        if($(this).val() !== '' && $(this).val() !== null && $(this).val() !== undefined){
            $('#customer_reference').attr('disabled', 'disabled');
            $('.quick_add_customer_reference').attr('disabled', 'disabled');
        }else{
            $('#customer_reference').removeAttr('disabled');
            $('.quick_add_customer_reference').removeAttr('disabled');
        }
    })

    
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
        
        
        var credit_data = {
            settlement_no: '',
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
            note: credit_note
        };
        
    
    
                $("#credit_sale_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `<input type="hidden" class="credit_data" value='` + JSON.stringify(credit_data) + `'></td>
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
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment"><i class="fa fa-times"></i></button>
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
});

    function calculateTotal(table_name, class_name_td, output_element) {
        let total = 0.0;
        $(table_name + " tbody")
            .find(class_name_td)
            .each(function () {
                total += parseFloat(__number_uf($(this).text()));
            });
            
            if(total <=0){
                $(".credit_sale_finalize").hide();
            }else{
                $(".credit_sale_finalize").show();
            }
        $(output_element).text(__number_f(total, false, false, __currency_precision));
    }
    
    $(document).on('input','#credit_total_amount', function() {
        $("#credit_sale_qty").attr('disabled',true);
        
        let price = __read_number($("#unit_price")) ?? 0;
        let total_amount = __read_number($("#credit_total_amount")) ?? 0;
        let qty = total_amount / price; 
        
        let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
        let unit_discount = total_discount / qty;
        let amount = total_amount - total_discount
        
        __write_number($("#credit_sale_amount"), amount);
        __write_number($("#unit_discount"), unit_discount);
        __write_number_without_decimal_format($("#credit_sale_qty"),qty);
        
        
    });
    
    $(document).on('input','#credit_sale_qty', function() {
        $("#credit_total_amount").attr('disabled',true);
        
        let price = __read_number($("#unit_price")) ?? 0;
        let qty = __read_number($("#credit_sale_qty")) ?? 0; 
        let total_amount = price * qty;
        
        let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
        let unit_discount = total_discount / qty;
        let amount = total_amount - total_discount
        
        __write_number($("#credit_sale_amount"), amount);
        __write_number($("#unit_discount"), unit_discount);
        __write_number($("#credit_total_amount"),total_amount);
        
    });
    
    $(document).on("change", "#credit_discount_amount, #unit_price", function () {
        let price = __read_number($("#unit_price")) ?? 0;
        let qty_check = __read_number($("#credit_sale_qty")) ?? 0;
        
        var qty = 0;
        var total_amount = 0;
        
        if(qty_check > 0){
            qty = __read_number($("#credit_sale_qty")) ?? 0;
            total_amount = price * qty; 
            __write_number($("#credit_total_amount"), total_amount);
        }else{
            total_amount = __read_number($("#credit_total_amount")) ?? 0;
            qty = total_amount / price; 
            __write_number_without_decimal_format($("#credit_sale_qty"),qty);
        }
        
        
        
        let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
        let unit_discount = total_discount / qty;
        let amount = total_amount - total_discount
        
        __write_number($("#unit_discount"), unit_discount);
        __write_number($("#credit_sale_amount"),amount);
        
    });
        
    $(document).on("change", "#credit_sale_product_id", function () {
        if ($(this).val()) {
            $.ajax({
                method: "get",
                url: "/petro/settlement/payment/get-product-price",
                data: { product_id: $(this).val() },
                success: function (result) {
                    $("#unit_price").val(result.price);
                    $("#unit_price").trigger('change');
                    
                    $("#credit_total_amount").attr("disabled", false);
                    $("#credit_sale_qty").attr("disabled", false);
                    if($("#manual_discount").val() == 1){
                        $("#credit_discount_amount").attr("disabled", false);
                    }
                    
                },
            });
        } else {
            $("#credit_total_amount").attr("disabled", true);
            $("#credit_sale_qty").attr("disabled", true);
            $("#credit_discount_amount").attr("disabled", true);
        }
    });
    $(document).on("change", "#credit_sale_customer_id", function () {
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + $(this).val(),
            data: {},
            success: function (result) {
                $(".current_outstanding").text(result.total_outstanding);
                $(".credit_limit").text(result.credit_limit);
                $("#customer_reference").empty();
                $("#customer_reference").append(`<option selected="selected" value="">Please Select</option>`);
                result.customer_references.forEach(function (ref, i) {
                    $("#customer_reference").append(`<option value="` + ref.reference + `">` + ref.reference + `</option>`);
                    // $("#customer_reference").val($("#customer_reference option:eq(1)").val()).trigger("change");
                });
            },
        });
    });
    $(document).on("click", ".delete_credit_sale_payment", function () {
        tr = $(this).closest("tr");
        tr.remove();
        
        calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
        calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
        calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
    });
    
    $(document).on("click", ".credit_sale_finalize", function (e) {
        e.preventDefault();
        var dataArray = [];
        $(".credit_data").each(function() {
            var jsonData = JSON.parse($(this).val());
            dataArray.push(jsonData);
        });
        $.ajax({
            method: "post",
            url: "{{action('\Modules\Petro\Http\Controllers\DailyVoucherController@store')}}",
            data: {
                credit_data : dataArray,
                pump_operator_id: $("#cr_pump_operator_id").val()
            },
            success: function (result) {
                if(result.success == true){
                    toastr.success(result.msg);
                    window.location.reload();
                }else{
                    toastr.error(result.msg);
                }
                
            },
        });
    });
</script>
<script type="text/javascript">
    $(document).ready( function(){
        
        if ($('#daily_cheque_date_range').length == 1) {
            $('#daily_cheque_date_range').daterangepicker(dateRangeSettings, function (start, end) {
                $('#daily_cheque_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                daily_cheques_table.ajax.reload();
            });
            $('#daily_cheque_date_range').on('cancel.daterangepicker', function (ev, picker) {
                daily_cheques_table.ajax.reload();
            });
            $('#daily_cheque_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
            $('#daily_cheque_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
        }
          
        
        daily_cheques_table = $('#daily_cheques_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@indexCheque')}}',
                data: function(d) {
                    d.location_id = $('select#location_id').val();
                    /*
                    * @ChangedBy Afes
                    * @Date 26-05-2021
                    * @Task 1526 
                    */
                    d.pump_operator = $('select#daily_cheque_pump_operator').val();
                    
                    d.settlement_id = $('select#daily_cheque_settlement_id').val();
                    
                    d.start_date = $('input#daily_cheque_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#daily_cheque_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.status = $("#daily_cheque_status").val();
                },
            },
            
            columns: [
                { data: 'date_and_time', name: 'date_and_time' },
                { data: 'pump_operator_name', name: 'pump_operators.name' },
                
                { data: 'payment_amount', name: 'payment_amount' },
                { data: 'total_collection', name: 'total_collection',searchable: false },
                { data: 'user', name: 'users.username' },
                { data: 'settlement_noo', name: 'settlements.settlement_no' },
                { data: 'settlement_date', name: 'settlements.transaction_date' },
                { data: 'status', name: 'status', searchable: false },
                
                { data: 'customer', name: 'settlements.transaction_date' },
                { data: 'cheque_date', name: 'daily_cheque_payments.cheque_date' },
                { data: 'cheque_number', name: 'daily_cheque_payments.cheque_number' },
                { data: 'bank_name', name: 'daily_cheque_payments.bank_name' },
                
                { data: 'action', searchable: false, orderable: false },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    
        /*
        * @ChangedBy Afes
        * @Date 26-05-2021
        * @Task 1526 
        */
        $('#daily_cheque_location_id, #daily_cheque_pump_operator, #daily_cheque_date_range,#daily_cheque_settlement_id, #daily_cheque_status').change(function(){
            daily_cheques_table.ajax.reload();
        });
        
        
        if ($('#daily_others_date_range').length == 1) {
            $('#daily_others_date_range').daterangepicker(dateRangeSettings, function (start, end) {
                $('#daily_others_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                daily_others_table.ajax.reload();
            });
            $('#daily_others_date_range').on('cancel.daterangepicker', function (ev, picker) {
                daily_others_table.ajax.reload();
            });
            $('#daily_others_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
            $('#daily_others_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
        }
          
        
        daily_others_table = $('#daily_others_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@indexOther')}}',
                data: function(d) {
                    d.location_id = $('select#location_id').val();
                    /*
                    * @ChangedBy Afes
                    * @Date 26-05-2021
                    * @Task 1526 
                    */
                    d.pump_operator = $('select#daily_others_pump_operator').val();
                    
                    d.settlement_id = $('select#daily_others_settlement_id').val();
                    
                    d.start_date = $('input#daily_others_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#daily_others_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.status = $("#daily_others_status").val();
                },
            },
            
            columns: [
                { data: 'date_and_time', name: 'date_and_time' },
                { data: 'pump_operator_name', name: 'pump_operators.name' },
                
                { data: 'payment_amount', name: 'payment_amount' },
                { data: 'total_collection', name: 'total_collection',searchable: false },
                { data: 'user', name: 'users.username' },
                { data: 'settlement_noo', name: 'settlements.settlement_no' },
                { data: 'settlement_date', name: 'settlements.transaction_date' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'action', searchable: false, orderable: false },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    
        /*
        * @ChangedBy Afes
        * @Date 26-05-2021
        * @Task 1526 
        */
        $('#daily_others_location_id, #daily_others_pump_operator, #daily_others_date_range,#daily_others_settlement_id, #daily_others_status').change(function(){
            daily_others_table.ajax.reload();
        });
        
        
        
    
    if ($('#se_date_range').length == 1) {
        $('#se_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#se_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            daily_shortage_excess_table.ajax.reload();
        });
        $('#se_date_range').on('cancel.daterangepicker', function (ev, picker) {
            daily_shortage_excess_table.ajax.reload();
        });
        $('#se_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#se_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
      
    var columns = [
            { data: 'created_at', name: 'created_at' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'collection_form_no', name: 'collection_form_no' },
            { data: 'current_amount', name: 'current_amount' },
            { data: 'total_collection', name: 'total_collection',searchable: false },
            { data: 'user', name: 'users.username' },
            { data: 'settlement_no', name: 'settlements.settlement_no' },
            { data: 'settlement_dates', name: 'settlements.transaction_date' },
            { data: 'status', name: 'status', searchable: false },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    daily_collection_table = $('#daily_collection_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@index')}}',
            data: function(d) {
                d.location_id = $('select#location_id').val();
                /*
                * @ChangedBy Afes
                * @Date 26-05-2021
                * @Task 1526 
                */
                d.pump_operator = $('select#pump_operator').val();
                
                d.settlement_id = $('select#dcollection_settlement_id').val();
                
                d.start_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.status = $("#dcollection_status").val();
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        },
        {
            "targets": 1,
            "visible": false
        } ],
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    /*
    * @ChangedBy Afes
    * @Date 26-05-2021
    * @Task 1526 
    */
    $('#location_id, #pump_operator, #expense_date_range,#dcollection_settlement_id, #dcollection_status').change(function(){
        daily_collection_table.ajax.reload();
    });
    
    
     var columns = [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            
            { data: 'shortage_amount', name: 'pump_operator_payments.payment_amount' },
            { data: 'excess_amount', name: 'pump_operator_payments.payment_amount' },
            
            { data: 'total_collection', name: 'total_collection',searchable: false },
            { data: 'settlement_noo', name: 'settlements.settlement_no' },
            { data: 'settlement_date', name: 'settlements.transaction_date' },
            { data: 'status', name: 'status', searchable: false },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    daily_shortage_excess_table = $('#daily_shortage_excess_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@indexShortageExcess')}}',
            data: function(d) {
                d.location_id = $('select#se_location_id').val();
                
                d.settlement_id = $('select#se_settlement_id').val();
               
                d.pump_operator = $('select#se_pump_operator').val();
                d.start_date = $('input#se_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#se_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.status = $("#se_status").val();
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        } ,
        {
            "targets": 1,
            "visible": false
        }],
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    /*
    * @ChangedBy Afes
    * @Date 26-05-2021
    * @Task 1526 
    */
    $('#se_location_id, #se_pump_operator, #se_date_range, #se_settlement_id, #se_status').change(function(){
        daily_shortage_excess_table.ajax.reload();
    });


    $(document).on('click', 'a.delete_daily_collection', function(e) {
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
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        daily_collection_table.ajax.reload();
                        daily_shortage_excess_table.ajax.reload();
                    },
                });
            }
        });
    });
});

$(document).on('click', '.edit_contact_button', function(e) {
    e.preventDefault();
    $('div.pump_operator_modal').load($(this).attr('href'), function() {
        $(this).modal('show');
    });
});

$(document).ready( function(){
    
    if ($('#cs_date_range').length == 1) {
        $('#cs_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#cs_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            collection_summary_table.ajax.reload();
        });
        $('#cs_date_range').on('cancel.daterangepicker', function (ev, picker) {
            collection_summary_table.ajax.reload();
        });
        $('#cs_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#cs_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
  
     // daily_voucher_table
     collection_summary_table = $('#collection_summary_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@collectionSummary')}}",
                data : function(d){
                    d.start_date = $('input#cs_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#cs_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.pump_operator_id = $("#cs_pump_operator").val();
                    d.daily_collection_type = $("#daily_collection_type").val();
                    
                }
            },
   
            columns: [
                {data: 'date', name: 'date'},
                {data: 'pump_operator_name', name: 'pump_operators.name'},
                {data: 'settlement_nos', name: 'settlement_nos',searchable: false},
                {data: 'collection_form_no', name: 'collection_form_no',searchable: false},
                { data: 'total_amount', name: 'total_amount',searchable: false },
                {data: 'type', name: 'type',searchable: false},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $('#cs_pump_operator,#daily_collection_type').change(function(){
            collection_summary_table.ajax.reload();
        });
});


$(document).ready( function(){
   
    
    if ($('#dc_date_range').length == 1) {
        $('#dc_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#dc_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            daily_card_table.ajax.reload();
        });
        $('#dc_date_range').on('cancel.daterangepicker', function (ev, picker) {
            daily_card_table.ajax.reload();
        });
        $('#dc_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#dc_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    
     // daily_voucher_table
     daily_card_table = $('#daily_card_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Petro\Http\Controllers\DailyCardController@index')}}",
                data : function(d){
                    d.start_date = $('input#dc_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#dc_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.pump_operator_id = $("#dc_pump_operator").val();
                    d.customer_id = $("#customer_id").val();
                    d.card_type = $("#card_type").val();
                    d.slip_no = $("#slip_no").val();
                    d.card_number = $("#card_number").val();
                    d.location_id = $("#dc_location_id").val();
                    
                    d.settlement_id = $('select#dc_settlement_id').val();
                    d.status = $("#dc_status").val();
                }
            },
            columnDefs:[{
                    "targets": 8,
                    "orderable": false,
                    "searchable": false
                },
        {
            "targets": 1,
            "visible": false
        }],
            columns: [
                {data: 'date', name: 'date'},
                { data: 'location_name', name: 'business_locations.name' },
                {data: 'pump_operator_name', name: 'pump_operators.name'},
                {data: 'collection_no', name: 'collection_no'},
                
                {data: 'customer_name', name: 'contacts.name'},
                {data: 'type_name', name: 'accounts.name'},
                {data: 'card_number', name: 'card_number'},
                
                {data: 'amount', name: 'amount'},
                
                { data: 'total_collection', name: 'total_collection',searchable: false },
                {data: 'slip_no', name: 'slip_no'},
                {data: 'settlement_nos', name: 'settlements.settlement_no'},
                {data: 'note', name: 'note'},
                { data: 'status', name: 'status', searchable: false },
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $('#dc_pump_operator,#customer_id,#card_type,#slip_no,#card_number,#dc_location_id,#dc_settlement_id, #dc_status').change(function(){
            daily_card_table.ajax.reload();
        });
});

$(document).on('click', '.print_btn_pump_operator', function() {
        var url = $(this).data('href');
        $.ajax({
            method: 'get',
			url: url,
            'Content-Type': 'html',
			data: {  },
			success: function(result) {
                console.log(result);
                $('#daily_collection_print').html(result);

                var divToPrint=document.getElementById('daily_collection_print');

                var newWin=window.open('','Print-Daily-Collection');
            
                newWin.document.open();
            
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            
                newWin.document.close();

			},
		});
    });

$(document).ready( function(){
    if ($('#daily_voucher_date_range').length == 1) {
        $('#daily_voucher_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#daily_voucher_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            expense_table.ajax.reload();
        });
        $('#daily_voucher_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#product_sr_date_filter').val('');
            expense_table.ajax.reload();
        });
        $('#daily_voucher_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#daily_voucher_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

     // daily_voucher_table
     daily_voucher_table = $('#daily_voucher_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Petro\Http\Controllers\DailyVoucherController@index')}}",
                data : function(d){
                    d.location_id = $('#daily_voucher_location_id').val();
                    
                    d.customer_id = $('#dv_customer_id').val();
                    
                    d.settlement_id = $('select#dv_settlement_id').val();
                    
                    d.start_date = $('input#daily_voucher_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#daily_voucher_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.status = $("#dv_status").val();
                }
            },
            columnDefs:[{
                    "targets": 8,
                    "orderable": false,
                    "searchable": false
                },
        {
            "targets": 1,
            "visible": false
        }],
            columns: [
                {data: 'voucher_order_date', name: 'voucher_order_date'},
                {data: 'location_name', name: 'business_locations.name'},
                {data: 'transaction_date', name: 'transaction_date'},
                {data: 'voucher_order_number', name: 'voucher_order_number'},
                {data: 'operator_name', name: 'pump_operators.name'},
                {data: 'customer_name', name: 'contacts.name'},
                
                {data: 'credit_limit', name: 'contacts.credit_limit'},
                {data: 'current_outstanding', name: 'current_outstanding'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'balance_available', name: 'balance_available'},
                { data: 'total_collection', name: 'total_collection',searchable: false },
            
                {data: 'username', name: 'users.username'},
                {data: 'settlement_nos', name: 'settlements.settlement_no'},
                { data: 'status', name: 'status', searchable: false },
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $('#daily_voucher_location_id, #daily_voucher_date_range,#dv_settlement_id,#dv_customer_id, #dv_status').change(function(){
            daily_voucher_table.ajax.reload();
        });
});

$(document).on('click', 'a.delete_daily_card', function(e){
        e.preventDefault();
        let href = $(this).data('href');
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    console.log(href);
                    
                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            daily_card_table.ajax.reload();
                        },
                    });
                }
            });
        });
        
        $(document).on('click', 'a.delete-issue_bill_customer', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            daily_voucher_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', 'a.print_bill', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });
        });


</script>
@endsection