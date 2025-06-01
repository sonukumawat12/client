@php
    $business_id = request()->session()->get('business.id');
    $tax_rate = \App\TaxRate::where('business_id',$business_id)->first();
    $tax = !empty($tax_rate) ? $tax_rate->amount : 0;
@endphp

<div class="modal-dialog" role="document" style="width: 85%;">
  <div class="modal-content">
      
       <input type="hidden" id="tax_rate" value="{{$tax}}">

    {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\IssueCustomerBillWithVATController@store'), 'method' =>
    'post', 'id' => 'issue_bill_customer_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'petro::lang.add_issue_bill_customers' )</h4>
   
    </div>

    <div class="modal-body">
        
     <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('location_id', __( 'petro::lang.location' )) !!}
          {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required']);
          !!}
        </div>
      </div>
    
    
     <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('pump_operator_id', __( 'petro::lang.pump_operator' )) !!}
          {!! Form::select('pump_operator_id', $pump_operators, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
          __( 'petro::lang.please_select')]);
          !!}
        </div>
      </div>
      
     
     <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('pump_id', __( 'petro::lang.pump' )) !!}
          {!! Form::select('pump_id', $pumps, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
          __( 'petro::lang.please_select')]);
          !!}
        </div>
      </div>
 
 
 
 
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('prefix', __( 'petro::lang.prefix' )) !!}
          {!! Form::select('prefix_id', $prefixes, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'id' => 'prefix_id', 'required']);
          !!}
        </div>
      </div>
      
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('customer_bill_no', __( 'petro::lang.customer_bill_no' )) !!}
          {!! Form::text('customer_bill_no', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'petro::lang.customer_bill_no')]);
          !!}
        </div>
      </div>
      
      <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('order_number', __( 'petro::lang.order_number' ) ) !!}
            {!! Form::text('order_number', null, ['class' => 'form-control credit_sale_fields
            order_number',
            'placeholder' => __(
            'petro::lang.order_number' ) ]); !!}
        </div>
    </div>
    
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('customer_id', __( 'petro::lang.customer' )) !!}
          {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
          __( 'petro::lang.please_select')]);
          !!}
        </div>
      </div>
         <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('voucher_order_creditlimit', __( 'petro::lang.voucher_order_creditlimit' )) !!}
          {!! Form::text('voucher_order_creditlimit', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'petro::lang.voucher_order_creditlimit')]);
          !!}
        </div>
      </div>
       <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('voucher_order_outstanding', __( 'petro::lang.voucher_order_outstanding' )) !!}
          {!! Form::text('voucher_order_outstanding', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'petro::lang.voucher_order_outstanding')]);
          !!}
        </div>
      </div>
      
      
       <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('reference_id', __( 'petro::lang.reference' )) !!}
          {!! Form::select('reference_id', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
          __( 'petro::lang.please_select')]);
          !!}
        </div>
      </div>
      
      
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('voucher_order_amount', __( 'petro::lang.voucher_order_amount' )) !!}
          {!! Form::text('voucher_order_amount', null, ['class' => 'form-control','readonly', 'required', 'placeholder' =>
          __( 'petro::lang.voucher_order_amount')]);
          !!}
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('voucher_order_newoutstanding', __( 'petro::lang.voucher_order_newoutstanding' )) !!}
          {!! Form::text('voucher_order_newoutstanding', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'petro::lang.voucher_order_newoutstanding')]);
          !!}
        </div>
      </div>
      
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('limit_balance', __( 'petro::lang.limit_balance' )) !!}
          {!! Form::text('limit_balance', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'petro::lang.limit_balance')]);
          !!}
        </div>
      </div>
    
 
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('voucher_order_date', __( 'petro::lang.voucher_order_date' )) !!}
          {!! Form::text('voucher_order_date', null, ['class' => 'form-control','readonly', 'placeholder' =>
          __( 'petro::lang.voucher_order_date')]);
          !!}
        </div>
      </div>
      

    <div class="clearfix"></div>

    <table class="table table-responsive" id="issue_customer_bill_add_table">
      <thead>
        <tr>
          <th width="20%">@lang('petro::lang.product')</th>
          <th>@lang('petro::lang.unit_price')</th>
          <th>@lang('petro::lang.qty')</th>
          <th>@lang('petro::lang.discount')</th>
          <th>@lang('petro::lang.vat')</th>
          <th>@lang('petro::lang.sub_total')</th>
          <th>@lang('petro::lang.action')</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            {!! Form::select('issue_customer_bill[product_id][]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('petro::lang.unit_price'), 'readonly']) !!}
            
            {!! Form::hidden('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
            
          </td>
          <td>
            {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('petro::lang.qty')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('petro::lang.discount')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('petro::lang.tax')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('petro::lang.sub_total')]) !!}
          </td>
          <td>
            <button type="button" class="btn btn-xs btn-primary add_row" style="margin-top: 6px;">+</button>
          </td>
        </tr>

      </tbody>
      <tfoot>
          <tr>
              <th colspan="4">@lang('petro::lang.total')</th>
              <th>
                  {!! Form::text('vat_total', 0, ['class' => 'form-control','readonly', 'id' => 'vat_total', 'placeholder' => __('petro::lang.total')]) !!}
              </th>
              <th>
                  {!! Form::text('grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'grand_total', 'placeholder' => __('petro::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
      </tfoot>
    </table>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_issue_bill_customer_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#voucher_order_date').datepicker("setDate" , new Date());
  $('.select2').select2();

  $('#customer_id').change(function(){
    let customer_id = $('#customer_id :selected').val();
    
        
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + customer_id,
            data: {},
            success: function (result) {
                
                $("#voucher_order_outstanding").val(result.total_outstanding);
                $("#voucher_order_creditlimit").val(result.credit_limit);
                
                $("#reference_id").empty();
                $("#reference_id").append(`<option selected="selected" value="">Please Select</option>`);
                result.customer_references.forEach(function (ref, i) {
                    $("#reference_id").append(`<option value="` + ref.reference + `">` + ref.reference + `</option>`);
                    $("#reference_id").val($("#reference_id option:eq(1)").val()).trigger("change");
                });
                $('#voucher_order_amount').trigger('change');
            },
        });
        
  })
  
  $('#pump_operator_id').change(function(){
    let operator_id = $('#pump_operator_id :selected').val();
    
    $("#pump_id").prop('disabled',true);
    $("#pump_id").empty();
        
        $.ajax({
            method: "get",
            url: "/petro/get-assigned-pumps/" + operator_id,
            data: {},
            success: function (result) {
                $("#pump_id").append("<option>{{__( 'petro::lang.please_select')}}</option>");
                $.each( result, function( index, value ){
                    $("#pump_id").append("<option value='"+index+"'>"+value+"</option>");
                });
                
                $("#pump_id").prop('disabled',false);
            },
        });
        
  })
  
  $('#voucher_order_amount').change(function(){
        var amount = __read_number($("#voucher_order_amount"));
        var current_outstanding = __read_number($("#voucher_order_outstanding"));
        var current_limit = __read_number($("#voucher_order_creditlimit"));
        
        if(current_limit == 0){
            $("#limit_balance").val("No Limit");
        }else{
            __write_number($("#limit_balance"), (current_limit - amount));
        }
        
        __write_number($("#voucher_order_newoutstanding"), (amount+current_outstanding));
        
        
  })
  
  $(document).on('change', '#prefix_id', function(){
    let prefix_id = $(this).val();
    
    $.ajax({
      method: 'get',
      url: '/petro/get-prefixes/'+prefix_id,
      data: {  },
      success: function(result) {
            $("#customer_bill_no").val(result.bill_no);
      },
    });
  });
  
  
  $(document).ready(function () {
      
      $("#prefix_id").trigger('change');
      
        var new_row = `
            <tr>
              <td>
                {!! Form::select('issue_customer_bill[product_id][]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
                __( 'petro::lang.please_select')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('petro::lang.unit_price'), 'readonly']) !!}
                
                {!! Form::hidden('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('petro::lang.qty')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('petro::lang.discount')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('petro::lang.tax')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('petro::lang.sub_total')]) !!}
              </td>
              <td>
                <button type="button" class="btn btn-xs btn-danger remove_row" style="margin-top: 6px;">-</button>
              </td>
            </tr>
        `;
        $('.add_row').on('click', function () {
            $('#issue_customer_bill_add_table tbody').prepend(new_row);
            $('.select2').select2();
        });
        
        
        $(document).on('click', '.remove_row', function () {
            $(this).closest('tr').remove();
            calculateGrandTotals();
        });
        
        $(document).on('change', '.unit_price, .qty, .discount', function(){
            calculate($(this));
        });
        
        function calculate($this) {
            var unitPriceField = $($this).closest('tr').find('.unit_price');
            var unitPriceFieldExcl = $($this).closest('tr').find('.unit_price_excl');
            var qtyField = $($this).closest('tr').find('.qty');
            var discountField = $($this).closest('tr').find('.discount');
            
            var unitPrice = __read_number(unitPriceField);
            var qty = __read_number(qtyField);
            var unitPriceExcl = __read_number(unitPriceFieldExcl);
            var discount = __read_number(discountField);
    
            var subTotal = (unitPrice * qty) - discount;
            var tax_rate = __read_number($("#tax_rate")) ?? 0;
            var vat = subTotal - (subTotal / (1 + (tax_rate/100)));
            
            __write_number($($this).closest('tr').find('.sub_total'),subTotal);
            __write_number($($this).closest('tr').find('.tax'),vat);
            
            calculateGrandTotals();
        }
        
    
        
        $(document).on('change', '.product_id', function () {
            var product_id = $(this).val();
            var unitPriceField = $(this).closest('tr').find('.unit_price');
            var unitPriceFieldExcl = $(this).closest('tr').find('.unit_price_excl');
            $.ajax({
                url: '/petro/issue-customer-bill/get-product-price/' + product_id,
                type: 'GET',
                success: function (data) {
                    // Populate the unit price field with the result
                    unitPriceField.val(data.unit_price);
                    unitPriceFieldExcl.val(data.unit_price_excl);
                    
                    unitPriceField.trigger('change');
                },
            });
            
        });
        
        function calculateGrandTotals() {
            var vatTotal = 0;
            var grandTotal = 0;
        
            $('#issue_customer_bill_add_table tbody tr').each(function () {
                var tax = __read_number($(this).find('.tax'));
                var subTotal = __read_number($(this).find('.sub_total'));
        
                vatTotal += tax;
                grandTotal += subTotal;
            });
        
            __write_number($("#vat_total"),vatTotal);
            __write_number($('#grand_total'),grandTotal);
            __write_number($("#voucher_order_amount"),grandTotal);
            $('#voucher_order_amount').trigger('change');
        }

        
    });
  

</script>