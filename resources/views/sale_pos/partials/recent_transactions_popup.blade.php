<div class="modal-dialog" role="document">
	
<div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> 
        @lang('lang_v1.recent_transactions')
    </h4>
</div>
<div class="modal-body min-height-50hv">
    <div class="row">
        <div class="col-md-12">
            @include('sale_pos.partials.recent_transactions_box')
            {{-- @if(!empty($transactions))
                <table class="table table-slim no-border">
                    @foreach ($transactions as $transaction)
                        <tr class="cursor-pointer" 
                            data-toggle="tooltip"
                            data-html="true"
                            title="Customer: {{optional($transaction->contact)->name}} 
                                @if(!empty($transaction->contact->mobile) && $transaction->contact->is_default == 0)
                                    <br/>Mobile: {{$transaction->contact->mobile}}
                                @endif
                            " >
                            <td>
                                {{ $loop->iteration}}.
                            </td>
                            <td>
                                {{ $transaction->invoice_no }} ({{optional($transaction->contact)->name}})
                            </td>
                            <td class="display_currency">
                                {{ $transaction->final_total }}
                            </td>
                            <td>
                                <a href="{{action('SellPosController@printInvoice', [$transaction->id])}}" class="print-invoice-link">
                                    <i class="fa fa-print text-muted" aria-hidden="true" title="{{__('lang_v1.click_to_print')}}"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>@lang('sale.no_recent_transactions')</p>
            @endif --}}
        </div>
    </div>
</div>   
</div>     