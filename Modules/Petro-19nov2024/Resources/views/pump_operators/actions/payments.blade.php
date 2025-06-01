@extends('layouts.'.$layout)
@section('content')

<div class="container">
  @include('petro::pump_operators.partials.payment_section', ['pop_up' => false])
</div>

<div class="modal fade" id="direct_cr" role="dialog" 
aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang( 'petro::lang.credit_sale' )</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            @include('petro::pump_operators.credit_sale')
        </div>

      </div>
    </div>
  </div>
  
 <div class="modal fade" id="cheque_payments" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang( 'petro::lang.cheque' )</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            @include('petro::pump_operators.cheque_payment')
        </div>

      </div>
    </div>
  </div>
  
  <div class="modal fade" id="cash_payments" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang( 'petro::lang.cash' )</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@saveCashDenom'), 'method' =>'post']) !!}
            <div class="row">
                <div class="col-md-6">
                    @foreach($cash_denoms as $denom)
                    
                        <div class="row">
                            <input type="hidden" value="{{$denom}}" class="denom_value">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{@num_format($denom)}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::number('qty[]', 0, ['class' => 'form-control cash_payment_input denom_qty', 'required',
                                    'placeholder' => __(
                                    'petro::lang.qty' ) ]); !!}
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::text('total_amount[]', 0, ['class' => 'form-control denom_amt', 'required','readonly',
                                    'placeholder' => __(
                                    'petro::lang.total_amount' ) ]); !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="row denoms_totals">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __( 'lang_v1.total' ) }}</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::text('grand_total', 0, ['class' => 'form-control denom_total', 'required','readonly',
                                'placeholder' => __(
                                'petro::lang.total' ),'style' => 'color:green;font-weight:bold;' ]); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-2 pull-right">
                            <button type="submit" class="btn btn-success pull-right other_sale_finalize"
                                style="margin-top: 23px;">@lang('petro::lang.correct')</button>
                        </div> 
                        
                        
                        
                    </div>
                </div>
 
                </div>
            </div>
            {!! Form::close() !!}
        </div>

      </div>
    </div>
  </div>
  
<div class="modal fade" id="card_payment" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang('petro::lang.card')</h4>
          <button type="button" class="btn btn-primary pull-right" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            @include('petro::pump_operators.card_payment')
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

      </div>
    </div>
  </div>
  
  <div class="modal fade" id="other_sales" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang('petro::lang.enter_meters')</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            @include('petro::pump_operators.other_sales',compact('pending_pumps'))
        </div>

      </div>
    </div>
  </div>

@endsection

@section('javascript')
<script src="{{url('Modules/Petro/Resources/assets/js/po_payment.js')}}?v={{ time()}}"></script>
<script>
    $(document).ready(function(){
        $(".select2").select2();
        
        $(document).on('click', '.add_other_sales', function(e) {
            $("#other_sales").modal({
                backdrop: 'static',
                keyboard: false 
            });
            
            $(".other_sale_finalize").attr('disabled',true);
        });
        
        
         $(document).on('click', '.add_cheque_payment', function(e) {
            $("#cheque_payments").modal({
                backdrop: 'static',
                keyboard: false 
            });
        });
        
        $(document).on('click', '.cash_denoms_enter', function(e) {
            $("#cash_payments").modal({
                backdrop: 'static',
                keyboard: false 
            });
        });
        
        
        $(document).on('click', '.card_payment_btn', function(e) {
            $("#card_payment").modal({
                backdrop: 'static',
                keyboard: false 
            });
            
            $(".card-save-btn").prop('disabled',true);
            
            $(".card_payment_add").attr('disabled',true);
        });
        
        $(document).on('change','#card_type,#slip_no,#card_amount',function(){
            
            if($("#card_type").val() && $("#card_amount").val() ){
                $(".card_payment_add").attr('disabled',false);
            }else{
                $(".card_payment_add").attr('disabled',true);
            }
            
        })
        
        function toggerCardSaveBtn(){
            if($(".card-data").length > 0){
                $(".card-save-btn").prop('disabled',false);
            }else{
                $(".card-save-btn").prop('disabled',true);
            }
        }
        
        $(document).on('click', '.card_payment_add', function () {
            
            var data = {
                card_type: $("#card_type").val(),
                slip_no: $("#slip_no").val(),
                amount: $("#card_amount").val(),
                card_number: $("#card_no").val()
            };
            var html = `
                <tr>
                    <td>`+ $("#card_type option:selected").text() + `</td>
                    <td>`+ $("#slip_no").val() + `</td>
                    <td>`+ $("#card_no").val() + `</td>
                    <td>`+ $("#card_amount").val() + `
                    
                    <input type="hidden" name="card_data[]" class="card-data" required value='`+JSON.stringify(data)+`'>
                    
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                </tr>
            `;
        
            $("#card_payment_table tbody").append(html);
            $(".card_payment_input").val(""); // Clear inputs
            toggerCardSaveBtn();
        });
        
        // Remove row when the remove button is clicked
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            toggerCardSaveBtn()
        });


        
        
        $(".credit_sale_finalize").hide();
        
        $(document).on('click', '.po_credit_payment', function(e){
          $("#direct_cr").modal('show');
        });
        
        
        $("#credit_sale_customer_id").val($("#credit_sale_customer_id option:eq(0)").val()).trigger('change');
        $('#order_date').datepicker("setDate", new Date());
        $('#credit_sale_product_id').select2();
        $('#credit_sale_customer_id').select2();
        $('#customer_reference').select2();
    });

    $(document).on('change', '#customer_reference_one_time', function(){
        if($(this).val() !== '' && $(this).val() !== null && $(this).val() !== undefined){
            $('#customer_reference').attr('disabled', 'disabled');
            $('.quick_add_customer_reference').attr('disabled', 'disabled');
        }else{
            $('#customer_reference').removeAttr('disabled');
            $('.quick_add_customer_reference').removeAttr('disabled');
        }
    })
    
    $(document).on('change', '.cash_payment_input', function() {
        var amount = $(this).val() ?? 0;  // Default to 0 if no value
        var row = $(this).closest('.row'); // Find the closest row element
        var denom_value = row.find(".denom_value").val(); // Get the denomination value
        var total = denom_value * amount; // Calculate the total
    
        console.log(total); // Log the calculated total
    
        row.find('.denom_amt').val(total); // Set the calculated total in the denom_amt field
    
        calculateDenomTotals(); // Call the function to update the totals
    });

    
   function calculateDenomTotals() {
        var total = 0;
    
        $('.denom_amt').each(function() {
            var denom_total = parseFloat($(this).val()) || 0; 
            total += denom_total; 
        });
    
        $('.denom_total').val(total.toFixed(2)); 
    }


    
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
        url: "/petro/pump-operator-pmts/save-credit",
        data: {
            credit_data : dataArray
        },
        success: function (result) {
            toastr.success(result.msg);
            window.location.reload();
        },
    });
});



    let cash_payment_currentInput = null;

    $(".cash_payment_input").on('focus', function() {
        cash_payment_currentInput = $(this); 
    });
    
    function cashPaymentEnterVal(val) {
        if (!cash_payment_currentInput) return;
    
        let str = cash_payment_currentInput.val(); 
    
        if (val === "precision") {
            if (!str.includes(".")) {
                str += ".";
                cash_payment_currentInput.val(str);
            }
            return;
        }
    
        if (val === "backspace") {
            str = str.substring(0, str.length - 1);
            cash_payment_currentInput.val(str);
            return;
        }
    
        str += val; 
        cash_payment_currentInput.val(str);
        cash_payment_currentInput.focus();
        cash_payment_currentInput.trigger('change');
    }
    
    
    let card_payment_currentInput = null;

    $(".card_payment_input").on('focus', function() {
        card_payment_currentInput = $(this); 
    });
    
    function cardPaymentEnterVal(val) {
        if (!card_payment_currentInput) return;
    
        let str = card_payment_currentInput.val(); 
    
        if (val === "precision") {
            if (!str.includes(".")) {
                str += ".";
                card_payment_currentInput.val(str);
            }
            return;
        }
    
        if (val === "backspace") {
            str = str.substring(0, str.length - 1);
            card_payment_currentInput.val(str);
            return;
        }
    
        str += val; 
        card_payment_currentInput.val(str);
        card_payment_currentInput.focus();
        card_payment_currentInput.trigger('change');
    }

</script>
@endsection