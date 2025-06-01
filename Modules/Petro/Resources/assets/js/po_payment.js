function enterVal(val) {
    console.log(val);
    $("#amount").focus();
    if (val === "precision") {
        str = $("#amount").val();
        str = str + ".";
        $("#amount").val(str);
        return;
    }
    if (val === "backspace") {
        str = $("#amount").val();
        str = str.substring(0, str.length - 1);
        $("#amount").val(str);
        
        return;
    }
    let amount = $("#amount").val() + val;
    amount = amount.replace(",", "");
    $("#amount").val(amount);
    
}

    let currentInput = null;

    $(".other_sale_input").on('focus', function() {
        currentInput = $(this); 
    });
    
    function otherSaleEnterVal(val) {
        if (!currentInput) return;
    
        let str = currentInput.val(); 
    
        if (val === "precision") {
            if (!str.includes(".")) {
                str += ".";
                currentInput.val(str);
            }
            return;
        }
    
        if (val === "backspace") {
            str = str.substring(0, str.length - 1);
            currentInput.val(str);
            return;
        }
    
        str += val; 
        currentInput.val(str);
        currentInput.focus();
        currentInput.trigger('change');
    }

$(document).on('input','.other_sale_input',function(){
    var entered_val = __read_number($(this));
    let row = $(this).closest('tr');
    
    var unit_price = __read_number(row.find('.other_sale_unit_price'));
    var starting_meter = __read_number(row.find('.other_sale_starting_meter'));
    var sold_qty = entered_val - starting_meter;
    
    var sold_amount = sold_qty * unit_price;
    
    row.find('.other_sale_span_sold_qty').text(__number_f(sold_qty));
    row.find('.other_sale_span_amount').text(__number_f(sold_amount));
    
    row.find('.other_sale_sold_qty').val(__number_uf(__number_f(sold_qty)));
    row.find('.other_sale_amount').val(__number_uf(__number_f(sold_amount)));
    
    calculate_other_sales_totals();
    
    
});

function calculate_other_sales_totals(){
    let totalAmount = 0;

   var there_is_incomplete = 0;
   
    $('#other_sale_table tbody tr').each(function () {
        let amount = parseFloat($(this).find('.other_sale_amount').val());
        
        if (!isNaN(amount)) {
            totalAmount += amount;
            if(amount < 0){
                there_is_incomplete += 1;
            }
        }else{
            there_is_incomplete += 1;
        }
    });
    
    
    if(there_is_incomplete > 0){
        $(".other_sale_finalize").prop('disabled',true);
    }else{
        $(".other_sale_finalize").prop('disabled',false);
    }
    
    
    $('.other_sale_grand_total_amount').text(__number_f(totalAmount));
    $('.other_sale_grand_total_amount_input').val(__number_uf(__number_f(totalAmount)));

    let todayDeposited = $(".other_sale_grand_today_deposited_input").val(); 
    let balanceToDeposit = totalAmount - todayDeposited;
    
    $('.other_sale_grand_balance_to_deposit').text(__number_f(balanceToDeposit));
    $('.other_sale_grand_balance_to_deposit_input').val(__number_uf(__number_f(balanceToDeposit)));
}

$(document).on("click", ".payment_type_btn", function () {
    clicked_btn = $(this);

    siblings = $(clicked_btn).siblings();
    return_false = false;
    siblings.each(function (i, ele) {
        if ($(ele).hasClass("active") && return_false === false) {
            return_false = true;
            console.log(return_false);
        }
    });

    if (return_false) {
        return false;
    }

    siblings.each(function (i, ele) {
        $(ele).addClass("active");
        $(this).find(".payment_type_checkbox").attr("checked", false);
    });
    $("#payment_type").val($(this).find(".payment_type_checkbox").val());
    $(this).find(".payment_type_checkbox").attr("checked", true);
    $(clicked_btn).removeClass("active");
    
});

$(document).on("click", "#payment_submit", function () {
    let amount = $("#amount").val();
    var slip_no = $("#sub_slip_no").val() ?? "";
    var card_type = $("#sub_card_type").val() ?? "";
    
    
    let payment_type = $("#payment_type").val();
    if (amount === "" || amount === undefined || amount === null) {
        toastr.error("Please enter amount");
        return false;
    }
    // remove the payment type check 10/18/2024
    // if (payment_type === "" || payment_type === undefined || payment_type === null) {
    //     toastr.error("Please select payment type");
    //     return false;
    // }
    
    
    if(payment_type == 'card'){
        // if(slip_no == ""){
        //     toastr.error("Please enter Slip No");
        //     return false;
        // }
        
        if(card_type == ""){
            toastr.error("Please enter Card Type");
            return false;
        }
    }
    
    var collection_form_no = $("#collection_form_no").val() ?? "";
    
    $("#payment_submit").attr("disabled", true);
    amount = parseFloat(amount);
    $.ajax({
        method: "POST",
        url: "/petro/pump-operator-payments",
        data: { amount, payment_type,slip_no,card_type, collection_form_no },
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                if ($(".view_modal").length) {
                    $(".view_modal").modal("hide");
                    
                    $(".collection_form_no").each(function() {
                        $(this).val(result.collection_form_no);
                    });
                    $("#reloadConfirmationModalLabel").html("Confirm Another Payment for Form No. " + result.collection_form_no);
                    $("#reloadConfirmationModal").modal("show");
                    // location.reload();
                }
            } else {
                toastr.error(result.msg);
                $("#payment_submit").attr("disabled", false);
            }
        },
    });
});

function reset() {
    document.getElementById("amount").value = "";
    document.getElementById("payment_type").value = "";
    $(".payment_type_btn").each(function (i, ele) {
        console.log("asdf");
        $(ele).removeClass("active");
        $(this).find(".payment_type_checkbox").attr("checked", false);
    });
}
$("#amount").focus();

$(".amount-correct").on('click',function () {
    enableSaveButton();
});

function enableSaveButton() {
    console.log('correct button clicked0');
    let amount = $("#amount").val();
    let payment_type = $("#payment_type").val();
    console.log(amount);
    console.log(payment_type.length);
    if (amount == "" || amount == undefined || amount == null) {
        $("#payment_submit").attr("disabled", true);
    } else if ($("#payment_type").prop("checked") == false && payment_type.length == 0) {
        $("#payment_submit").attr("disabled", true);
    } else {
        $("#payment_submit").attr("disabled", false);
    }
}
