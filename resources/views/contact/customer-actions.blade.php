<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
            data-toggle="dropdown" aria-expanded="false">
        {{ __("messages.actions") }}
        <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-left" role="menu">
        @if($total_due > 0)
            <li><a href="{{action('TransactionPaymentController@getPayContactDue', [$id])}}?type=sell" class="pay_sale_due"><i class="fa fa-credit-card" aria-hidden="true"></i> @lang("contact.pay_due_amount")</a></li>
        @endif
        @if(($total_sell_return - $sell_return_paid)  > 0 && $total_due < 0)
            <li><a href="{{action('TransactionPaymentController@getPayContactDue', [$id])}}?type=sell_return" class="pay_purchase_due"><i class="fa fa-credit-card" aria-hidden="true"></i> @lang("lang_v1.pay_sell_return_due")</a></li>
        @endif
        <li><a href="{{action('TransactionPaymentController@getAdvancePayment', [$id])}}?type=advance_payment" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i> @lang("lang_v1.advance_payment")</a></li>
        
        <li><a href="{{action('TransactionPaymentController@getDirectLoan', [$id])}}" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i> @lang("lang_v1.direct_loan")</a></li>
        
        <li><a href="{{action('TransactionPaymentController@getRefundDeposit', [$id])}}" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i> @lang("contact.refund_deposit")</a></li>
        
        <li><a href="{{action('TransactionPaymentController@getSecurityDeposit', [$id])}}?type=security_deposit" class="pay_purchase_due"><i class="fa fa-shield" aria-hidden="true"></i> @lang("lang_v1.security_deposit")</a></li>
        <li><a href="{{action('TransactionPaymentController@getRefundPayment', [$id])}}?type=refund_payment" class="pay_purchase_due"><i class="fa fa-recycle" aria-hidden="true"></i> @lang("lang_v1.refund_cheque_return")</a></li>
        @can("customer.view")
            <li><a href="{{action('ContactController@show', [$id])}}"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
        @endcan
        @can("customer.update")
            <li><a href="{{action('ContactController@edit', [$id])}}" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</a></li>
            @if($should_notify == 1)
                <li><a href="{{action('ContactController@add_notification_numbers', [$id])}}" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.add_more_numbers")</a></li>
            @endif
        @endcan
        @if(!$is_default)
            @can("customer.delete")
                <li><a href="{{action('ContactController@destroy', [$id])}}" class="delete_contact_button"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
            @endcan
        @endif
        @can("customer.view")
            <li class="divider"></li>
            <li><a href="{{action('ContactController@balanceDetails', [$id])}}" class="edit_contact_button"><i class="fa fa-eye"></i> @lang("contact.balance_details")</a></li>
            <li><a href="{{action('ContactController@show', [$id])."?view=contact_info"}}"><i class="fa fa-user" aria-hidden="true"></i> @lang("contact.contact_info", ["contact" => __("contact.contact") ])</a></li>
            <li><a href="{{action('ContactController@show', [$id])."?view=ledger&type=customer"}}"><i class="fa fa-anchor" aria-hidden="true"></i> @lang("lang_v1.ledger")</a></li>
            @if(in_array($type, ["both", "supplier"]))
                <li><a href="{{action('ContactController@show', [$id])."?view=purchase"}}"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> @lang("purchase.purchases")</a></li>
                <li><a href="{{action('ContactController@show', [$id])."?view=stock_report"}}"><i class="fa fa-hourglass-half" aria-hidden="true"></i> @lang("report.stock_report")</a></li>
            @endif
            @if(in_array($type, ["both", "customer"]))
                <li><a href="{{action('ContactController@show', [$id])."?view=sales"}}"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i> @lang("sale.sells")</a></li>
            @endif
            <li><a href="{{action('ContactController@show', [$id])."?view=references"}}"><i class="fa fa-link" aria-hidden="true"></i> @lang("lang_v1.references")</a></li>
            <li><a href="{{action('ContactController@show', [$id])."?view=documents_and_notes"}}"><i class="fa fa-paperclip" aria-hidden="true"></i> @lang("lang_v1.documents_and_notes")</a></li>
            <li><a href="{{action('ContactController@toggleActivate', [$id])}}">
                    @if($active)
                        <i class="fa fa-times" aria-hidden="true"></i> @lang("lang_v1.deactivate")
                    @else
                        <i class="fa fa-check" aria-hidden="true"></i> @lang("lang_v1.activate")
                    @endif
                </a></li>
        @endcan
    </ul>
</div>

