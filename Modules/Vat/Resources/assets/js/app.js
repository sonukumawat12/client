//meter sale tab
updateTotalSoldQty();
$('#pump_operator_id').change(function () {

    let store_id = $("select#store_id option").filter(":selected").val();
    
    if ($(this).val() === '' || $(this).val() === undefined) {
        toastr.error('Please Select the Pump operator and continue');
    } else {
        $('#below_box *').attr('disabled', false);
    }
});
$(document).ready(function () {
    var settlement_id = 0;
    let store_id = $("select#store_id option").filter(":selected").val();
    if ($('#pump_operator_id').val() === '' || $('#pump_operator_id').val() === undefined) {
        $('#below_box *').attr('disabled', true);
    } else {
        $('#below_box *').attr('disabled', false);
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

$('#pump_no').change(function () {
    pump_closing_meter = 0.0;
    pump_starting_meter = 0.0;

    $.ajax({
        method: 'get',
        url: '/petro/settlement/get-pump-details/' + $(this).val(),
        data: {},
        success: function (result) {
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

$('#pump_closing_meter,#pump_starting_meter').change(function () {
    pump_closing_meter = parseFloat($("#pump_closing_meter").val());
    pump_starting_meter = parseFloat($('#pump_starting_meter').val());
    sold_qty = (pump_closing_meter - pump_starting_meter).toFixed(6);

    if (pump_closing_meter < pump_starting_meter) {
        toastr.error('Closing meter value should not less then starting meter value');
        $(this).val('');
    } 
    
    else {
        $('#sold_qty').val(sold_qty);
    }
});



$('.btn_meter_sale').click(function () {
    var testing_qty = $('#testing_qty').val();
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
    $.ajax({
        method: 'post',
        url: '/vat-module/settlement/save-meter-sale',
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
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
                return false;
            }

            $('#meter_sale_total').val(meter_sale_total);
            $('#pump_no')
                .find('option[value=' + pump_id + ']')
                .remove();

            meter_sale_id = result.meter_sale_id;
            settlement_id = result.settlement_id;
            
            
            $('#note, #work_shift, #transaction_date, #pump_operator_id, #location_id').change(function() {
                
                $.ajax({
                    method: 'put',
                    url: "/vat-module/settlement/"+settlement_id,
                    data: {
                        note: $('#note').val(),
                        transaction_date: $('#transaction_date').val(),
                        pump_operator_id: $('#pump_operator_id').val()
                    },
                    success: function(result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            })
            
            
            meter_sale_totals = __number_f(sub_total);
            var with_disc = __number_f(sub_total + meter_sale_discount);
            
            sold_qty = (sold_qty);
            $('#meter_sale_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    code +
                    `</td>
                    <td><span class="product_name">` +
                        product_name +
                    `</span></td>
                    <td>` +
                    pump_name +
                    `</td>
                    <td>` +
                    pump_starting_meter +
                    `</td>
                    <td>` +
                    pump_closing_meter +
                    `</td>
                    <td>` +
                        price +
                    `</td>
                    
                    <td>
                     <span class="sold_qty">` +
                        sold_qty +
                    `</span>
                    </td>
                    <td>` +
                    meter_sale_discount_type_text +
                        `</td>
                    <td>` +
                      __number_f(meter_sale_discount) +
                    `</td>
                    <td>` +
                    testing_qty
                     +
                    `</td>
                    <td>` +
                    total_qty
                     +
                    `</td>
                    <td>` +
                     __number_f(sub_total)  +
                    `</td>
                    <td>` +
                     __number_f(meter_sale_discount_amount) +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_meter_sale" data-href="/vat-module/settlement/delete-meter-sale/` +
                    meter_sale_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.meter_sale_fields').val('');
            $('.testing_qty').val(0);
            calculate_payment_tab_total();
            updateTotalSoldQty();
        },
    });
});


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
        productSummaryHtml += productName + ' = ' + productSoldQty[productName] + '<br>';
    }
    
    // Set the HTML content in the product_summary element
    $('.product_summary').html(productSummaryHtml);
}

// product_summary


function formatNumber(number) {
    return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
        
        

function calculate_discount(discount_type, discount_value , amount){
    if(discount_type == 'fixed'){
        return parseFloat(discount_value) || 0;
    }
    if(discount_type == 'percentage'){
        return ((amount * parseFloat(discount_value)) / 100) || 0;
    }
    return 0;
}

$(document).on('click', '.delete_meter_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let meter_sale_total =
                    parseFloat($('#meter_sale_total').val()) - parseFloat(result.amount);
                meter_sale_total_text = __number_f(
                    meter_sale_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.meter_sale_total').text(meter_sale_total_text);
                $('#meter_sale_total').val(meter_sale_total);
                $('#pump_no').append(
                    `<option value="` + result.pump_id + `">` + result.pump_name + `</option>`
                );
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

//other sale tab
other_sale_code = null;
other_sale_product_name = null;
other_sale_price = 0.0;
other_sale_qty = 0.0;
other_sale_discount = 0.0;
other_sale_total = parseFloat($('#other_sale_total').val());
$('#item').change(function () {
    let item_id = $(this).val();    
    if(item_id){
        $.ajax({
            method: 'get',
            url: '/vat-module/settlement/get_balance_stock_by_id/' + item_id,
            data : {
            },
            success: function (result) {
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
$('.btn_other_sale').click(function (e) {
    e.preventDefault();
    
    var allowoverselling = $("#allowoverselling").val();
    
    var other_sale_discount         = $('#other_sale_discount').val();
    var other_sale_discount_type    = $('#other_sale_discount_type').val();
    var other_sale_qty              = $('#other_sale_qty').val();
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
        url: '/vat-module/settlement/save-other-sale',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            note: $('#note').val(),
            product_id: $('#item').val(), //item is product in whole page
            price: other_sale_price,
            qty: other_sale_qty,
            discount: other_sale_discount,
            discount_type: other_sale_discount_type,
            discount_amount: other_sale_discount_amount,
            sub_total: sub,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
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
                    <td>`+__number_f(other_sale_price)+`</td>
                    <td>`+other_sale_qty+`</td>
                    <td>`+capitalizeFirstLetter(other_sale_discount_type)+`</td>
                    <td>`+ __number_f(other_sale_discount)+`</td>
                    <td>`+sub_total+`</td>
                    <td>`+ __number_f(with_discount)+`</td>
                    <td><button class="btn btn-xs btn-danger delete_other_sale" data-href="/vat-module/settlement/delete-other-sale/` +
                        other_sale_id +
                        `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_sale_fields').val('').trigger('change');
            $("#store_id").trigger('change');
            
            calculate_payment_tab_total();
        },
    });
});

$(document).on('click', '.delete_other_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
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


function calculate_payment_tab_total() {
    let meter_sale_totals = parseFloat($('#meter_sale_total').val());
    let other_sale_totals = parseFloat($('#other_sale_total').val());
    
    let all_totals =
        meter_sale_totals + other_sale_totals;

    $('.payment_meter_sale_total').text(
        __number_f(meter_sale_totals, false, false, __currency_precision)
    );
    $('.payment_other_sale_total').text(
        __number_f(other_sale_totals, false, false, __currency_precision)
    );
    
    $('.meter_sale_total').text(__number_f(meter_sale_totals, false, false, __currency_precision));
    $('.other_sale_total').text(__number_f(other_sale_totals, false, false, __currency_precision));
    

    $('#payment_due').text(__number_f(all_totals, false, false, __currency_precision));
}