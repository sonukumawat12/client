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
            <b>@lang('purchase.Supplier_Invoice_Number'):</b> #{{ $purchase->invoice_no }}<br /> 
            <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br />
            {{-- start --}}
            <b>@lang('purchase.invoice_date'):</b> {{ @format_date($purchase->invoice_date) }}<br />
            {{-- end --}}
            <b>@lang('purchase.purchase_status'):</b> {{ ucfirst($purchase->status) }}<br>
            <b>@lang('purchase.payment_status'):</b> {{ ucfirst($purchase->payment_status) }}<br>
            <b>@lang('purchase.Payment_Reference_Number'):</b> #{{ $purchase->ref_no }}<br />
            <b>@lang('purchase.payment_method'):</b> 
            @if (
                $transaction->payment_status == 'due' ||
                    (empty($transactionPayment->method) && $transaction->payment_status == 'paid'))
                {{ 'Credit Purchase' }}
            @elseif (strtolower($transactionPayment->method) == 'bank_transfer' ||
                    strtolower($transactionPayment->method) == 'direct_bank_deposit' ||
                    strtolower($transactionPayment->method) == 'bank' ||
                    strtolower($transactionPayment->method) == 'cheque')
                {{ ucfirst(str_replace('_', ' ', $transactionPayment->method)) }}
            @else
                {{ ucfirst(str_replace('_', ' ', $transactionPayment->method)) }}
            @endif
            <br />
            @if (!empty($transaction->overpayment_setoff) && $transaction->overpayment_setoff == 1)
                <span class="badge bg-danger">@lang('lang_v1.overpayment_setoff')</span>
            @endif
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
        <div class="col-sm-4">
            <strong>@lang('purchase.payment_method_details'):</strong><br>
            <p class="well well-sm no-shadow bg-gray">

                @if (
                    $transaction->payment_status == 'due' ||
                        (empty($transactionPayment->method) && $transaction->payment_status == 'paid'))
                    <b>{{ 'Credit Purchase' }}</b>
                @elseif (strtolower($transactionPayment->method) == 'bank_transfer' ||
                        strtolower($transactionPayment->method) == 'direct_bank_deposit' ||
                        strtolower($transactionPayment->method) == 'bank' ||
                        strtolower($transactionPayment->method) == 'cheque')
                    <b>{{ ucfirst(str_replace('_', ' ', $transactionPayment->method)) }}</b>

                    @if ($transactionPayment->payment_account)
                        <br><b>Bank Name:</b> {{ $transactionPayment->payment_account->name }}
                    @endif

                    @if ($transactionPayment->cheque_number)
                        <br><b>Cheque Number:</b> {{ $transactionPayment->cheque_number }}
                    @endif
                    @if ($transactionPayment->cheque_date)
                        <br><b>Cheque Date:</b> {{ $transactionPayment->cheque_date }}
                    @endif
                @else
                    <b>{{ ucfirst(str_replace('_', ' ', $transactionPayment->method)) }}</b>
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
