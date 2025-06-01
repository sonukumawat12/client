@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@php
    $business_id = request()->session()->get('business.id');
    $tax_rate = \App\TaxRate::where('business_id',$business_id)->first();
    $tax = !empty($tax_rate) ? $tax_rate->amount : 0;
@endphp

@section('content')
@include('vat::vat_invoice2.partials.nav')
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
    'post', 'id' => 'issue_bill_customer_form' ])
    !!}
    <div class="row">
        
        <input type="hidden" id="tax_rate" value="{{$tax}}">
        <input type="hidden" id="route_operation_id" name="route_operation_id">
    
    <div class="col-md-12" style="margin-top: 20px;">
        <button type="button" class="btn btn-danger pull-right btn-modal" data-href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@customerQuickAdd')}}" data-container=".contact_modal">
            @lang('vat::lang.first_time_customer')
        </button>
        
        @if($fleet_active && !empty($fleet_customers))
            <button type="button" class="btn btn-info pull-left select-ro-btn hide">
                @lang('vat::lang.select_ro')
            </button>
        @endif
        
    </div>
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
        <div class="form-group col-sm-3">
            {!! Form::label('vat_number', __( 'airline::lang.customer_vat_no' ) . '') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_vat_no')}}">
                        <i class="fa fa-user"></i>
                    </button>
                </div>
                {!! Form::text('vat_number', null, ['class' => 'form-control', 'id' => 'customer_vat_number', 'readonly','placeholder' => __('airline::lang.customer_vat_no')]); !!}
                <input type="hidden" id="vat_btn_input">
                <span class="input-group-btn vat-btn-group hide">
                    <button type="button" class="btn btn-default bg-white btn-flat btn-vat-modal vat-btn-group-action" data-href="" data-container=".contact_modal_noreload">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
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
               
      
    </div>
    
    <div class="row">
        
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('voucher_order_outstanding', __( 'vat::lang.outstanding' )) !!}
              {!! Form::text('voucher_order_outstanding', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
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
                {!! Form::select('reference_id', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
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
              {!! Form::select('sale_type', array('Product' => __('vat::lang.product'),'Service' => __('vat::lang.service')), null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
              __( 'vat::lang.please_select')]);
              !!}
            </div>
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
          <th>Sale Price before tax</th>
          <th>@lang('vat::lang.qty')</th>
          <th>@lang('vat::lang.unit_discount')</th>
          <th>@lang('vat::lang.unit_vat') {{$tax}} %</th>
          <th>@lang('vat::lang.vat')</th>
          <th>@lang('vat::lang.sub_total')</th>
          <th>@lang('vat::lang.action')</th>
        </tr>
      </thead>
      <tbody>
        <tr class="first-row">
          <td>
            {!! Form::select('issue_customer_bill[product_id][]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'vat::lang.please_select')]) !!}
          </td>
          <td>
            {!! Form::hidden('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('vat::lang.unit_price'), 'readonly']) !!}
            
            {!! Form::text('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
            {!! Form::hidden('issue_customer_bill[unit_price_unformatted][]', 0, ['class' => 'form-control unit_price_unformatted']) !!}
            {!! Form::hidden('issue_customer_bill[unit_price_excl_unformatted][]', 0, ['class' => 'form-control unit_price_excl_unformatted']) !!}
            
          </td>
          <td>
            {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('vat::lang.qty')]) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('vat::lang.discount')]) !!}
          </td>
          
           <td>
            {!! Form::text('issue_customer_bill[unit_vat_rate][]', 0, ['class' => 'form-control unit_vat_rate', 'placeholder' => __('vat::lang.unit_vat'), 'readonly']) !!}
          </td>
          
          <td>
            {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('vat::lang.tax')]) !!}
            {!! Form::hidden('issue_customer_bill[tax_unformatted][]', 0, ['class' => 'form-control tax_unformatted']) !!}
          </td>
          <td>
            {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('vat::lang.sub_total')]) !!}
            {!! Form::hidden('issue_customer_bill[sub_total_unformatted][]', 0, ['class' => 'form-control sub_total_unformatted']) !!}
          </td>
          <td>
            <button type="button" class="btn btn-xs btn-primary add_row" style="margin-top: 6px;">+</button>
          </td>
        </tr>

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
              <th></th>
          </tr>
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.tax_base_value')
              </th>
              <th>
                  {!! Form::text('grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'grand_total_with_vat', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
          
           <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.vat') ({{$tax}}%)
                  
              </th>
              <th>
                  {!! Form::text('vat_total', 0, ['class' => 'form-control','readonly', 'id' => 'vat_total', 'placeholder' => __('vat::lang.total')]) !!}
              </th>
              <th></th>
          </tr>
          
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.price_adjustment')
              </th>
              <th>
                  {!! Form::text('price_adjustment', @num_format(0), ['class' => 'form-control', 'id' => 'price_adjustment', 'placeholder' => __('vat::lang.price_adjustment')]) !!}
              </th>
              <th></th>
          </tr>
          
          
          <tr>
              <th colspan="4"></th>
              <th colspan="2">
                  @lang('vat::lang.total_invoice_amount_with_vat')
              </th>
              <th>
                  {!! Form::text('final_grand_total', 0, ['class' => 'form-control','readonly', 'id' => 'final_grand_total_with_vat', 'placeholder' => __('vat::lang.total')]) !!}
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
      <button type="submit" class="btn btn-primary" id="save_issue_bill_customer_btn" formaction="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@store')}}">@lang( 'messages.save' )</button>
      <button type="submit" class="btn btn-danger" id="save_issue_bill_customer_btn"  formaction="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@store')}}?is_print=true">@lang( 'messages.save_and_print' )</button>
    </div>

    {!! Form::close() !!}
    
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade contact_modal_noreload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    </div>
    
    <div class="modal fade route_operations_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        
        <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
            
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('vat::lang.select_ro')</h4>
            </div>
        
            <div class="modal-body">
                
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      {!! Form::label('ro_customer_id', __( 'vat::lang.customer' )) !!}
                      {!! Form::select('ro_customer_id', $fleet_customers, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                      __( 'vat::lang.please_select')]);
                      !!}
                    </div>
                  </div>
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      {!! Form::label('ro_id', __( 'vat::lang.route_operation' )) !!}
                      {!! Form::select('ro_id', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                      __( 'vat::lang.please_select')]);
                      !!}
                    </div>
                  </div>
              </div>
        
            </div>
        
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" id="update_ro" data-dismiss="modal" disabled>@lang( 'messages.save' )</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        
            {!! Form::close() !!}
        
          </div><!-- /.modal-content -->
        </div>
    </div>
    
</div>
@endsection
@section('javascript')
<script src="{{url('Modules/Vat/Resources/assets/js/app-new.js')}}"></script>
<script>
  $('#voucher_order_date').datepicker("setDate" , new Date());
  $('.select2').select2();
  $(".reference-btn").hide();
  
  
  
  $(document).on('click','.select-ro-btn',function(){
      $(".route_operations_modal").modal('show');
  })
  
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

  $('#customer_id').change(function(){
    let customer_id = $('#customer_id :selected').val();
    
    if(customer_id){
        $(".reference-btn").show();
        $(".vat-btn-group").removeClass('hide');
        $("#vat_btn_input").val(customer_id);
    }else{
        $(".reference-btn").hide();
        $(".vat-btn-group").addClass('hide');
        $("#customer_vat_number").val("");
    }
    
        
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + customer_id,
            data: {},
            success: function (result) {
                
                $("#voucher_order_outstanding").val(result.total_outstanding);
                $("#voucher_order_creditlimit").val(result.credit_limit);
                $("#customer_vat_number").val(result.vat_number);
                
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
  
  $('#ro_customer_id').change(function(){
    let customer_id = $('#ro_customer_id :selected').val();
    $("#update_ro").attr('disabled',true);
    
    let $select = $('#ro_id');
    $select.empty().append('<option value="">Please select</option>');
    
    if(customer_id){
        $.ajax({
            method: "get",
            url: "/vat-module/get-route-ops/" + customer_id,
            data: {},
            success: function (result) {
                
                for (const [key, value] of Object.entries(result)) {
                    let $option = $('<option>', {
                        value: key,
                        text: value
                    });
                    $select.append($option);
                }
                $select.select2();
            },
        });
    }
        
  })
  
  $('#ro_id').change(function(){
    let customer_id = $('#ro_id :selected').val();
    $("#update_ro").attr('disabled',true);
    
    if(customer_id){
        $.ajax({
            method: "get",
            url: "/vat-module/get-ro-details/" + customer_id,
            data: {},
            success: function (result) {
                
                $("#customer_id").val(result.contact_id).trigger('change');
                $("#sale_type").val('Product').trigger('change');
                __write_number($("#voucher_order_amount"),result.amount);
                $("#voucher_order_date").val(result.date_of_operation);
                $("#supplied_on").val(result.date_of_operation);
                $("#route_operation_id").val(result.id);
                
                $("#update_ro").attr('disabled',false);
                
                var products_arr = JSON.parse(result.product_id);
                var qty_arr = JSON.parse(result.qty);
                
                for(var i = 0; i<products_arr.length; i++){
                    if(i > 0){
                        $('#issue_customer_bill_add_table tbody').prepend(new_row);
                    }
                }
                
                var k=0;
                $('#issue_customer_bill_add_table tbody tr').each(function () {
                    $(this).find('.product_id').val(products_arr[k]).trigger('change');
                    $(this).find('.qty').val(qty_arr[k]);
            
                    k++;
                });
            },
        });
    }
        
  })
  
  
  $(document).on('change', '#prefix_id', function(){
    let prefix_id = $(this).val();
    if(prefix_id){
        $.ajax({
          method: 'get',
          url: '/vat-module/get-prefix2/'+prefix_id,
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
                {!! Form::hidden('issue_customer_bill[unit_price][]', 0, ['class' => 'form-control unit_price', 'placeholder' => __('vat::lang.unit_price'), 'readonly']) !!}
                
                {!! Form::text('issue_customer_bill[unit_price_excl][]', 0, ['class' => 'form-control unit_price_excl', 'readonly']) !!}
                {!! Form::hidden('issue_customer_bill[unit_price_unformatted][]', 0, ['class' => 'form-control unit_price_unformatted']) !!}
                {!! Form::hidden('issue_customer_bill[unit_price_excl_unformatted][]', 0, ['class' => 'form-control unit_price_excl_unformatted']) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[qty][]', 0, ['class' => 'form-control qty', 'placeholder' => __('vat::lang.qty')]) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[discount][]', 0, ['class' => 'form-control discount', 'placeholder' => __('vat::lang.discount')]) !!}
              </td>
              
               <td>
                {!! Form::text('issue_customer_bill[unit_vat_rate][]', 0, ['class' => 'form-control unit_vat_rate', 'placeholder' => __('vat::lang.unit_vat'), 'readonly']) !!}
              </td>
              
              <td>
                {!! Form::text('issue_customer_bill[tax][]', 0, ['class' => 'form-control tax','readonly', 'placeholder' => __('vat::lang.tax')]) !!}
                {!! Form::hidden('issue_customer_bill[tax_unformatted][]', 0, ['class' => 'form-control tax_unformatted']) !!}
              </td>
              <td>
                {!! Form::text('issue_customer_bill[sub_total][]', 0, ['class' => 'form-control sub_total','readonly', 'placeholder' => __('vat::lang.sub_total')]) !!}
                {!! Form::hidden('issue_customer_bill[sub_total_unformatted][]', 0, ['class' => 'form-control sub_total_unformatted']) !!}
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
    var unitPriceField = $($this).closest('tr').find('.unit_price_excl_unformatted');
    var qtyField = $($this).closest('tr').find('.qty');
    var discountField = $($this).closest('tr').find('.discount');
    var unitVatRateField = $($this).closest('tr').find('.unit_vat_rate');

    var unitPrice = __read_number(unitPriceField);
    var qty = __read_number(qtyField);
    var discount = __read_number(discountField);
    var unitVatRate = __read_number(unitVatRateField);

    var discountedUnitPrice = unitPrice - discount;

    var tax_rate = __read_number($("#tax_rate")) || 0; // Default tax rate to 0 if not found
    var unit_vat = ((discountedUnitPrice * tax_rate) / 100).toFixed(6); // Calculate unit VAT
    console.log('unit_vat',unit_vat);
    var unit_vat_tax = (unitPrice * tax_rate) / 100;  
    var vat = parseFloat(unit_vat) * qty; // Calculate total VAT for the line item
    
    var subTotal = (discountedUnitPrice + parseFloat(unit_vat)) * qty; // Calculate subTotal using the provided formula

    __write_number($($this).closest('tr').find('.sub_total'), subTotal);
    $($this).closest('tr').find('.sub_total_unformatted').val(subTotal);

    __write_number($($this).closest('tr').find('.tax'), vat);
    $($this).closest('tr').find('.tax_unformatted').val(vat);

    unitVatRateField.val(unit_vat);

    calculateGrandTotals();
}



        
    
        
        $(document).on('change', '.product_id', function () {
            var product_id = $(this).val();
            var unitPriceField = $(this).closest('tr').find('.unit_price');
            var unitPriceField_unformatted = $(this).closest('tr').find('.unit_price_unformatted');
            var unitPriceFieldExcl = $(this).closest('tr').find('.unit_price_excl');
            var unitPriceFieldExcl_unformatted = $(this).closest('tr').find('.unit_price_excl_unformatted');
            $.ajax({
                url: '/petro/issue-customer-bill/get-product-price/' + product_id,
                type: 'GET',
                success: function (data) {
                    // Populate the unit price field with the result
                    __write_number(unitPriceField,data.unit_price);
                    unitPriceField_unformatted.val(data.unit_price);
                    console.log('data.unit_price_excl',data.unit_price_excl);
                    unitPriceFieldExcl.val(data.unit_price_excl);
                    unitPriceFieldExcl_unformatted.val(data.unit_price_excl);

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
            var grandTotalWithVat = 0
            var price_adjustment= __read_number($("#price_adjustment"));
        
            $('#issue_customer_bill_add_table tbody tr').each(function () {
                var tax = __read_number($(this).find('.tax_unformatted'));
                var subTotal = __read_number($(this).find('.sub_total_unformatted'));
        
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