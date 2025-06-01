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
    var is_edit = $("#is_edit").val() ?? 0;
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cash-payment",
        data: {
            customer_id: cash_customer_id,
            amount: cash_amount,
            settlement_no: settlement_no,
            note: cash_note,
            is_edit: is_edit
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
$(document).on("click", ".delete_cash_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
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


$(document).on("click", ".customer_loans_add", function () {
    if ($("#customer_loans_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cash_customer_id = $("#customer_loans_customer_id").val();
    var cash_amount = $("#customer_loans_amount").val();
    var settlement_no = $("#settlement_no").val();
    var customer_name = $("#customer_loans_customer_id :selected").text();
    var is_edit = $("#is_edit").val() ?? 0;
    
    var note = $("#customer_loans_note").val();
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-customer-loans",
        data: {
            customer_id: cash_customer_id,
            amount: cash_amount,
            settlement_no: settlement_no,
            is_edit: is_edit,
            note: note
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                
                settlement_customer_loan_id = result.settlement_customer_loan_id;
                add_payment(cash_amount);
                $("#customer_loans_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td class="customer_loan_amount">` +
                        __number_f(cash_amount, false, false, __currency_precision) +
                        `</td>
                        
                        <td>` +
                             note +
                        `</td>
                        
                        <td><button type="button" class="btn btn-xs btn-danger delete_customer_loans_payment" data-href="/petro/settlement/payment/delete-customer-loans/` +
                        settlement_customer_loan_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".customer_loans_field").val("");
                calculateTotal("#customer_loans_table", ".customer_loan_amount", ".customer_loans_total");
            }
        },
    });
});
$(document).on("click", ".delete_customer_loans_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#customer_loans_table", ".customer_loan_amount", ".customer_loans_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});


$(document).on("click", ".loan_payments_add", function () {
    if ($("#loan_payments_amount").val() === "") {
        toastr.error("Please enter amount");
        return false;
    }
    
    if ($("#loan_payments_bank").val() === "") {
        toastr.error("Please choose loan account");
        return false;
    }
    
    
    var loan_payments_amount = $("#loan_payments_amount").val();
    var settlement_no = $("#settlement_no").val();
    var loan_payments_bank = $("#loan_payments_bank").val();
    
    var bank_name = $("#loan_payments_bank :selected").text();
    var loan_payments_note = $("#loan_payments_note").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-loan-payment",
        data: {
            loan_account: loan_payments_bank,
            amount: loan_payments_amount,
            settlement_no: settlement_no,
            note: loan_payments_note,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                
                settlement_loan_payment_id = result.settlement_loan_payment_id;
                add_payment(loan_payments_amount);
                $("#loan_payments_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        bank_name +
                        `</td>
                        <td class="loan_payments_amount">` +
                        __number_f(loan_payments_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        loan_payments_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_loan_payment" data-href="/petro/settlement/payment/delete-loan-payment/` +
                        settlement_loan_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".loan_payments_fields").val("");
                calculateTotal("#loan_payments_table", ".loan_payments_amount", ".loan_payments_total");
            }
        },
    });
});
$(document).on("click", ".delete_loan_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#loan_payments_table", ".loan_payments_amount", ".loan_payments_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on("click", ".drawing_payments_add", function () {
    if ($("#drawing_payments_amount").val() === "") {
        toastr.error("Please enter amount");
        return false;
    }
    
    if ($("#drawing_payments_bank").val() === "") {
        toastr.error("Please choose account");
        return false;
    }
    
    
    var loan_payments_amount = $("#drawing_payments_amount").val();
    var settlement_no = $("#settlement_no").val();
    var loan_payments_bank = $("#drawing_payments_bank").val();
    
    var bank_name = $("#drawing_payments_bank :selected").text();
    var loan_payments_note = $("#drawing_payments_note").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-drawing-payment",
        data: {
            loan_account: loan_payments_bank,
            amount: loan_payments_amount,
            settlement_no: settlement_no,
            note: loan_payments_note,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                
                settlement_loan_payment_id = result.settlement_loan_payment_id;
                add_payment(loan_payments_amount);
                $("#drawing_payments_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        bank_name +
                        `</td>
                        <td class="loan_payments_amount">` +
                        __number_f(loan_payments_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        loan_payments_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_drawing_payment" data-href="/petro/settlement/payment/delete-drawing-payment/` +
                        settlement_loan_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".loan_payments_fields").val("");
                calculateTotal("#drawing_payments_table", ".drawing_payments_amount", ".drawing_payments_total");
            }
        },
    });
});
$(document).on("click", ".delete_drawing_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#drawing_payments_table", ".drawing_payments_amount", ".drawing_payments_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});


$(document).on("click", ".cash_deposit_add", function () {
    if ($("#cash_deposit_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var bank_id = $("#cash_deposit_bank").val();
    var bank_name = $("#cash_deposit_bank :selected").text();
    var cash_deposit_amount = $("#cash_deposit_amount").val();
    var settlement_no = $("#settlement_no").val();
    var account = $("#cash_deposit_account").val();
    var time = $("#cash_deposit_time").val();
    // var image = $("#image").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cash-deposit",
        data: {
            bank_id,cash_deposit_amount,settlement_no,account,time,is_edit },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            }
            else {
                
                settlement_cash_payment_id = result.settlement_cash_payment_id;
                add_payment(cash_deposit_amount);
                $("#cash_deposit_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        bank_name +
                        `</td>
                        <td>` +
                       account +
                        `</td>
                        <td class="cash_deposit_amount">` +
                        __number_f(cash_deposit_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        time +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_deposit" data-href="/petro/settlement/payment/delete-cash-deposit/` +
                        settlement_cash_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                
                calculateTotal("#cash_deposit_table", ".cash_deposit_amount", ".cash_deposit_total");
            }
        },
    });
});
$(document).on("click", ".delete_cash_deposit", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#cash_deposit_table", ".cash_deposit_amount", ".cash_deposit_total");
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
    var is_edit = $("#is_edit").val() ?? 0;
    
    if(card_type_id == null || card_type_id == "" || card_type_id == "undefined"){
        toastr.error("Please select card type");
        return false;
    }
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-card-payment",
        data: {
            customer_id: card_customer_id,
            amount: card_amount,
            card_type: card_type_id,
            card_number: card_number,
            settlement_no: settlement_no,
            note: card_note,
            slip_no: slip_no,
            is_edit: is_edit
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
                        <td><button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="/petro/settlement/payment/delete-card-payment/` +
                        settlement_card_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".card_fields").val("").trigger('change');
                $(".cash_fields").val("").trigger('change');
                calculateTotal("#card_table", ".card_amount", ".card_total");
            }
        },
    });
});
$(document).on("click", ".delete_card_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
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
//cheque payments
$(document).on("click", ".cheque_add", function () {
    if ($("#cheque_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cheque_customer_id = $("#cheque_customer_id").val();
    var customer_name = $("#cheque_customer_id :selected").text();
    var cheque_amount = $("#cheque_amount").val();
    var settlement_no = $("#settlement_no").val();
    var cheque_date = $("#cheque_date").val();
    var bank_name = $("#bank_name").val();
    var cheque_number = $("#cheque_number").val();
    var cheque_post_dated_cheque = $("#cheque_post_dated_cheque").val();
    var cheque_note = $("#cheque_note").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cheque-payment",
        data: {
            customer_id: cheque_customer_id,
            amount: cheque_amount,
            bank_name: bank_name,
            cheque_date: cheque_date,
            cheque_number: cheque_number,
            settlement_no: settlement_no,
            note: cheque_note,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_cheque_payment_id = result.settlement_cheque_payment_id;
                add_payment(cheque_amount);
                $("#cheque_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td>` +
                        bank_name +
                        `</td>
                        <td>` +
                        cheque_number +
                        `</td>
                        <td>` +
                        cheque_date +
                        `</td>
                        <td class="cheque_amount">` +
                        __number_f(cheque_amount, false, false, __currency_precision) +
                        `</td>
                         <td>` +
                        cheque_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cheque_payment" data-href="/petro/settlement/payment/delete-cheque-payment/` +
                        settlement_cheque_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".cheque_fields").val("");
                $(".cash_fields").val("");
                calculateTotal("#cheque_table", ".cheque_amount", ".cheque_total");
            }
        },
    });
});
$(document).on("click", ".delete_cheque_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#cheque_table", ".cheque_amount", ".cheque_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//credit_sale payments
// $(document).on("click", ".credit_sale_add", function () {
//      console.log('789');
//     if ($("#credit_sale_amount").val() == "") {
//         toastr.error("Please enter amount");
//         return false;
//     }
//     var credit_sale_customer_id = $("#credit_sale_customer_id").val();
//     var customer_name = $("#credit_sale_customer_id :selected").text();
//     var credit_sale_product_id = $("#credit_sale_product_id").val();
//     var credit_sale_product_name = $("#credit_sale_product_id :selected").text();
//     if ($("#customer_reference_one_time").val() !== "" && $("#customer_reference_one_time").val() !== null && $("#customer_reference_one_time").val() !== undefined) {
//         var customer_reference = $("#customer_reference_one_time").val();
//     } else {
//         var customer_reference = $("#customer_reference").val();
//     }
//     var settlement_no = $("#settlement_no").val();
//     var order_date = $("#order_date").val();
//     var order_number = $("#order_number").val();
    
//     var credit_sale_price = __read_number($("#unit_price"));
//     var credit_unit_discount = __read_number($("#unit_discount")) ?? 0;
//     var credit_sale_qty = __read_number($("#credit_sale_qty")) ?? 0;
//     var credit_total_amount = __read_number($("#credit_total_amount")) ?? 0;
//     var credit_total_discount = __read_number($("#credit_discount_amount")) ?? 0;
//     var credit_sub_total = __read_number($("#credit_sale_amount")) ?? 0;
    
//     var outstanding = $(".current_outstanding").text();
//     var credit_limit = $(".credit_limit").text();
//     var credit_note = $("#credit_note").val();
//     var is_edit = $("#is_edit").val() ?? 0;
    
//     $.ajax({
//         method: "post",
//         url: "/petro/settlement/payment/save-credit-sale-payment",
//         data: {
//             settlement_no: settlement_no,
//             customer_id: credit_sale_customer_id,
//             product_id: credit_sale_product_id,
//             order_number: order_number,
//             order_date: order_date,
            
//             price: credit_sale_price,
//             unit_discount: credit_unit_discount,
//             qty: credit_sale_qty,
//             amount: credit_total_amount,
//             sub_total: credit_sub_total,
//             total_discount: credit_total_discount,
//             outstanding: outstanding,
//             credit_limit: credit_limit,
//             customer_reference: customer_reference,
//             note: credit_note,
//             is_edit: is_edit
//         },
//         success: function (result) {
//             if (!result.success) {
//                 toastr.error(result.msg);
//             } else {
//                 settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;
//                 add_payment(credit_total_amount-credit_total_discount);
//                 $("#credit_sale_table tbody").prepend(
//                     `
//                     <tr> 
//                         <td>` +
//                         customer_name +
//                         `</td>
//                         <td>` +
//                         outstanding +
//                         `</td>
//                         <td>` +
//                         credit_limit +
//                         `</td>
//                         <td>` +
//                         order_number +
//                         `</td>
//                         <td>` +
//                         order_date +
//                         `</td>
//                         <td>` +
//                         customer_reference +
//                         `</td>
//                         <td>` +
//                         credit_sale_product_name +
//                         `</td>
//                         <td>` +
//                         __number_f(credit_sale_price, false, false, __currency_precision) +
//                         `</td>
//                         <td>` +
//                         __number_f(credit_sale_qty, false, false, __currency_precision) +
//                         `</td>
//                         <td class="credit_sale_amount">` +
//                         __number_f(credit_total_amount, false, false, __currency_precision) +
//                         `</td>
                        
//                         <td class="credit_tbl_discount_amount">` +
//                         __number_f(credit_total_discount, false, false, __currency_precision) +
//                         `</td>
//                         <td class="credit_tbl_total_amount">` +
//                         __number_f(credit_sub_total, false, false, __currency_precision) +
//                         `</td>
                        
                        
//                         <td>` +
//                       credit_note +
//                         `</td>
//                         <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/petro/settlement/payment/delete-credit-sale-payment/` +
//                         settlement_credit_sale_payment_id +
//                         `"><i class="fa fa-times"></i></button>
//                         </td>
//                     </tr>
//                 `
//                 );
//                 $("#customer_reference_one_time").val("").trigger("change");
//                 $(".credit_sale_fields").val("");
//                 $(".cash_fields").val("");
//                 $("#credit_sale_product_id").trigger('change');
//                 $("#order_number").val(order_number);
//                 calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
//                 calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
//                 calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
                
//             }
//         },
//     });
// });

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
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
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
//expense payments
$(document).on("click", ".expense_add", function () {
    if ($("#expense_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var settlement_no = $("#settlement_no").val();
    var expense_number = $("#expense_number").val();
    var reference_no = $("#reference_no").val();
    var expense_account = $("#expense_account").val();
    var expense_account_name = $("#expense_account :selected").text();
    var expense_category = $("#expense_category").val();
    var expense_category_name = $("#expense_category :selected").text();
    var expense_reason = $("#expense_reason").val();
    var expense_amount = $("#expense_amount").val();
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-expense-payment",
        data: {
            settlement_no: settlement_no,
            expense_number: expense_number,
            category_id: expense_category,
            reference_no: reference_no,
            account_id: expense_account,
            reason: expense_reason,
            amount: expense_amount,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_expense_payment_id = result.settlement_expense_payment_id;
                add_payment(expense_amount);
                $("#expense_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        expense_number +
                        `</td>
                        <td>` +
                        expense_category_name +
                        `</td>
                        <td>` +
                        reference_no +
                        `</td>
                        <td>` +
                        expense_account_name +
                        `</td>
                        <td>` +
                        expense_reason +
                        `</td>
                        <td class="expense_amount">` +
                        __number_f(expense_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_expense_payment" data-href="/petro/settlement/payment/delete-expense-payment/` +
                        settlement_expense_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".expense_fields").val("").trigger('change');
                $("#expense_number").val(result.expense_number);
                calculateTotal("#expense_table", ".expense_amount", ".expense_total");
            }
        },
    });
});
$(document).on("click", ".delete_expense_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#expense_table", ".expense_amount", ".expense_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//shortage payments
$(document).on("click", ".shortage_add", function () {
    if ($("#shortage_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var settlement_no = $("#settlement_no").val();
    var shortage_amount = $("#shortage_amount").val();
    var shortage_note = $("#shortage_note").val();
    
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-shortage-payment",
        data: {
            settlement_no: settlement_no,
            amount: shortage_amount,
            note: shortage_note,
            is_edit: is_edit
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_shortage_payment_id = result.settlement_shortage_payment_id;
                add_payment(shortage_amount);
                $("#shortage_table tbody").prepend(
                    `
                    <tr> 
                        <td></td>
                        <td class="shortage_amount">` +
                        __number_f(shortage_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        shortage_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_shortage_payment" data-href="/petro/settlement/payment/delete-shortage-payment/` +
                        settlement_shortage_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".shortage_fields").val("");
                $(".cash_fields").val("");
                $("#shortage_number").val(result.shortage_number);
                calculateTotal("#shortage_table", ".shortage_amount", ".shortage_total");
            }
        },
    });
});
$(document).on("click", ".delete_shortage_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#shortage_table", ".shortage_amount", ".shortage_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//excess payments
$(document).on("click", ".excess_add", function () {
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
            console.log(result)
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_excess_payment_id = result.settlement_excess_payment_id;
                add_payment(excess_amount);
                $("#excess_table tbody").prepend(
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
            }
            console.log('result',result)
        },
    });
});
$(document).on("click", ".delete_excess_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    var is_edit = $("#is_edit").val() ?? 0;
    
    $.ajax({
        method: "delete",
        url: url,
        data: {is_edit},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#excess_table", ".excess_amount", ".excess_total");
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
  total_balance = parseFloat($("#total_balance").val().replace(/,/g, ""));

  
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
    let total_balance = parseFloat($("#total_balance").val().replace(/,/g, ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance - add_amount;
    total_paid = total_paid + add_amount;
    $("#total_balance").val(__number_f(total_balance, false, false, __currency_precision));
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
//    if (total_balance === 0) {
//        $("#settlement_save_btn").removeClass("hide");
//    } else {
//        $("#settlement_save_btn").addClass("hide");
//    }
    show_hide_excess_shortage_tab();
    calculateDenoms(add_amount);
}
function show_hide_excess_shortage_tab() {
    var totbal = $("#total_balance").val();
    let total_balance = parseFloat($("#total_balance").val().replace(/,/g, ""));
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
    
    total_balance = parseFloat($("#total_balance").val().replace(/,/g, ""));
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
    var no_change = $("#no_change").val();
    
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
        data: { settlement_no: settlement_no,denom_qty: qtyArray, denom_value: denomArray,denom_enabled : denomEnabled, no_change: no_change },
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

