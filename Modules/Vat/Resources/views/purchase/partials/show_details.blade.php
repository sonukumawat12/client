@php
$business_id = request()->session()->get('user.business_id');
$enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
@endphp
<div class="modal-header">
  <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
      aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details')
    (<b>@lang('purchase.add_purchase_number'):</b> {{ $purchase->purchase_no }})
  </h4>
</div>
<div class="modal-body">
    
            
  <div class="row">
    <div class="col-sm-12">
      <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->invoice_date) }}</p>
    </div>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      @lang('purchase.supplier'):
      <address>
        <strong>{{ $purchase->name }}</strong>
            {{ $purchase->name }}
        
        @if(!empty($purchase->mobile))
        <br>@lang('contact.mobile'): {{$purchase->mobile}}
        @endif
        
      </address>
     
    </div>

    <div class="col-sm-4 invoice-col">
      <b>@lang('purchase.ref_no'):</b> #{{ $purchase->invoice_no }}<br />
      <b>@lang('messages.date'):</b> {{ @format_date($purchase->invoice_date) }}<br />
      <b>@lang('purchase.purchase_status'):</b> {{ ucfirst( $purchase->status ) }}<br>
      <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $purchase->payment_status ) }}<br>
    </div>
  </div>

  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table bg-gray">
          <thead>
            <tr class="bg-green">
              <th>#</th>
              <th>@lang('product.product_name')</th>
              <th>@lang('purchase.purchase_quantity')</th>
              @if ($enable_free_qty)
              <th>@lang( 'purchase.free_qty' )</th>
              @endif
              <th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
              <th>@lang( 'lang_v1.discount_percent' )</th>
              <th class="no-print">@lang('purchase.unit_cost_before_tax')</th>
              <th class="no-print">@lang('purchase.subtotal_before_tax')</th>
              <th>@lang('sale.tax')</th>
              <th>@lang('purchase.unit_cost_after_tax')</th>
              <th>@lang('purchase.unit_selling_price')</th>
              <th>@lang('sale.subtotal')</th>
            </tr>
          </thead>
          @php
          $total_before_tax = 0.00;
          @endphp
          @foreach($products as $purchase_line)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              @if(!empty($purchase_line->name)) {{ $purchase_line->name }} @endif
            </td>
            <td><span class="display_currency" data-is_quantity="true"
                data-currency_symbol="false">{{ $purchase_line->purchase_qty }}</span>
            </td>
            <td><span class="display_currency" data-is_quantity="true"
                data-currency_symbol="false">{{ $purchase_line->free_qty }}</span>
            </td>
            <td><span class="display_currency"
                data-currency_symbol="true">{{ $purchase_line->unit_before_discount}}</span></td>
            <td><span class="display_currency">{{ $purchase_line->discountt}}</span> %</td>
            <td class="no-print"><span class="display_currency"
                data-currency_symbol="true">{{ $purchase_line->unit_cost }}</span></td>
            <td class="no-print"><span class="display_currency"
                data-currency_symbol="true">{{ $purchase_line->purchase_qty * $purchase_line->unit_cost }}</span></td>
            <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->item_tax }} </span>
              <br /><small>@if(!empty($taxes[$purchase_line->tax_id])) ( {{ $taxes[$purchase_line->tax_id]}} )
              </small>@endif</td>
            <td><span class="display_currency"
                data-currency_symbol="true">{{ $purchase_line->net_cost }}</span></td>
            @php
            $sp = $purchase_line->unit_selling_price;
            @endphp
            <td><span class="display_currency" data-currency_symbol="true">{{$sp}}</span></td>
            
            <td><span class="display_currency"
                data-currency_symbol="true">{{ $purchase_line->line_total }}</span>
            </td>
          </tr>
          @php
          $total_before_tax += ($purchase_line->line_total);
          @endphp
          @endforeach
        </table>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <div class="box-body unload_tank unload_div">
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <h4>{{ __('sale.payment_info') }}:</h4>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table">
          <tr class="bg-green">
            <th>#</th>
            <th>{{ __('messages.date') }}</th>
            <th>{{ __('purchase.ref_no') }}</th>
            <th>{{ __('sale.amount') }}</th>
            <th>{{ __('sale.payment_mode') }}</th>
            <th>{{ __('sale.payment_note') }}</th>
          </tr>
          @php
          $total_paid = 0;
          @endphp
          @forelse($payments as $payment_line)
          @php
          $total_paid += $payment_line->amount;
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ @format_date($payment_line->paid_on) }}</td>
            <td>{{ $payment_line->payment_ref_no }}</td>
            <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
            <td>@if($payment_line->method == 'cheque') @lang('lang_v1.bank_name'):
              <b>{{ $payment_line->bank_name }}</b><br> @lang('lang_v1.cheque_number'):
              <b>{{ $payment_line->cheque_number }}</b> @else {{$payment_methods[$payment_line->method]}} @endif</td>
            <td>@if($payment_line->note)
              {{ ucfirst($payment_line->note) }}
              @else
              --
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">
              @lang('purchase.no_payments')
            </td>
          </tr>
          @endforelse
        </table>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table">
          <!-- <tr class="hide">
            <th>@lang('purchase.total_before_tax'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr> -->
          <tr>
            <th>@lang('purchase.net_total_amount'): </th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total_before_tax }}</span>
            </td>
          </tr>
          <tr>
            <th>@lang('purchase.discount'):</th>
            <td>
              
            </td>
            <td>
              <span class="display_currency pull-right" data-currency_symbol="true">
                {{$purchase->discount_amount}}
              </span>
            </td>
          </tr>
          <tr>
            <th>@lang('purchase.purchase_tax'):</th>
            <td><b>(+)</b></td>
            <td class="text-right">
              <strong><small></small></strong> - <span class="display_currency pull-right"
                data-currency_symbol="true">{{$purchase->vat_amount}}</span><br>
            </td>
          </tr>
          
          <tr>
            <th>@lang('purchase.purchase_total'):</th>
            <td></td>
            <td><span class="display_currency pull-right dddd"
                data-currency_symbol="true">{{ @num_format($purchase->total_amount) }}</span></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  
  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block"
        src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->purchase_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</div>
