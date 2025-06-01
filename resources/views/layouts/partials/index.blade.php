@extends('layouts.app')
@section('title', 'Settlement Sw')

@section('content')
@php
  $meter_sale_arr = [];
   if(!empty($active_settlement) && request()->segment(2)=='edit')
       $meter_sale_arr = $active_settlement->meter_sales->toArray()[0];
   $pump_no = null;
$pump_starting_meter = null;
$pump_closing_meter = null;
$sold_qty = null;
$meter_sale_unit_price = null;
$testing_qty = "0.00";
$meter_sale_discount_type = null;
$meter_sale_discount = "0.00";
$meter_sale_id = null;
$final_total = 0.00;
if(!empty($meter_sale)){
    $pump_no = $meter_sale['pump_id'];
    $pump_starting_meter = number_format($meter_sale['starting_meter'], 3);
    $pump_closing_meter = number_format($meter_sale['closing_meter'], 3);
    $sold_qty = $meter_sale['qty'];
    $meter_sale_unit_price = $meter_sale['price'];
    $testing_qty = $meter_sale['testing_qty'];
    $meter_sale_discount_type = $meter_sale['discount_type'];
    $meter_sale_discount = $meter_sale['discount'];
    $meter_sale_id = $meter_sale['id'];
}
@endphp
<style>
/* Container */
.settlement-container { padding: 1rem; font-family: Arial, sans-serif; }

/* Card */
.card-custom {
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.card-custom .card-header {
  background: #f8f9fa;
  font-weight: 600;
  font-size: 1rem;
  padding: .75rem 1.25rem;
  border-bottom: 1px solid #e9ecef;
}
.card-custom .card-body { padding: 1rem; }

/* Form row */
.form-row-custom {
  display: flex;
  flex-wrap: wrap;
  margin: -0.5rem;
}
.form-group-custom {
  padding: 0.5rem;
  flex: 1 1 16.6667%;
  max-width: 16.6667%;
}
.form-group-custom.full-width {
  flex: 1 1 100%;
  max-width: 100%;
}
.form-group-custom label {
  display: block;
  font-size: .875rem;
  margin-bottom: .25rem;
  color: #495057;
}
.form-group-custom .form-control {
  width: 100%;
  padding: .375rem .75rem;
  font-size: .875rem;
  line-height: 1.5;
  border: 1px solid #ced4da;
  border-radius: .25rem;
}

/* Tables */
.table-custom {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
.table-custom th,
.table-custom td {
  border: 1px solid #dee2e6;
  padding: .5rem;
  font-size: .875rem;
}
.table-custom th {
  background: #f1f1f1;
  color: #333;
}
.table-custom tfoot td {
  font-weight: 600;
}

/* Buttons */
.btn-add {
  background-color: #28a745;
  color: #fff;
  border: none;
  padding: .25rem .5rem;
  border-radius: 4px;
  cursor: pointer;
}
.btn-add:hover {
  background-color: #218838;
}
.btn-primary-custom {
  background-color: #007bff;
  color: #fff;
  border: none;
  padding: .5rem 1rem;
  font-size: .9rem;
  border-radius: 4px;
  cursor: pointer;
}
.btn-primary-custom:hover {
  background-color: #0069d9;
}

/* Lists */
.list-unstyled-custom {
  list-style: none;
  padding-left: 0;
  margin-top: 1rem;
}
.list-unstyled-custom li {
  display: flex;
  justify-content: space-between;
  padding: .25rem 0;
  border-bottom: 1px dashed #ccc;
}

/* Summary */
.summary-text {
  text-align: right;
  font-weight: 600;
  font-size: .95rem;
  margin-top: .5rem;
}
</style>

<div class="settlement-container">

  <!-- Filters -->
  <div class="card-custom">
    <div class="card-header">Filters</div>
    <div class="card-body">
      <form id="filters-form" class="form-row-custom">
        <div class="form-group-custom">
          {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
          {!! Form::text('settlement_no', !empty($active_settlement) ? $active_settlement->settlement_no :
          $settlement_no, ['class' => 'form-control', 'readonly']); !!}
        </div>

        <div class="form-group-custom">
          {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
          {!! Form::select('location_id', $business_locations, !empty($active_settlement) ?
          $active_settlement->location_id : (!empty($default_location) ? $default_location : null), ['class'
          => 'form-control select2', 'id' => 'location_id',
          'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
          {!! Form::select('pump_operator_id', $pump_operators, !empty($active_settlement) ?
          $active_settlement->pump_operator_id : null, ['class' => 'form-control select2', 'id' =>
          'pump_operator_id', 'disabled' => !empty($select_pump_operator_in_settlement) ? false : true,
          'placeholder' => __('petro::lang.please_select')]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
          {!! Form::text('transaction_date', null, ['class' =>
          'form-control transaction_date', 'required',
          'placeholder' => __(
          'petro::lang.transaction_date' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('work_shift', __('petro::lang.work_shift').':') !!}
          {!! Form::select('work_shift[]', $wrok_shifts, !empty($active_settlement) ?
          $active_settlement->work_shift : [], ['class' => 'form-control select2', 'id' => 'work_shift',
          'multiple']); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('shift_number', __('petro::lang.shift_number').':') !!}
          <select id="shift_number", class="form-control select2" multiple></select>
        </div>
        <div class="form-group-custom full-width">
          {!! Form::label('note', __('petro::lang.note') . ':') !!}
          {!! Form::text('note', !empty($active_settlement) ? $active_settlement->note : null, ['class' =>
          'form-control note',
          'placeholder' => __(
          'petro::lang.note' ) ]); !!}
        </div>
      </form>
    </div>
  </div>

  <!-- Meter Sales -->
  <div class="card-custom">
    <div class="card-header">Meter Sales</div>
    <div class="card-body">
      <form id="meter-sales-form" class="form-row-custom align-items-end">
        <div class="form-group-custom">
          {!! Form::label('pump_no', __('petro::lang.pump_no').':') !!}
          {!! Form::select('pump_no', $pump_nos, $pump_no, ['class' => 'form-control meter_sale_fields check_pumper
          select2',
          'placeholder' => __('petro::lang.please_select')]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('pump_starting_meter', __( 'petro::lang.pump_starting_meter' ) ) !!}
          {!! Form::text('pump_starting_meter', $pump_starting_meter, ['class' => 'form-control meter_sale_fields check_pumper
          input_number
          pump_starting_meter', 'required', 'readonly',
          'placeholder' => __(
          'petro::lang.pump_starting_meter' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('pump_closing_meter', __( 'petro::lang.pump_closing_meter' ) ) !!}
          {!! Form::text('pump_closing_meter', $pump_closing_meter, ['class' => 'form-control meter_sale_fields check_pumper
          input_number
          pump_closing_meter',
          'required',
          'step' => '0.001',
          'min' => '0',
          'placeholder' => __(
          'petro::lang.pump_closing_meter' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('sold_qty', __( 'petro::lang.sold_qty' ) ) !!}
          {!! Form::text('sold_qty', $sold_qty, ['class' => 'form-control meter_sale_fields check_pumper sold_qty
          input_number',
          'required', 'disabled',
          'placeholder' => __(
          'petro::lang.sold_qty' ) ]); !!}
          <input type="hidden" class="meter_sale_fields is_from_pumper" id="is_from_pumper" value="0">

          <input type="hidden" class="meter_sale_fields assignment_id" id="assignment_id" value="0">
          <input type="hidden" class="meter_sale_fields pumper_entry_id" id="pumper_entry_id" value="0">
        </div>
        <div class="form-group-custom">
          {!! Form::label('unit_price', __( 'petro::lang.unit_price' ) ) !!}
          {!! Form::text('meter_sale_unit_price', $meter_sale_unit_price, ['id' => 'meter_sale_unit_price', 'class' => 'form-control
          meter_sale_fields check_pumper unit_price input_number',
          'readonly',
          'placeholder' => __(
          'petro::lang.unit_price' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('testing_qty', __( 'petro::lang.testing_qty' ) ) !!}
          {!! Form::text('testing_qty', $testing_qty, ['class' => 'form-control check_pumper input_number
          testing_qty', 'required',
          'placeholder' => __(
          'petro::lang.testing_qty' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('meter_sale_discount_type', __( 'petro::lang.discount_type' ) ) !!}
          {!! Form::select('meter_sale_discount_type', $discount_types, $meter_sale_discount_type, ['class' => 'form-control meter_sale_fields check_pumper
          input_number
          meter_sale_discount_type', 'required',
          'placeholder' => __(
          'petro::lang.please_select' ) ]); !!}
        </div>
        <div class="form-group-custom">
          {!! Form::label('meter_sale_discount', __( 'petro::lang.discount' ) ) !!}
          {!! Form::text('meter_sale_discount', $meter_sale_discount, ['class' => 'form-control meter_sale_fields check_pumper
          input_number
          meter_sale_discount', 'required',
          'placeholder' => __(
          'petro::lang.discount' ) ]); !!}
        </div>
        <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:center;">
          <button type="button" class="btn-add" id="add-meter-sale">
            <i class="fa fa-plus"></i>
          </button>
        </div>
      </form>

      <input type="hidden" value="{{$final_total}}" name="meter_sale_total" id="meter_sale_total">

      <table class="table-custom">
        <thead>
          <tr>
            <th>Code</th><th>Products</th><th>Pump</th><th>Starting Meter</th><th>Closing Meter</th>
            <th>Price</th><th>Sold Qty</th><th>Discount Type</th><th>Discount Value</th>
            <th>Testing Qty</th><th>Total Qty</th><th>Before Discount</th><th>After Discount</th><th>Action</th>
          </tr>
        </thead>
        <tbody id="meter-sales-tbody">
          {{-- rows --}}
        </tbody>
        <tfoot>
          <tr>
            <td colspan="12" class="text-right">Meter Sales Total:</td>
            <td colspan="2" id="meter-sales-total">0.00</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Other Sales -->
  <div class="card-custom">
    <div class="card-header">Other Sales</div>
    <div class="card-body">
      <form id="other-sales-form" class="form-row-custom align-items-end">
        <div class="form-group-custom">
          <label for="store">Store</label>
          <select id="store" class="form-control">
            <option value="">Main Store</option>
            {{-- stores --}}
          </select>
        </div>
        <div class="form-group-custom">
          <label for="item">Select Item</label>
          <select id="item" class="form-control">
            <option value="">Please Select</option>
            {{-- items --}}
          </select>
        </div>
        <div class="form-group-custom">
          <label for="balance_stock">Balance Stock</label>
          <input type="number" id="balance_stock" class="form-control" readonly>
        </div>
        <div class="form-group-custom">
          <label for="other_price">Price</label>
          <input type="number" id="other_price" class="form-control" step="0.01">
        </div>
        <div class="form-group-custom">
          <label for="other_qty">Qty</label>
          <input type="number" id="other_qty" class="form-control">
        </div>
        <div class="form-group-custom">
          <label for="other_discount_type">Discount Type</label>
          <select id="other_discount_type" class="form-control">
            <option value="">Please Select</option>
            <option value="fixed">Fixed</option>
            <option value="percent">%</option>
          </select>
        </div>
        <div class="form-group-custom">
          <label for="other_discount_val">Discount</label>
          <input type="number" id="other_discount_val" class="form-control" step="0.01" value="0">
        </div>
        <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:center;">
          <button type="button" class="btn-add" id="add-other-sale">
            <i class="fa fa-plus"></i>
          </button>
        </div>
      </form>

      <table class="table-custom">
        <thead>
          <tr>
            <th>Code</th><th>Products</th><th>Balance Stock</th><th>Price</th>
            <th>Qty</th><th>Discount Type</th><th>Discount Value</th>
            <th>Before Discount</th><th>After Discount</th><th>Action</th>
          </tr>
        </thead>
        <tbody id="other-sales-tbody">
          {{-- rows --}}
        </tbody>
        <tfoot>
          <tr>
            <td colspan="7" class="text-right">Other Sales Total:</td>
            <td colspan="3" id="other-sales-total">0.00</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Credit Sales -->
  <div class="card-custom">
    <div class="card-header">Credit Sales</div>
    <div class="card-body">
      <div style="margin-bottom:1rem;">
        <button type="button" class="btn-primary-custom">Add a New Customer</button>
        <button type="button" class="btn-primary-custom">Add a New Vehicle</button>
      </div>
      <form id="credit-sales-form" class="form-row-custom align-items-end">
        <div class="form-group-custom">
          <label for="credit_customer">Customer</label>
          <select id="credit_customer" class="form-control">
            <option value="">Please Select</option>
            {{-- customers --}}
          </select>
        </div>
        <div class="form-group-custom">
          <label for="credit_vehicle">Vehicle No</label>
          <select id="credit_vehicle" class="form-control">
            <option value="">Please Select</option>
            {{-- vehicles --}}
          </select>
        </div>
        <div class="form-group-custom">
          <label for="credit_product">Product</label>
          <select id="credit_product" class="form-control">
            <option value="">Please Select</option>
            {{-- products --}}
          </select>
        </div>
        <div class="form-group-custom">
          <label for="credit_qty">Qty</label>
          <input type="number" id="credit_qty" class="form-control">
        </div>
        <div class="form-group-custom">
          <label for="credit_discount_type">Discount Type</label>
          <select id="credit_discount_type" class="form-control">
            <option value="">Please Select</option>
            <option value="fixed">Fixed</option>
            <option value="percent">%</option>
          </select>
        </div>
        <div class="form-group-custom">
          <label for="credit_discount_val">Discount</label>
          <input type="number" id="credit_discount_val" class="form-control" step="0.01" value="0">
        </div>
        <div class="form-group-custom">
          <label for="credit_total">Total Amount</label>
          <input type="number" id="credit_total" class="form-control" readonly>
        </div>
        <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:center;">
          <button type="button" class="btn-add" id="add-credit-sale">
            <i class="fa fa-plus"></i>
          </button>
        </div>
      </form>
      <div class="summary-text">Total Credit Sales: <span id="credit-sales-total">0.00</span></div>
    </div>
  </div>

  <!-- Other Income & Customer Payments -->
  <div style="display:flex; gap:1rem; flex-wrap:wrap;">
    <!-- Other Income -->
    <div style="flex:1; min-width:300px;">
      <div class="card-custom">
        <div class="card-header">Other Income</div>
        <div class="card-body">
          <form id="other-income-form" class="form-row-custom align-items-end">
            <div class="form-group-custom" style="flex:2;">
              <label for="income_details">Details</label>
              <input type="text" id="income_details" class="form-control">
            </div>
            <div class="form-group-custom" style="flex:1;">
              <label for="income_amount">Amount</label>
              <input type="number" id="income_amount" class="form-control" step="0.01">
            </div>
            <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:center;">
              <button type="button" class="btn-add" id="add-income"><i class="fa fa-plus"></i></button>
            </div>
          </form>
          <ul class="list-unstyled-custom" id="other-income-list">
            {{-- <li>Details… <span>Amount</span></li> --}}
          </ul>
          <div class="summary-text">Other Income Total: <span id="other-income-total">0.00</span></div>
        </div>
      </div>
    </div>
    <!-- Customer Payments -->
    <div style="flex:1; min-width:300px;">
      <div class="card-custom">
        <div class="card-header">Customer Payments</div>
        <div class="card-body">
          <form id="cust-payments-form" class="form-row-custom align-items-end">
            <div class="form-group-custom" style="flex:2;">
              <label for="cust_customer">Customer</label>
              <select id="cust_customer" class="form-control">
                <option value="">Please Select</option>
                {{-- customers --}}
              </select>
            </div>
            <div class="form-group-custom" style="flex:1;">
              <label for="cust_amount">Amount</label>
              <input type="number" id="cust_amount" class="form-control" step="0.01">
            </div>
            <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:center;">
              <button type="button" class="btn-add" id="add-payment"><i class="fa fa-plus"></i></button>
            </div>
          </form>
          <ul class="list-unstyled-custom" id="cust-payments-list">
            {{-- <li>Customer… <span>Amount</span></li> --}}
          </ul>
          <div class="summary-text">Customer Payments Total: <span id="cust-payments-total">0.00</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payments Summary -->
  <div class="card-custom">
    <div class="card-header">Payments</div>
    <div class="card-body">
      <form id="payments-summary" class="form-row-custom">
        <div class="form-group-custom"><label for="pay_cash">Cash</label><input type="number" id="pay_cash" class="form-control" step="0.01"></div>
        <div class="form-group-custom"><label for="pay_cards">Cards</label><input type="number" id="pay_cards" class="form-control" step="0.01"></div>
        <div class="form-group-custom"><label for="pay_cheques">Cheques</label><input type="number" id="pay_cheques" class="form-control" step="0.01"></div>
        <div class="form-group-custom"><label for="pay_credit_sales">Credit Sales</label><input type="number" id="pay_credit_sales" class="form-control" readonly></div>
        <div class="form-group-custom"><label for="pay_expenses">Expenses</label><input type="number" id="pay_expenses" class="form-control" step="0.01"></div>
        <div class="form-group-custom"><label for="pay_shortage">Shortage</label><input type="number" id="pay_shortage" class="form-control" readonly></div>
        <div class="form-group-custom"><label for="pay_excess">Excess</label><input type="number" id="pay_excess" class="form-control" readonly></div>
        <div class="form-group-custom"><label for="pay_total">Total</label><input type="number" id="pay_total" class="form-control" readonly></div>
      </form>
    </div>
  </div>

  <!-- Save -->
  <div style="text-align:right; margin-bottom:2rem;">
    <button type="button" class="btn-primary-custom">Save</button>
  </div>

</div>
@endsection

@section('javascript')
  <script>
    $('#pump_operator_id').change(function () {

      let store_id = $("select#store_id option").filter(":selected").val();

      var op_id = $(this).val();

      if ($(this).val() === '' || $(this).val() === undefined) {
        toastr.error('Please Select the Pump operator and continue');
      } else {

        $.ajax({
          method: 'get',
          url: "/settlement-sw/get_pumps/" + op_id,
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
        url: '/settlement-sw/get-pump-details/' + $(this).val(),
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
      $('#meter-sales-tbody').append(`
    <tr>
        <td>${response.data.code ?? ''}</td>
        <td>${response.data.product.name ?? ''}</td>
        <td>${response.data.pump_no}</td>
        <td>${response.data.pump_start}</td>
        <td>${response.data.pump_close}</td>
        <td>${response.data.unit_price}</td>
        <td>${response.data.sold_qty}</td>
        <td>${response.data.discount_type}</td>
        <td>${response.data.discount_val}</td>
        <td>${response.data.testing_qty}</td>
        <td>${parseFloat(response.data.sold_qty) + parseFloat(response.data.testing_qty)}</td>
        <td>${response.data.unit_price * response.data.sold_qty}</td>
        <td>--</td>
        <td><button class="btn btn-sm btn-danger">Delete</button></td>
      </tr>
  `);

  // Optional: Reset the form
  $('#meter-sales-form')[0].reset();
  },
  error: function (xhr) {
  alert('Failed to save meter sale. Please check the fields.');
  console.log(xhr.responseJSON);
  }
  });
  })
  </script>
@endsection