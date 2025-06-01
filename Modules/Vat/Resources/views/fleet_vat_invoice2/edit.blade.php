@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@php
    $business_id = request()->session()->get('business.id');
    $tax_rate = \App\TaxRate::where('business_id',$business_id)->first();
    $tax = !empty($tax_rate) ? $tax_rate->amount : 0;
@endphp

@section('content')
@include('vat::fleet_vat_invoice2.partials.nav')
<style>
    table>tbody>tr>td {
        vertical-align: middle;
    }

    .select2-results__option {
        padding: 8px;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
    }
</style>

<div class="col-md-12">
    {!! Form::open(['method' =>
    'put', 'id' => 'issue_bill_customer_form' ])
    !!}
    <div class="row">
        <input type="hidden" id="tax_rate" value="{{$tax}}">
    
           
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('location_id', __( 'vat::lang.location' )) !!}
          {!! Form::select('location_id', $business_locations, $invoice->location_id, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required']);
          !!}
        </div>
      </div>
    
   
      
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('customer_bill_no', __( 'vat::lang.bill_no' )) !!}
          {!! Form::text('customer_bill_no', $invoice->customer_bill_no, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
          __( 'vat::lang.bill_no')]);
          !!}
        </div>
      </div>
    
    <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('customer_id', __( 'vat::lang.customer' )) !!}
           {!! Form::text('customer_id', $customer->name, ['class' => 'form-control', 'required','disabled', 'placeholder' =>
          __( 'vat::lang.customer')]);
          !!}
        </div>
      </div>
      
       <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('ro_id', __( 'vat::lang.route_operation' )) !!}
          {!! Form::text('ro_id', $ro->invoice_no, ['class' => 'form-control', 'required','disabled', 'placeholder' =>
          __( 'vat::lang.route_operation')]);
          !!}
        </div>
      </div>
      
      
    </div>
    
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('vat_number', __( 'airline::lang.customer_vat_no' ) . '') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_vat_no')}}">
                        <i class="fa fa-user"></i>
                    </button>
                </div>
                {!! Form::text('vat_number', $customer->vat_number, ['class' => 'form-control', 'id' => 'customer_vat_number', 'readonly','placeholder' => __('airline::lang.customer_vat_no')]); !!}
                <input type="hidden" id="vat_btn_input">
                
            </div>
        </div>
        
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
              {!! Form::select('invoice_to', ['customer' => __('vat::lang.customer'),'sub_customer' => __('vat::lang.sub_customer')], $invoice->invoice_to, ['class' => 'form-control select2', 'style' => 'width:100%;']);
              !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_creditlimit', __( 'vat::lang.credit_limit' )) !!}
              {!! Form::text('voucher_order_creditlimit', $invoice->credit_limit, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
              __( 'vat::lang.credit_limit')]);
              !!}
            </div>
        </div>
           
          
    </div>
    
    
    <div class="row">
        
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_outstanding', __( 'vat::lang.outstanding' )) !!}
              {!! Form::text('voucher_order_outstanding', @num_format($invoice->outstanding_amount), ['class' => 'form-control', 'required','readonly', 'placeholder' =>
              __( 'vat::lang.outstanding')]);
              !!}
            </div>
          </div>
        
     <div class="form-group col-sm-3">
            {!! Form::label('reference_id', __( 'vat::lang.reference' )) !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" style="" title="{{__('airline::lang.customer')}}">
                        <i class="fa fa-user"></i>
                    </button>
                </div>
                {!! Form::select('reference_id', $customer_ref, $invoice->reference_id, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
                      __( 'vat::lang.please_select')]);
                      !!}
                <span class="input-group-btn">
                    <button type="button" style="" class="btn btn-default bg-white btn-flat btn-modal  reference-btn" data-href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@referenceQuickAdd')}}" data-container=".contact_modal">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('sale_type', __( 'vat::lang.sale_type' )) !!}
              {!! Form::text('sale_type', 'Product', ['class' => 'form-control', 'style' => 'width:100%;', 'readonly', 'placeholder' =>
              __( 'vat::lang.please_select')]);
              !!}
            </div>
         </div>
      
     
    </div>
      
       <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_amount', __( 'vat::lang.invoice_amount' )) !!}
              {!! Form::text('voucher_order_amount', @num_format($invoice->total_amount), ['class' => 'form-control','readonly', 'required', 'placeholder' =>
              __( 'vat::lang.invoice_amount')]);
              !!}
            </div>
          </div>
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_date', __( 'vat::lang.transaction_date' )) !!}
              {!! Form::text('voucher_order_date', @format_date($invoice->date), ['class' => 'form-control','readonly', 'placeholder' =>
              __( 'vat::lang.transaction_date')]);
              !!}
            </div>
      </div>
      
      <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('supplied_on', __( 'vat::lang.supplied_on' )) !!}
              {!! Form::date('supplied_on', $invoice->supplied_on, ['class' => 'form-control', 'placeholder' =>
              __( 'vat::lang.supplied_on')]);
              !!}
            </div>
      </div>
  

    <div class="clearfix"></div>

    <table class="table table-responsive" id="issue_customer_bill_add_table">
      <thead>
        <tr>
          <th width="20%">@lang('vat::lang.product')</th>
          <th>@lang('vat::lang.unit_price_before_vat')</th>
          <th>@lang('vat::lang.qty')</th>
          <th>@lang('vat::lang.discount')</th>
           <th>@lang('vat::lang.unit_vat') {{$tax}} %</th>
          <th>@lang('vat::lang.vat')</th>
          <th>@lang('vat::lang.sub_total')</th>
        </tr>
      </thead>
      <tbody>
          @foreach($invoice_details as $key => $detail)
            <tr class="first-row">
          <td>
            {!! Form::hidden('issue_customer_bill[product_id][]', $detail->product_id, ['required', 'id' => 'product_id','class' => 'product_id']) !!}
            {!! Form::text('product_name',$ro->invoice_no, ['class' => 'form-control product_name', 'style' => 'width:100%;', 'readonly', 'placeholder' =>
            __( 'vat::lang.product')]) !!}
          </td>
          <td>
            {!! Form::hidden('issue_customer_bill[unit_price][]', $detail->unit_price, ['class' => 'form-control unit_price', 'placeholder' => __('vat::lang.unit_price'), 'readonly']) !!}
            
            {!! Form::text('issue_customer_bill[unit_price_excl][]', $detail->unit_price_before_tax, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
            
          </td>
          <td>
            {!! Form::text('issue_customer_bill[qty][]', $detail->qty, ['class' => 'form-control qty', 'placeholder' => __('vat::lang.qty')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[discount][]', $detail->discount, ['class' => 'form-control discount', 'placeholder' => __('vat::lang.discount')]) !!}
          </td>
          
           <td>
            {!! Form::text('issue_customer_bill[unit_vat_rate][]', $detail->unit_vat_rate, ['class' => 'form-control unit_vat_rate', 'placeholder' => __('vat::lang.unit_vat'), 'readonly']) !!}
          </td>
          
          <td>
            {!! Form::text('issue_customer_bill[tax][]', $detail->tax, ['class' => 'form-control tax','readonly', 'placeholder' => __('vat::lang.tax')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[sub_total][]', $detail->sub_total, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('vat::lang.sub_total')]) !!}
          </td>
        </tr>
          @endforeach
        

      </tbody>
      <tfoot>
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.total_invoice_amount_with_vat')
              </th>
              <th>
                  {!! Form::text('grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'grand_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
          </tr>
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.tax_base_value')
              </th>
              <th>
                  {!! Form::text('grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'grand_total_with_vat', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
          </tr>
          
           <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.vat') ({{$tax}}%)
                  
              </th>
              <th>
                  {!! Form::text('vat_total', 0, ['class' => 'form-control','readonly', 'id' => 'vat_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
          </tr>
          
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.price_adjustment')
              </th>
              <th>
                  {!! Form::text('price_adjustment', @num_format($invoice->price_adjustment), ['class' => 'form-control', 'id' => 'price_adjustment', 'placeholder' => __('vat::lang.price_adjustment')]) !!}
              </th>
          </tr>
          
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.total_invoice_amount_with_vat')
              </th>
              <th>
                  {!! Form::text('final_grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'final_grand_total_with_vat', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
          </tr>
          
      </tfoot>
    </table>
    </div>
    
    <div class="box-body payment_row" data-row_id="0">
		<div id="payment_rows_div">
            		@foreach($payment as $index => $one)
                        @include('sale_pos.partials.payment_row_form', ['row_index' => $index, 'payment' => $one])
                    @endforeach 
			<hr>
		</div>

	</div>
	
	<div class="pull-right">
      <button type="submit" class="btn btn-primary" id="save_issue_bill_customer_btn" formaction="{{action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@update',[$invoice->id])}}">@lang( 'messages.save' )</button>
      <button type="submit" class="btn btn-danger" id="save_issue_bill_customer_btn"  formaction="{{action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@update',[$invoice->id])}}?is_print=true">@lang( 'messages.save_and_print' )</button>
    </div>
  

    {!! Form::close() !!}
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade contact_modal_noreload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    </div>
</div>
@endsection
@section('javascript')
<script src="{{url('Modules/Vat/Resources/assets/js/app-new.js')}}"></script>
<script>
  $('#voucher_order_date').datepicker("setDate" , new Date());
  $('.select2').select2();
  $(".reference-btn").hide();
  
  $(document).on('click','#update_vat_number',function(e) {
            e.preventDefault();
            
            if($("#update_fields_type").val() == 'nic_number'){
                var data = {'nic_number' : $("#add_nic_number").val()};
            } else if($("#update_fields_type").val() == 'mobile'){
                var data = {'mobile' : $("#add_mobile").val()};
            }else{
                if($("#is_single_field").val() == 'yes'){
                    var data = {'vat_number' : $("#main_add_vat_number").val()};  
                }else{
                    var data = {'vat_number' : $("#add_vat_number").val()};  
                }
                
            }
            
            
            $.ajax({
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                url: $('#contact_vat_number_form').attr('action'),
                success: function(result) {
                    if (result.success == true) {
                        $('div.contact_modal_noreload').modal('hide');
                        toastr.success(result.msg);
                        
                        if($("#update_fields_type").val() == 'nic_number'){
                            $("#passport_number_text").val(result.contact.nic_number);
                        } else if($("#update_fields_type").val() == 'mobile'){
                            $("#passenger_mobile_text").val(result.contact.mobile);
                        }else{
                            $("#customer_vat_number").val(result.contact.vat_number);
                        }
                        
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
  
  $(document).on('click', '.btn-vat-modal', function(e) {

          e.preventDefault();
          
          var url = '/contacts/update-vatnumber/' + $("#vat_btn_input").val();
          
          
          console.log(url);
    
          var container = $(this).data('container');
          
          $(container).empty();
    
          $.ajax({
    
              url: url,
    
              dataType: 'html',
    
              success: function(result) {
                  // var contact = $('#default_contact_id').val();
                  $(container).html(result).modal('show');
                  // $(container).find('input#contact_id').val(contact);
              },
    
          });
    
      });

  
  $(document).ready(function () {
      
        calculateGrandTotals();
        
        $(".payment_types_dropdown").trigger('change');
        
        $(document).on('change', '.unit_price, .qty, .discount', function(){
            calculate($(this));
        });
        
        function calculate($this) {
            var unitPriceField = $($this).closest('tr').find('.unit_price');
            var qtyField = $($this).closest('tr').find('.qty');
            var discountField = $($this).closest('tr').find('.discount');
            var unitVatRateField = $($this).closest('tr').find('.unit_vat_rate');
        
            var unitPrice = __read_number(unitPriceField);
            var qty = __read_number(qtyField);
            var discount = __read_number(discountField);
            var unitVatRate = __read_number(unitVatRateField);
        
            var discountedUnitPrice = unitPrice - discount;
            
            var tax_rate = __read_number($("#tax_rate")) || 0; // Default tax rate to 0 if not found
            
            
            
            var unit_vat = ((discountedUnitPrice * tax_rate) / (100+tax_rate)).toFixed(2); // Calculate unit VAT
            
            var unitPriceExc = unitPrice - unit_vat;
           
            var vat = parseFloat(unit_vat) * qty; // Calculate total VAT for the line item
            
            var subTotal = (discountedUnitPrice) * qty; // Calculate subTotal using the provided formula
        
            __write_number($($this).closest('tr').find('.sub_total'), subTotal);
            __write_number($($this).closest('tr').find('.tax'), vat);
            __write_number(unitVatRateField, parseFloat(unit_vat));
            
            __write_number($($this).closest('tr').find('.unit_price_excl'), unitPriceExc);
            
        
            calculateGrandTotals();
        }

        
        $(document).on('change', '#price_adjustment', function () {
            calculateGrandTotals();
        });
        
        function calculateGrandTotals() {
            var vatTotal = 0;
            var grandTotal = 0;
            var grandTotalWithVat = 0
            var price_adjustment= __read_number($("#price_adjustment"));
        
            $('#issue_customer_bill_add_table tbody tr').each(function () {
                var tax = __read_number($(this).find('.tax'));
                var subTotal = __read_number($(this).find('.sub_total'));
        
                vatTotal += tax;
                grandTotal += subTotal;
            });
            
            grandTotalWithVat += grandTotal-vatTotal ;
            
            var final_grand_total = grandTotal + price_adjustment;
        
            __write_number($("#vat_total"),vatTotal);
            __write_number($('#grand_total'),grandTotal);
            __write_number($('#grand_total_with_vat'),grandTotalWithVat);
            __write_number($('#final_grand_total_with_vat'),final_grand_total);
            __write_number($("#voucher_order_amount"),grandTotal);
            __write_number($("#amount_0"),final_grand_total);
            $("#amount_0").attr('readonly',true);
            $('#voucher_order_amount').trigger('change');
        }

        
    });
    
</script>
@endsection
