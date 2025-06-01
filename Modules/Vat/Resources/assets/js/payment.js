$(document).on("click", ".cash_add", function () {
    if ($("#cash_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cash_customer_id = $("#cash_customer_id").val();
    var cash_amount = $("#cash_amount").val();
    var settlement_no = $("#settlement_no").val();
    var customer_name = $("#cash_customer_id :selected").text();
    var cash_note = $("#cash_note").val();
    
    
    $.ajax({
        method: "post",
        url: "/vat-module/settlement/payment/save-cash-payment",
        data: {
            customer_id: cash_customer_id,
            amount: cash_amount,
            settlement_no: settlement_no,
            note: cash_note
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                if ($('#calculate_cash').is(':checked')) {
                    $(".denoms_totals").hide();
                    $(".cash_to_disable").hide();
                    $("#cash_amount").prop('readonly', true);
                } else {
                  $(".denoms_totals").show();
                  $(".cash_to_disable").show();
                  $("#cash_amount").prop('readonly', false);
                }
                
                console.log('here is cash add data ==>', result);
                settlement_cash_payment_id = result.settlement_cash_payment_id;
                add_payment(cash_amount);
                $("#cash_table tbody").prepend(
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
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/vat-module/settlement/payment/delete-cash-payment/` +
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
$(document).on("click", ".delete_cash_payment", function () {
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
                calculateTotal("#cash_table", ".cash_amount", ".cash_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

//card payments
$(document).on("click", ".card_add", function () {
    if ($("#card_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    
    
    var card_customer_id = $("#card_customer_id").val();
    var customer_name = $("#card_customer_id :selected").text();
    var card_amount = $("#card_amount").val();
    var settlement_no = $("#settlement_no").val();
    var card_type = $("#card_type :selected").text();
    var card_type_id = $("#card_type").val();
    var card_number = $("#card_number").val();
    var card_note = $("#card_note").val();
    var slip_no = $("#slip_no").val();
    
    if(card_type_id == null || card_type_id == "" || card_type_id == "undefined"){
        toastr.error("Please select card type");
        return false;
    }
    
    $.ajax({
        method: "post",
        url: "/vat-module/settlement/payment/save-card-payment",
        data: {
            customer_id: card_customer_id,
            amount: card_amount,
            card_type: card_type_id,
            card_number: card_number,
            settlement_no: settlement_no,
            note: card_note,
            slip_no: slip_no
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_card_payment_id = result.settlement_card_payment_id;
                add_payment(card_amount);
                $("#card_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td>` +
                        card_type +
                        `</td>
                        <td>` +
                        card_number +
                        `</td>
                        <td class="card_amount">` +
                        __number_f(card_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                           slip_no +
                        `</td>
                        <td>` +
                           card_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="/vat-module/settlement/payment/delete-card-payment/` +
                        settlement_card_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".card_fields").val("");
                $(".cash_fields").val("");
                calculateTotal("#card_table", ".card_amount", ".card_total");
            }
        },
    });
});
$(document).on("click", ".delete_card_payment", function () {
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
                calculateTotal("#card_table", ".card_amount", ".card_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

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
    
    var settlement_no = $("#settlement_no").val();
    var order_date = $("#order_date").val();
    var order_number = $("#order_number").val();
    
    var credit_sale_price = __read_number($("#unit_price"));
    var credit_unit_discount = __read_number($("#unit_discount")) ?? 0;
    var credit_sale_qty = __read_number($("#credit_sale_qty")) ?? 0;
    var credit_total_amount = __read_number($("#credit_total_amount")) ?? 0;
    var credit_total_discount = __read_number($("#credit_discount_amount")) ?? 0;
    var credit_sub_total = __read_number($("#credit_sale_amount")) ?? 0;
    
    
    var credit_note = $("#credit_note").val();
    
    $.ajax({
        method: "post",
        url: "/vat-module/settlement/payment/save-credit-sale-payment",
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
            note: credit_note
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
                        order_number +
                        `</td>
                        <td>` +
                        order_date +
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
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/vat-module/settlement/payment/delete-credit-sale-payment/` +
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
                calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
                calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
                
            }
        },
    });
});

$(document).on("change", "#credit_sale_amount,#credit_sale_qty", function () {
    let price = __read_number($("#unit_price")) ?? 0;
    let qty = __read_number($("#credit_sale_qty")) ?? 0;
    let total_amount = price * qty; 
    
    let amount = __read_number($("#credit_sale_amount")) ?? 0;
    let total_discount = total_amount - amount;
    let unit_discount = total_discount / qty;
    
    __write_number($("#credit_total_amount"), total_amount);
    __write_number($("#unit_discount"), unit_discount);
    __write_number($("#credit_discount_amount"),total_discount);
});
$(document).on("change", "#credit_sale_product_id", function () {
    if ($(this).val()) {
        $.ajax({
            method: "get",
            url: "/vat-module/settlement/payment/get-product-price",
            data: { product_id: $(this).val() },
            success: function (result) {
                $("#unit_price").val(result.price);
                $("#credit_sale_qty").change();
                
                $("#credit_sale_amount").attr("disabled", false);
                $("#credit_sale_qty").attr("disabled", false);
            },
        });
    } else {
        $("#credit_sale_amount").attr("disabled", true);
        $("#credit_sale_qty").attr("disabled", true);
    }
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
                calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
                calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
            } else {
                toastr.error(result.msg);
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
  
  if($('#enable_cash_denoms').is(':checked')){
      if(bal == 0 && total_balance == 0){
          $("#settlement_save_btn").removeClass("hide");
      }else{
          $("#settlement_save_btn").addClass("hide");
      }
  }else{
      if(total_balance == 0){
          $("#settlement_save_btn").removeClass("hide");
      }else{
          $("#settlement_save_btn").addClass("hide");
      }
  }
      
  
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
    if (total_balance === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
    calculateDenoms(add_amount);
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
    if (total_balance.toFixed(__currency_precision) === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
    calculateDenoms((0-delete_amount));
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
//save settlement
$(document).on("click", "#settlement_save_btn", function () {
    $(this).attr("disabled", "disabled");
    var url = $("#settlement_form").attr("action");
    var settlement_no = $("#settlement_no").val();
    
    var qtyArray = [];
    $('.denom_qty').each(function() {
      var qtyValue = $(this).val();
      qtyArray.push(qtyValue); // Add the value to the array
    });
    
    var denomArray = [];
    $('.denom_value').each(function() {
      var denomValue = $(this).val();
      denomArray.push(denomValue); // Add the value to the array
    });
    
    var denoEnabled = 0;
    
    if($('#enable_cash_denoms').is(':checked')) {
        denomEnabled = 1;
    } else {
      denomEnabled = 0;
    }
    
    
    
    
    $.ajax({
        method: "post",
        url: url,
        data: { settlement_no: settlement_no,denom_qty: qtyArray, denom_value: denomArray,denom_enabled : denomEnabled },
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
        
        calculateDenoms();
    }, 1000);
});
