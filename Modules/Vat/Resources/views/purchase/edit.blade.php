@extends('layouts.app')
@section('title', __('purchase.edit_purchase'))

@section('content')



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('purchase.edit_purchase') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom"
                  data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover"
                  data-original-title="" title=""></i></h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Purchases</a></li>
                    <li><span>@lang('purchase.edit_purchase')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">

  <!-- Page level currency setting -->
  <input type="hidden" id="p_code" value="{{$currency_details->code}}">
  <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
  <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
  <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

  @include('layouts.partials.error')

  {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatPurchaseController@update' , [$purchase->id] ), 'method' => 'PUT', 'id' =>
  'add_purchase_form', 'files' => true ]) !!}

  @php
  $currency_precision = config('constants.currency_precision', 2);
  @endphp

  <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">

  @component('components.widget', ['class' => 'box-primary'])
  <div class="row">
    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('purchase_no', __('purchase.purchase_no').':') !!}
        {!! Form::text('purchase_no', !empty($purchase->purchase_no) ? $purchase->invoice_no : 1, ['class' =>
        'form-control'] ); !!}
      </div>
    </div>

    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
        {!! Form::select('supplier',$suppliers, $purchase->supplier_id,
          ['class' => 'form-control', 'placeholder' => __('messages.please_select') , 'required']); !!}
      </div>
    </div>

    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('invoice_no', __('purchase.ref_no') . '*') !!}
        {!! Form::text('invoice_no', $purchase->invoice_no, ['class' => 'form-control', 'required']); !!}
      </div>
    </div>

    
    
    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('invoice_date', __('purchase.invoice_date') . ':*') !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {!! Form::date('invoice_date', date('Y-m-d',strtotime($purchase->invoice_date)), ['class' => 'form-control', 'required']); !!}
        </div>
      </div>
    </div>

    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
        @show_tooltip(__('tooltip.order_status'))
        {!! Form::select('purchase_status', $orderStatuses, $purchase->purchase_status, ['class' => 'form-control select2', 'placeholder'
        => __('messages.please_select') , 'required']); !!}
      </div>
    </div>
    
    <div class="col-sm-3">
		<div class="form-group">
			{!! Form::label('vat_invoice', __('lang_v1.is_vat')) !!}
			{!! Form::select('vat_invoice', ['0' => __('lang_v1.no'),'1' => __('lang_v1.yes')],$purchase->vat_invoice, ['class' => 'form-control
			select2', 'required']); !!}
		</div>
	</div>
  </div>
  @endcomponent

  @component('components.widget', ['class' => 'box-primary'])
  <div class="row">
    <div class="col-sm-8 col-sm-offset-2">
      <div class="form-group">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-search"></i>
          </span>
          {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product',
          'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
        </div>
      </div>
    </div>
    
  </div>

  <div class="row">
    <div class="col-sm-12">
      @include('vat::purchase.partials.edit_purchase_entry_row')

      <hr />
      <div class="pull-right col-md-5">
        <table class="pull-right col-md-12">
          <tr class="hide">
            <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
            <td class="col-md-5 text-left">
              <span id="total_st_before_tax" class="display_currency"></span>
              <input type="hidden" id="st_before_tax_input" value=0>
            </td>
          </tr>
          <tr>
            <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
            <td class="col-md-5 text-left">
              <span id="total_subtotal"
                class="display_currency">{{$purchase->sub_total}}</span>
              <!-- This is total before purchase tax-->
              <input type="hidden" id="total_subtotal_input"
                value="{{$purchase->sub_total}}" name="sub_total">
            </td>
          </tr>
        </table>
      </div>

    </div>
  </div>
  @endcomponent

  @component('components.widget', ['class' => 'box-primary'])
  <div class="row">
    <div class="col-sm-12">
      <table class="table">
        <tr>
            
          <td class="col-md-3">
              <div class="form-group">
				{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
			</div>
          </td>
          <td class="col-md-3">
            
          </td>
          <td class="col-md-3">
            &nbsp;
          </td>
          <td class="col-md-3">
            <b>Discount:</b>(-)
            <span id="discount_calculated_amount" class="display_currency">0</span>
            {!! Form::hidden('discount_amount',$purchase->discount_amount , ['class' => 'form-control input_number']); !!}
          </td>
        </tr>
        <tr>
          <td>
            <div class="form-group">
              {!! Form::label('tax_id', __( 'purchase.purchase_tax' ) . ':') !!}
              
            </div>
          </td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>
            <b>@lang( 'purchase.purchase_tax' ):</b>(+)
            <span id="tax_calculated_amount" class="display_currency">0</span>
            {!! Form::hidden('vat_amount', $purchase->vat_amount, ['id' => 'tax_amount']); !!}
          </td>
        </tr>

        
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>
            {!! Form::hidden('total_amount', $purchase->total_amount , ['id' => 'grand_total_hidden']); !!}
            <b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency"
              data-currency_symbol='true'>{{$purchase->total_amount}}</span>
          </td>
        </tr>
        
      </table>
    </div>
  </div>
  @endcomponent

  

  @component('components.widget', ['class' => 'box-primary'])
  <div class="box-body payment_row"  data-row_id="0">
            @if(!empty($is_admin))
                @if (!empty($payments))
                    @foreach($payments as $index => $one)
                        @include('sale_pos.partials.payment_row_form', ['row_index' => $index, 'payment' => $one])
                    @endforeach
    			@else
        			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
    			@endif
    		@endif
    <hr>
  
    <div class="row">
      <div class="col-sm-12">
        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-6">
        <a id="" href="{{url('purchases')}}" class="btn btn-danger pull-left btn-flat">@lang('lang_v1.back')</a>
      </div>
      <div class="col-sm-6">
        <button type="button" id="submit_purchase_form"
          class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
      </div>
    </div>
  </div>
  @endcomponent
  <input type="hidden" name="cash_account_id" id="cash_account_id" value="{{$cash_account_id}}">
  <input type="hidden" name="is_edit" id="is_edit" value="1">
  {!! Form::close() !!}
</section>
<!-- @eng START 15/2 -->
<style>
  .swal-title {
    color: red;
  }
</style>
<!-- @eng END 15/2 -->
<!-- /.content -->
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  @include('contact.create', ['quick_add' => true])
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
  $(document).ready( function(){
      update_table_total();
      update_grand_total();
      $('#method_0').trigger('change');
    });
</script>
@include('purchase.partials.keyboard_shortcuts')



<script>
$(document).ready(function(){
    
    function get_purchase_entry_row(product_id, variation_id) {
    if (product_id) {
        let duplicate_found = false;
        $('#purchase_entry_table tbody')
            .find('tr')
            .each(function () {
                if (
                    parseInt($(this).find('.hidden_variation_id').val()) === parseInt(variation_id)
                ) {
                    duplicate_found = true;
                }
            });
        if (duplicate_found) {
            return;
        }
        var row_count = $('#row_count').val();
        
        var purchase_pos = 0;
        if ($('#purchase_pos').length) {
            purchase_pos = $('#purchase_pos').val();
        }
        $.ajax({
            method: 'POST',
            url: '/vat-module/vat-purchases/get_purchase_entry_row',
            dataType: 'html',
            data: {
                product_id: product_id,
                row_count: row_count,
                variation_id: variation_id,
                purchase_pos: purchase_pos,
                location_id : $('#location_id').val(),
            },
            success: function (result) {
                $(result)
                    .find('.purchase_quantity')
                    .each(function () {
                        row = $(this).closest('tr');

                        $('#purchase_entry_table tbody').append(
                            update_purchase_entry_row_values(row)
                        );
                        update_row_price_for_exchange_rate(row);

                        update_inline_profit_percentage(row);

                        update_table_total();
                        update_grand_total();
                        update_table_sr_number();

                        //Check if multipler is present then multiply it when a new row is added.
                        if (__getUnitMultiplier(row) > 1) {
                            row.find('select.sub_unit').trigger('change');
                        }
                    });
                if ($(result).find('.purchase_quantity').length) {
                    $('#row_count').val(
                        $(result).find('.purchase_quantity').length + parseInt(row_count)
                    );
                }

            },
        });
    }
}
    
    
    //  $('#search_products').prop('disabled', true);

    // When the supplier list box is selected
    $('#contact_id').on('change', function() {
      var supplierId = $(this).val();

      // Enable or disable the input based on the supplier selection
      if (supplierId !== '') {
        $('#search_products').prop('disabled', false);
      } else {
        $('#search_products').prop('disabled', true);
      }
    });

  $(document).on('click', '#search_products', function () {
        
    if ($('#search_products').length >0) {
         var selectedSupplierId = $('#contact_id').val();
        
        // Check if the value is empty or not
        if ($.trim(selectedSupplierId).length === 0) {
          selectedSupplierId=0;
        } 
        
      
        $('#search_products')
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(
                        '/vat-module/vat-product-search',
                        { location_id: $('#location_id').val(), term: request.term ,supplier_id: $('#contact_id').val()},
                        response
                    );
                },
                minLength: 0,
                response: function (event, ui) { 
                },
                select: function (event, ui) {
                    $(this).val(null);
                    console.log(ui.item);
                    get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
                console.log(item);
            return $('<li>')
                .append('<div>' + item.name + '</div>')
                .appendTo(ul);
        };
    }
        
    });
});
</script>
@endsection