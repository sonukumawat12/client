//meter sale tab
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



$(document).on('click', '.btn_meter_sale', function () {
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
        method: 'post',
        url: '/petro/settlement/save-meter-sale',
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
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
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
                    url: "/petro/settlement/"+settlement_id,
                    data: {
                        note: $('#note').val(),
                        work_shift: $('#work_shift').val(),
                        transaction_date: $('#transaction_date').val(),
                        pump_operator_id: $('#pump_operator_id').val(),
                        location_id: $('#location_id').val()
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
                    pump_starting_meter.toFixed(3) +
                    `</td>
                    <td>` +
                    pump_closing_meter.toFixed(3) +
                    `</td>
                    <td>` +
                        __number_f(price) +
                    `</td>
                    
                    <td>
                     <span class="sold_qty">` +
                        formatNumber(sold_qty) +
                    `</span>
                    </td>
                    <td>` +
                    meter_sale_discount_type_text +
                        `</td>
                    <td>` +
                      __number_f(meter_sale_discount) +
                    `</td>
                    <td>` +
                    formatNumber(testing_qty)
                     +
                    `</td>
                    <td>` +
                    formatNumber(total_qty)
                     +
                    `</td>
                    <td>` +
                     __number_f(sub_total)  +
                    `</td>
                    <td>` +
                     __number_f(meter_sale_discount_amount) +
                    `</td>`+
                    `<td>` +
					`<button class="btn btn-xs btn-primary get_meter_sale_from" data-type="edit" data-href="/petro/settlement/get-meter-sale-form/`+
                    meter_sale_id +
                    `"><i class="fa fa-edit"></i></button>`+
                    `<button class="btn btn-xs btn-danger delete_meter_sale" data-href="/petro/settlement/delete-meter-sale/` +
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
        productSummaryHtml += productName + ' = ' + __number_f(productSoldQty[productName]) + '<br>';
    }
    
    // Set the HTML content in the product_summary element
    $('.product_summary').html(productSummaryHtml);
}

// product_summary


function formatNumber(number) {
    if (typeof number !== 'number') {
        number = parseFloat(number); // Or use Number(number)
    }

    if (!isNaN(number)) {
        return number.toFixed(3); // Or however many decimals you need
    } else {
        return 'Invalid number';
    }
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
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: 'delete',
        url: url,
        data: {is_edit},
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
            url: '/petro/settlement/get_balance_stock_by_id/' + item_id,
            data : {
                store_id : $("select#store_id option").filter(":selected").val(), 
                location_id : $("select#location_id option").filter(":selected").val()
            },
            success: function (result) {
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
$('.btn_other_sale').click(function (e) {
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
        url: '/petro/settlement/save-other-sale',
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

//other income tab
var other_income_total = parseFloat($('#other_income_total').val().replace(',', ''));
var sub_total = 0.0;
var other_income_code = null;
var other_income_product_name = null;
var other_income_price = 0.0;

$('.btn_other_income').click(function () {
    var other_income_product_id = $('#other_income_product_id').val();
    var other_income_qty = $('#other_income_qty').val();
    var other_income_reason = $('#other_income_reason').val();
    var other_income_id = null;
    
    var otherIncomePrice = $('#other_income_price').val();
    var priceWithoutCommas = otherIncomePrice.replace(/,/g, '');

    other_income_price = parseFloat(priceWithoutCommas);

    var other_income_amount = parseFloat(other_income_qty) * other_income_price;
    var is_edit = $("#is_edit").val() ?? 0;

    let other_income_total = parseFloat($('#other_income_total').val().replace(',', ''));
    other_income_total = other_income_total + other_income_amount;
    $('#other_income_total').val(other_income_total);
    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-other-income',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            product_id: other_income_product_id,
            qty: other_income_qty,
            price: other_income_price,
            other_income_reason: other_income_reason,
            sub_total: other_income_amount,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
                return false;
            }

            other_income_id = result.other_income_id;
            other_income_sub_total = __number_f(other_income_amount);
            $('#other_income_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    other_income_product_name +
                    `</td>
                    <td>` +
                    __number_f(other_income_qty) +
                    `</td>
                    <td>` +
                    other_income_reason +
                    `</td>
                    <td>` +
                    other_income_sub_total +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_other_income" data-href="/petro/settlement/delete-other-income/` +
                    other_income_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_income_fields').val('').trigger('change');
            calculate_payment_tab_total();
        },
    });
});
$('#other_income_product_id').change(function () {
    let item_id = $(this).val();
    $.ajax({
        method: 'get',
        url: '/petro/settlement/get_balance_stock/' + item_id,
        data: {},
        success: function (result) {
            other_income_code = result.code;
            other_income_product_name = result.product_name;
            other_income_price = result.price;
            $('#other_income_price').val(__number_f(other_income_price));
        },
    });
});

$(document).on('click', '.delete_other_income', function () {
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
                let other_income_total =
                    parseFloat($('#other_income_total').val()) - parseFloat(result.sub_total);
                other_income_total_text = __number_f(
                    other_income_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.other_income_total').text(other_income_total_text);
                $('#other_income_total').val(other_income_total);
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//customer_payment tab
var customer_payment_total = parseFloat($('#customer_payment_total').val().replace(',', ''));
var sub_total = 0.0;

$('.btn_customer_payment').click(function () {
    var customer_payment_amount = parseFloat($('#customer_payment_amount').val());
    var customer_name = $('#customer_payment_customer_id :selected').text();
    var payment_method = $('#customer_payment_payment_method').val();
    var bank_name = $('#customer_payment_bank_name').val();
    var cheque_date = $('#customer_payment_cheque_date').val();
    var cheque_number = $('#customer_payment_cheque_number').val();
    var post_dated_cheque = $('#customer_payment_post_dated_cheque').val();
    var customer_payment_id = null;

    let customer_payment_total = parseFloat($('#customer_payment_total').val().replace(',', ''));
    customer_payment_total = customer_payment_total + customer_payment_amount;
    $('#customer_payment_total').val(customer_payment_total);
    var is_edit = $("#is_edit").val() ?? 0;

    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-customer-payment',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),

            customer_id: $('#customer_payment_customer_id').val(),
            payment_method: $('#customer_payment_payment_method').val(),
            bank_name: bank_name,
            cheque_date: cheque_date,
            cheque_number: cheque_number,
            amount: customer_payment_amount,
            sub_total: customer_payment_amount,
            is_edit: is_edit,
            post_dated_cheque: post_dated_cheque
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
                return false;
            }

            customer_payment_id = result.customer_payment_id;
            customer_payment_amount = __number_f(customer_payment_amount);
            $('#customer_payment_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    customer_name +
                    `</td>
                    <td>` +
                    payment_method +
                    `</td>
                    <td>` +
                    bank_name +
                    `</td>
                    <td>` +
                    cheque_date +
                    `</td>
                    <td>` +
                    cheque_number +
                    `</td>
                    <td>` +
                    customer_payment_amount +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_customer_payment" data-href="/petro/settlement/delete-customer-payment/` +
                    customer_payment_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.customer_payment_fields').val('').trigger('change');
            calculate_payment_tab_total();
        },
    });
});

$('#customer_payment_payment_method').change(function () {
    if ($(this).val() == 'cheque') {
        $('.cheque_divs').removeClass('hide');
    } else {
        $('.cheque_divs').addClass('hide');
    }
});

$(document).on('click', '.delete_customer_payment', function () {
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
                let customer_payment_total =
                    parseFloat($('#customer_payment_total').val()) - parseFloat(result.amount);
                customer_payment_total_text = __number_f(
                    customer_payment_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.customer_payment_total').text(customer_payment_total_text);
                $('#customer_payment_total').val(customer_payment_total);
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
    let other_income_totals = parseFloat($('#other_income_total').val());
    let customer_payment_totals = parseFloat($('#customer_payment_total').val());

    let all_totals =
        meter_sale_totals + other_sale_totals + other_income_totals + customer_payment_totals;

    $('.payment_meter_sale_total').text(
        __number_f(meter_sale_totals, false, false, __currency_precision)
    );
    $('.payment_other_sale_total').text(
        __number_f(other_sale_totals, false, false, __currency_precision)
    );
    $('.payment_other_income_total').text(
        __number_f(other_income_totals, false, false, __currency_precision)
    );
    $('.payment_customer_payment_total').text(
        __number_f(customer_payment_totals, false, false, __currency_precision)
    );
    $('.meter_sale_total').text(__number_f(meter_sale_totals, false, false, __currency_precision));
    $('.other_sale_total').text(__number_f(other_sale_totals, false, false, __currency_precision));
    $('.other_income_total').text(
        __number_f(other_income_totals, false, false, __currency_precision)
    );
    $('.customer_payment_total').text(
        __number_f(customer_payment_totals, false, false, __currency_precision)
    );

    $('#payment_due').text(__number_f(all_totals, false, false, __currency_precision));
}

// Added by Muneeb Ahmad for Store Dropdown - Task#3454
$(document).on('change','#location_id', function(){
    let location_id = $(this).val();
    console.log('change#location_id');
    $.ajax({
        method: 'get',
        url: "/petro/get-stores-by-id",
        data: { location_id },
        contentType: 'html',
        success: function(result) {
            $('#store_id').empty().append(result);
        },
    });
});
$(document).on('change','#store_id', function(){
    let location_id = $('#location_id').val();
    let store_id = $(this).val();
    let tab = 'any';
    if($('#other_sale_tab').length && $('#other_sale_tab').hasClass('active')){
        tab = 'other_sat';
    }
    $.ajax({
        method: 'get',
        url: "/petro/get-products-by-store-id",
        data: { 'location_id' : location_id, 'store_id' : store_id ,'tab':tab},
        contentType: 'html',
        success: function(result) {
            $('#item').empty().append(result);
            if(store_id !== null || store_id !==""){
                document.getElementById("item").disabled = false;
            }
        },
    });
});

function updateCancelMeterForm(url, data){
    $.ajax({
        method: 'get',
        url: url,
        data: data,
        success: function (result) {
            if (result.success) {
                $('#meter-sale-form-block').html(result.html);
                $('#pump_no').select2();
                $('#meter_sale_discount_type').select2();
            } else {
                toastr.error(result.msg);
            }
        },
    });
}
$(document).on('click', '.get_meter_sale_from', function () {
    url = $(this).data('href');
    data = {action_type:'edit'};
    updateCancelMeterForm(url, data);
});

$(document).on('click', '.btn_meter_sale_cancel', function () {
    url = $(this).data('href');
    data = {action_type:'cancel'};
    updateCancelMeterForm(url, data);
});

$(document).on('click', '.btn_update_meter_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    var is_edit = $("#is_edit").val() ?? 0;
    pump_id = $('#pump_no').val();

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
    
    $.ajax({
        method: 'post',
        url: url,
        data: {
            pump_id: pump_id,
            starting_meter: $('#pump_starting_meter').val(),
            closing_meter: $('#pump_closing_meter').val(),
            product_id: product_id,
            price: $('#meter_sale_unit_price').val(),
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
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
                return false;
            }
            toastr.success(result.msg);

            $('#meter_sale_total').val(meter_sale_total);
            $('#pump_no')
                .find('option[value=' + pump_id + ']')
                .remove();

            meter_sale_id = result.meter_sale_id;
            settlement_id = result.settlement_id;
            
            meter_sale_totals = __number_f(sub_total);
            var with_disc = __number_f(sub_total + meter_sale_discount);
            
            sold_qty = (sold_qty);
            tr.replaceWith(
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
                        __number_f(price) +
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
                    <td>`+
					`<button class="btn btn-xs btn-primary get_meter_sale_from" data-type="edit" data-href="/petro/settlement/get-meter-sale-form/`+
                    meter_sale_id +
                    `"><i class="fa fa-edit"></i></button>`+
                    `<button class="btn btn-xs btn-danger delete_meter_sale" data-href="/petro/settlement/delete-meter-sale/` +
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