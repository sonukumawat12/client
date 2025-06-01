$('#pump_operator_id').change(function () {

    let store_id = $("select#store_id option").filter(":selected").val();

    var op_id = $(this).val();

    if ($(this).val() === '' || $(this).val() === undefined) {
        toastr.error('Please Select the Pump operator and continue');
    } else {

        $.ajax({
            method: 'get',
            url: "/petro/settlement/get_pumps/" + op_id,
            data: {
                settlement_no: $('#settlement_no').val(),
                location_id: $('#location_id').val(),
                pump_operator_id: $('#pump_operator_id').val(),
                transaction_date: $('#transaction_date').val(),
                work_shift: $('#work_shift').val(),
                note: $('#note').val(),
            },
            success: function(result) {
                if (result.success == false) {
                    toastr.error(result.msg);
                    return false;
                }

                if(result.should_reload > 0){
                    window.location.reload();
                }

                $('#below_box *').attr('disabled', false);
                if(store_id == null || store_id ==""){
                    $('.other_sale_fields#item').attr('disabled', true);
                }

                // Select the dropdown menu
                var dropdown = $('#pump_no');

                // Clear any existing options
                dropdown.empty();

                // Add the "Please select" option as the first option
                dropdown.append($('<option>').text('Please select').val(''));

                // Iterate through the object and add options to the dropdown
                $.each(result.pumps, function(key, value) {
                    dropdown.append($('<option>').text(value).val(key));
                });
            },
        });


    }
});
$(document).ready(function () {
    updateTotalSoldQty();
    var settlement_id = 0;
    let store_id = $("select#store_id option").filter(":selected").val();
    if ($('#pump_operator_id').val() === '' || $('#pump_operator_id').val() === undefined) {
        $('#below_box *').attr('disabled', true);
    } else {
        $('#below_box *').attr('disabled', false);
        if(store_id == null || store_id ==""){
            $('.other_sale_fields#item').attr('disabled', true);
        }
    }
});
var tank_qty = 0;
var code = '';
var price = 0.0;
var product_name = '';
var pump_name = '';
var pump_closing_meter = 0.0;
var pump_starting_meter = 0.0;
var meter_sale_total = parseFloat($('#meter_sale_total').val());
var product_id = null;
var pump_id = null;

$(document).on('change', '#pump_no', function () {
    pump_closing_meter = 0.0;
    pump_starting_meter = 0.0;

    $.ajax({
        method: 'get',
        url: '/petro/settlement/get-pump-details/' + $(this).val(),
        data: {},
        success: function (result) {

            if(result.is_open > 0){
                toastr.error('Please close the pump first before adding a meter sale!');
                return false;
            }
            console.log(result.po_closing);

            $('#pump_starting_meter').val(result.colsing_value);

            if(result.po_closing > 0){
                $('#pump_closing_meter').val(result.po_closing);
                $("#pump_closing_meter").prop('readonly',true);
                $('#pump_closing_meter').trigger('change');
                $("#is_from_pumper").val(1);

                $('#assignment_id').val(result.assignment_id);
                $('#pumper_entry_id').val(result.pumper_entry_id);

            }else{
                $('#pump_closing_meter').val("");
                $("#pump_closing_meter").prop('readonly',false);
                $("#is_from_pumper").val(0);
            }

            if(result.po_testing > 0){
                $('#testing_qty').val(result.po_testing);
                $("#testing_qty").prop('readonly',true);
                $('#testing_qty').trigger('change');
            }else{
                $('#testing_qty').val(0);
                $("#testing_qty").prop('readonly',false);
            }


            pump_starting_meter = result.colsing_value;
            tank_qty = result.tank_remaing_qty;
            code = result.product.sku;
            price = result.product.default_sell_price;
            product_name = result.product.name;
            pump_name = result.pump_name;
            pump_id = result.pump_id;
            product_id = result.product_id;
            if (result.bulk_sale_meter == '1') {
                $('#bulk_sale_meter').val(1);
                $('.pump_starting_meter_div').addClass('hide');
                $('.pump_closing_meter_div').addClass('hide');
                $('#sold_qty').prop('disabled', false);
            } else {
                $('#bulk_sale_meter').val(0);
                $('.pump_starting_meter_div').removeClass('hide');
                $('.pump_closing_meter_div').removeClass('hide');
                $('#sold_qty').prop('disabled', true);
            }
            $('#meter_sale_unit_price').val(price);
        },
    });
});

$(document).on('change', '#pump_closing_meter', function () {
    pump_closing_meter = parseFloat($(this).val());
    pump_starting_meter = parseFloat($('#pump_starting_meter').val());
    sold_qty = (pump_closing_meter - pump_starting_meter).toFixed(6);

    if (pump_closing_meter < pump_starting_meter) {
        toastr.error('Closing meter value should not less then starting meter value');
        $(this).val('');
    }
        // I commented this line -- Bekzod Erkinov
        // else if (tank_qty >= sold_qty) {
        //     toastr.error('Out of Stock');
        //     $(this).val('');
    // }
    else {
        $('#sold_qty').val(sold_qty);
    }
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

function updateTotalSoldQty() {
    var productSoldQty = {};

    $('#meter_sale_table tbody tr').each(function() {
        var productName = $(this).find('.product_name').text();

        var soldQty = parseFloat($(this).find('span.sold_qty').text().replace(',', ''));

        if (!isNaN(soldQty)) {
            if (productSoldQty[productName] === undefined) {
                productSoldQty[productName] = soldQty;
            } else {
                productSoldQty[productName] += soldQty;
            }
        }
    });

    var productSummaryHtml = '';
    for (var productName in productSoldQty) {
        productSummaryHtml += productName + ' = ' + __number_f(productSoldQty[productName]) + '<br>';
    }

    // Set the HTML content in the product_summary element
    $('.product_summary').html(productSummaryHtml);
}

$('#add-meter-sale').on('click', function () {
    var testing_qty = $('#testing_qty').val();
    var is_from_pumper = $("#is_from_pumper").val() ?? 0;

    var assignment_id = $("#assignment_id").val() ?? 0;
    var pumper_entry_id = $("#pumper_entry_id").val() ?? 0;

    var meter_sale_discount = $('#meter_sale_discount').val();
    var meter_sale_discount_type = $('#meter_sale_discount_type').val();
    var meter_sale_discount_type_text = '';
    if ($('#meter_sale_discount_type').val() !== '') {
        meter_sale_discount_type_text = $('#meter_sale_discount_type option[value="'+$('#meter_sale_discount_type').val()+'"]').text();
    }
    var sold_qty = parseFloat($('#sold_qty').val()) - parseFloat(testing_qty);
    var total_qty = parseFloat($('#sold_qty').val());
    sub_total = parseFloat(sold_qty) * parseFloat(price);

    if (!meter_sale_discount) {
        meter_sale_discount = 0;
    }
    var meter_sale_discount_amount = sub_total - calculate_discount(meter_sale_discount_type, meter_sale_discount, sub_total);
    var meter_sale_id = null;

    let meter_sale_total = parseFloat($('#meter_sale_total').val().replace(',', ''));
    meter_sale_total = meter_sale_total + meter_sale_discount_amount;
    var is_edit = $("#is_edit").val() ?? 0;

    $.ajax({
        url: '/settlement-sw/save-meter-sale',
        type: 'POST',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            pump_id: pump_id,
            starting_meter: pump_starting_meter,
            closing_meter: $('#pump_closing_meter').val(),
            product_id: product_id,
            price: price,
            qty: sold_qty,
            discount: meter_sale_discount,
            discount_type: meter_sale_discount_type,
            discount_amount: meter_sale_discount_amount,
            testing_qty: testing_qty,
            sub_total: sub_total,
            is_edit: is_edit,
            is_from_pumper : is_from_pumper,
            assignment_id : assignment_id,
            pumper_entry_id : pumper_entry_id,
        },
        success: function (response) {
            alert('Meter Sale Added Successfully!');

            // Append row to table (example)
        //     $('#meter-sales-tbody').append(`
        //   <tr>
        //     <td>${response.data.code ?? ''}</td>
        //     <td>${response.data.product_name ?? ''}</td>
        //     <td>${response.data.pump_no}</td>
        //     <td>${response.data.pump_start}</td>
        //     <td>${response.data.pump_close}</td>
        //     <td>${response.data.unit_price}</td>
        //     <td>${response.data.sold_qty}</td>
        //     <td>${response.data.discount_type}</td>
        //     <td>${response.data.discount_val}</td>
        //     <td>${response.data.testing_qty}</td>
        //     <td>${parseFloat(response.data.sold_qty) + parseFloat(response.data.testing_qty)}</td>
        //     <td>${response.data.unit_price * response.data.sold_qty}</td>
        //     <td>--</td>
        //     <td><button class="btn btn-sm btn-danger">Delete</button></td>
        //   </tr>
        // `);

            // Optional: Reset the form
            $('#meter-sales-form')[0].reset();
        },
        error: function (xhr) {
            alert('Failed to save meter sale. Please check the fields.');
            console.log(xhr.responseJSON);
        }
    });
})

$('#add-other-sale').click(function (e) {
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
    var is_edit = $("#is_edit").val() ?? 0;

    $.ajax({
        method: 'post',
        url: '/settlement-sw/save-other-sale',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            product_id: $('#item').val(), //item is product in whole page
            store_id: $('#store_id').val(),
            price: other_sale_price,
            qty: other_sale_qty,
            balance_stock: balance_stock,
            discount: other_sale_discount,
            discount_type: other_sale_discount_type,
            discount_amount: other_sale_discount_amount,
            sub_total: sub,
            is_edit: is_edit
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
                    <td><button class="btn btn-xs btn-danger delete_other_sale" data-href=""><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_sale_fields').val('').trigger('change');

            calculate_payment_tab_total();
        },
    });
});