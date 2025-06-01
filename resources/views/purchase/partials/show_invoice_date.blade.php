<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details')
        (<b>@lang('purchase.add_purchase_number'):</b> {{ $purchase->invoice_no }})
    </h4>
</div>
<div class="modal-body">


    <div class="row">
        <div class="col-sm-6">
            <strong>@lang('purchase.invoice_date_details'):</strong><br>
            <p class="well well-sm no-shadow bg-gray">

                {{ $invoiceDate }}
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
