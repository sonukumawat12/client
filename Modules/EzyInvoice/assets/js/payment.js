//Add Invoice
$(document).on("click", ".credit_sale_add", function () {
    if ($("#credit_sale_amount").val() === "") {
        toastr.error("Please enter amount");
        return false;
    }
    var credit_sale_customer_id = $("#credit_sale_customer_id").val();
    var customer_name = $("#credit_sale_customer_id :selected").text();
    var credit_sale_product_id = $("#credit_sale_product_id").val();
    console.log($("#credit_sale_product_id").val());
    var credit_sale_product_name = $("#credit_sale_product_id :selected").text();
    if ($("#customer_reference_one_time").val() !== "" && $("#customer_reference_one_time").val() !== null && $("#customer_reference_one_time").val() !== undefined) {
        var customer_reference = $("#customer_reference_one_time").val();
    } else {
        customer_reference = $("#customer_reference").val();
    }
    var settlement_no = $("#settlement_no").val();
    var order_date = $("#order_date").val();
    var order_number = $("#order_number").val();
    var credit_sale_price = __read_number($("#unit_price"));
    var credit_sale_qty_hidden = $("#credit_sale_qty_hidden").val();
    var credit_sale_amount_hidden = $("#credit_sale_amount_hidden").val();
    var outstanding = $(".current_outstanding").text();
    var credit_limit = $(".credit_limit").text();
    var credit_note = $("#credit_note").val();
    
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
            qty: credit_sale_qty_hidden,
            amount: credit_sale_amount_hidden,
            outstanding: outstanding,
            credit_limit: credit_limit,
            customer_reference: customer_reference,
            note: credit_note
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;
                add_payment(credit_sale_amount_hidden);
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
                        __number_f(credit_sale_qty_hidden, false, false, __currency_precision) +
                        `</td>
                        <td class="credit_sale_amount">` +
                        __number_f(credit_sale_amount_hidden, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                       credit_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/ezy-invoice/invoices/payment/delete-credit-sale-payment/` +
                        settlement_credit_sale_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $("#customer_reference_one_time").val("").trigger("change");
                $(".credit_sale_fields").val("");
                $(".cash_fields").val("");
                $("#order_number").val(order_number);
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
            }
        },
    });
});
$(document).on("change", "#credit_sale_qty, #unit_price", function () {
    let price = __read_number($("#unit_price"));
    let qty = __read_number($("#credit_sale_qty"));
    let amount = price * qty;
    __write_number($("#credit_sale_amount"), amount);
    $("#credit_sale_qty_hidden").val(qty);
    $("#credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#credit_sale_amount", function () {
    let price = __read_number($("#unit_price"));
    let amount = __read_number($(this));
    let qty = amount / price;
    __write_number($("#credit_sale_qty"), qty);
    $("#credit_sale_qty_hidden").val(qty);
    $("#credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#credit_sale_product_id", function () {
    if ($(this).val()) {
        $.ajax({
            method: "get",
            url: "/ezy-invoice/invoices/payment/get-product-price",
            data: { product_id: $(this).val() },
            success: function (result) {
                $("#unit_price").val(result.price);
                $("#credit_sale_qty").change();
                $("#credit_sale_amount").attr("disabled", false);
                $("#credit_sale_qty").attr("disabled", true);
            },
        });
    } else {
        $("#credit_sale_amount").attr("disabled", true);
        $("#credit_sale_qty").attr("disabled", true);
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
                $("#customer_reference").val($("#customer_reference option:eq(1)").val()).trigger("change");
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
                delete_payment(this_amount);
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});


// edit Invoice
$(document).on("click", ".edit_credit_sale_add", function () {
    if ($("#edit_credit_sale_amount").val() === "") {
        toastr.error("Please enter amount");
        return false;
    }
    var credit_sale_customer_id = $("#edit_credit_sale_customer_id").val();
    var customer_name = $("#edit_credit_sale_customer_id :selected").text();
    var credit_sale_product_id = $("#edit_credit_sale_product_id").val();
    console.log($("#edit_credit_sale_product_id").val());
    var credit_sale_product_name = $("#edit_credit_sale_product_id :selected").text();
    if ($("#edit_customer_reference_one_time").val() !== "" && $("#edit_customer_reference_one_time").val() !== null && $("#edit_customer_reference_one_time").val() !== undefined) {
        var customer_reference = $("#edit_customer_reference_one_time").val();
    } else {
        customer_reference = $("#edit_customer_reference").val();
    }
    var settlement_no = $("#edit_settlement_no").val();
    var order_date = $("#edit_order_date").val();
    var order_number = $("#edit_order_number").val();
    var credit_sale_price = __read_number($("#edit_unit_price"));
    var credit_sale_qty_hidden = $("#edit_credit_sale_qty_hidden").val();
    var credit_sale_amount_hidden = $("#edit_credit_sale_amount_hidden").val();
    var outstanding = $(".edit_current_outstanding").text();
    var credit_limit = $(".edit_credit_limit").text();
    var credit_note = $("#edit_credit_note").val();
    
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
            qty: credit_sale_qty_hidden,
            amount: credit_sale_amount_hidden,
            outstanding: outstanding,
            credit_limit: credit_limit,
            customer_reference: customer_reference,
            note: credit_note
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;
                add_payment(credit_sale_amount_hidden);
                $("#edit_credit_sale_table tbody").prepend(
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
                        __number_f(credit_sale_qty_hidden, false, false, __currency_precision) +
                        `</td>
                        <td class="edit_credit_sale_amount">` +
                        __number_f(credit_sale_amount_hidden, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                       credit_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger edit_delete_credit_sale_payment" data-href="/ezy-invoice/invoices/payment/delete-credit-sale-payment/` +
                        settlement_credit_sale_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $("#edit_customer_reference_one_time").val("").trigger("change");
                $(".edit_credit_sale_fields").val("");
                $(".edit_cash_fields").val("");
                $("#edit_order_number").val(order_number);
                calculateTotal("#edit_credit_sale_table", ".edit_credit_sale_amount", ".edit_credit_sale_total");
            }
        },
    });
});
$(document).on("change", "#edit_credit_sale_qty, #edit_unit_price", function () {
    let price = __read_number($("#edit_unit_price"));
    let qty = __read_number($("#edit_credit_sale_qty"));
    let amount = price * qty;
    __write_number($("#edit_credit_sale_amount"), amount);
    $("#edit_credit_sale_qty_hidden").val(qty);
    $("#edit_credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#edit_credit_sale_amount", function () {
    let price = __read_number($("#edit_unit_price"));
    let amount = __read_number($(this));
    let qty = amount / price;
    __write_number($("#edit_credit_sale_qty"), qty);
    $("#edit_credit_sale_qty_hidden").val(qty);
    $("#edit_credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#edit_credit_sale_product_id", function () {
    if ($(this).val()) {
        $.ajax({
            method: "get",
            url: "/ezy-invoice/invoices/payment/get-product-price",
            data: { product_id: $(this).val() },
            success: function (result) {
                $("#edit_unit_price").val(result.price);
                $("#edit_credit_sale_qty").change();
                $("#edit_credit_sale_amount").attr("disabled", false);
                $("#edit_credit_sale_qty").attr("disabled", true);
            },
        });
    } else {
        $("#edit_credit_sale_amount").attr("disabled", true);
        $("#edit_credit_sale_qty").attr("disabled", true);
    }
});
$(document).on("change", "#edit_credit_sale_customer_id", function () {
    $.ajax({
        method: "get",
        url: "/ezy-invoice/invoices/payment/get-customer-details/" + $(this).val(),
        data: {},
        success: function (result) {
            $(".edit_current_outstanding").text(result.total_outstanding);
            $(".edit_credit_limit").text(result.credit_limit);
            $("#edit_customer_reference").empty();
            $("#edit_customer_reference").append(`<option selected="selected" value="">Please Select</option>`);
            result.customer_references.forEach(function (ref, i) {
                $("#edit_customer_reference").append(`<option value="` + ref.reference + `">` + ref.reference + `</option>`);
                $("#edit_customer_reference").val($("#customer_reference option:eq(1)").val()).trigger("change");
            });
        },
    });
});
$(document).on("click", ".edit_delete_credit_sale_payment", function () {
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
                delete_payment(this_amount);
                calculateTotal("#edit_credit_sale_table", ".edit_credit_sale_amount", ".edit_credit_sale_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on("click", "#edit_settlement_save_btn", function () {
    $(this).attr("disabled", "disabled");
    var url = $("#edit_settlement_form").attr("action");
    var settlement_no = $("#edit_settlement_no").val();
    
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
                newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + "</body></html>");
                newWin.document.close();
            }
        },
    });
});


function calculateDenoms(amount = 0){
  var grand_total = 0;
  $('.denom_amt').each(function() {
    var amt = $(this).val();
    
    amt = parseFloat(amt.replace(/,/g, ""))
    
    if (!isNaN(amt)) {
      grand_total += amt;
    }
  });
  
  
  if ($('#calculate_cash').is(':checked')) {
        $("#cash_amount").val(grand_total);
        $("#cash_amount").prop('readonly', true);
    }else{
        $("#cash_amount").prop('readonly', false);
    }
  
  $('.denom_total').val(grand_total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
  
  var cashtotal = $(".cash_total").text();
  var bal = parseFloat(cashtotal.replace(/,/g, "")) - grand_total + amount;
  
  total_balance = parseFloat($("#total_balance").val().replace(",", ""));
  
  $(".denom_bal").val(bal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}
          
function add_payment(add_amount) {
    add_amount = parseFloat(add_amount);
    total_balance = parseFloat($("#total_balance").val().replace(",", ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance - add_amount;
    total_paid = total_paid + add_amount;
    $("#total_balance").val(__number_f(total_balance, false, false, __currency_precision));
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
    show_hide_excess_shortage_tab();
    calculateDenoms(add_amount);
}
function show_hide_excess_shortage_tab() {
    var totbal = $("#total_balance").val();
    total_balance = parseFloat(totbal.replace(",", ""));
    $(document).find('li.disabled a').off('click');
    $('#excess_amount').prop('disabled', false);
    if(total_balance > 0){
        $('#excess_add').prop('disabled', true);
        // $(".excess_tab").click();
        $(".excess_tab").parents("li:first").addClass("disabled");
        $(".shortage_tab").parents("li:first").removeClass("disabled");
    }else if(total_balance < 0){
        $('#shortage_add').prop('disabled', true);
        $('#shortage_amount').prop('disabled', true);
        // $(".shortage_tab").click();
        $(".shortage_tab").parents("li:first").addClass("disabled");
        $(".excess_tab").parents("li:first").removeClass("disabled");
    }else{
        $('#excess_add').prop('disabled', false);
        $(".excess_tab").parents("li:first").removeClass("disabled");
        $(".shortage_tab").parents("li:first").removeClass("disabled");
        $('#shortage_add').prop('disabled', false);
        $('#shortage_amount').prop('disabled', false);
    }
    $(document).find('li.disabled a').on('click', function(e) { e.preventDefault(); return false; });
    $(document).find('#settlement_form .settlement_tabs li a').on('click', function(e) {
        setTimeout(() => {
           $('#excess_amount').prop('disabled', false);
        }, 500);
        localStorage.setItem("settlement_tabs", $(this).attr('href'));
    });
}
function delete_payment(delete_amount) {
    delete_amount = parseFloat(delete_amount);
    console.log(delete_amount);
    total_balance = parseFloat($("#total_balance").val().replace(",", ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance + delete_amount;
    total_paid = total_paid - delete_amount;
    $("#total_balance").val(total_balance);
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
    show_hide_excess_shortage_tab();
    calculateDenoms((0-delete_amount));
}
function calculateTotal(table_name, class_name_td, output_element) {
    let total = 0.0;
    $(table_name + " tbody")
        .find(class_name_td)
        .each(function () {
            total += parseFloat($(this).text().replace(",", ""));
        });
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
                newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + "</body></html>");
                newWin.document.close();
            }
        },
    });
});
function myFloatNumber(i) {
    var value = Math.floor(i * 100) / 100;
    return value;
}
$(document).on("change", "#expense_category", function () {
    $.ajax({
        method: "get",
        url: "/get-expense-account-category-id/" + $(this).val(),
        data: {},
        success: function (result) {
            $("#expense_account").empty().append(`<option value="${result.expense_account_id}" selected>${result.name}</option>`);
        },
    });
});

//Check Active Tab
$(document).on("click", "#add_payment", function () {
    var myVar = setInterval(() => {
        if($('.add_payment').hasClass('in')){
            if($("#cash_tab").hasClass("active")){
                $("#cash_amount").focus();
            }
        }
        if ($("#cash_amount").is(":focus")) {
            clearInterval(myVar);
        }
        show_hide_excess_shortage_tab();
        calculateDenoms();
    }, 1000);
});
$(document).on("click", ".tabs", function () {
    var tab_id = $(this).attr("href");

    if(tab_id == "#expense_tab"  && ($("#expense_tab").hasClass("active")) && ($(".total_balance").val()) <= 0){
        $('#expense_category').focus();
        $('.excess_amount').prop('disabled', false);
    }else if(tab_id == "#credit_sales_tab"  && ($("#credit_sales_tab").hasClass("active"))){
        $('#order_number').focus();
    }else{
        $(tab_id+' :input:enabled:visible:first').focus();
        $('.excess_amount').prop('disabled', true);
    }
});

