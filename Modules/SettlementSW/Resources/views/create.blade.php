    @extends('layouts.app')
    @section('title', 'Settlement Sw')
    @section('css')
    <style>

      /* Increase font size of meter sale form inputs by ~1pt */
    #meter-sales-form input.form-control,
    #meter-sales-form select.form-control {
        font-size: 13px; /* default is ~14px, so this is +1pt */
    }
      /* Custom SweetAlert Styling */

    /* Custom SweetAlert Styling - 50% Smaller */
    .swal-modal.custom-swal {
      width: 350px !important;               /* Half of the previous 500px */
      padding: 10px 12px !important;         /* Reduced padding */
      border-radius: 5px !important;
      box-shadow: 0 0 6px rgba(0, 0, 0, 0.1); /* Softer shadow */
    }

    .swal-modal.custom-swal .swal-title {
      font-size: 16px !important;            /* Reduced from 32px */
      font-weight: 600;
      margin-bottom: 10px;
      text-align: center;
    }

    .swal-modal.custom-swal .swal-text {
      font-size: 14px !important;            /* Reduced from 22px */
      text-align: center;
      margin-bottom: 10px;
    }

    .swal-modal.custom-swal .swal-footer {
      display: flex !important;
      flex-direction: row-reverse !important;
      justify-content: right !important;
      gap: 10px;                             /* Tighter spacing between buttons */
      padding: 5px 0;
    }

    .swal-modal.custom-swal .swal-button {
      padding: 5px 10px !important;
      font-size: 13px !important;
      min-width: unset !important;
    }


    .meter-sale-scroll {
        overflow-x: auto;
        max-width: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }



    .swal-btn-no {
      background-color: #dc3545 !important;
      color: #fff !important;
      font-size: 20px !important;
      padding: 10px 25px !important;
    }

    .swal-btn-yes {
      background-color: #28a745 !important;
      color: #fff !important;
      font-size: 20px !important;
      padding: 10px 25px !important;
    }

    body,
    .form-control,
    .table-custom th,
    .table-custom td {
      font-family: Calibri, sans-serif;
      font-size: 12px;
    }

    .settlement-page-wrapper {
      padding: 20px 25px;
    }
    .form-group-custom {
      flex: 1 1 14.28%; /* 7 columns in a row */
      max-width: 14.28%;
      padding: 8px;
    }
    .form-group-custom2 {
      flex: 1 1 14.28%; /* 7 columns in a row */
      max-width: 7%;
      padding: 8px;
    }
    input[type="text"].input_number,
    input[type="number"].form-control,
    .table-custom td:nth-child(n+4):not(:last-child) {
      text-align: right;
    }
    
    .table-custom th:nth-child(2) {
      width: 12%;
    }
    .product-column {
      width: 12%;
    }
    .starting-meter,
    .closing-meter {
      width: calc(current_width + 4%);
    }
    .swal-modal.small-swal {
      width: 250px !important;        /* Smaller width */
      padding: 10px 15px !important;  /* Compact padding */
      font-size: 12px !important;     /* Smaller font */
      line-height: 1.3 !important;    /* Tighten line spacing */
      box-sizing: border-box;
    }

    .swal-modal.small-swal .swal-title {
        font-size: 12px !important;
        margin: 5px 0 8px 0 !important;
        line-height: 1.2 !important;
    }

    .swal-modal.small-swal .swal-text {
        font-size: 12px !important;
        margin: 0 0 10px 0 !important;
        padding: 0 !important;
        line-height: 1.3 !important;
        text-align: center;
    }

    .swal-modal.small-swal .swal-footer {
        padding: 5px 0 0 0 !important;
        margin: 0 !important;
        text-align: center;
    }

    .swal-modal.small-swal .swal-button {
        padding: 4px 10px !important;
        font-size: 12px !important;
        margin: 0 5px !important;
    }



    </style>

    @endsection

    @section('content')
    @php
    $business_id = session()->get('user.business_id');
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

    $default_store = request()->session()->get('business.default_store');



    $p1=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_cash');
    $p2=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_cash_deposit');
    $p3=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_cards');
    $p4=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_cheque');
    $p5=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_payment_expenses');
    $p6=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_shortage');
    $p7=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_excess');
    $p8=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_credit_sales');
    $p9=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_loan_payments');
    $p10=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_owners_drawings');
    $p11=\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'settlement_sw_loan_to_customer');

        $other_income_final_total = 0.00;
        $customer_payment_total = 0.00;
        
    @endphp
    <style>
    /* Container */
    .settlement-container { padding: 1rem; }

    .modal-dialog{
      width: 100%!important;
    }

    /* Card */
    .card-custom {
      border: 1px solid #ddd;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .card-custom .card-header {
      background: #f8f9fa;
      font-weight: 500;
      font-size: 1.5rem;
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
      font-size: 12px;
      margin-bottom: .25rem;
      color: #495057;
    }
    .form-group-custom .form-control {
      width: 100%;
      padding: .375rem .75rem;
      font-size: 12px;
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
      font-size: 12px;
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
      font-size: 12px;
      border-radius: 4px;
      cursor: pointer;
    }
    .product_summary {
        white-space: nowrap;
        font-weight: 500;
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
      font-size: 12px;
      margin-top: .5rem;
    }

    . {
      display: inline-block;
      margin-right: 0.5rem;
      position: relative;
    }

    .:not(:last-child)::after {
      content: ",";
      position: absolute;
      right: -8px; /* fine-tune horizontal position */
      top: 60%;    /* move comma slightly lower */
      transform: translateY(-50%);
      font-size: 16px;
      color: #333;
      line-height: 1;
    }
    </style>
    
    <div class="breadcrumb-bar" style="margin-bottom: 15px;">
      <span style="font-size: 16px; color: #6c757d;"></span> 
      <strong>Settlement SW</strong>
    </div>
    <div class="settlement-page-wrapper">
      <div class="settlement-container" id="setlementForm">
        @php
          $settlement_no_new= !empty($active_settlement) ? $active_settlement->settlement_no : $settlement_no;
        @endphp
        <!-- Filters -->
        <div class="card-custom">
          <div class="card-header">Filters</div>
          <div class="card-body">
            <form id="filters-form" class="form-row-custom">
              <div class="form-group-custom ">
                {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
                {!! Form::text('settlement_no', $settlement_no_new, ['class' => 'form-control', 'readonly']); !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, !empty($active_settlement) ?
                $active_settlement->location_id : (!empty($default_location) ? $default_location : null), ['class'
                => 'form-control select2', 'id' => 'location_id',
                'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
              </div>
              <div class="form-group-custom ">
                {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                {!! Form::select('pump_operator_id', $pump_operators, !empty($active_settlement) ?
                $active_settlement->pump_operator_id : null, ['class' => 'form-control select2', 'id' =>
                'pump_operator_id', 'disabled' => !empty($select_pump_operator_in_settlement) ? false : true,
                'placeholder' => __('petro::lang.please_select')]); !!}
              </div>
              <div class="form-group-custom ">
                {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                {!! Form::text('transaction_date', null, ['class' =>
                'form-control transaction_date', 'required',
                'placeholder' => __(
                'petro::lang.transaction_date' ) ]); !!}
              </div>
              <div class="form-group-custom ">
                {!! Form::label('work_shift', __('petro::lang.work_shift').':') !!}
                {!! Form::select('work_shift[]', $wrok_shifts, !empty($active_settlement) ?
                $active_settlement->work_shift ?? [] : [], ['class' => 'form-control select2', 'id' => 'work_shift',
                'multiple']); !!}
              </div>
              
              <div class="form-group-custom ">
                {!! Form::label('shift_number', __('petro::lang.shift_number').':') !!}
                <select id="shift_number", class="form-control select2" multiple></select>
              </div>
              <div class="form-group-custom  full-width">
                {!! Form::text('note', 
                    !empty($active_settlement) 
                        ? (is_array($active_settlement->note) ? implode(', ', $active_settlement->note) : $active_settlement->note) 
                        : null, 
                    [
                        'class' => 'form-control note',
                        'placeholder' => __('petro::lang.note')
                    ]
                ) !!}
              </div>
            </form>
          </div>
        </div>

        <!-- Meter Sales -->
      <div class="card-custom">
        <div class="card-header">Meter Sales</div>
        <div class="card-body">
          <form id="meter-sales-form" class="form-row-custom align-items-end">

            <div class="form-group-custom " style="flex: 0 0 30%;">
              {!! Form::label('pump_no', __('petro::lang.pump_no').':') !!}
              {!! Form::select('pump_no', $pump_nos, !empty($temp_data->pump_no) ? $temp_data->pump_no : $pump_no, ['class' => 'form-control meter_sale_fields check_pumper select2', 'placeholder' => __('petro::lang.please_select')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 20%;">
              {!! Form::label('pump_starting_meter', __('petro::lang.pump_starting_meter')) !!}
              {!! Form::text('pump_starting_meter', !empty($temp_data->pump_starting_meter) ? $temp_data->pump_starting_meter : $pump_starting_meter, ['class' => 'form-control meter_sale_fields check_pumper input_number pump_starting_meter', 'required', 'readonly', 'placeholder' => __('petro::lang.pump_starting_meter')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 20%;">
              {!! Form::label('pump_closing_meter', __('petro::lang.pump_closing_meter')) !!}
              {!! Form::text('pump_closing_meter', !empty($temp_data->pump_closing_meter) ? $temp_data->pump_closing_meter : $pump_closing_meter, ['class' => 'form-control meter_sale_fields check_pumper input_number pump_closing_meter', 'required', 'step' => '0.001', 'min' => '0', 'placeholder' => __('petro::lang.pump_closing_meter')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 25%;">
              {!! Form::label('sold_qty', __('petro::lang.sold_qty')) !!}
              {!! Form::text('sold_qty', !empty($temp_data->sold_qty) ? number_format($temp_data->sold_qty, 3) : number_format($sold_qty, 3), ['class' => 'form-control meter_sale_fields check_pumper sold_qty input_number', 'required', 'disabled', 'placeholder' => __('petro::lang.sold_qty'), 'step' => '0.001']) !!}
              <input type="hidden" class="meter_sale_fields is_from_pumper" id="is_from_pumper" value="{{ !empty($temp_data->is_from_pumper) ? $temp_data->is_from_pumper : 0 }}">
              <input type="hidden" class="meter_sale_fields assignment_id" id="assignment_id" value="{{ !empty($temp_data->assignment_id) ? $temp_data->assignment_id : 0 }}">
              <input type="hidden" class="meter_sale_fields pumper_entry_id" id="pumper_entry_id" value="{{ !empty($temp_data->pumper_entry_id) ? $temp_data->pumper_entry_id : 0 }}">
            </div>

            <div class="form-group-custom " style="flex: 0 0 30%;">
              {!! Form::label('unit_price', __('petro::lang.unit_price')) !!}
              {!! Form::text('meter_sale_unit_price', !empty($temp_data->unit_price) ? $temp_data->unit_price : $meter_sale_unit_price, ['id' => 'meter_sale_unit_price', 'class' => 'form-control meter_sale_fields check_pumper unit_price input_number', 'readonly', 'placeholder' => __('petro::lang.unit_price')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 30%;">
              {!! Form::label('testing_qty', __('petro::lang.testing_qty')) !!}
              {!! Form::text('testing_qty', !empty($temp_data->testing_qty) ? $temp_data->testing_qty : $testing_qty, ['class' => 'form-control check_pumper input_number testing_qty', 'required', 'placeholder' => __('petro::lang.testing_qty')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 20%;">
              {!! Form::label('meter_sale_discount_type', __('petro::lang.discount_type')) !!}
              {!! Form::select('meter_sale_discount_type', $discount_types, !empty($temp_data->discount_type) ? $temp_data->discount_type : $meter_sale_discount_type, ['class' => 'form-control meter_sale_fields check_pumper meter_sale_discount_type', 'required', 'placeholder' => __('petro::lang.please_select')]) !!}
            </div>

            <div class="form-group-custom " style="flex: 0 0 30%;">
              {!! Form::label('meter_sale_discount', __('petro::lang.discount')) !!}
              {!! Form::text('meter_sale_discount', !empty($temp_data->discount) ? $temp_data->discount : $meter_sale_discount, ['class' => 'form-control meter_sale_fields check_pumper meter_sale_discount input_number', 'required', 'placeholder' => __('petro::lang.discount')]) !!}
            </div>

            <div class="form-group-custom " style="display:flex;align-items:flex-end;justify-content:center;">
              <button type="button" class="btn-add" id="add-meter-sale">
                <i class="fa fa-plus"></i>
              </button>
            </div>
          </form>

          <input type="hidden" value="{{ $final_total ?? 0 }}" name="meter_sale_total" id="meter_sale_total">
          <div class="table-responsive meter-sale-scroll">
            <table id="meter_sale_table" class="table-custom">
              <thead>
                <tr>
                  <th>@lang('petro::lang.code' )</th>
                  <th class="product-column">@lang('petro::lang.products' )</th>
                  <th>@lang('petro::lang.pump' )</th>
                  <th class="starting-meter">@lang('petro::lang.starting_meter')</th>
                  <th class="closing-meter">@lang('petro::lang.closing_meter')</th>
                  <th class="price-meter">@lang('petro::lang.price')</th>
                  <th>@lang('petro::lang.sold_qty' )</th> {{-- Qty = Closing Meter- Starting Meter - Testing Qty --}}
                  <th style="width: 10px;">@lang('petro::lang.discount_type' )</th>
                  <th style="width: 6px;">@lang('petro::lang.discount_value' )</th>
                  <th style="width: 10px;">@lang('petro::lang.testing_qty' )</th>
                  <th class="total-qty-meter">@lang('petro::lang.total_qty' )</th>
                  <th class="before-discount-meter">@lang('petro::lang.before_discount' )</th>
                  <th class="after-discount-meter">@lang('petro::lang.after_discount' )</th>
                  <th class="action-meter">@lang('petro::lang.action' )</th>
                </tr>
              </thead>
              <tbody id="meter-sales-tbody">
                {{-- rows --}}
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6"></td>
                  <td><span class="product_summary"></span></td>
                  <td colspan="6" class="text-right">Meter Sales Total:</td>
                  <td colspan="2" id="meter-sales-total">0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

        <!-- Other Sales -->
        <div class="card-custom">
          <div class="card-header">Other Sales</div>
          <div class="card-body">
            <form id="other-sales-form" class="form-row-custom align-items-end">
              <div class="d-flex" style="width: 100%;">
                <div class="" style="flex: 0 0 16%; margin: auto;">
                  {!! Form::label('store', __('petro::lang.store').':') !!}
                  {!! Form::select('store_id', $stores, !empty($temp_data->store_id) ? $temp_data->store_id : $default_store, [
                      'class' => 'form-control check_pumper select2',
                      'id' => 'store_id',
                      'placeholder' => __('petro::lang.please_select')
                  ]) !!}
                </div>
  
                <div class=" " style="flex: 0 0 30%; margin: auto;">
                  {!! Form::label('item', __('SettlementSW::lang.Select_Product').':') !!}
                  {!! Form::select('item', $items, !empty($temp_data->item) ? $temp_data->item : null, [
                      'class' => 'form-control other_sale_fields check_pumper select2',
                      'placeholder' => __('petro::lang.please_select')
                  ]) !!}
                </div>
  
                <div class=" " style="flex: 0 0 13%; margin: auto;">
                  {!! Form::label('balance_stock', __('petro::lang.balance_stock')) !!}
                  {!! Form::text('balance_stock', null, [
                      'class' => 'form-control other_sale_fields check_pumper input_number balance_stock',
                      'required',
                      'id' => 'balance_stock',
                      'readonly',
                      'placeholder' => __('petro::lang.balance_stock')
                  ]) !!}
                </div>
  
                <div class=" " style="flex: 0 0 13%; margin: auto;">
                  {!! Form::label('other_sale_price', __('petro::lang.price')) !!}
                  {!! Form::text('other_sale_price', !empty($temp_data->other_sale_price) ? $temp_data->other_sale_price : null, [
                      'class' => 'form-control other_sale_fields check_pumper input_number other_sale_price',
                      'required',
                      'readonly',
                      'placeholder' => __('petro::lang.price')
                  ]) !!}
                </div>
  
                <div class=" " style="flex: 0 0 13%; margin: auto;">
                  {!! Form::label('other_sale_qty', __('petro::lang.qty')) !!}
                  {!! Form::text('other_sale_qty', !empty($temp_data->other_sale_qty) ? $temp_data->other_sale_qty : null, [
                      'class' => 'form-control other_sale_fields check_pumper qty input_number',
                      'required',
                      'id' => 'other_sale_qty',
                      'placeholder' => __('petro::lang.qty')
                  ]) !!}
                </div>
  
                <div class=" " style="flex: 0 0 11%; margin: auto;">
                  {!! Form::label('other_sale_discount_type', __('petro::lang.discount_type')) !!}
                  {!! Form::select('other_sale_discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'],
                      !empty($temp_data->other_sale_discount_type) ? $temp_data->other_sale_discount_type : null, [
                      'class' => 'form-control other_sale_fields check_pumper other_sale_discount_type',
                      'required',
                      'placeholder' => __('petro::lang.please_select')
                  ]) !!}
                </div>
              </div>

              <div class="form-group-custom " style="flex: 0 0 20%;">
                {!! Form::label('other_sale_discount', __('petro::lang.discount')) !!}
                {!! Form::text('other_sale_discount', !empty($temp_data->other_sale_discount) ? $temp_data->other_sale_discount : null, [
                    'class' => 'form-control other_sale_fields check_pumper input_number other_sale_discount',
                    'required',
                    'placeholder' => __('petro::lang.discount')
                ]) !!}
              </div>

              <input type="hidden" value="{{ $pump_other_sale_final_total ?? 0 }}" name="other_sale_total" id="other_sale_total">
              <input type="hidden" value="{{ !empty($temp_data->shift_operator_other_sale_total) ? $temp_data->shift_operator_other_sale_total : 0 }}" id="shift_operator_other_sale_total">
              <input type="hidden" value="{{ $check_qty }}" id="allowoverselling">

              <div class="form-group-custom " style="display:flex;align-items:flex-end;justify-content:center;">
                <button type="button" class="btn-add" id="add-other-sale"><i class="fa fa-plus"></i></button>
              </div>
            </form>

            <table class="table-custom">
              <thead>
                <tr>
                  <th style="width: 15%;">Code</th>
                  <th style="width: 20%;">Products</th>
                  <th style="width: 10%;">Balance Stock</th>
                  <th style="width: 10%;">Price</th>
                  <th style="width: 10%;">Qty</th>
                  <th style="width: 8%;">Discount Type</th>
                  <th style="width: 8%;">Discount Value</th>
                  <th style="width: 10%;">Before Discount</th>
                  <th style="width: 10%;">After Discount</th>
                  <th style="width: 9%;">Action</th>
                </tr>
              </thead>
              <tbody id="other-sales-tbody">
                {{-- Dynamic rows via JS --}}
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="8" class="text-right">Other Sales Total:</td>
                  <td colspan="2" id="other-sale-after-discount-total">0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Other Income & Customer Payments -->
        <div style="gap:1rem; flex-wrap:wrap;">
          <!-- Other Income -->
          @if(isset($is_other_income) && $is_other_income != 0)
            <div style="flex:1; min-width:300px;">
              <div class="card-custom">
                <div class="card-header">Other Income</div>
                <div class="card-body">
                  <form id="other-income-form" class="form-row-custom align-items-end" style="margin-bottom: 10px;">
                  <div class="form-group-custom " style="flex: 2;">
                    {!! Form::label('other_income_product_id', __('SettlementSW::lang.service')) !!}
                    {!! Form::select('other_income_product_id', $services, !empty($temp_data->other_income_product_id) ? $temp_data->other_income_product_id : null, [
                        'class' => 'form-control other_income_fields check_pumper other_income_product',
                        'required',
                        'placeholder' => __('SettlementSW::lang.please_select')
                    ]) !!}
                  </div>

                  <div class="form-group-custom " style="flex:2;">
                    {!! Form::label('other_income_reason', __('SettlementSW::lang.details')) !!}
                    {!! Form::text('other_income_reason', !empty($temp_data->other_income_reason) ? $temp_data->other_income_reason : null, [
                        'class' => 'form-control other_income_fields check_pumper other_income_reason',
                        'required',
                        'placeholder' => __('SettlementSW::lang.details')
                    ]) !!}
                  </div>

                  <div class="form-group-custom " style="flex:1;">
                    {!! Form::label('other_income_qty', __('SettlementSW::lang.amount')) !!}
                    {!! Form::text('other_income_qty', !empty($temp_data->other_income_qty) ? $temp_data->other_income_qty : null, [
                        'class' => 'form-control other_income_fields check_pumper other_income_qty input_number',
                        'required', 'readonly', 'id' => 'other_income_price',
                        'placeholder' => __('SettlementSW::lang.amount')
                    ]) !!}
                  </div>

                  <div class="form-group-custom " style="display:flex;align-items:flex-end;justify-content:center;">
                    @can('edit_other_income_prices')
                    <button type="button" class="btn btn-warning edit_price_other_income" data-toggle="modal" data-target="#edit_price_other_income" style="margin-right: 10px;">@lang('petro::lang.edit_price')</button>
                    @endcan
                    <button type="button" class="btn-add" id="add-other-income"><i class="fa fa-plus"></i></button>
                  </div>

                  <input type="hidden" value="{{ $other_income_final_total ?? 0 }}" name="other_income_total" id="other_income_total">
                </form>
                <table class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>Service</th>
                              <th>Details</th>
                              <th>Amount</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="other-income-tbody">

                      </tbody>

                      <tfoot>
                          <tr>
                              <td colspan="7" style="text-align: right; font-weight: bold;">Total :</td>
                              <td colspan="3" style="text-align: left; font-weight: bold;" id="other-income-total">0.00</td>
                          </tr>
                      </tfoot>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal" tabindex="-1" role="dialog" data-backdrop="false" id="edit_price_other_income" style="background: rgba(0, 0, 0, 0.5);">
              <div class="modal-dialog" role="document" style="max-width: 550px;">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title">@lang('petro::lang.edit_price')</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body">
                          <label for="other_income_edit_price">@lang('petro::lang.price'): </label>
                          <input type="text" value="0" name="other_income_edit_price" id="other_income_edit_price" placeholder="Price" class="form-control">
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="save_edit_price_other_income_btn">Save</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
          </div>
          @endif
          <!-- Customer Payments -->
          @if(isset($is_customer_payments) && $is_customer_payments != 0)
          <div style="flex:1; min-width:300px;">
            <div class="card-custom">
              <div class="card-header">Customer Payments</div>
              <div class="card-body">
                <form id="cust-payments-form" class="form-row-custom align-items-end">
                  <div class="d-flex" style="width: 100%;">
                    <div class="form-group" style="flex: 0 0 18%; margin: 5px;">
                      {!! Form::label('settlement_customer_payment_no', __('SettlementSW::lang.settlement_customer_payment_no') . ':') !!}
                      {!! Form::text('settlement_customer_payment_no', $customer_payment_settlement_no ?? '', ['class' => 'form-control', 'readonly']) !!}
                    </div>
                    <div class="form-group-custom " style="flex: 0 0 18%; margin: 5px;">
                      {!! Form::label('customer_payment_customer_id', __('SettlementSW::lang.customer').':') !!}
                      {!! Form::select('customer_payment_customer_id', $customers, !empty($temp_data->customer_payment_customer_id) ? $temp_data->customer_payment_customer_id : null, [
                      'class' => 'form-control select2',
                      'style' => 'width: 100%;'
                      ]) !!}
                    </div>

                    <div class="form-group-custom " style="flex: 0 0 17%; margin: 5px;">
                      {!! Form::label('customer_payment_payment_method', __('SettlementSW::lang.payment_method').':') !!}
                      {!! Form::select('customer_payment_payment_method', ['cash' => 'Cash', 'card' => 'Card', 'cheque' => 'Cheque'],
                      !empty($temp_data->customer_payment_payment_method) ? $temp_data->customer_payment_payment_method : null,
                      [
                      'class' => 'form-control select2',
                      'style' => 'width: 100%;',
                      'placeholder' => __('SettlementSW::lang.please_select')
                      ]) !!}
                    </div>

                    <div class="form-group-custom hide card_div" style="flex: 0 0 12.5%; margin: 5px;">
                      {!! Form::label('customer_payment_account_module', __('lang_v1.payment_account').':') !!}
                      {!! Form::select('customer_payment_account_module', $account_modules,
                      !empty($temp_data->customer_payment_payment_method) ? $temp_data->customer_payment_payment_method : null,
                      [
                      'class' => 'form-control select2',
                      'style' => 'width: 100%;',
                      'placeholder' => __('SettlementSW::lang.please_select')
                      ]) !!}
                    </div>

                    <div class="form-group-custom  hide cheque_divs" style="flex: 0 0 11.5%; margin: 5px;">
                      {!! Form::label('customer_payment_bank_name', __('SettlementSW::lang.bank_name')) !!}
                      {!! Form::text('customer_payment_bank_name', !empty($temp_data->customer_payment_bank_name) ? $temp_data->customer_payment_bank_name : null, [
                      'class' => 'form-control customer_payment_fields bank_name',
                      'placeholder' => __('SettlementSW::lang.bank_name')
                      ]) !!}
                    </div>

                    <div class="form-group-custom  hide cheque_divs" style="flex: 0 0 9%; margin: 5px;">
                      {!! Form::label('customer_payment_cheque_date', __('SettlementSW::lang.cheque_date')) !!}
                      {!! Form::text('customer_payment_cheque_date', !empty($temp_data->customer_payment_cheque_date) ? $temp_data->customer_payment_cheque_date : null, [
                      'class' => 'form-control cheque_date',
                      'placeholder' => __('SettlementSW::lang.cheque_date')
                      ]) !!}
                    </div>

                    <div class="form-group-custom  hide cheque_divs" style="flex: 0 0 9%; margin: 5px;">
                      {!! Form::label('customer_payment_cheque_number', __('SettlementSW::lang.cheque_number')) !!}
                      {!! Form::text('customer_payment_cheque_number', !empty($temp_data->customer_payment_cheque_number) ? $temp_data->customer_payment_cheque_number : null, [
                      'class' => 'form-control customer_payment_fields cheque_number',
                      'placeholder' => __('SettlementSW::lang.cheque_number')
                      ]) !!}
                    </div>

                    <div class="form-group-custom " style="flex: 0 0 10.5%; margin: 5px;">
                      {!! Form::label('customer_payment_amount', __('SettlementSW::lang.amount')) !!}
                      {!! Form::text('customer_payment_amount', !empty($temp_data->customer_payment_amount) ? $temp_data->customer_payment_amount : null, [
                      'class' => 'form-control customer_payment_fields customer_payment',
                      'required',
                      'id' => 'customer_payment_amount',
                      'placeholder' => __('SettlementSW::lang.amount')
                      ]) !!}
                    </div>

                    <div class="form-group-custom " style="display:flex;align-items:flex-end;justify-content:center;flex: 0 0 2.5%;margin: 8px;">
                      <button type="button" class="btn-add" id="add-payment"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>

                  <input type="hidden" value="{{ $customer_payment_total ?? 0 }}" name="customer_payment_total" id="customer_payment_total">
                </form>
                <ul class="list-unstyled-custom d-none" id="cust-payments-list">
                  <li>Customer… <span>Amount</span></li>
                </ul>
                <div id="business-payments-cust" class="summary-text d-none">Customer Payments Total: <span id="cust-payments-total">0.00</span></div>
              </div>
            </div>
          </div>
          @endif
        </div>

        <!-- Credit Sales -->
        <div class="card-custom">
          <div class="card-header">Credit Sales</div>
          <div class="card-body">
            <div style="margin-bottom:1rem;">
              @if (auth()->user()->can('customer.create'))
              <button type="button" data-href="{{action('ContactController@create',['type' => 'customer','is_credit' => true])}}"
              data-container=".contact_modal" class="btn-primary-custom btn-modal">{{__('SettlementSW::lang.add_new_customer')}}</button>

              @endif
            </div>
            <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            </div>
              <form id="credit-sales-form" class="form-row-custom align-items-end">
              <div class="form-group-custom ">
                {!! Form::label('sw_credit_sale_customer_iddelete_expense_payment', __('SettlementSW::lang.customer').':') !!}
                {!! Form::select('sw_credit_sale_customer_iddelete_expense_payment', !empty($only_walkin) ? [] : $credit_customers,
                  !empty($temp_data->credit_sale_customer_id) ? $temp_data->credit_sale_customer_id : null,
                  ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_order_number', __('SettlementSW::lang.order_number')) !!}
                {!! Form::text('sw_order_number', !empty($temp_data->order_number) ? $temp_data->order_number : null,
                  ['class' => 'form-control credit_sale_fields sw_order_number',
                  'placeholder' => __('SettlementSW::lang.order_number')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_order_date', __('SettlementSW::lang.order_date')) !!}
                {!! Form::text('sw_order_date', !empty($temp_data->order_date) ? $temp_data->order_date : null,
                  ['class' => 'form-control sw_order_date',
                  'placeholder' => __('SettlementSW::lang.order_date')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_customer_reference', __('SettlementSW::lang.select_customer_vehicle_no')) !!}
                {!! Form::select('sw_customer_reference', [], !empty($temp_data->customer_reference) ? $temp_data->customer_reference : null,
                  ['class' => 'form-control credit_sale_fields select2 sw_customer_reference',
                  'required', 'id' => 'sw_customer_reference', 'style' => 'width: 100%',
                  'placeholder' => __('SettlementSW::lang.please_select')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_credit_sale_product_id', __('SettlementSW::lang.credit_sale_product').':') !!}
                {!! Form::select('sw_credit_sale_product_id', $products,
                  !empty($temp_data->credit_sale_product_id) ? $temp_data->credit_sale_product_id : null,
                  ['class' => 'form-control select2', 'style' => 'width: 100%',
                  'placeholder' => __('SettlementSW::lang.please_select')]) !!}
              </div>

              <input type="hidden" id="manual_discount" value="{{ auth()->user()->can('manual_discount') ? 1 : 0 }}">
              <input type="hidden" id="is_modal" value="1">

              <div class="form-group-custom ">
                {!! Form::label('sw_credit_sale_qty', __('SettlementSW::lang.credit_sale_qty')) !!}
                {!! Form::text('sw_credit_sale_qty', !empty($temp_data->credit_sale_qty) ? $temp_data->credit_sale_qty : null,
                  ['class' => 'form-control credit_sale_fields input_number sw_credit_sale_qty',
                  'placeholder' => __('SettlementSW::lang.credit_sale_qty')]) !!}
                <input type="hidden" name="credit_sale_qty_hidden" value="0" id="credit_sale_qty_hidden">
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_unit_price', __('SettlementSW::lang.unit_price')) !!}
                {!! Form::text('sw_unit_price', !empty($temp_data->unit_price) ? $temp_data->unit_price : null,
                  ['class' => 'form-control input_number sw_unit_price',
                  'readonly', 'placeholder' => __('SettlementSW::lang.unit_price')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_unit_discount', __('SettlementSW::lang.unit_discount')) !!}
                {!! Form::text('sw_unit_discount', !empty($temp_data->unit_discount) ? $temp_data->unit_discount : null,
                  ['class' => 'form-control input_number sw_unit_discount',
                  'disabled' => true, 'placeholder' => __('SettlementSW::lang.unit_discount')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_credit_total_amount', __('SettlementSW::lang.amount') . __('SettlementSW::lang.before_discount_cr')) !!}
                {!! Form::text('sw_credit_total_amount', !empty($temp_data->credit_total_amount) ? $temp_data->credit_total_amount : null,
                  ['id' => 'sw_credit_total_amount', 'class' => 'form-control credit_sale_fields cust_input_number sw_credit_total_amount',
                  'required', 'disabled' => true, 'placeholder' => __('SettlementSW::lang.credit_total_amount')]) !!}
              </div>

              <div class="form-group-custom ">
                {!! Form::label('sw_credit_sale_amount', __('SettlementSW::lang.amount') . __('SettlementSW::lang.after_discount_cr')) !!}
                {!! Form::text('sw_credit_sale_amount', !empty($temp_data->credit_sale_amount) ? $temp_data->credit_sale_amount : null,
                  ['id' => 'sw_credit_sale_amount', 'class' => 'form-control credit_sale_fields cust_input_number sw_credit_sale_amount',
                  'required', 'disabled' => true, 'placeholder' => __('SettlementSW::lang.amount')]) !!}
                <input type="hidden" name="credit_sale_amount_hidden" value="0" id="credit_sale_amount_hidden">
              </div>
              
              <div class="form-group-custom " id="customer-reference-one-time-wrapper">
                <label for="customer_reference_one_time">
                  {{__('SettlementSW::lang.enter_customer_vehicle_no')}}
                </label>
                <input type="text" class="form-control customer_reference_one_time"
                      id="customer_reference_one_time"
                      name="sw_customer_reference"
                      placeholder="{{__('SettlementSW::lang.enter_customer_vehicle_no')}}">
              </div>
                  
              <div class="form-group-custom" style="display:flex;align-items:flex-end;justify-content:bottom;">
                  <button type="button" class="btn-primary-custom" id="noteButton">+ Note</button><br>
                  <div id="noteDisplay" style="margin-left: 5px; color: #333;"></div>
              </div>

              <div id="noteModal" style="display: none; position: fixed; top: 20%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border: 1px solid #aaa; border-radius: 8px; z-index: 1000; width: 300px;">
                  <h4>{{ __('SettlementSW::lang.Add_note') }} </h4>
                  <div class="form-group">
                      {!! Form::textarea('note_temp', null, [
                          'class' => 'form-control',
                          'id' => 'noteInput',
                          'rows' => 4
                      ]) !!}
                  </div>
                  <button type="button" class="btn btn-primary" style="padding: 5px; margin-right: 10px" id="saveNote">{{ __('SettlementSW::lang.Save') }}</button>
                  <button type="button" class="btn btn-light" style="padding: 5px; margin-right: 10px" id="closeNote">{{ __('SettlementSW::lang.Close') }}</button>
              </div>

              {{-- Overlay  modal --}}
              <div id="noteOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); z-index: 999;"></div>
              {!! Form::hidden('note', null, ['id' => 'noteHidden']) !!}

              <div class="form-group " style="display:flex;align-items:flex-end;justify-content:center;">
                <button type="button" class="btn-add" id="sw_add-credit-sale">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
            </form>
            {{-- Credit Sales Data Table --}}
              <table class="table table-bordered mt-4" id="credit-sales-table">
                <thead>
                  <tr>
                    <th>Customer Name</th>
                    <th>Outstanding</th>
                    <th>Limit</th>
                    <th>Order No</th>
                    <th>Order Date</th>
                    <th>Customer Reference</th>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Sub Total</th>
                    <th>Discount Total</th>
                    <th>Total</th>
                    <th>Note</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="credit-sales-tbody">
                  {{-- Dynamic rows will be inserted here via JavaScript --}}
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="9" class="text-right"><strong>Total:</strong></td>
                    <td id="credit-sales-subtotal">0.00</td>
                    <td id="credit-sales-discount-total">0.00</td>
                    <td id="credit-sales-total">0.00</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
            <div class="summary-text total-credit-sales-value hidden">Total Credit Sales: <span id="credit-sales-total">0.00</span></div>
          </div>
        </div>

        <!-- Expanse tab -->
        @if(isset($is_expenses) && $is_expenses != 0)
          <div class="card-custom">
          <div class="card-header">Expense</div>
            <div class="card-body">
              <div class="row">
                  <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {!! Form::label('sw_expense_number', __( 'petro::lang.expense_number' )) !!}
                          {!! Form::text('sw_expense_number', $expense_payment_settlement_no, [
                              'class' => 'form-control check_pumper sw_expense_number expense_fields2',
                              'required',
                              'readonly',
                              'placeholder' => __( 'petro::lang.expense_number' )
                          ]) !!}
                      </div>
                  </div>
                      <div class="col-sm-2">
                          <div class="form-group">
                              {!! Form::label('sw_expense_category', __('petro::lang.category').':') !!}
                              {!! Form::select('sw_expense_category', $expense_categories, null, ['class' => 'form-control check_pumper select2 expense_fields2', 'style' => 'width: 100%;',
                              'placeholder' => __('petro::lang.please_select')]); !!}
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              {!! Form::label('sw_reference_no', __( 'petro::lang.reference_no' )) !!}
                              {!! Form::text('sw_reference_no', null, ['class' => 'form-control check_pumper sw_reference_no expense_fields2', 'required',
                              'placeholder' => __(
                              'petro::lang.reference_no' ) ]); !!}
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              {!! Form::label('sw_expense_amount', __( 'petro::lang.amount' )) !!}
                              {!! Form::text('sw_expense_amount', null, ['class' => 'form-control check_pumper sw_expense_amount expense_fields2', 'required',
                              'placeholder' => __(
                              'petro::lang.amount' ) ]); !!}
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              {!! Form::label('sw_expense_reason', __( 'petro::lang.reason' )) !!}
                              {!! Form::text('sw_expense_reason', null, ['class' => 'form-control check_pumper sw_expense_reason expense_fields2', 'required',
                              'placeholder' => __(
                              'petro::lang.reason' ) ]); !!}
                          </div>
                      </div>
                      <div class="col-sm-2">
                          <div class="form-group">
                              {!! Form::label('sw_expense_account', __('petro::lang.expense_account').':') !!}
                              {!! Form::select('sw_expense_account', $expense_accounts, null, ['class' => 'form-control check_pumper select2 expense_fields2', 'style' => 'width: 100%;',
                              'placeholder' => __('petro::lang.please_select')]); !!}
                          </div>
                      </div>
                      <div class="form-group-custom " style="display:flex;align-items:flex-end;justify-content:center;">
                        <button type="submit" class="btn-add sw_expense_add"><i class="fa fa-plus"></i></button>
                      </div>
                      
                  </div>
              </div>
            </div>
          <br>
          <br>
          <div class="row">
              <div class="col-md-12">
              <table class="table table-bordered table-striped" id="expense_table_new">
                      <thead>
                          <tr>
                              <th>Expense Number</th>
                              <th>Expense Category</th>
                              <th>Reference No</th>
                              <th>Expense Account</th>
                              <th>Reason</th>
                              <th>Amount</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="expense-tbody">

                      </tbody>

                      <tfoot>
                          <tr>
                              <td colspan="7" style="text-align: right; font-weight: bold;">Total :</td>
                              <td colspan="3" style="text-align: left; font-weight: bold;" class="sw_expense_total">0.00</td>
                          </tr>
                      </tfoot>
                  </table>
              </div>
          </div>
          </div>
        @endif

        @if(isset($is_payment) && $is_payment != 0)
        <!-- Payments Summary -->
        <div id="add_payment_container"></div>
        @endif

        <!-- Save -->
        <div style="text-align:right; margin-bottom:2rem;">
          <button type="button" class="btn-primary-custom">Save</button>
        </div>

      </div>
    </div>
    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade add_payment" role="dialog" aria-labelledby="gridSystemModalLabel" style="overflow-y: auto;">
    </div>
    <div class="modal fade preview_settlement" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print"></div>
    @endsection

    @section('javascript')
    <script src="{{ asset('js/petro_payment.js') }}"></script>
    <script>
$('#settlement_print').css('visibility', 'hidden');
      function formatWithCommas(value) {
          if (isNaN(value) || value === '') return value;
          const parts = parseFloat(value).toFixed(3).split(".");
          parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          return parts.join(".");
      }
      function formatWithCommasFixed(value, decimals = 3) {
        if (isNaN(value) || value === '') return value;
        const parts = parseFloat(value).toFixed(decimals).split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
        @if(isset($is_payment) && $is_payment != 0)
          $(document).ready(function () {
            let operatorId = $('#pump_operator_id').val();

            let shiftIds = $('#shift_number').val();

            if (operatorId && shiftIds) {
                let url = "{{ action('\Modules\Petro\Http\Controllers\AddPaymentController@create') }}" +
                          "?settlement_no={{ $settlement_no }}" +
                          "&operator_id=" + operatorId +
                          "&shift_ids=" + shiftIds +
                          "&provider=SET_SW"+
                          "&settlement_page=1"; 

                $('#add_payment_container').load(url, function(response, status, xhr) {
                  const showCashTab = <?php echo $p1 == 1 ? 'true' : 'false'; ?>;
                  const showCashDeposit = <?php echo $p2 == 1 ? 'true' : 'false'; ?>;
                  const showCards = <?php echo $p3 == 1 ? 'true' : 'false'; ?>;
                  const showCheque = <?php echo $p4 == 1 ? 'true' : 'false'; ?>;
                  const showExpenses = <?php echo $p5 == 1 ? 'true' : 'false'; ?>;
                  const showShortage = <?php echo $p6 == 1 ? 'true' : 'false'; ?>;
                  const showExcess = <?php echo $p7 == 1 ? 'true' : 'false'; ?>;
                  const showCreditSale = <?php echo $p8 == 1 ? 'true' : 'false'; ?>;
                  const showLoanPayments = <?php echo $p9 == 1 ? 'true' : 'false'; ?>;
                  const showOwnersDrawings = <?php echo $p10 == 1 ? 'true' : 'false'; ?>;
                  const showLoanToCustomer = <?php echo $p11 == 1 ? 'true' : 'false'; ?>;
                  if (!showCashTab) {
                      const el = document.querySelector('.cash_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showCashDeposit) {
                      const el = document.querySelector('.cash_deposit_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showCards) {
                      const el = document.querySelector('.cards_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showCheque) {
                      const el = document.querySelector('.cheques_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showExpenses) {
                      const el = document.querySelector('.expense_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showShortage) {
                      const el = document.querySelector('.shortage_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showExcess) {
                      const el = document.querySelector('.excess_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showCreditSale) {
                      const el = document.querySelector('.credit_sales_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showLoanPayments) {
                      const el = document.querySelector('.loan_payments_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }

                  if (!showOwnersDrawings) {
                      const el = document.querySelector('.drawing_payments_tab');
                      if (el) el.closest('li').style.display = 'none';
                  }
                  if (!showLoanToCustomer) {
                      const loanTabLink = document.querySelector('.settlement_customer_loans_tab');
                      if (loanTabLink) {
                          const parentLi = loanTabLink.closest('li');
                          if (parentLi) {
                              parentLi.style.display = 'none';
                          }
                      }
                  }
                    if (status === "error") {
                        console.error("Error loading Add Payment HTML:", xhr.status, xhr.statusText);
                        $('#add_payment_container').html('<p class="text-danger">Failed to load payment section.</p>');
                    }
                });
            } else {
                console.warn('Missing operator or shift ID');
            }
        });
      @endif

        function calculateTotal_interne(table_name, class_name_td, output_element) {
            let total = 0.0;
            $(table_name + " tbody")
                .find(class_name_td)
                .each(function () {
                    total += parseFloat(__number_uf($(this).text()));
                });
            $(output_element).text(__number_f(total, false, false, __currency_precision));
        }
        $(document).on("click", ".sw_expense_add", function () {
          if ($("#sw_expense_amount").val() == "") {
              toastr.error("Please enter amount");
              return false;
          }
          var settlement_no = $("#settlement_no").val();
          var expense_number = $("#sw_expense_number").val();
          var reference_no = $("#sw_reference_no").val();
          var expense_account = $("#sw_expense_account").val();
          var expense_account_name = $("#sw_expense_account :selected").text();
          var expense_category = $("#sw_expense_category").val();
          var expense_category_name = $("#sw_expense_category :selected").text();
          var expense_reason = $("#sw_expense_reason").val();
          var expense_amount = $("#sw_expense_amount").val();
          var is_edit = $("#is_edit").val() ?? 0;
          
          $.ajax({
              method: "post",
              url: "/settlement-sw/save-expense-payment",
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
                      $("#expense-tbody").prepend(
                        `
                        <tr> 
                            <td>${expense_number}</td>
                            <td>${expense_category_name}</td>
                            <td>${reference_no}</td>
                            <td>${expense_account_name}</td>
                            <td>${expense_reason}</td>
                            <td class="sw_expense_amount">${formatWithCommasFixed(expense_amount, __currency_precision)}</td>
                            <td>
                              <button type="button" class="btn btn-xs btn-danger sw_delete_expense_payment"
                                      data-href="/petro/settlement/payment/delete-expense-payment/${settlement_expense_payment_id}">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                        </tr>
                        `
                      );
                      calculateTotal("#expense_table_new", ".sw_expense_amount", ".sw_expense_total");
                      calculate_payment_tab_total();
                      saveTempData();
                  }
              },
          });
      });
      $(document).on("click", ".sw_delete_expense_payment", function () {
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
                      calculateTotal("#expense_table_new", ".sw_expense_amount", ".sw_expense_total");
                      saveTempData();
                      calculate_payment_tab_total();
                  } else {
                      toastr.error(result.msg);
                  }
              },
          });
      });
        function appendCreditSaleRow(data) {
          const row = `
            <tr>
              <td>${data.customer_name}</td>
              <td>${formatWithCommasFixed(data.outstanding)}</td>
              <td>${formatWithCommasFixed(data.limit)}</td>
              <td>${data.order_no}</td>
              <td>${data.order_date}</td>
              <td>${data.customer_reference}</td>
              <td>${data.product}</td>
              <td>${formatWithCommasFixed(data.unit_price)}</td>
              <td>${formatWithCommasFixed(data.qty)}</td>
              <td>${formatWithCommasFixed(data.sub_total)}</td>
              <td>${formatWithCommasFixed(data.discount_total)}</td>
              <td>${formatWithCommasFixed(data.total)}</td>
              <td>
                <a href="#" 
                  class="btn-note-view" 
                  data-note="${data.note ?? ''}">
                  <i class="fa fa-eye" aria-hidden="true"></i> {{ __("messages.view") }}
                </a>
              </td> 
              <td><button type="button" class="btn btn-danger btn-sm remove-credit-sale-row">X</button></td>
            </tr>
          `;
          $('#credit-sales-tbody').append(row);
          updateCreditSaleTotals();
        }

        function updateCreditSaleTotals() {
          let subTotal = 0;
          let discountTotal = 0;
          let total = 0;

          $('#credit-sales-tbody tr').each(function () {
            subTotal += parseFloat($(this).find('td').eq(9).text()) || 0;
            discountTotal += parseFloat($(this).find('td').eq(10).text()) || 0;
            total += parseFloat($(this).find('td').eq(11).text()) || 0;
          });

          $('#credit-sales-subtotal').text(formatWithCommasFixed(subTotal, 2));
          $('#credit-sales-discount-total').text(formatWithCommasFixed(discountTotal, 2));
          $('#credit-sales-total').text(formatWithCommasFixed(total, 2));
        }

    $(document).on('click', '.remove-credit-sale-row', function () {
      $(this).closest('tr').remove();
      updateCreditSaleTotals();
      calculate_payment_tab_total();
      saveTempData();
    });


        $('#customerModal').on('hidden.bs.modal', function () {
        // Assuming the new customer data is returned and stored in newCustomer
        var newCustomer = {
            id: 123, // Replace with actual ID
            name: 'John Doe' // Replace with actual name
        };

        var newOption = new Option(newCustomer.name, newCustomer.id, true, true);
        $('#sw_credit_sale_customer_iddelete_expense_payment').append(newOption).trigger('change');
    });
        let usedPumps = [];

        function refreshPumpDropdown() {
          $('#pump_no option').each(function () {
            if (usedPumps.includes($(this).val())) {
              $(this).hide();
            } else {
              $(this).show();
            }
          });
          $('#pump_no').select2(); // Refresh dropdown
        }

        $('#add-meter-sale').on('click', function () {
          if ($('#pump_operator_id').val() == "") {
            toastr.error("Please select pump operator first.");
            return false;
          }
          const selectedPump = $('#pump_no').val();
          if (selectedPump && !usedPumps.includes(selectedPump)) {
            usedPumps.push(selectedPump);
            refreshPumpDropdown();
          }
          calculate_payment_tab_total();
          saveTempData();
        });

        $(document).on('click', '.delete_meter_sale', function (e) {
          e.preventDefault();
          const row = $(this).closest('tr');
          const pumpId = row.data('pump-id');
          usedPumps = usedPumps.filter(id => id !== pumpId.toString());
          refreshPumpDropdown();
          row.remove();
          calculate_payment_tab_total();
          saveTempData();
        });
        function getAllSettlementData() {
        // Serialize all forms
        const filtersData = $('#filters-form').serializeArray();
        const meterSalesData = $('#meter-sales-form').serializeArray();
        const otherSalesData = $('#other-sales-form').serializeArray();
        const creditSalesData = $('#credit-sales-form').serializeArray();
        const otherIncomeData = $('#other-income-form').serializeArray();
        const custPaymentsData = $('#cust-payments-form').serializeArray();
        const paymentSummaryData = $('#payments-summary').serializeArray();

        // Extract dynamic meter sales from the table
        const meterSalesRows = [];
        $('#meter-sales-tbody tr').each(function() {
          const row = $(this);
          const sold_qtyText = $(this).find('td:eq(6)').text().trim();
          const sold_qty = parseFloat(sold_qtyText.replace(/,/g, ''));

          const discount_typeText = $(this).find('td:eq(7)').text().trim();
          const discount_type = parseFloat(discount_typeText.replace(/,/g, ''));

          const discount_valText = $(this).find('td:eq(8)').text().trim();
          const discount_val = parseFloat(discount_valText.replace(/,/g, ''));

          const testing_qtyText = $(this).find('td:eq(9)').text().trim();
          const testing_qty = parseFloat(testing_qtyText.replace(/,/g, ''));

          const total_qtyText = $(this).find('td:eq(10)').text().trim();
          const total_qty = parseFloat(total_qtyText.replace(/,/g, ''));

          const before_discountText = $(this).find('td:eq(11)').text().trim();
          const before_discount = parseFloat(before_discountText.replace(/,/g, ''));

          const after_discountText = $(this).find('td:eq(12)').text().trim();
          const after_discount = parseFloat(after_discountText.replace(/,/g, ''));

          const meter_sale_totalText = $(this).find('td:eq(13)').text().trim();
          const meter_sale_total = parseFloat(meter_sale_totalText.replace(/,/g, ''));

          meterSalesRows.push({
            code: row.find('td:eq(0)').text(),
            pump_id: row.data('pump-id'),
            product_name: row.find('.product_name').text(),
            pump_no: row.find('td:eq(2)').text(),
            pump_start: row.find('td:eq(3)').text(),
            pump_close: row.find('td:eq(4)').text(),
            unit_price: row.find('td:eq(5)').text(),
            sold_qty: sold_qty,
            discount_type: discount_type,
            discount_val: discount_val,
            testing_qty: testing_qty,
            total_qty: total_qty,
            before_discount: before_discount,
            after_discount: after_discount,
            meter_sale_total: meter_sale_total,
            id: row.find('.delete_meter_sale').data('href')?.split('/').pop()
          });
        });

        const OtherSalesRows = [];
        $('#other-sales-tbody tr').each(function() {
          const row = $(this);
          let product_id = row.find('td:eq(1)').data('product-id');
          let sub_total = row.find('td:eq(7)').text();
          let with_discount = row.find('td:eq(8)').text();
          let discount_amount = Number(sub_total) - Number(with_discount);
          OtherSalesRows.push({
            code: row.find('td:eq(0)').text(),
            product_name: row.find('td:eq(1)').text(),
            product_id: product_id,
            discount_amount: discount_amount,
            balance_stock: row.find('td:eq(2)').text(),   
            price: row.find('td:eq(3)').text(),
            qty: row.find('td:eq(4)').text(),
            discount_type: row.find('td:eq(5)').text(),
            discount: row.find('td:eq(6)').text(),
            sub_total: row.find('td:eq(7)').text(),
            with_discount: row.find('td:eq(8)').text(),
            id: row.find('.delete_other_sale').data('href')?.split('/').pop()
          });
        });

        const ExpenseRows = [];
        $('#expense-tbody tr').each(function() {
          const row = $(this);
          ExpenseRows.push({
            expense_number: row.find('td:eq(0)').text(),
            expense_category_name: row.find('td:eq(1)').text(),
            reference_no: row.find('td:eq(2)').text(),
            expense_account_name: row.find('td:eq(3)').text(),
            expense_reason: row.find('td:eq(4)').text(),
            expense_amount: row.find('td:eq(5)').text(),
            delete_url: row.find('.sw_delete_expense_payment').data('href')
          });
        });
        
        const CreditSalesRows = [];
        $('#credit-sales-tbody tr').each(function() {
          const row = $(this);
          const unitPriceText = $(this).find('td:eq(7)').text().trim();
          const unitPrice = parseFloat(unitPriceText.replace(/,/g, ''));

          const qtyText = $(this).find('td:eq(8)').text().trim();
          const qty = parseFloat(qtyText.replace(/,/g, ''));

          const sub_totalText = $(this).find('td:eq(9)').text().trim();
          const sub_total = parseFloat(sub_totalText.replace(/,/g, ''));

          const discount_totalText = $(this).find('td:eq(10)').text().trim();
          const discount_total = parseFloat(discount_totalText.replace(/,/g, ''));

          const totalText = $(this).find('td:eq(11)').text().trim();
          const total = parseFloat(totalText.replace(/,/g, ''));

          CreditSalesRows.push({
            customer_name: row.find('td:eq(0)').text(),
            outstanding: row.find('td:eq(1)').text(),
            limit: row.find('td:eq(2)').text(),
            order_no: row.find('td:eq(3)').text(),
            order_date: row.find('td:eq(4)').text(),
            customer_reference: row.find('td:eq(5)').text(),
            product: row.find('td:eq(6)').text(),
            unit_price: unitPrice,
            qty: qty,
            sub_total: sub_total,
            discount_total: discount_total,
            total: total,
            note: row.find('td:eq(12)').text(),
          });
        });  

        const CardRows = [];
        $('#card_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(3)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          CardRows.push({ 
            customer_name: row.find('td:eq(0)').text(),
            card_type: row.find('td:eq(1)').text(),
            card_number: row.find('td:eq(2)').text(),
            card_amount: amount,
            slip_no: row.find('td:eq(4)').text(),
            card_note: row.find('td:eq(5)').text(),
            delete_url: row.find('.delete_card_payment').data('href')
          });
        });

        const CashRows = []
        $('#cash_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          CashRows.push({ 
            customer_name: row.find('td:eq(0)').text(),
            cash_amount: amount,
            cash_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_cash_payment').data('href')
          });
        });

        const ChequeRows = []
        $('#cheque_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(4)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          ChequeRows.push({ 
            customer_name: row.find('td:eq(0)').text(),
            bank_name: row.find('td:eq(1)').text(),
            cheque_number: row.find('td:eq(2)').text(),
            cheque_date: row.find('td:eq(3)').text(),
            cheque_amount: amount,
            cheque_note: row.find('td:eq(5)').text(),
            delete_url: row.find('.delete_cheque_payment').data('href')
          });
        });

        const cashDepositRows = []
        $('#cash_deposit_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(2)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          cashDepositRows.push({ 
            bank_name: row.find('td:eq(0)').text(),
            account: row.find('td:eq(1)').text(),
            cash_deposit_amount: amount,
            time: row.find('td:eq(3)').text(),
            delete_url: row.find('.delete_cash_payment').data('href')
          });
        });

        const AddPaymentExpenseRows = []
        $('#expense_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(5)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          AddPaymentExpenseRows.push({ 
            expense_number: row.find('td:eq(0)').text(),
            expense_category_name: row.find('td:eq(1)').text(),
            reference_no: row.find('td:eq(2)').text(),
            expense_account_name: row.find('td:eq(3)').text(),
            expense_reason: row.find('td:eq(4)').text(),
            expense_amount: amount,
            delete_url: row.find('.delete_expense_payment').data('href')
          });
        });

        const ShortageRows = []
        $('#shortage_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          ShortageRows.push({ 
            shortage_amount: amount,
            shortage_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_shortage_payment').data('href')
          });
        });

        const ExcessRows = []
        $('#excess_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          ExcessRows.push({ 
            excess_amount: amount,
            excess_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_excess_payment').data('href')
          });
        });
        
        const LoanPaymentRows = []
        $('#loan_payments_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          LoanPaymentRows.push({ 
            bank_name: row.find('td:eq(0)').text(),
            loan_payments_amount: amount,
            loan_payments_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_loan_payment').data('href')
          });
        });

        const DrawingPaymentsRows = []
        $('#drawing_payments_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          DrawingPaymentsRows.push({ 
            cash_payment_loan_account_name: row.find('td:eq(0)').text(),
            cash_payment_amount: amount,
            cash_payment_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_drawing_payment').data('href')
          });
        });

        const CustomerLoansRows = []
        $('#customer_loans_table_body tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(1)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          CustomerLoansRows.push({ 
            customer_name: row.find('td:eq(0)').text(),
            customer_loan_amount: amount,
            customer_loan_note: row.find('td:eq(2)').text(),
            delete_url: row.find('.delete_customer_loans_payment').data('href')
          });
        });

        const AddPaymentCreditSalesRows = []
        $('#credit_sale_table_body tr').each(function() {
          const row = $(this);
          const unitPriceText = $(this).find('td:eq(7)').text().trim();
          const unitPrice = parseFloat(unitPriceText.replace(/,/g, ''));

          const qtyText = $(this).find('td:eq(8)').text().trim();
          const qty = parseFloat(qtyText.replace(/,/g, ''));

          const sub_totalText = $(this).find('td:eq(9)').text().trim();
          const sub_total = parseFloat(sub_totalText.replace(/,/g, ''));

          const discount_totalText = $(this).find('td:eq(10)').text().trim();
          const discount_total = parseFloat(discount_totalText.replace(/,/g, ''));

          const totalText = $(this).find('td:eq(11)').text().trim();
          const total = parseFloat(totalText.replace(/,/g, ''));

          AddPaymentCreditSalesRows.push({
            customer_name: row.find('td:eq(0)').text(),
            outstanding: row.find('td:eq(1)').text(),
            limit: row.find('td:eq(2)').text(),
            order_no: row.find('td:eq(3)').text(),
            order_date: row.find('td:eq(4)').text(),
            customer_reference: row.find('td:eq(5)').text(),
            product: row.find('td:eq(6)').text(),
            unit_price: unitPrice,
            qty: qty,
            sub_total: sub_total,
            discount_total: discount_total,
            total: total,
            note: row.find('td:eq(12)').text(),
          });
        });
        
        const OtherIncomeItems = [];
        $('#other-income-tbody tr').each(function() {
          const row = $(this);
          const amounText = $(this).find('td:eq(2)').text().trim();
          const amount = parseFloat(amounText.replace(/,/g, ''));
          OtherIncomeItems.push({
            service: row.find('td:eq(0)').text(),
            details: row.find('td:eq(1)').text(),
            amount: amount,
            delete_url: row.find('.sw_delete_other_income').data('href')
          });
        });
    
        const customerPayments = [];

        $('#cust-payments-list .custum-name').each(function () {
            const nameText = $(this).clone().children().remove().end().text().trim(); // Récupère le texte sans le span
            const paymentText = $(this).find('.custum-val').text().trim();
            const payment = parseFloat(paymentText.replace(/,/g, ''));

            customerPayments.push({
                customer_name: nameText,
                total_customer_payments: payment
            });
        });

        // Combine all serialized data
        const combinedData = [
          ...filtersData,
          ...meterSalesData,
          ...otherSalesData,
          ...creditSalesData,
          ...otherIncomeData,
          ...custPaymentsData,
          ...paymentSummaryData
        ];

        // Convert to key-value object
        const finalData = {};
        combinedData.forEach(item => {
          if (finalData[item.name]) {
            if (!Array.isArray(finalData[item.name])) {
              finalData[item.name] = [finalData[item.name]];
            }
            finalData[item.name].push(item.value);
          } else {
            finalData[item.name] = item.value;
          }
        });

        // Add dynamic rows separately
        finalData.meter_sales = meterSalesRows;
        finalData.other_sales = OtherSalesRows;
        finalData.credit_sales = CreditSalesRows;
        finalData.other_income = OtherIncomeItems;
        finalData.cust_payments_list = customerPayments;
        finalData.expense_rows = ExpenseRows;
        finalData.card_rows = CardRows;
        finalData.cash_rows = CashRows;
        finalData.cash_deposit_rows = cashDepositRows;
        finalData.cheque_rows = ChequeRows;
        finalData.add_payment_expense_rows = AddPaymentExpenseRows;
        finalData.shortage_rows = ShortageRows;
        finalData.add_payment_credit_sale = AddPaymentCreditSalesRows;
        finalData.excess_rows = ExcessRows;
        finalData.loan_payment_rows = LoanPaymentRows;
        finalData.drawing_payments_rows = DrawingPaymentsRows;
        finalData.customer_loans_rows = CustomerLoansRows;

        console.log(finalData)

        return finalData;
      }

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

            let operatorId = $('#pump_operator_id').val();
            let shiftIds = $('#shift_number').val();

            if (operatorId && shiftIds) {
                let url = "{{ action('\Modules\Petro\Http\Controllers\AddPaymentController@create') }}" +
                          "?settlement_no={{ $settlement_no }}" +
                          "&operator_id=" + operatorId +
                          "&shift_ids=" + shiftIds +
                          "&provider=SET_SW"+
                          "&settlement_page=1";

                $('#add_payment_container').load(url, function(response, status, xhr) {
                    if (status === "error") {
                        console.error("Error loading Add Payment HTML:", xhr.status, xhr.statusText);
                        $('#add_payment_container').html('<p class="text-danger">Failed to load payment section.</p>');
                    }
                });
            } else {
                console.warn('Missing operator or shift ID');
            }


          }
        });
        $(document).ready(function () {
          @if(auth()->user()->can('unfinished_form.settlement_sw'))
            setInterval(function(){ 
              $.ajax({
                  method: 'POST',
                  url: '{{action("TempController@saveSettlementSWTemp")}}',
                  dataType: 'json',
                  data: getAllSettlementData(),
                  success: function(data) {

                  },
                });
            }, 10000);
        @endif

          $('#noteButton').on('click', function () {
              $('#noteInput')
                  .val($('#noteHidden').val())
                  .prop('readonly', false); // éditable

              $('#saveNote').show();
              $('#noteOverlay').fadeIn(200);
              $('#noteModal').fadeIn(200);
          });

          $(document).on('click', '.btn-note-view', function (e) {
              e.preventDefault();
              const note = $(this).data('note') || '';

              $('#noteInput')
                  .val(note)
                  .prop('readonly', true); 

              $('#saveNote').hide();
              $('#noteOverlay').fadeIn(200);
              $('#noteModal').fadeIn(200);
          });

          $('#closeNote').on('click', function () {
              $('#noteModal').fadeOut(200);
              $('#noteOverlay').fadeOut(200);
          });

          $('#saveNote').on('click', function () {
              var note = $('#noteInput').val();
              $('#noteHidden').val(note);
              $('#noteDisplay').text(note);

              $('#noteModal').fadeOut(200);
              $('#noteOverlay').fadeOut(200);
          });

          const vehicleInputHtml = `
            <div class="form-group-custom field-with-comma" id="customer-reference-one-time-wrapper">
              <label for="customer_reference_one_time">
                ${@json(__('SettlementSW::lang.enter_customer_vehicle_no'))}
              </label>
              <input type="text" class="form-control customer_reference_one_time"
                    id="customer_reference_one_time"
                    name="customer_reference"
                    placeholder="${@json(__('SettlementSW::lang.enter_customer_vehicle_no'))}">
            </div>
          `;
          $('#add-credit-sale').closest('.form-group-custom').before(vehicleInputHtml);

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

              $('#pump_starting_meter').val(formatWithCommas(result.colsing_value));

              if(result.po_closing > 0){
                $('#pump_closing_meter').val(formatWithCommas(result.po_closing));
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
                $('#testing_qty').val(formatWithCommas(result.po_testing));
                $("#testing_qty").prop('readonly',true);
                $('#testing_qty').trigger('change');
              }else{
                $('#testing_qty').val(formatWithCommas(0));
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
          const closing_meterText = $(this).val().trim();
          const pump_closing_meter = parseFloat(closing_meterText.replace(/,/g, ''));

          const starting_meterText = $('#pump_starting_meter').val().trim();
          const pump_starting_meter = parseFloat(starting_meterText.replace(/,/g, ''));

          if (pump_closing_meter < pump_starting_meter) {
              toastr.error('Closing meter value should not be less than starting meter value');
              $(this).val('');
          } else {
              sold_qty = (pump_closing_meter - pump_starting_meter).toFixed(3); // changed from 6 to 3
              $('#sold_qty').val(formatWithCommas(sold_qty));
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

    $(document).off('click', '.btn-modal').on('click', '.btn-modal', function(e) {
        e.preventDefault();

        var $this = $(this);
        $this.prop('disabled', true);

        var container = $this.data('container');
        var url = $this.data('href');

        // Fully remove any previous modal DOM elements before loading a new one
        $('.modal').modal('hide').remove(); // removes all modals completely

        // Add the modal container back to the DOM if needed
        if ($(container).length === 0) {
            // If the container doesn't exist, create it
            $('body').append('<div class="' + container.replace('.', '') + ' modal fade" tabindex="-1" role="dialog"></div>');
        }

        // Load the modal content via AJAX
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(result) {
                $(container).html(result).modal('show');
            },
            complete: function() {
                $this.prop('disabled', false);
            },
            error: function() {
                toastr.error('Failed to load modal.');
                $this.prop('disabled', false);
            }
        });
    });


        function updateMeterSalesTotal() {
            let total = 0;
            $('#meter-sales-tbody tr').each(function () {
              const afterDiscountText = $(this).find('td:eq(12)').text().trim();
              const afterDiscount = parseFloat(afterDiscountText.replace(/,/g, ''));
              total += afterDiscount;
            });
            let currencyPrecision2 = {{ $currency_precision }};
            $('#meter-sales-total').text(formatWithCommasFixed(total, currencyPrecision2));
            $('#meter_sale_total').val(formatWithCommasFixed(total, currencyPrecision2));
        }

        function updateTotalSoldQty() {
          var productSoldQty = {};

          $('#meter_sale_table tbody tr').each(function () {
              var productName = $(this).find('.product_name').text().trim();
              var soldQty = parseFloat($(this).find('span.sold_qty').text().replace(/,/g, ''));

              if (!isNaN(soldQty)) {
                  productSoldQty[productName] = (productSoldQty[productName] || 0) + soldQty;
              }
          });

          var productSummaryParts = [];
          for (var productName in productSoldQty) {
              // Format with comma separators and 3 decimal places
              var formattedQty = productSoldQty[productName].toLocaleString(undefined, {
                  minimumFractionDigits: 3,
                  maximumFractionDigits: 3
              });
              productSummaryParts.push(`${productName} = ${formattedQty}`);
          }

          // Join with line breaks instead of " | "
          var productSummaryHtml = productSummaryParts.join('<br>');
          $('.product_summary').html(productSummaryHtml);
      }


      $('#add-meter-sale').on('click', function () {
        if ($('#pump_operator_id').val() == "") {
            toastr.error("Please select pump operator first.");
            return false;
          }
      
      var is_from_pumper = $("#is_from_pumper").val() ?? 0;

      var assignment_id = $("#assignment_id").val() ?? 0;
      var pumper_entry_id = $("#pumper_entry_id").val() ?? 0;

      var meter_sale_discount = $('#meter_sale_discount').val();
      var meter_sale_discount_type = $('#meter_sale_discount_type').val();
      var meter_sale_discount_type_text = '';
      if ($('#meter_sale_discount_type').val() !== '') {
      meter_sale_discount_type_text = $('#meter_sale_discount_type option[value="'+$('#meter_sale_discount_type').val()+'"]').text();
      }
      const sold_qtyText = $('#sold_qty').val().trim();
      var sold_qty = parseFloat(sold_qtyText.replace(/,/g, ''));
      var total_qty = sold_qty;

      const testing_qtyText = $('#testing_qty').val().trim(); 
      var testing_qty = parseFloat(testing_qtyText.replace(/,/g, ''));

      sold_qty = sold_qty - testing_qty;
     
      sub_total = sold_qty * parseFloat(price);

      if (!meter_sale_discount) {
        meter_sale_discount = 0;
      }
      var meter_sale_discount_amount = sub_total - calculate_discount(meter_sale_discount_type, meter_sale_discount, sub_total);
      var meter_sale_id = null;

      let meter_sale_total = parseFloat($('#meter_sale_total').val().replace(',', ''));
      meter_sale_total = meter_sale_total + meter_sale_discount_amount;
      var is_edit = $("#is_edit").val() ?? 0;
      let currencyPrecision2 = {{ $currency_precision }};
                  swal({
                      title: "You are going to add a new meter sale",
                      text: "Are you sure to add it?",
                      icon: "warning",
                      buttons: {
                        cancel: {
                          text: "Yes",
                          visible: true,
                          className: "btn btn-danger swal-btn-yes"
                        },
                        confirm: {
                          text: "No",
                          visible: true,
                          className: "btn btn-success swal-btn-no"
                        }
                      },
                      dangerMode: true,
                      className: 'custom-swal'
                    }).then((confirmed) => {
                      if (!confirmed) {
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
                              toastr.success('Meter Sale Added Successfully!');
                              const selectedPumpId = $('#pump_no').val();
                            // alert('Meter Sale Added Successfully!');
                              $('#meter_sale_total').val(meter_sale_total);
                            // Append row to table (example)
                                $('#meter-sales-tbody').append(`
                                  <tr data-pump-id="${response?.data?.pump_id}">
                                      <td>${response?.data?.product?.sku?.slice(-2)}</td>
                                      <td><span class="product_name">${response.data?.product?.name}</span></td>
                                      <td>${response?.data?.pump_no}</td>
                                      <td>${formatWithCommas(parseFloat(response?.data?.pump_start).toFixed(3))}</td>
                                      <td>${formatWithCommas(parseFloat(response?.data?.pump_close).toFixed(3))}</td>
                                      <td>${formatWithCommas(response?.data?.unit_price)}</td>
                                      <td><span class="sold_qty">${formatWithCommas(parseFloat(response?.data?.sold_qty).toFixed(3))}</span></td>
                                      <td>${response?.data?.discount_type}</td>
                                      <td>${formatWithCommas(response?.data?.discount_val)}</td>
                                      <td>${formatWithCommas(response?.data?.testing_qty)}</td>
                                      <td>${formatWithCommas((parseFloat(response?.data?.sold_qty) + parseFloat(response?.data?.testing_qty)).toFixed(3))}</td>
                                      <td>${formatWithCommas((Number(response?.data?.unit_price) * Number(response?.data?.sold_qty)).toFixed(currencyPrecision2))}</td>
                                      <td>${formatWithCommas(response?.data?.after_discount)}</td>
                                      <td>
                                        <button class="btn btn-xs btn-danger delete_meter_sale"
                                                data-href="/settlement-sw/delete-meter-sale/${response?.data?.meter_sale_id}">
                                          <i class="fa fa-times"></i>
                                        </button>
                                      </td>
                                  </tr>
                                `);
                                updateTotalSoldQty()
                                // Update meter sale total in footer
                                updateMeterSalesTotal();
                                calculate_payment_tab_total()
                            // Optional: Reset the form
                            $('#meter-sales-form')[0].reset();
                            // Get the selected pump's ID
                            // Remove from dropdown
                            $(`#pump_no option[value="${selectedPumpId}"]`).remove();
                            saveTempData();

                            // Optionally reset the dropdown
                            $('#pump_no').val(null).trigger('change');
                            },
                            error: function (xhr) {
                            toastr.error('Failed to save meter sale. Please check the fields.');
                            // console.log(xhr.responseJSON);
                            }
                            });
                        }
                    });

      
      })


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
              url: '/settlement-sw/get_balance_stock_by_id/' + item_id,
              data : {
                store_id : $("select#store_id option").filter(":selected").val(),
                location_id : $("select#location_id option").filter(":selected").val()
              },
              success: function (result) {
                let currencyPrecision2 = {{ $currency_precision }};
                $('#balance_stock').val(formatWithCommasFixed(result.balance_stock, currencyPrecision2));
                $('#other_sale_price').val(formatWithCommas(result.price));
                other_sale_code = result.code;
                other_sale_product_name = result.product_name;
                other_sale_price = result.price;
              },
            });
          }

        });

        $('#add-other-sale').click(function (e) {
          if ($('#pump_operator_id').val() == "") {
            toastr.error("Please select pump operator first.");
            return false;
          }

          var other_sale_discount         = $('#other_sale_discount').val();
          var other_sale_discount_type    = $('#other_sale_discount_type').val();
          var other_sale_qty              = $('#other_sale_qty').val();
          var balance_stockText               = $('#balance_stock').val().trim();
          var sub_total                       = parseFloat(other_sale_qty) * parseFloat(other_sale_price);
          var allowoverselling = $("#allowoverselling").val();
          const balance_stock = parseFloat(balance_stockText.replace(/,/g, ''));
          
          if(parseFloat(other_sale_qty) > parseFloat(balance_stock) && Boolean(allowoverselling) == false){
            toastr.error('Out of Stock');
            $(this).val('').focus();
            return false;
          }
          
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
            url: '/settlement-sw/save-other-sale',
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
              $('#other_sale_total').val(formatWithCommas(other_sale_total));

              other_sale_id = result.other_sale_id;
              sub_total = __number_f(sub_total);
              let currencyPrecision2 = {{ $currency_precision }};

              $('#other-sales-tbody').append(
                  `
                    <tr>
                        <td>${other_sale_code}</td>
                        <td data-product-id="${$('#item').val()}">${other_sale_product_name}</td>
                        <td>${formatWithCommasFixed(balance_stock, currencyPrecision2)}</td>
                        <td>${formatWithCommasFixed(other_sale_price)}</td>
                        <td>${formatWithCommasFixed(other_sale_qty, currencyPrecision2)}</td>
                        <td>${capitalizeFirstLetter(other_sale_discount_type)}</td>
                        <td>${formatWithCommasFixed(other_sale_discount)}</td>
                        <td>${formatWithCommasFixed(sub_total)}</td>
                        <td>${formatWithCommasFixed(with_discount)}</td>
                        <td>
                          <button class="btn btn-xs btn-danger delete_other_sale" data-href="/settlement-sw/delete-other-sale/${other_sale_id}">
                            <i class="fa fa-times"></i>
                          </button>
                        </td>
                    </tr>
                  `
                );
              $('.other_sale_fields').val('').trigger('change');
              // Update the total in the footer
              updateOtherSalesTotal();
              calculate_payment_tab_total();
              saveTempData();
            },
          });
        });

        function updateOtherSalesTotal() {
          let total = 0;

          $('#other-sales-tbody tr').each(function () {
              // Assuming the "With Discount" value is in the 9th column (index 8)
              const afterDiscount = parseFloat($(this).find('td:eq(8)').text().replace(/,/g, '')) || 0;
              total += afterDiscount;
          });

          $('#other-sale-after-discount-total').text(formatWithCommasFixed(total, 2));
      }

        function updateOtherIncomeTotal() {
          let total = 0;

          $('#other-income-tbody tr').each(function () {
              // Assuming the "With Discount" value is in the 9th column (index 8)
              const amount = parseFloat($(this).find('td:eq(2)').text().replace(/,/g, '')) || 0;
              total += amount;
          });

          $('#other-income-total').text(formatWithCommasFixed(total, 2));
          $('#other_income_total').val(total);
      }


        function calculate_payment_tab_total() {
          const meter_sale_totalsText = $('#meter_sale_total').val().trim();
          const meter_sale_totals = parseFloat(meter_sale_totalsText.replace(/,/g, '')); 

          const shift_operator_other_sale_totalText = $('#shift_operator_other_sale_total').val().trim();
          const shift_operator_other_sale_total = parseFloat(shift_operator_other_sale_totalText.replace(/,/g, '')); 
          
          const other_sale_totalText = $('#other-sale-after-discount-total').text().trim();
          const other_sale_totals = parseFloat(other_sale_totalText.replace(/,/g, ''));

          const other_income_totalsText = $('#other_income_total').val().trim();
          const other_income_totals = parseFloat(other_income_totalsText.replace(/,/g, ''));
          
          const customer_payment_totalsText = $('#customer_payment_total').val().trim();
          const customer_payment_totals = parseFloat(customer_payment_totalsText.replace(/,/g, ''));

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
          console.log('All Total: ', all_totals)
          $('#amount_total').val(__number_f(all_totals, false, false, __currency_precision));
          $('.total_amount').text(__number_f(all_totals, false, false, __currency_precision));
          
        }

        function capitalizeFirstLetter(string) {
          return string.charAt(0).toUpperCase() + string.slice(1);
        }


        //other income tab
        var other_income_total = parseFloat($('#other_income_total').val().replace(',', ''));
        var sub_total = 0.0;
        var other_income_code = null;
        var other_income_product_name = null;
        var other_income_price = 0.0;
        $('#add-other-income').click(function () {
          var other_income_product_id = $('#other_income_product_id').val();
          var other_income_qty = $('.other_income_qty').val();
          var other_income_reason = $('#other_income_reason').val();
          var other_income_id = null;
          let selectedText = $('.other_income_product option:selected').text();
          var is_edit = $("#is_edit").val() ?? 0;
          $('#other_income_total').val(other_income_total);
          $.ajax({
            method: 'post',
            url: '/settlement-sw/save-other-income',
            data: {
              settlement_no: $('#settlement_no').val(),
              product_id: other_income_product_id,
              qty: other_income_qty,
              other_income_reason: other_income_reason,
              is_edit: is_edit
            },
            success: function (result) {
              if (!result.success) {
                toastr.error(result.msg);
                return false;
              }
              console.log(result)

              // Append reason and qty to the list
              $('#other-income-tbody').append(`
                <tr>
                      <td>${selectedText}</td>
                      <td>${result.data.reason}</td>
                      <td>${formatWithCommasFixed(result.data.qty)}</td>
                      <td>
                        <button type="button" class="btn btn-xs btn-danger sw_delete_other_income"
                            data-href=/petro/settlement/delete-other-income/${result.other_income_id}>
                          <i class="fa fa-times"></i>
                        </button>
                      </td>
                    </tr>
              `);
              updateOtherIncomeTotal()
              calculate_payment_tab_total();
            },
          });
        });


        //customer_payment tab
        var customer_payment_total = parseFloat($('#customer_payment_total').val().replace(',', ''));
        var sub_total = 0.0;

        $('#add-payment').click(function () {
          if ($('#pump_operator_id').val() == "") {
            toastr.error("Please select pump operator first.");
            return false;
          }
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
            url: '/settlement-sw/save-customer-payment',
            data: {
              settlement_no: $("#settlement_no").val(),
              location_id: $('#location_id').val(),
              pump_operator_id: $('#pump_operator_id').val(),
              transaction_date: $('#transaction_date').val(),
              work_shift: $('#work_shift').val(),
              note: $('#note').val(),
              settlement_customer_payment_no: $('#settlement_customer_payment_no').val(),

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

              // Show the customer payments section if hidden
              $('#cust-payments-list').removeClass('d-none');
              $('#business-payments-cust').removeClass('d-none');

              // Append customer payment line
              $('#cust-payments-list').append(`
                <li class="custum-name">${result.customer_name} <span class="custum-val">${formatWithCommasFixed(result.total_customer_payments, 2)}</span></li>
              `);

              $('#cust-payments-total').text(formatWithCommasFixed(result.total_business_payments, 2));


              //  reset fields
              $('#customer_payment_amount').val('');
              $('#customer_payment_customer_id').val('').trigger('change');

              $("#settlement_customer_payment_no").val(result.settlement_no);

              calculate_payment_tab_total();
              saveTempData();
            },
          });
        });


         function togglePaymentFields() {
          const method = $('#customer_payment_payment_method').val();

          $('.card_div').addClass('hide');
          $('.cheque_divs').addClass('hide');

          if (method === 'cheque') {
            $('.cheque_divs').removeClass('hide');
          } else if (method === 'card') {
            $('.card_div').removeClass('hide');
          }
        }

        $('#customer_payment_payment_method').on('change', togglePaymentFields);
        togglePaymentFields();

        $('#customer_payment_cheque_date').datepicker("setDate", new Date());


        $(document).ready(function(){
          $("#sw_credit_sale_customer_iddelete_expense_payment").val($("#sw_credit_sale_customer_iddelete_expense_payment option:eq(0)").val()).trigger('change');
          $('#sw_order_date,.transaction_date').datepicker("setDate", new Date());
          $('#sw_credit_sale_product_id').select2();
          $('#sw_credit_sale_customer_iddelete_expense_payment').select2();
          $('#sw_customer_reference').select2();
        });

        $(document).on('change', '#customer_reference_one_time', function(){
          if($(this).val() !== '' && $(this).val() !== null && $(this).val() !== undefined){
            $('#sw_customer_reference').attr('disabled', 'disabled');
            $('.quick_add_customer_reference').attr('disabled', 'disabled');
          }else{
            $('#sw_customer_reference').removeAttr('disabled');
            $('.quick_add_customer_reference').removeAttr('disabled');
          }
        })

        $(document).on('submit', '#customer_reference_add_form', function(e){
          e.preventDefault();
          let url = $('#customer_reference_add_form').attr('action');
          let data = $('#customer_reference_add_form').serialize();
          $.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: data,
            success: function(result) {
              if(result.success){
                let customer_reference = result.customer_reference;
                $('#sw_credit_sale_customer_iddelete_expense_payment').trigger('change');
              }

              $('.view_modal').modal('hide');
            },
          });
        })

        $(document).on('input','#sw_credit_sale_qty', function() {
          $("#sw_credit_total_amount").attr('disabled',true);

          let price = __read_number($("#sw_unit_price")) ?? 0;
          let qty = __read_number($("#sw_credit_sale_qty")) ?? 0;
          let total_amount = price * qty;

          let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
          let unit_discount = total_discount / qty;
          let amount = total_amount - total_discount

          __write_number($("#sw_credit_sale_amount"), amount);
          __write_number($("#sw_unit_discount"), unit_discount);
          __write_number($("#sw_credit_total_amount"),total_amount);

        });

        $(document).on("change", "#credit_discount_amount, #sw_unit_price", function () {
          let price = __read_number($("#sw_unit_price")) ?? 0;
          let qty_check = __read_number($("#sw_credit_sale_qty")) ?? 0;

          var qty = 0;
          var total_amount = 0;

          if(qty_check > 0){
            qty = __read_number($("#sw_credit_sale_qty")) ?? 0;
            total_amount = price * qty;
            __write_number($("#sw_credit_total_amount"), total_amount);
          }else{
            total_amount = __read_number($("#sw_credit_total_amount")) ?? 0;
            qty = total_amount / price;
            __write_number_without_decimal_format($("#sw_credit_sale_qty"),qty);
          }



          let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
          let unit_discount = total_discount / qty;
          let amount = total_amount - total_discount

          __write_number($("#sw_unit_discount"), unit_discount);
          __write_number($("#sw_credit_sale_amount"),amount);

        });

        $(document).on("change", "#sw_credit_sale_customer_iddelete_expense_payment", function () {
          $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + $(this).val(),
            data: {},
            success: function (result) {
              $(".current_outstanding").text(result.total_outstanding);
              $(".credit_limit").text(result.credit_limit);
              $("#sw_customer_reference").empty();
              $("#sw_customer_reference").append(`<option selected="selected" value="">Please Select</option>`);
              result.customer_references.forEach(function (ref, i) {
                $("#sw_customer_reference").append(`<option value="` + ref.reference + `">` + ref.reference + `</option>`);
                // $("#customer_reference").val($("#customer_reference option:eq(1)").val()).trigger("change");
              });
            },
          });
        });

        $(document).on("change", "#sw_credit_sale_product_id", function () {
          if ($(this).val()) {
            $.ajax({
              method: "get",
              url: "/petro/settlement/payment/get-product-price",
              data: { product_id: $(this).val() },
              success: function (result) {
                $("#sw_unit_price").val(formatWithCommasFixed(result.price));
                $("#sw_unit_price").trigger('change');

                $("#sw_credit_total_amount").attr("disabled", false);
                $("#sw_credit_sale_qty").attr("disabled", false);
                if($("#manual_discount").val() == 1){
                  $("#credit_discount_amount").attr("disabled", false);
                }

              },
            });
          } else {
            $("#sw_credit_total_amount").attr("disabled", true);
            $("#sw_credit_sale_qty").attr("disabled", true);
            $("#credit_discount_amount").attr("disabled", true);
          }
        });


        $(document).on("click", "#sw_add-credit-sale", function () {
          if ($("#sw_credit_sale_amount").val() == "") {
            toastr.error("Please enter amount");
            return false;
          }
          var credit_sale_customer_id = $("#sw_credit_sale_customer_iddelete_expense_payment").val();
          var customer_name = $("#sw_credit_sale_customer_iddelete_expense_payment :selected").text();
          var credit_sale_product_id = $("#sw_credit_sale_product_id").val();
          var credit_sale_product_name = $("#sw_credit_sale_product_id :selected").text();
          if ($("#customer_reference_one_time").val() !== "" && $("#customer_reference_one_time").val() !== null && $("#customer_reference_one_time").val() !== undefined) {
            var customer_reference = $("#customer_reference_one_time").val();
          } else {
            var customer_reference = $("#sw_customer_reference").val();
          }
          var settlement_no = $("#settlement_no").val();
          var order_date = $("#sw_order_date").val();
          var order_number = $("#sw_order_number").val();

          var credit_sale_price = __read_number($("#sw_unit_price"));
          var credit_unit_discount = __read_number($("#sw_unit_discount")) ?? 0;
          var credit_sale_qty = __read_number($("#sw_credit_sale_qty")) ?? 0;
          var credit_total_amount = __read_number($("#sw_credit_total_amount")) ?? 0;
          var credit_total_discount = __read_number($("#credit_discount_amount")) ?? 0;
          var credit_sub_total = __read_number($("#sw_credit_sale_amount")) ?? 0;

          var outstanding = $(".current_outstanding").text();
          var credit_limit = $(".credit_limit").text();
          var credit_note = $("#credit_note").val();
          var is_edit = $("#is_edit").val() ?? 0;

          $.ajax({
            method: "post",
            url: "/settlement-sw/save-credit-sale-payment",
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
                const data = {
                  customer_name: $('#sw_credit_sale_customer_iddelete_expense_payment option:selected').text(),
                  outstanding: '0.00', // You can calculate/fetch this if needed
                  limit: '0.00', // Same here
                  order_no: order_number,
                  order_date:order_date,
                  customer_reference: customer_reference,
                  product: $('#sw_credit_sale_product_id option:selected').text(),
                  unit_price: parseFloat(credit_sale_price) || 0,
                  qty: parseFloat(credit_sale_qty) || 0,
                  sub_total: parseFloat(credit_sub_total) || 0,
                  discount_total: parseFloat(credit_total_discount) || 0,
                  total: parseFloat(credit_sub_total - credit_total_discount) || 0,
                  note: $('#noteInput').val() || '' // If you have a note field
                };

                appendCreditSaleRow(data);
                settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;

                // Update the total credit sales
                if(result.total_credit_sales !== undefined){
                  $("#credit-sales-total").text(formatWithCommasFixed(result.total_credit_sales, 2));

                  // Show the credit sales summary if it was hidden
                  $(".total-credit-sales-value").removeClass('hidden').show();
                }

                $('#noteHidden').val('');
                $('#noteDisplay').text('');
                //  reset the form
                $('#credit-sales-form')[0].reset();
                $('#credit-sales-form').find('select.select2').val(null).trigger('change');
                calculate_payment_tab_total();
                saveTempData();
              }
            },
          });
        });


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
                        updateTotalSoldQty()
                        calculate_payment_tab_total();
                    } else {
                        toastr.error(result.msg);
                    }
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

        $(document).on('input','#sw_credit_total_amount', function() {
          $("#sw_credit_sale_qty").attr('disabled',true);

          let price = __read_number($("#sw_unit_price")) ?? 0;
          let total_amount = __read_number($("#sw_credit_total_amount")) ?? 0;
          let qty = total_amount / price;

          let total_discount = __read_number($("#credit_discount_amount")) ?? 0;
          let unit_discount = total_discount / qty;
          let amount = total_amount - total_discount

          __write_number($("#sw_credit_sale_amount"), amount);
          __write_number($("#sw_unit_discount"), unit_discount);
          __write_number_without_decimal_format($("#sw_credit_sale_qty"),qty);


        });
      </script>

    <script>
      function formatDecimal(value, places = 3) {
        return parseFloat(value).toFixed(places);
    }

    function saveTempData(){
      $.ajax({
          method: 'POST',
          url: '{{ action("TempController@saveSettlementSWTemp") }}',
          dataType: 'json',
          data: getAllSettlementData(),
          success: function(data) {
              
          },
      });
    }

      $(document).ready(function () {
        let currencyPrecision = {{ session('business.currency_precision', 2) }};
        let currencyPrecision2 = {{ $currency_precision }};
        
        console.log('temp data: ', @json($temp_data))
        
        @if(!empty($temp_data->meter_sales))
          let meterSalesRows = @json($temp_data->meter_sales);

          meterSalesRows.forEach(row => {
            $('#meter-sales-tbody').append(`
              <tr data-pump-id="${row.pump_id}">
                <td>${row.code ?? ''}</td>
                <td><span class="product_name">${row.product_name ?? ''}</span></td>
                <td>${row.pump_no ?? ''}</td>
                <td>${formatWithCommasFixed(row.pump_start)}</td>
                <td>${formatWithCommasFixed(row.pump_close)}</td>
                <td>${formatWithCommasFixed(row.unit_price, currencyPrecision)}</td>
                <td><span class="sold_qty">${formatWithCommasFixed(row.sold_qty)}</span></td>
                <td>${row.discount_type ?? ''}</td>
                <td>${formatWithCommasFixed(row.discount_val, 2)}</td>
                <td>${formatWithCommasFixed(row.testing_qty)}</td>
                <td>${formatWithCommasFixed(Number(row.sold_qty) + Number(row.testing_qty))}</td>
                <td>${formatWithCommasFixed(row.before_discount)}</td>
                <td>${formatWithCommasFixed(row.after_discount)}</td>
                <td>
                  <button class="btn btn-xs btn-danger delete_meter_sale" data-href="/settlement-sw/delete-meter-sale/${row.meter_sale_id}">
                    <i class="fa fa-times"></i>
                  </button>
                </td>
              </tr>
            `);
          });
          updateTotalSoldQty(); // Refresh summary if needed
          updateMeterSalesTotal();
          calculate_payment_tab_total();
        @endif

        @if(!empty($temp_data->other_sales))
          let otherSalesRows = @json($temp_data->other_sales);
          otherSalesRows.forEach(row =>{
            $('#other-sales-tbody').append(
                  `
                    <tr>
                        <td>${row.code}</td>
                        <td data-product-id="${row.product_id}">${row.product_name ?? ''}</td>
                        <td>${formatWithCommasFixed(row.balance_stock, currencyPrecision2)}</td>
                        <td>${formatWithCommasFixed(row.price)}</td>
                        <td>${formatWithCommasFixed(row.qty, currencyPrecision2)}</td>
                        <td>${capitalizeFirstLetter(row.discount_type)}</td>
                        <td>${formatWithCommasFixed(row.discount)}</td>
                        <td>${formatWithCommasFixed(row.sub_total)}</td>
                        <td>${formatWithCommasFixed(row.with_discount)}</td>
                        <td>
                          <button class="btn btn-xs btn-danger delete_other_sale" data-href="/settlement-sw/delete-other-sale/${row.id}">
                            <i class="fa fa-times"></i>
                          </button>
                        </td>
                    </tr>
                  `
                );
          })
          updateOtherSalesTotal();
          calculate_payment_tab_total();
        @endif
        
        @if(!empty($temp_data->expense_rows))
          let expense_rows = @json($temp_data->expense_rows);
          expense_rows.forEach(row =>{
            $("#expense-tbody").prepend(
              `
              <tr> 
                  <td>${row.expense_number}</td>
                  <td>${row.expense_category_name}</td>
                  <td>${row.reference_no}</td>
                  <td>${row.expense_account_name}</td>
                  <td>${row.expense_reason}</td>
                  <td class="sw_expense_amount">${formatWithCommasFixed(row.expense_amount, __currency_precision)}</td>
                  <td>
                    <button type="button" class="btn btn-xs btn-danger sw_delete_expense_payment"
                            data-href="${row.delete_url}">
                      <i class="fa fa-times"></i>
                    </button>
                  </td>
              </tr>
              `
            );
            calculateTotal("#expense_table_new", ".sw_expense_amount", ".sw_expense_total");
        calculate_payment_tab_total();
          })
        @endif

        @if(!empty($temp_data->credit_sales))
          let creditSalesRows = @json($temp_data->credit_sales);
          creditSalesRows.forEach(row =>{
            $('#credit-sales-tbody').append(
                  `
                    <tr>
                      <td>${row.customer_name}</td>
                      <td>${formatWithCommasFixed(row.outstanding)}</td>
                      <td>${formatWithCommasFixed(row.limit)}</td>
                      <td>${row.order_no}</td>
                      <td>${row.order_date}</td>
                      <td>${row.customer_reference}</td>
                      <td>${row.product}</td>
                      <td>${formatWithCommasFixed(row.unit_price)}</td>
                      <td>${formatWithCommasFixed(row.qty)}</td>
                      <td>${formatWithCommasFixed(row.sub_total)}</td>
                      <td>${formatWithCommasFixed(row.discount_total)}</td>
                      <td>${formatWithCommasFixed(row.total)}</td>
                      <td>
                        <a href="#" 
                          class="btn-note-view" 
                          data-note="${row.note ?? ''}">
                          <i class="fa fa-eye" aria-hidden="true"></i> {{ __("messages.view") }}
                        </a>
                      </td> 
                      <td><button type="button" class="btn btn-danger btn-sm remove-credit-sale-row">X</button></td>
                    </tr>
                  `
                );
          })
          updateCreditSaleTotals();
        calculate_payment_tab_total();
        @endif

        @if(!empty($temp_data->other_income))
          let other_income_list = @json($temp_data->other_income);
          other_income_list.forEach(row =>{
            $('#other-income-tbody').append(`
            <tr>
                      <td>${row.service}</td>
                      <td>${row.details}</td>
                      <td>${formatWithCommasFixed(row.amount)}</td>
                      <td>
                        <button type="button" class="btn btn-xs btn-danger sw_delete_other_income"
                            data-href="${row.delete_url}">
                          <i class="fa fa-times"></i>
                        </button>
                      </td>
                    </tr>
              `);
          })
          updateOtherIncomeTotal()
          calculate_payment_tab_total();
        @endif

        @if(!empty($temp_data->cust_payments_list))
          let cust_payments_list = @json($temp_data->cust_payments_list);
          let total = 0
          cust_payments_list.forEach(row =>{
              total += Number(row.total_customer_payments);
              // Show the customer payments section if hidden
              $('#cust-payments-list').removeClass('d-none');
              $('#business-payments-cust').removeClass('d-none');

              // Append customer payment line
              $('#cust-payments-list').append(`
                <li class="custum-name">${row.customer_name} <span class="custum-val">${formatWithCommasFixed(row.total_customer_payments, 2)}</span></li>
              `);
          })
          
          $('#customer_payment_total').val(total);
          $('#cust-payments-total').text(total);
          calculate_payment_tab_total();
        @endif
        // loadData();
      });
    </script>

    <script>
    setTimeout(() => {
      $('.input_number').each(function () {
        let raw = $(this).val().replace(/,/g, '');
        $(this).val(formatWithCommas(raw));
      });
    }, 100);
    </script>
<!-- script for new css style related to the doc 7425 -->
    <script>
      $(document).ready(function () {
        let balanceTimer;
      balanceTimer = setTimeout(function () {
        clearTimeout(balanceTimer)
        const all_totalsText = $('.total_amount').text().trim();
            const all_totals = parseFloat(all_totalsText.replace(/,/g, ''));

            const total_paidText = $('#total_paid').val().trim();
            const total_paid = parseFloat(total_paidText.replace(/,/g, ''));
            const bal = all_totals - total_paid
            console.log('Total: ',all_totals, total_paid)
            $('#total_balance').val(__number_f(bal, false, false, __currency_precision));
            $('.total_balance').text(__number_f(bal, false, false, __currency_precision));
      }, 4000);

        // Ciblage des colonnes par classe ou attribut
        const before = $('.before-discount-meter');
        const after = $('.after-discount-meter');
        const action = $('.action-meter');
        const price = $('.price-meter');
        const totalQty = $('.total-qty-meter');

        // Récupération des largeurs actuelles
        const beforeW = before.outerWidth();
        const afterW = after.outerWidth();
        const actionW = action.outerWidth();

        // Calcul des réductions (10% chacune)
        const beforeReduce = beforeW * 0.1;
        const afterReduce = afterW * 0.1;
        const actionReduce = actionW * 0.1;

        // Application des nouvelles largeurs réduites
        before.css('width', beforeW - beforeReduce);
        after.css('width', afterW - afterReduce);
        action.css('width', actionW - actionReduce);

        // Total gagné à redistribuer : 30%
        const totalGain = beforeReduce + afterReduce + actionReduce;
        const redistribute = totalGain / 2;

        // Ajout de 15% chacun à Price et Total Qty
        const priceW = price.outerWidth();
        const totalQtyW = totalQty.outerWidth();

        price.css('width', priceW + redistribute);
        totalQty.css('width', totalQtyW + redistribute);
        
      });
    </script>

    <script>
      $(document).on('click', '.edit_price_other_income', function(){
            $('#edit_price_other_income').modal('show');
        });
        $(document).on('click', '#save_edit_price_other_income_btn', function() {
          var edit_price = $('#other_income_edit_price').val();

          $('#other_income_price').val(edit_price);
          // $('#other_income_edit_price').val('0');
          $('#edit_price_other_income').modal('hide');
          calculate_payment_tab_total();
            saveTempData();
        });
  </script>

  <script>
    $(document).on("custom:payment_added", function (e) {
        saveTempData()
    });

    function loadData(){
      console.log('loadData payment')
      let settlementTimer;
      settlementTimer = setTimeout(function () {
        @if(!empty($temp_data->card_rows))
            let card_rows = @json($temp_data->card_rows);
             $("#card_table_body").empty(); // Vider d'abord l'ancien contenu
              
            card_rows.forEach(row => {
               add_payment(row.card_amount);
                $("#card_table_body").append(`
                    <tr>
                        <td>${row.customer_name}</td>
                        <td>${row.card_type}</td>
                        <td>${row.card_number}</td>
                        <td class="card_amount">${__number_f(row.card_amount, false, false, __currency_precision)}</td>
                        <td>${row.slip_no}</td>
                        <td>${row.card_note}</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="${row.delete_url}">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            })
            calculateTotal("#card_table", ".card_amount", ".card_total");
          @endif
        @if(!empty($temp_data->cash_rows))
            let cash_rows = @json($temp_data->cash_rows);
             $("#cash_table_body").empty(); // Vider d'abord l'ancien contenu
              
            cash_rows.forEach(row => {
                add_payment(row.cash_amount);
                $("#cash_table_body").append(`
                    <tr>
                        <td>${row.customer_name}</td>
                        <td class="card_amount">${__number_f(row.cash_amount, false, false, __currency_precision)}</td>
                        <td>${row.cash_note}</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="${row.delete_url}">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            })
          @endif
          @if(!empty($temp_data->cash_deposit_rows))
              let cash_deposit_rows = @json($temp_data->cash_deposit_rows);
              $("#cash_deposit_table_body").empty(); // Vider d'abord l'ancien contenu
               
              cash_deposit_rows.forEach(row => {
                 add_payment(row.cash_deposit_amount);
                  $("#cash_deposit_table_body").append(`
                      <tr> 
                        <td>` +
                        row.bank_name +
                        `</td>
                        <td>` +
                       row.account +
                        `</td>
                        <td class="cash_deposit_amount">` +
                        __number_f(row.cash_deposit_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        row.time +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                  `);
              })
            calculateTotal("#cash_deposit_table", ".cash_deposit_amount", ".cash_deposit_total");
            @endif
          @if(!empty($temp_data->cheque_rows))
              let cheque_rows = @json($temp_data->cheque_rows);
              $("#cheque_table_body").empty(); // Vider d'abord l'ancien contenu
               
              cheque_rows.forEach(row => {
                 add_payment(row.cheque_amount);
                  $("#cheque_table_body").append(`
                      <tr> 
                        <td>` +
                        row.customer_name +
                        `</td>
                        <td>` +
                        row.bank_name +
                        `</td>
                        <td>` +
                        row.cheque_number +
                        `</td>
                        <td>` +
                        row.cheque_date +
                        `</td>
                        <td class="cheque_amount">` +
                        __number_f(row.cheque_amount, false, false, __currency_precision) +
                        `</td>
                         <td>` +
                        row.cheque_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cheque_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    </tr>
                  `);
              })
                calculateTotal("#cheque_table", ".cheque_amount", ".cheque_total");
            @endif
            @if(!empty($temp_data->add_payment_expense_rows))
              let add_payment_expense_rows = @json($temp_data->add_payment_expense_rows);
              $("#expense_table_body").empty(); // Vider d'abord l'ancien contenu
               
              add_payment_expense_rows.forEach(row => {
                 add_payment(row.expense_amount);
                  $("#expense_table_body").append(`
                      <tr> 
                        <td>` +
                        row.expense_number +
                        `</td>
                        <td>` +
                        row.expense_category_name +
                        `</td>
                        <td>` +
                        row.reference_no +
                        `</td>
                        <td>` +
                        row.expense_account_name +
                        `</td>
                        <td>` +
                        row.expense_reason +
                        `</td>
                        <td class="expense_amount">` +
                        __number_f(row.expense_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_expense_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                  `);
              })
                calculateTotal("#expense_table", ".expense_amount", ".expense_total");
            @endif
            @if(!empty($temp_data->shortage_rows))
              let shortage_rows = @json($temp_data->shortage_rows);
              $("#shortage_table_body").empty(); // Vider d'abord l'ancien contenu
               
              shortage_rows.forEach(row => {
                 add_payment(row.shortage_amount);
                  $("#shortage_table_body").append(`
                      <tr> 
                        <td></td>
                        <td class="shortage_amount">` +
                        __number_f(row.shortage_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        row.shortage_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_shortage_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                  `);
              })
                calculateTotal("#shortage_table", ".shortage_amount", ".shortage_total");
            @endif
            @if(!empty($temp_data->excess_rows)) 
              let excess_rows = @json($temp_data->excess_rows);
              $("#excess_table_body").empty(); // Vider d'abord l'ancien contenu
               
              excess_rows.forEach(row => {
                 add_payment(row.excess_amount);
                  $("#excess_table_body").append(`
                      <tr> 
                        <td></td>
                        <td class="shortage_amount">` +
                        __number_f(row.excess_amount, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        row.excess_note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_excess_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                  `);
              })
                calculateTotal("#excess_table", ".excess_amount", ".excess_total");
            @endif
            @if(!empty($temp_data->add_payment_credit_sale)) 
              let add_payment_credit_sale = @json($temp_data->add_payment_credit_sale);
              $("#credit_sale_table_body").empty(); // Vider d'abord l'ancien contenu
               
              add_payment_credit_sale.forEach(row => {
                 add_payment(row.sub_total);
                  $("#credit_sale_table_body").append(`
                      <tr> 
                        <td>` +
                        row.customer_name +
                        `</td>
                        <td>` +
                        row.outstanding +
                        `</td>
                        <td>` +
                        row.limit +
                        `</td>
                        <td>` +
                        row.order_no +
                        `</td>
                        <td>` +
                        row.order_date +
                        `</td>
                        <td>` +
                        row.customer_reference +
                        `</td>
                        <td>` +
                        row.product +
                        `</td>
                        <td>` +
                        __number_f(row.unit_price, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        __number_f(row.qty, false, false, __currency_precision) +
                        `</td>
                        <td class="credit_sale_amount">` +
                        __number_f(row.sub_total, false, false, __currency_precision) +
                        `</td>
                        
                        <td class="credit_tbl_discount_amount">` +
                        __number_f(row.discount_total, false, false, __currency_precision) +
                        `</td>
                        <td class="credit_tbl_total_amount">` +
                        __number_f(row.total, false, false, __currency_precision) +
                        `</td>                        
                        <td>` +
                        row.note +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="${row.delete_url}"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                  `);
              })
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
                calculateTotal("#credit_sale_table", ".credit_tbl_discount_amount", ".credit_tb_discount_total");
                calculateTotal("#credit_sale_table", ".credit_tbl_total_amount", ".credit_tbl_amount_total");
            @endif
            @if(!empty($temp_data->loan_payment_rows)) 
              let loan_payment_rows = @json($temp_data->loan_payment_rows);
              $("#loan_payments_table_body").empty(); // Vider d'abord l'ancien contenu
               
              loan_payment_rows.forEach(row => {
                 add_payment(row.loan_payments_amount);
                  $("#loan_payments_table_body").append(`
                  <tr>
                      <td>${row.bank_name}</td>
                        <td class="loan_payments_amount">${__number_f(row.loan_payments_amount, __currency_precision)}</td>
                        <td>${row.loan_payments_note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_loan_payment" data-href="${row.delete_url}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                  `);
              })
                calculateTotal("#loan_payments_table", ".loan_payments_amount", ".loan_payments_total");
            @endif
            @if(!empty($temp_data->drawing_payments_rows)) 
              let drawing_payments_rows = @json($temp_data->drawing_payments_rows);
              $("#drawing_payments_table_body").empty(); // Vider d'abord l'ancien contenu
               
              drawing_payments_rows.forEach(row => {
                 add_payment(row.cash_payment_amount);
                  $("#drawing_payments_table_body").append(`
                  <tr>
                      <td>${row.cash_payment_loan_account_name}</td>
                        <td class="loan_payments_amount">${__number_f(row.cash_payment_amount, __currency_precision)}</td>
                        <td>${row.cash_payment_note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_drawing_payment" data-href="/petro/settlement/payment/delete-loan-payment/{{$cash_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                  `);
              })
                calculateTotal("#drawing_payments_table", ".drawing_payments_amount", ".drawing_payments_total");
            @endif
            @if(!empty($temp_data->customer_loans_rows)) 
              let customer_loans_rows = @json($temp_data->customer_loans_rows);
              $("#customer_loans_table_body").empty(); // Vider d'abord l'ancien contenu
               
              customer_loans_rows.forEach(row => {
                console.log('customer', row)
                  add_payment(row.customer_loan_amount);
                  $("#customer_loans_table_body").append(`
                  <tr>
                      <td>${row.customer_name}</td>
                        <td class="loan_payments_amount">${__number_f(row.customer_loan_amount, __currency_precision)}</td>
                        <td>${row.customer_loan_note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_customer_loans_payment" data-href="${row.delete_url}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                  `);
              })
                calculateTotal("#customer_loans_table", ".customer_loan_amount", ".customer_loans_total");
            @endif
            calculateTotal("#cash_table", ".cash_amount", ".cash_total");
            calculate_payment_tab_total()
            clearTimeout(settlementTimer);
      }, 3000)
    }; 
    function handlePaymentSuccess() {
    // Lancer un événement personnalisé global
    $(document).trigger("custom:payment_added");
}
  </script>

    @endsection
