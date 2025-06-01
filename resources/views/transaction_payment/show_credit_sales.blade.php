<div class="modal-dialog" role="document" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title no-print">
                @lang('lang_v1.credit_sales') & @lang('report.pump_operator_shortage')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped" id="view_credit_sales_table">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('lang_v1.contact_no')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('sale.type')</th>
                                <th>@lang('petro::lang.pump_operator')</th>
                                <th>@lang('sale.total_amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    {{ !empty($transaction->created_at) ? date('Y-m-d', strtotime($transaction->created_at)) : '' }}
                                </td>
                                <td>
                                    {{ $transaction->invoice_no }}
                                </td>
                                <td>
                                    {{ (!is_null($transaction->contact)) ? $transaction->contact->name : '' }}
                                </td>
                                <td>
                                    {{ (!is_null($transaction->contact)) ? $transaction->contact->mobile : '' }}
                                </td>
                                <td>
                                    {{ (!is_null($transaction->business)) ? $transaction->business->name : '' }}
                                </td>
                                <td>
                                    {{ str_replace('_', ' ', $transaction->sub_type) }}
                                </td>
                                <td>
                                    {{ $transaction->name ?? '' }}
                                </td>
                                <td>
                                    <span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{ $transaction->final_total }}">
                                        {{ number_format($transaction->final_total, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close')</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->