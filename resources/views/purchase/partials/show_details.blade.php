@php
    $business_id = request()->session()->get('user.business_id');
    $enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
@endphp
<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details')
        (<b>@lang('purchase.add_purchase_number'):</b> {{ $purchase->invoice_no }})
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        @php
            $deleted_by = null;
            if (!empty($transaction->deleted_by)) {
                $deletedBy = \App\User::find($transaction->deleted_by);
            }

        @endphp
        @if (!empty($transaction->deleted_by))
            <div class="alert alert-danger text-center">@lang('sale.deleted_by') : @if (!empty($deletedBy))
                    {{ $deletedBy->username }}
                @endif
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
        </div>
    </div>
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            @lang('purchase.supplier'):
            <address>
                <strong>{{ $purchase->contact->supplier_business_name }}</strong>
                {{ $purchase->contact->name }}
                @if (!empty($purchase->contact->landmark))
                    <br>{{ $purchase->contact->landmark }}
                @endif
                @if (!empty($purchase->contact->city) || !empty($purchase->contact->state) || !empty($purchase->contact->country))
                    <br>{{ implode(',', array_filter([$purchase->contact->city, $purchase->contact->state, $purchase->contact->country])) }}
                @endif
                @if (!empty($purchase->contact->tax_number))
                    <br>@lang('contact.tax_no'): {{ $purchase->contact->tax_number }}
                @endif
                @if (!empty($purchase->contact->mobile))
                    <br>@lang('contact.mobile'): {{ $purchase->contact->mobile }}
                @endif
                @if (!empty($purchase->contact->email))
                    <br>Email: {{ $purchase->contact->email }}
                @endif
            </address>
            @if ($purchase->document_path)
                <a href="{{ $purchase->document_path }}" download="{{ $purchase->document_name }}"
                    class="btn btn-sm btn-success pull-left no-print">
                    <i class="fa fa-download"></i>
                    &nbsp;{{ __('purchase.download_document') }}
                </a>
            @endif
        </div>

        <div class="col-sm-4 invoice-col">
            @lang('business.business'):
            <address>
                <strong>{{ $purchase->business->name }}</strong>
                {{ $purchase->location->name }}
                @if (!empty($purchase->location->landmark))
                    <br>{{ $purchase->location->landmark }}
                @endif
                @if (!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
                    <br>{{ implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country])) }}
                @endif

                @if (!empty($purchase->business->tax_number_1))
                    <br>{{ $purchase->business->tax_label_1 }}: {{ $purchase->business->tax_number_1 }}
                @endif

                @if (!empty($purchase->business->tax_number_2))
                    <br>{{ $purchase->business->tax_label_2 }}: {{ $purchase->business->tax_number_2 }}
                @endif

                @if (!empty($purchase->location->mobile))
                    <br>@lang('contact.mobile'): {{ $purchase->location->mobile }}
                @endif
                @if (!empty($purchase->location->email))
                    <br>@lang('business.email'): {{ $purchase->location->email }}
                @endif
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            <b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br />
            <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br />
            {{-- start --}}
            <b>@lang('purchase.invoice_date'):</b> {{ @format_date($purchase->invoice_date) }}<br />
            {{-- end --}}
            <b>@lang('purchase.purchase_status'):</b> {{ ucfirst($purchase->status) }}<br>
            <b>@lang('purchase.payment_status'):</b> {{ ucfirst($purchase->payment_status) }}<br>
            @if (!empty($transaction->overpayment_setoff) && $transaction->overpayment_setoff == 1)
                <span class="badge bg-danger">@lang('lang_v1.overpayment_setoff')</span>
            @endif
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
                                <th>@lang('purchase.free_qty')</th>
                            @endif
                            <th>@lang('lang_v1.unit_cost_before_discount')</th>
                            <th>@lang('lang_v1.discount_percent')</th>
                            <th class="no-print">@lang('purchase.unit_cost_before_tax')</th>
                            <th class="no-print">@lang('purchase.subtotal_before_tax')</th>
                            <th>@lang('sale.tax')</th>
                            <th>@lang('purchase.unit_cost_after_tax')</th>
                            <th>@lang('purchase.unit_selling_price')</th>
                            @if (session('business.enable_lot_number'))
                                <th>@lang('lang_v1.lot_number')</th>
                            @endif
                            @if (session('business.enable_product_expiry'))
                                <th>@lang('product.mfg_date')</th>
                                <th>@lang('product.exp_date')</th>
                            @endif
                            <th>@lang('sale.subtotal')</th>
                        </tr>
                    </thead>
                    @php
                        $total_before_tax = 0.0;
                        $vat = 0.0;
                    @endphp
                    @foreach ($purchase->purchase_lines as $purchase_line)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if (!empty($purchase_line->product->name))
                                    {{ $purchase_line->product->name }}
                                @endif
                                @if ($purchase_line->product->type == 'variable')
                                    @if (!empty($purchase_line->variations->product_variation->name))
                                        -
                                        {{ $purchase_line->variations->product_variation->name }}
                                    @endif
                                    @if (!empty($purchase_line->variations->name))
                                        - {{ $purchase_line->variations->name }}
                                    @endif
                                @endif
                            </td>
                            <td><span class="display_currency" data-is_quantity="true"
                                    data-currency_symbol="false">{{ $purchase_line->quantity - $purchase_line->bonus_qty }}</span>
                                @if (!empty($purchase_line->sub_unit))
                                    {{ $purchase_line->sub_unit->short_name }}
                                @else
                                    {{ $purchase_line->product->unit->short_name }}
                                @endif
                            </td>
                            @if ($enable_free_qty)
                                <td><span class="display_currency" data-is_quantity="true"
                                        data-currency_symbol="false">{{ $purchase_line->bonus_qty }}</span>
                                    @if (!empty($purchase_line->sub_unit))
                                        {{ $purchase_line->sub_unit->short_name }}
                                    @else
                                        {{ $purchase_line->product->unit->short_name }}
                                    @endif
                                </td>
                            @endif
                            <td><span class="display_currency"
                                    data-currency_symbol="true">{{ $purchase_line->pp_without_discount != 0 ? number_format($purchase_line->pp_without_discount, 2) : '0' }}</span>
                            </td>
                            <td><span
                                    class="display_currency">{{ $purchase_line->discount_percent != 0 ? number_format($purchase_line->discount_percent, 2) : '0' }}</span>
                                % </td>
                            <td class="no-print"><span class="display_currency"
                                    data-currency_symbol="true">{{ $purchase_line->purchase_price != 0 ? number_format($purchase_line->purchase_price, 2) : '0' }}</span>
                            </td>
                            <td class="no-print">
                                @php
                                    $subtotal_bf = $purchase_line->quantity * $purchase_line->purchase_price;
                                @endphp
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $subtotal_bf != 0 ? number_format($subtotal_bf, 2) : '0' }}</span>
                            </td>
                            <td><span class="display_currency"
                                    data-currency_symbol="true">{{ $purchase_line->item_tax != 0 ? number_format($purchase_line->item_tax, 2) : '0' }}
                                </span>
                                <br /><small>
                                    @if (!empty($taxes[$purchase_line->tax_id]))
                                        ( {{ $taxes[$purchase_line->tax_id] }} )
                                    @endif
                                </small>
                            </td>
                            <td><span class="display_currency"
                                    data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax != 0 ? number_format($purchase_line->purchase_price_inc_tax, 2) : '0' }}</span>
                            </td>
                            @php
                                $sp = $purchase_line->variations->default_sell_price;
                                if (!empty($purchase_line->sub_unit->base_unit_multiplier)) {
                                    $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
                                }
                            @endphp
                            <td><span class="display_currency" data-currency_symbol="true">{{ $sp }}</span>
                            </td>

                            @if (session('business.enable_lot_number'))
                                <td>{{ $purchase_line->lot_number }}</td>
                            @endif

                            @if (session('business.enable_product_expiry'))
                                <td>
                                    @if (!empty($purchase_line->product->expiry_period_type))
                                        @if (!empty($purchase_line->mfg_date))
                                            {{ @format_date($purchase_line->mfg_date) }}
                                        @endif
                                    @else
                                        @lang('product.not_applicable')
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($purchase_line->product->expiry_period_type))
                                        @if (!empty($purchase_line->exp_date))
                                            {{ @format_date($purchase_line->exp_date) }}
                                        @endif
                                    @else
                                        @lang('product.not_applicable')
                                    @endif
                                </td>
                            @endif
                            <td>
                                @php
                                    $subtotal = $purchase_line->purchase_price_inc_tax * $purchase_line->quantity;
                                @endphp
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $subtotal != 0 ? number_format($subtotal, 2) : '0' }}</span>
                            </td>
                        </tr>
                        @php
                            $total_before_tax += $purchase_line->quantity * $purchase_line->purchase_price;
                            $vat += $purchase_line->item_tax;
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
                    @forelse($purchase->payment_lines as $payment_line)
                        @php
                            $total_paid += $payment_line->amount;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ @format_date($payment_line->paid_on) }}</td>
                            <td>{{ $payment_line->payment_ref_no }}</td>
                            <td><span class="display_currency"
                                    data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                            <td>
                                @if ($payment_line->method == 'cheque')
                                    @lang('lang_v1.bank_name'):
                                    <b>{{ $payment_line->bank_name }}</b><br> @lang('lang_v1.cheque_number'):
                                    <b>{{ $payment_line->cheque_number }}</b>
                                @else
                                    {{ !empty($payment_methods[$payment_line->method]) ? $payment_methods[$payment_line->method] : '' }}
                                @endif
                            </td>
                            <td>
                                @if ($payment_line->note)
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
                    <tr>
                        <th>@lang('purchase.total_before_tax'): </th>
                        <td></td>
                        <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
                    </tr>
                    <tr>
                        <th>@lang('purchase.price_adjustment'): </th>
                        <td></td>
                        <td><span class="display_currency pull-right">{{ $purchase->price_adjustment }}</span></td>
                    </tr>
                    {{-- <tr>
                        <th>@lang('purchase.net_total_amount'): </th>
                        <td></td>
                        <td><span class="display_currency pull-right"
                                data-currency_symbol="true">{{ $total_before_tax }}</span>
                        </td>
                    </tr> --}}
                    <tr>
                        <th>@lang('purchase.discount'):</th>
                        <td>
                            <b>(-)</b>
                            @if ($purchase->discount_type == 'percentage')
                                ({{ $purchase->discount_amount }} %)
                            @endif
                        </td>
                        <td>
                            <span class="display_currency pull-right" data-currency_symbol="true">
                                @if ($purchase->discount_type == 'percentage')
                                    @php
                                        $discount = ($purchase->discount_amount * $total_before_tax) / 100;
                                    @endphp
                                    {{ $discount }}
                                @else
                                    @php
                                        $discount = $purchase->discount_amount;
                                    @endphp
                                    {{ $discount }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('purchase.purchase_tax'):</th>
                        <td><b>(+)</b></td>
                        <td class="text-right">
                            @if (!empty($purchase_taxes))
                                @foreach ($purchase_taxes as $k => $v)
                                    <strong><small>{{ $k }}</small></strong> &nbsp; <span
                                        class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $vat }}</span><br>
                                @endforeach
                            @else
                                0.00
                            @endif
                        </td>
                    </tr>
                    @if (!empty($purchase->shipping_charges))
                        <tr>
                            <th>@lang('purchase.additional_shipping_charges'):</th>
                            <td><b>(+)</b></td>
                            <td><span class="display_currency pull-right">{{ $purchase->shipping_charges }}</span></td>
                        </tr>
                    @endif
                    <tr>
                        <th>@lang('purchase.purchase_total'):</th>
                        <td></td>
                        <td><span class="display_currency pull-right dddd"
                                data-currency_symbol="true">{{ @num_format($purchase->final_total - $discount) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <strong>@lang('purchase.shipping_details'):</strong><br>
            <p class="well well-sm no-shadow bg-gray">
                {{ $purchase->shipping_details ?? '' }}

                @if (!empty($purchase->shipping_custom_field_1))
                    <br><strong>{{ $custom_labels['purchase_shipping']['custom_field_1'] ?? '' }}: </strong>
                    {{ $purchase->shipping_custom_field_1 }}
                @endif
                @if (!empty($purchase->shipping_custom_field_2))
                    <br><strong>{{ $custom_labels['purchase_shipping']['custom_field_2'] ?? '' }}: </strong>
                    {{ $purchase->shipping_custom_field_2 }}
                @endif
                @if (!empty($purchase->shipping_custom_field_3))
                    <br><strong>{{ $custom_labels['purchase_shipping']['custom_field_3'] ?? '' }}: </strong>
                    {{ $purchase->shipping_custom_field_3 }}
                @endif
                @if (!empty($purchase->shipping_custom_field_4))
                    <br><strong>{{ $custom_labels['purchase_shipping']['custom_field_4'] ?? '' }}: </strong>
                    {{ $purchase->shipping_custom_field_4 }}
                @endif
                @if (!empty($purchase->shipping_custom_field_5))
                    <br><strong>{{ $custom_labels['purchase_shipping']['custom_field_5'] ?? '' }}: </strong>
                    {{ $purchase->shipping_custom_field_5 }}
                @endif
            </p>
        </div>
        <div class="col-sm-6">
            <strong>@lang('purchase.additional_notes'):</strong><br>
            <p class="well well-sm no-shadow bg-gray">
                @if ($purchase->additional_notes)
                    {{ $purchase->additional_notes }}
                @else
                    --
                @endif
            </p>
        </div>
    </div>

    {{-- Barcode --}}
    <div class="row print_section">
        <div class="col-xs-12">
            <img class="center-block"
                src="data:image/png;base64,{{ DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2, 30, [39, 48, 54], true) }}">
        </div>
    </div>
</div>


@foreach ($purchase->purchase_lines as $purchase_line)
    @if (!empty($purchase_line->product->category->name))
        @if ($purchase_line->product->category->name == 'Fuel')
            <script>
                $(document).ready(function() {
                    $.ajax({
                        method: 'get',
                        url: '/purchases/get_edit_unload_tank_row',
                        data: {
                            product_id: {{ $purchase_line->product_id }},
                            transaction_id: {{ $purchase_line->transaction_id }},
                            location_id: {{ !empty($purchase->location) ? $purchase->location->id : '0' }},
                            is_view: true,
                        },
                        success: function(data) {
                            if (data) {
                                $('.unload_div').removeClass('hide');
                                $('.unload_tank').append(data);
                            }
                        },
                    });
                });
            </script>
        @endif
    @endif
@endforeach
