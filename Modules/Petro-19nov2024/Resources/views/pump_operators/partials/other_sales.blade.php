@extends('layouts.pumper')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .btn-large {
        padding: 18px 28px;
        font-size: 22px; //change this to your desired size
        line-height: normal;
    }

    .active {
        background: #666 !important;
    }

select {
  padding: 10px; /* Adjust this for the select field's height */
  line-height: 1.6; /* Increase this value to make options taller */
  font-size: 16px; /* Optional: Adjust the font size */
}
    #key_pad input {
        border: none;
    }

    #key_pad button {
        height: 80px;
        width: 30%;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    .payment_type_checkbox {
        display: none;
    }
    
    .toplabel {
        font-size: 29px;
        font-weight: bold;
    }
</style>
<div class="container">
    <form name="calculator">
    <div class="clearfix"></div>
    <br />
        <div class="col-md-12 col-lg-12" style="margin-bottom: 1rem;">
            <div class="row">
                <div class='col-md-4'>
                    <label for='product' class='toplabel'>Product</label>
                    <!--<input class="form-control" name="product" />-->
                    <select class='form-control' id='products'>
                        <option></option>
                        @foreach($products as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class='col-md-3'>
                    <label for='unit' class='toplabel'>Unit</label>
                    <input class="form-control" id="unit" name="unit" disabled />
                </div>
                <div class='col-md-2'>
                    <label for='price' class='toplabel'>Price</label>
                    <input class="form-control" id="price" name="price" disabled />
                </div>
                <div class='col-md-2'>
                    <label for='qty' class='toplabel'>Qty</label>
                    <input class="form-control" id="amount" name="qty" />
                    <input type="hidden" name="payment_type" id="payment_type" value="" />
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success" type="button" id="confirm" style="margin-top: 4.5rem;"><svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                    </svg></button>
                </div>
            </div>
            <!--<div class="col-md-6">-->
            <!--        <input name="display" class="form-control input-lg amount input_number" style="margin-top: 10px; background: #fff; border: 2px solid #333;" id="amount" value="" />-->
            <!--        <input type="hidden" name="payment_type" id="payment_type" value="" />-->
            <!--    </div>-->
        </div>
    <div class="row">
        <div class="col-md-8">
            <div class="col-md-6 col-lg-6">
                <div class="row">
                    <div class="col-md-6" style='color: red;'>
                        <h3>Total Amount</h3>
                    </div>
                    <div class="col-md-6" style='color: red;'>
                        <h3 id='totalAmount'>0.00</h3>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-striped table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>product</th>
                                <th>unit</th>
                                <th>price</th>
                                <th>Qty</th>
                                <th>amount</th>
                            </tr>
                        </thead>
                        <tbody id='readyBody'>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="key_pad" class="row col-md-6 text-center" style="margin-left: 7px;">
                <div class="row">
                    <button id="7" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">7</button>
                    <button id="8" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">8</button>
                    <button id="9" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">9</button>
                </div>
                <div class="row">
                    <button id="4" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">4</button>
                    <button id="5" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">5</button>
                    <button id="6" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">6</button>
                </div>
                <div class="row">
                    <button id="1" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">1</button>
                    <button id="2" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">2</button>
                    <button id="3" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">3</button>
                </div>
                <div class="row">
                    <button id="backspace" type="button" class="btn btn-danger" onclick="enterVal(this.id)">⌫</button>
                    <button id="0" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">0</button>
                    <button id="precision" type="button" class="btn btn-success" onclick="enterVal(this.id)">.</button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                
                <button class="btn btn-success btn-flat btn-lg btn-block" id="correctbtn" type="button">@lang('petro::lang.amount_correct_click_here')</button>
                <br />
                
                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"><input value="Dashboard" class="btn btn-flat btn-lg btn-block" style="color: #fff; background-color: #810040;" type="button" /> </a>
                
                <br />
                <button disabled value="save" id="payment_submit1" name="submit" class="btn btn-flat btn-lg btn-block" style="color: #fff; background-color: #2874a6;" type="button">@lang('lang_v1.save')</button>
                <br />
                <span onclick="reset()">
                    <button type="button" class="btn btn-flat btn-lg btn-block" id="cancelbtn" style="color: #fff; background-color: #cc0000;" type="button"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('petro::lang.cancel')</button>
                </span>
                <br />
                <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-block btn-lg pull-right" style="background-color: orange; color: #fff;">@lang('petro::lang.logout')</a>
            </div>
        </div>
    </div>
</form>

</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{url('Modules/Petro/Resources/assets/js/po_payment.js')}}?v={{ time()}}"></script>
<script>
    $(document).ready(function() {
        let dataArray = [];
        let totalAmount = 0.0;
        let correctFlag = false;
        $(document).on('change', '#products', function() {
            $.ajax({
            method: "get",
            url: "/petro/pump-operator-payments/othersale/getproducts",
            data: { product_id: $(this).val() },
                success: function (result) {
                    console.log(result.currency_precision.currency_precision);
                    var price = parseFloat(result.product.sell_price_inc_tax).toFixed(2);
                    $("#unit").val(result.product.short_name);
                    $("#price").val(price);
                    $("amount").val("");
                    $("#amount").focus();
                },
            });
        });
        
        $(document).on('click', '#confirm', function() {
            var amount = $("#amount").val();
            if(amount == 0) {
                alert("Please enter the amount");
                $("#amount").focus();
            } else {
                var product = $("#products option:selected").text();
                var product_id = $("#products option:selected").val();
                var unit = $("#unit").val();
                var price = $("#price").val();
                var rowamount = parseFloat(amount) * parseFloat(price);
                const innerData = {product, unit, price, amount, rowamount, product_id};
                dataArray.push(innerData);
                display(dataArray);
            }
        })
        
        function display(datas){
            var bodyHtml = "";
            
            let totalAmount = 0;

            datas.forEach((value) => {
                bodyHtml += generateRow(value);
                totalAmount += value.rowamount;
            });
            
            console.log(totalAmount);
            
            const formattedNumber = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            $('#totalAmount').html(formattedNumber);
            $('#readyBody').html(bodyHtml);
        }
        
        function generateRow(value) {
            return '<tr><td>' + value.product + '</td><td>' + value.unit + '</td><td>' + value.price + '</td><td>' + value.amount + '</td><td>' + value.rowamount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td></tr>';
        }
        
        $(document).on('click', '#correctbtn', function() {
            let totalAmount = $("#totalAmount").html();
            console.log(totalAmount);
            console.log(dataArray.length);
            if (totalAmount == "" || totalAmount == undefined || totalAmount == null) {
                $("#payment_submit1").attr("disabled", true);
            } else if ($("#payment_type").prop("checked") == false && dataArray.length == 0) {
                $("#payment_submit1").attr("disabled", true);
            } else {
                $("#payment_submit1").attr("disabled", false);
            }
        });
        
        $(document).on('click', '#cancelbtn', function() {
            correctFlag = false;
            dataArray = [];
            totalAmount = 0.0
            display(dataArray);
            $('#payment_submit1').prop('disabled', true);
        });

        $(document).on('click', '#payment_submit1', function() {
            var saveButton = document.getElementById('payment_submit1');
            saveButton.innerText = 'Saving...'; 
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var items = dataArray;
            correctFlag = false;
            dataArray = [];
            totalAmount = 0.0
            display(dataArray);
            $('#payment_submit1').prop('disabled', true);
            $.ajax({
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            url: "/petro/pump-operator-pmts/save-other-sale-items",
            data: { items },
            success: function (result) {
                if(result.success){
                    var saveButton = document.getElementById('payment_submit1');
                    saveButton.innerText = 'Save';
                    alert(result.msg);
                } else {
                    var saveButton = document.getElementById('payment_submit1');
                    saveButton.innerText = 'Save';
                    alert(result.msg);
                }
            },
            error: function(xhr, status, error) {
                var saveButton = document.getElementById('payment_submit1');
                saveButton.innerText = 'Save';
                alert("An error occurred: " + xhr.status + " " + xhr.statusText);
                console.error("Error details: ", status, error);
            }
            });
        });
    });
</script>
@endsection

