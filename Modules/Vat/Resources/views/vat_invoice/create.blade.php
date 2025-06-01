@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@php
    $business_id = request()->session()->get('business.id');
    $tax_rate = \App\TaxRate::where('business_id',$business_id)->first();
    $tax = !empty($tax_rate) ? $tax_rate->amount : 0;
@endphp

@section('content')
<div class="col-md-12">
 @include('vat::vat_invoice.partials.nav')

    {!! Form::open(['method' =>
    'post', 'id' => 'issue_bill_customer_form' ])
    !!}
    <div class="row">
        
        <input type="hidden" id="tax_rate" value="{{$tax}}">
    
           
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('location_id', __( 'vat::lang.location' )) !!}
          {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required']);
          !!}
        </div>
      </div>
    
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('prefix', __( 'vat::lang.prefix' )) !!}
          {!! Form::select('prefix_id', $prefixes, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'id' => 'prefix_id', 'required']);
          !!}
        </div>
      </div>
      
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('customer_bill_no', __( 'vat::lang.bill_no' )) !!}
          {!! Form::text('customer_bill_no', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'vat::lang.bill_no')]);
          !!}
        </div>
      </div>
    
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('customer_id', __( 'vat::lang.customer' )) !!}
          {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
          __( 'vat::lang.please_select')]);
          !!}
        </div>
      </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('sub_customer', __( 'vat::lang.sub_customer' )) !!}
              {!! Form::select('sub_customer', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
              __( 'vat::lang.please_select')]);
              !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('invoice_to', __( 'vat::lang.invoice_to' )) !!}
              {!! Form::select('invoice_to', ['customer' => __('vat::lang.customer'),'sub_customer' => __('vat::lang.sub_customer')],null, ['class' => 'form-control select2', 'style' => 'width:100%;']);
              !!}
            </div>
          </div>
          
           <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('voucher_order_creditlimit', __( 'vat::lang.credit_limit' )) !!}
                  {!! Form::text('voucher_order_creditlimit', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
                  __( 'vat::lang.credit_limit')]);
                  !!}
                </div>
            </div>
               <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('voucher_order_outstanding', __( 'vat::lang.outstanding' )) !!}
                  {!! Form::text('voucher_order_outstanding', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
                  __( 'vat::lang.outstanding')]);
                  !!}
                </div>
              </div>
      
    </div>
    
    <div class="row">
      
      
       <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('reference_id', __( 'vat::lang.reference' )) !!}
          
          <br><button type="button" class="btn btn-primary btn-flat reference-btn btn-modal" data-href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@referenceQuickAdd')}}" data-container=".contact_modal">
                        @lang('vat::lang.add_new')
                    </button><br>
          
          {!! Form::select('reference_id', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
          __( 'vat::lang.please_select')]);
          !!}
        </div>
      </div>
      
       <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_amount', __( 'vat::lang.invoice_amount' )) !!}
              {!! Form::text('voucher_order_amount', null, ['class' => 'form-control','readonly', 'required', 'placeholder' =>
              __( 'vat::lang.invoice_amount')]);
              !!}
            </div>
          </div>
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_date', __( 'vat::lang.transaction_date' )) !!}
              {!! Form::text('voucher_order_date', null, ['class' => 'form-control','readonly', 'placeholder' =>
              __( 'vat::lang.transaction_date')]);
              !!}
            </div>
      </div>
      
      <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('supplied_on', __( 'vat::lang.supplied_on' )) !!}
              {!! Form::date('supplied_on', null, ['class' => 'form-control', 'placeholder' =>
              __( 'vat::lang.supplied_on')]);
              !!}
            </div>
      </div>
  

    <div class="clearfix"></div>

    <table class="table table-responsive" id="issue_customer_bill_add_table">
      <thead>
        <tr>
          <th width="20%">@lang('vat::lang.product')</th>
          <th>@lang('vat::lang.unit_price')</th>
          <th>@lang('vat::lang.qty')</th>
          <th>@lang('vat::lang.discount')</th>
          <th>@lang('vat::lang.vat')</th>
          <th>@lang('vat::lang.sub_total')</th>
          <th>@lang('vat::lang.action')</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            {!! Form::select('issue_customer_bill[product_id][]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'vat::lang.please_select')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('vat::lang.unit_price'), 'readonly']) !!}
            
            {!! Form::hidden('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
            
          </td>
          <td>
            {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('vat::lang.qty')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('vat::lang.discount')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('vat::lang.tax')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('vat::lang.sub_total')]) !!}
          </td>
          <td>
            <button type="button" class="btn btn-xs btn-primary add_row" style="margin-top: 6px;">+</button>
          </td>
        </tr>

      </tbody>
      <tfoot>
          <tr>
              <th colspan="4"></th>
              <th>
                  @lang('vat::lang.total')
              </th>
              <th>
                  {!! Form::text('grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'grand_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
          <tr>
              <th colspan="4"></th>
              <th>
                  @lang('vat::lang.vat') ({{$tax}}%)
                  
              </th>
              <th>
                  {!! Form::text('vat_total', 0, ['class' => 'form-control','readonly', 'id' => 'vat_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
          <tr>
              <th colspan="4"></th>
              <th>
                  @lang('vat::lang.price_adjustment')
                  
              </th>
              <th>
                  {!! Form::text('price_adjustment', 0, ['class' => 'form-control', 'id' => 'price_adjustment', 'placeholder' => __('vat::lang.price_adjustment')]) !!}
              </th>
              <th></th>
          </tr>
          
           <tr>
              <th colspan="4"></th>
              <th>
                  @lang('vat::lang.final') @lang('vat::lang.total')
              </th>
              <th>
                  {!! Form::text('final_grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'final_grand_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
          
      </tfoot>
    </table>
    </div>
    
    <div class="box-body payment_row" data-row_id="0">
		<div id="payment_rows_div">
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
			<hr>
		</div>

	</div>
    
    <div class="pull-right">
      <button type="submit" class="btn btn-primary" id="save_issue_bill_customer_btn" formaction="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@store')}}">@lang( 'messages.save' )</button>
      <button type="submit" class="btn btn-danger" id="save_issue_bill_customer_btn"  formaction="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@store')}}?is_print=true">@lang( 'messages.save_and_print' )</button>
    </div>

    {!! Form::close() !!}
    
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
</div>
@endsection
@section('javascript')
<script src="{{url('Modules/Vat/Resources/assets/js/app-new.js')}}"></script>
<script>
  $('#voucher_order_date').datepicker("setDate" , new Date());
  $('.select2').select2();
  $(".reference-btn").hide();

  $('#customer_id').change(function(){
    let customer_id = $('#customer_id :selected').val();
    
    if(customer_id){
        $(".reference-btn").show();
    }else{
        $(".reference-btn").hide();
    }
        
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + customer_id,
            data: {},
            success: function (result) {
                3
                
                $("#voucher_order_outstanding").val(result.total_outstanding);
                $("#voucher_order_creditlimit").val(result.credit_limit);
                
                $('#voucher_order_amount').trigger('change');
            },
        });
        
        $.ajax({
            method: "get",
            url: "/petro/issue-customer-bill/get-customer-reference/" + customer_id,
            data: {},
            success: function (result) {
                $("#reference_id").empty().append(result);
            },
        });
        
  })
  
  
  $(document).on('change', '#prefix_id', function(){
    let prefix_id = $(this).val();
    if(prefix_id){
        $.ajax({
          method: 'get',
          url: '/vat-module/get-prefix/'+prefix_id,
          data: {  },
          success: function(result) {
                $("#customer_bill_no").val(result.bill_no);
          },
        });
    }else{
        $("#customer_bill_no").val("");
    }
    
        
  });
  
  
  $(document).ready(function () {
      $('#prefix_id').trigger('change');
      
        var new_row = `
            <tr>
              <td>
                {!! Form::select('issue_customer_bill[product_id][]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
                __( 'vat::lang.please_select')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('vat::lang.unit_price'), 'readonly']) !!}
                
                {!! Form::hidden('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('vat::lang.qty')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('vat::lang.discount')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('vat::lang.tax')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('vat::lang.sub_total')]) !!}
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
        
        $(document).on('change', '#price_adjustment', function () {
            calculateGrandTotals();
        });
        
        function calculateGrandTotals() {
            var vatTotal = 0;
            var grandTotal = 0;
            var price_adjustment= __read_number($("#price_adjustment"));
        
            $('#issue_customer_bill_add_table tbody tr').each(function () {
                var tax = __read_number($(this).find('.tax'));
                var subTotal = __read_number($(this).find('.sub_total'));
        
                vatTotal += tax;
                grandTotal += subTotal;
            });
            
            var final_grand_total = grandTotal + price_adjustment;
        
            __write_number($("#vat_total"),vatTotal);
            __write_number($('#grand_total'),grandTotal);
            __write_number($('#final_grand_total'),final_grand_total);
            __write_number($("#voucher_order_amount"),grandTotal);
            __write_number($("#amount_0"),grandTotal);
            $("#amount_0").attr('readonly',true);
            $('#voucher_order_amount').trigger('change');
        }

        
    });
</script>
@endsection


<script>
  
  

</script>