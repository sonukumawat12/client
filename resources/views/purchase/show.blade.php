<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

        @if ($isPaymentMethod)
            @include('purchase.partials.show_payment_details')
            <div class="modal-footer">
                <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        @elseif ($isInvoiceDate)
            @include('purchase.partials.show_invoice_date')
            <div class="modal-footer">
                <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        @else
            @include('purchase.partials.show_details')
            <div class="modal-footer">
                @can('purchase.update')
                    @if (empty($purchase->deleted_by))
                        <a href="{{ action('PurchaseController@edit', [$purchase->id]) }}" class="btn btn-danger no-print"
                            aria-label="Edit"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')
                        </a>
                    @endif
                @endcan
                <button type="button" class="btn btn-primary no-print" aria-label="Print"
                    onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i>
                    @lang('messages.print')
                </button>
                <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>
