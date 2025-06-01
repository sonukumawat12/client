@extends('layouts.'.$layout)
@section('title', __('petro::lang.close_shift'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('petro::lang.close_shift') <br>
        <span class="text-red">{{$pump_operator_name}}</span>
    </h1>
    <h2 style="color: red;">Shift NO: {{$shift_number}}</h2>
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
        style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
        class="btn btn-flat btn-lg pull-right"
        style="color: #fff; background-color:#810040; margin-left: 5px;">@lang('petro::lang.dashboard')
    </a>
    <a data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@getPaymentSummaryModal', ['only_pumper' => true])}}"
        class="btn btn-flat btn-lg pull-right btn-modal" data-container=".view_modal"
        style="color: #fff; background-color:#71b306;">@lang('petro::lang.payment_summary')
    </a>
    
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@create')}}"
        class="btn btn-flat btn-lg pull-right btn-info" style="margin-left: 10px; margin-right: 10px;">@lang('petro::lang.payment')
    </a>
    
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@othersalespage')}}"
        class="btn btn-flat btn-lg pull-right btn-danger" style="margin-left: 10px; margin-right: 10px;">@lang('petro::lang.other_sales')
    </a> &nbsp;&nbspother_sales
</section>
<div class="clearfix"></div>
@include('petro::pump_operators.partials.closing_shift')
@endsection
@section('javascript')
<script>
    $('#submit').click(function(){
      let amount = $('#amount').val();
      let payment_type = $('#payment_type').val();
      if(amount === '' || amount === undefined ){
        toastr.error('Please enter amount');
        return false
      }
      if(payment_type === '' || payment_type === undefined){
        toastr.error('Please select payment type');
        return false
      }
      amount = parseFloat(amount);
      console.log(amount);
      $.ajax({
        method: 'POST',
        url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@store')}}",
        data: { amount, payment_type },
        success: function(result) {
          if(result.success){
            toastr.success(result.msg);
            reset();
          }else{
            toastr.error(result.msg)
          }
        },
      });
    })
  </script>
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range').length == 1) {
        $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
        });
        $('#date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
$(document).ready( function(){
    reloadClosingShift();
    
    pump_operators_closing_shift_table = $('#pump_operators_closing_shift_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@index', ['only_pumper' => true])}}",
            data: function(d) {
                @if(empty(auth()->user()->pump_operator_id))
                // d.start_date = $('input#date_range')
                //     .data('daterangepicker')
                //     .startDate.format('YYYY-MM-DD');
                // d.end_date = $('input#date_range')
                //     .data('daterangepicker')
                //     .endDate.format('YYYY-MM-DD');
                // d.location_id = $('#day_entries_location_id').val();
                // d.pump_operator_id = $('#day_entries_pump_operators').val();
                // d.pump_id = $('#day_entries_pumps').val();
                // d.payment_method = $('#day_entries_payment_method').val();
                // d.difference = $('#day_entries_difference').val();
                @endif
                d.shift_id = $("#closing_shift_id").val();
                console.log($("#closing_shift_id").val());
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        },
        {
            "targets": 2,
            "visible": false
        }],
        columns: [
            { data: 'action', searchable: false, orderable: false },
            { data: 'date', name: 'date' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'time', name: 'time' },
            { data: 'name', name: 'pump_operators.name' },
            { data: 'pump_no', name: 'pumps.pump_no' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'testing_ltr', name: 'testing_ltr' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'amount', name: 'amount', searchable: false  },
            { data: 'short_amount', name: 'short_amount', searchable: false  },
        ],
        fnDrawCallback: function(oSettings) {
            var testing_ltr = sum_table_col($('#pump_operators_closing_shift_table'), 'testing_ltr');
            $('#footer_cs_testing_ltr').text(testing_ltr);
            var sold_ltr = sum_table_col($('#pump_operators_closing_shift_table'), 'sold_ltr');
            $('#footer_cs_sold_ltr').text(sold_ltr);
            var sold_amount = sum_table_col($('#pump_operators_closing_shift_table'), 'sold_amount');
            $('#footer_cs_sold_amount').text(sold_amount);
            var short_amount = sum_table_col($('#pump_operators_closing_shift_table'), 'short_amount');
            $('#footer_cs_short_amount').text(short_amount);
            __currency_convert_recursively($('#pump_operators_closing_shift_table'));
        },
    });
    $('#day_entries_location_id, #day_entries_pump_operator, #day_entries_pump_operator, #day_entries_payment_method, #day_entries_date_range, #day_entries_difference, #closing_shift_id').change(function(){
        pump_operators_closing_shift_table.ajax.reload();
        reloadClosingShift();
    });
});


function reloadClosingShift(){
    $("#pumper_day_entry_summary").empty();
    
    $.ajax({
        method: 'GET',
        url:  '{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@getClosingShiftSummary')}}',
        dataType: 'html',
        data: {'shift_id': $("#closing_shift_id").val(), 'only_pumper' : 1  },
        success: function(result) {
            $("#closing_shift_summary").html(result);
        },
    });
}


$(document).on('shown.bs.modal', '.view_modal', function(){
    $('#amount').focus();
});

other_sale_code = null;
other_sale_product_name = null;
other_sale_price = 0.0;
other_sale_qty = 0.0;
other_sale_discount = 0.0;
other_sale_total = parseFloat($('#other_sale_total').val());
$(document).on('change', '#item', function () {
    let item_id = $(this).val(); 
    
    if(item_id){
        $.ajax({
            method: 'get',
            url: '/petro/settlement/get_balance_stock_by_id/' + item_id,
            data : {
                store_id : $("select#store_id option").filter(":selected").val(), 
                location_id : $("#location_id").val()
            },
            success: function (result) {
                console.log(result);
                
                $('#balance_stock').val(result.balance_stock);
                $('#other_sale_price').val(result.price);
                other_sale_code = result.code;
                other_sale_product_name = result.product_name;
                other_sale_price = result.price;
            },
        });
    }
        
});
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
$(document).on('click', '.btn_other_sale', function (e) {
    e.preventDefault();
    
    var allowoverselling = $("#allowoverselling").val();
    if(parseFloat(other_sale_qty) > parseFloat(balance_stock) && allowoverselling == true){
        toastr.error('Out of Stock');
        $(this).val('').focus();
        return false;
    }

    var other_sale_discount         = $('#other_sale_discount').val();
    var other_sale_discount_type    = $('#other_sale_discount_type').val();
    var other_sale_qty              = $('#other_sale_qty').val();
    var balance_stock               = $('#balance_stock').val();
    var sub_total                       = parseFloat(other_sale_qty) * parseFloat(other_sale_price);
    if (!other_sale_discount_type) {
        other_sale_discount_type = 'fixed';
    }
    var other_sale_discount_amount  = calculate_discount(other_sale_discount_type, other_sale_discount, sub_total);
    
    var other_sale_id               = null;
    let sub                         = parseFloat(sub_total);
    let other_sale_total            = parseFloat($('#other_sale_total').val().replace(',', ''));
    
    let with_discount              = sub_total - other_sale_discount_amount;
    
    other_sale_total = other_sale_total + with_discount;

    $.ajax({
        method: 'post',
        url: '/petro/pump-operator-pmts/save-other-sale',
        data: {
            shift_id: $('#shift_id').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            product_id: $('#item').val(), //item is product in whole page
            store_id: $('#store_id').val(),
            price: other_sale_price,
            qty: other_sale_qty,
            balance_stock: balance_stock,
            discount: other_sale_discount,
            discount_type: other_sale_discount_type,
            discount_amount: other_sale_discount_amount,
            sub_total: sub,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
                return false;
            }
            $('#other_sale_total').val(other_sale_total);
            
            other_sale_id = result.other_sale_id;
            sub_total = __number_f(sub_total);
            $('#other_sale_table tbody').prepend(
                `
                <tr> 
                    <td>`+other_sale_code+`</td>
                    <td>`+other_sale_product_name+`</td>
                    <td>`+balance_stock+`</td>
                    <td>`+__number_f(other_sale_price)+`</td>
                    <td>`+other_sale_qty+`</td>
                    <td>`+capitalizeFirstLetter(other_sale_discount_type)+`</td>
                    <td>`+ __number_f(other_sale_discount)+`</td>
                    <td>`+sub_total+`</td>
                    <td>`+ __number_f(with_discount)+`</td>
                    <td><button class="btn btn-xs btn-danger delete_other_sale" data-href="/petro/settlement/delete-other-sale/` +
                        other_sale_id +
                        `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_sale_fields').val('').trigger('change');
            
            calculate_payment_tab_total();
        },
    });
});

$(document).on('click', '.delete_other_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: 'delete',
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let other_sale_total =
                    parseFloat($('#other_sale_total').val().replace(',', '')) -
                    parseFloat(result.amount);
                other_sale_total_text = __number_f(
                    other_sale_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.other_sale_total').text(other_sale_total_text);
                $('#other_sale_total').val(other_sale_total);
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

function calculate_discount(discount_type, discount_value , amount){
    if(discount_type == 'fixed'){
        return parseFloat(discount_value) || 0;
    }
    if(discount_type == 'percentage'){
        return ((amount * parseFloat(discount_value)) / 100) || 0;
    }
    return 0;
}


$(document).on('change','#store_id', function(){
    let location_id = $('#location_id').val();
    let store_id = $(this).val();
    let tab = 'other_sat';
   
   
    $.ajax({
        method: 'get',
        url: "/petro/get-products-by-store-id",
        data: { 'location_id' : location_id, 'store_id' : store_id ,'tab':tab},
        contentType: 'html',
        success: function(result) {
            $('#item').empty().append(result);
        },
    });
});

function calculate_payment_tab_total() {
    let other_sale_totals = parseFloat($('#other_sale_total').val());
    
    $('.payment_other_sale_total').text(
        __number_f(other_sale_totals, false, false, __currency_precision)
    );
    
    $('.other_sale_total').text(__number_f(other_sale_totals, false, false, __currency_precision));
}

</script>
@endsection