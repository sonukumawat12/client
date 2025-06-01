<div class="modal-dialog modal-lg no-print" role="document" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">
                @lang('customer_payments.view_customer_payment') (<b>@lang('customer_payments.payment_ref_no'):</b> {{ $parent_payment->payment_ref_no }})
            </h4>
        </div>
        <div class="modal-body">
            {!! $receipt_data !!}
        </div>
        <div class="modal-footer">
            <a href="#" class="print-invoice btn btn-primary" data-href="{{url('/customer-payment-print', [
                $id
            ])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>
