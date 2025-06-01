function calculateDenoms(amount = 0) {
    var grand_total = 0;
    $('.denom_amt').each(function () {
        var amt = $(this).val();

        amt = parseFloat(amt)

        if (!isNaN(amt)) {
            grand_total += amt;
        }
    });


    if ($('#calculate_cash').is(':checked')) {
        $("#cash_amount").val(grand_total);
        $("#cash_amount").prop('readonly', true);
    } else {
        $("#cash_amount").prop('readonly', false);
    }

    $('.denom_total').val(grand_total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    var cashtotal = $(".cash_total").text();
    var bal = parseFloat(cashtotal) - grand_total + amount;

    total_balance = parseFloat($("#total_balance").val());

   /* if ($('#enable_cash_denoms').is(':checked')) {
        if (bal == 0 && total_balance == 0) {
            $("#settlement_save_btn").removeClass("hide");
        } else {
            $("#settlement_save_btn").addClass("hide");
        }
    } else {
        if (total_balance == 0) {
            $("#settlement_save_btn").removeClass("hide");
        } else {
            $("#settlement_save_btn").addClass("hide");
        }
    }*/


    $(".denom_bal").val(bal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}

function add_payment(add_amount) {
    add_amount = parseFloat(add_amount);
    total_balance = parseFloat($("#total_balance").val());
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance - add_amount;
    total_paid = total_paid + add_amount;
    $("#total_balance").val(__number_f(total_balance, false, false, __currency_precision));
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
   /* if (total_balance === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }*/
    show_hide_excess_shortage_tab();
    calculateDenoms(add_amount);
}
function show_hide_excess_shortage_tab() {
    var totbal = $("#total_balance").val();
    total_balance = parseFloat(totbal);
    $(document).find('li.disabled a').off('click');
    $('#excess_amount').prop('disabled', false);
    if (total_balance > 0) {
        $('#excess_add').prop('disabled', true);
        // $(".excess_tab").click();
        $(".excess_tab").parents("li:first").addClass("disabled");
        $(".shortage_tab").parents("li:first").removeClass("disabled");
    } else if (total_balance < 0) {
        $('#shortage_add').prop('disabled', true);
        $('#shortage_amount').prop('disabled', true);
        // $(".shortage_tab").click();
        $(".shortage_tab").parents("li:first").addClass("disabled");
        $(".excess_tab").parents("li:first").removeClass("disabled");
    } else {
        $('#excess_add').prop('disabled', false);
        $(".excess_tab").parents("li:first").removeClass("disabled");
        $(".shortage_tab").parents("li:first").removeClass("disabled");
        $('#shortage_add').prop('disabled', false);
        $('#shortage_amount').prop('disabled', false);
    }
    $(document).find('li.disabled a').on('click', function (e) { e.preventDefault(); return false; });
    $(document).find('#settlement_form .settlement_tabs li a').on('click', function (e) {
        setTimeout(() => {
            $('#excess_amount').prop('disabled', false);
        }, 500);
        localStorage.setItem("settlement_tabs", $(this).attr('href'));
    });
}
function delete_payment(delete_amount) {
    delete_amount = parseFloat(delete_amount);
    console.log(delete_amount);
    total_balance = parseFloat($("#total_balance").val());
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance + delete_amount;
    total_paid = total_paid - delete_amount;
    $("#total_balance").val(total_balance);
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
    if (total_balance.toFixed(__currency_precision) === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
    show_hide_excess_shortage_tab();
    calculateDenoms((0 - delete_amount));
}
function calculateTotal(table_name, class_name_td, output_element) {
    let total = 0.0;
    $(table_name + " tbody")
        .find(class_name_td)
        .each(function () {
            total += parseFloat(__number_uf($(this).text()));
        });
    $(output_element).text(__number_f(total, false, false, __currency_precision));
}
//credit_sale payments
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
        url: "/ezy-invoice/invoices/payment/save-credit-sale-payment",
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
                add_payment(credit_total_amount - credit_total_discount);
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
$(document).on('input', '#credit_total_amount', function () {
    $("#credit_sale_qty").attr('disabled', true);

    let price = __read_number($("#unit_price")) ?? 0;
    let total_amount = __read_number($("#credit_total_amount")) ?? 0;
    let qty = total_amount / price;

    let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
    let unit_discount = total_discount / qty;
    let amount = total_amount - total_discount

    __write_number($("#credit_sale_amount"), amount);
    __write_number($("#unit_discount"), unit_discount);
    __write_number_without_decimal_format($("#credit_sale_qty"), qty);


});

$(document).on('input', '#credit_sale_qty', function () {
    $("#credit_total_amount").attr('disabled', true);

    let price = __read_number($("#unit_price")) ?? 0;
    let qty = __read_number($("#credit_sale_qty")) ?? 0;
    let total_amount = price * qty;

    let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
    let unit_discount = total_discount / qty;
    let amount = total_amount - total_discount

    __write_number($("#credit_sale_amount"), amount);
    __write_number($("#unit_discount"), unit_discount);
    __write_number($("#credit_total_amount"), total_amount);

});

$(document).on("change", "#credit_discount_amount, #unit_price", function () {
    let price = __read_number($("#unit_price")) ?? 0;
    let qty_check = __read_number($("#credit_sale_qty")) ?? 0;

    var qty = 0;
    var total_amount = 0;

    if (qty_check > 0) {
        qty = __read_number($("#credit_sale_qty")) ?? 0;
        total_amount = price * qty;
        __write_number($("#credit_total_amount"), total_amount);
    } else {
        total_amount = __read_number($("#credit_total_amount")) ?? 0;
        qty = total_amount / price;
        __write_number_without_decimal_format($("#credit_sale_qty"), qty);
    }



    let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
    let unit_discount = total_discount / qty;
    let amount = total_amount - total_discount

    __write_number($("#unit_discount"), unit_discount);
    __write_number($("#credit_sale_amount"), amount);

});
$(document).on("change", "#credit_sale_product_id", function () {
    if ($(this).val()) {
        $.ajax({
            method: "get",
            url: "/ezy-invoice/invoices/payment/get-product-price",
            data: { product_id: $(this).val() },
            success: function (result) {
                $("#unit_price").val(result.price);
                $("#unit_price").trigger('change');

                $("#credit_total_amount").attr("disabled", false);
                $("#credit_sale_qty").attr("disabled", false);
                if ($("#manual_discount").val() == 1) {
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
        url: "/ezy-invoice/invoices/payment/get-customer-details/" + $(this).val(),
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
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});



function calculateTotal(table_name, class_name_td, output_element) {
    let total = 0.0;
    $(table_name + " tbody")
        .find(class_name_td)
        .each(function () {
            total += parseFloat($(this).text());
        });
   /* if (total == 0) {
        $("#settlement_save_btn").hide()
    } else {
        $("#settlement_save_btn").show();
    } */
    $("#settlement_save_btn").show();
    $(output_element).text(__number_f(total, false, false, __currency_precision));
}

//save settlement
$(document).on("click", "#settlement_save_btn", function () {
    $(this).attr("disabled", "disabled"); 
    var url = $("#settlement_form").attr("action");
    var settlement_no = $("#settlement_no").val();

    $.ajax({
        method: "post",
        url: url,
        data: { settlement_no: settlement_no },
        success: function (result) {
            if (result.success === 0) {
                toastr.error(result.msg);
            } else {
                $("#settlement_print").html(result);
                
                var divToPrint = document.getElementById("settlement_print");
                var newWin = window.open("", "_self");
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">' + result + "</body></html>");
                newWin.document.close(); 
                
               
            }
        },
    });
});
function myFloatNumber(i) {
    var value = Math.floor(i * 100) / 100;
    return value;
}

