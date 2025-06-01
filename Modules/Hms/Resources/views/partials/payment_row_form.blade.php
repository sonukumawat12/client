<div class="row">
    <input type="hidden" class="payment_row_index" value="{{ $row_index}}">
    @php
    $col_class = 'col-md-2';
    $col_class2 = 'col-md-4';
    $readonly = $payment_line['method'] == 'advance' ? true : false;

    $business_id = session()->get('user.business_id');
    $business_details = App\Business::find($business_id);
    $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
    : 2;
    @endphp
    {!! Form::hidden('readonly', $readonly, ['id' => 'readonly']) !!}
    <div class="{{$col_class}}">
        <div class="form-group">
            {!! Form::label("amount_$row_index" ,__('sale.amount') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::text("payment[$row_index][amount]", number_format($payment_line['amount'], $currency_precision), ['class' => 'form-control payment-amount input_number', 'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount'), 'readonly' => $readonly]); !!}
            </div>
        </div>
    </div>
    <div class="payment_row" data-row-id="{{ $row_index }}">
        <div class="{{$col_class2}}">

            <div class="form-group">

                {!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}

                <div class="input-group">

                    <span class="input-group-addon">

                        <i class="fa fa-money"></i>

                    </span>
                    @php
                    $_payment_method = empty($payment_line['method']) && array_key_exists('cash', $payment_types) ? 'cash' : $payment_line['method'];
                    @endphp
                    {!! Form::select("payment[$row_index][method]", $payment_types, $_payment_method, ['class' => 'form-control col-md-12 payment_types_dropdown', 'required', 'id' => !$readonly ? "method_$row_index" : "method_advance_$row_index", 'style' => 'width:100%;', 'disabled' => $readonly, 'data-row-index' => $row_index]); !!}

                    @if($readonly)
                    {!! Form::hidden("payment[$row_index][method]", $payment_line['method'], ['class' => 'payment_types_dropdown', 'required', 'id' => "method_$row_index"]); !!}
                    @endif
                </div>

            </div>

        </div>

        <div class="{{$col_class2}}  account_module">

            <div class="form-group" id="select_account_div">

                {!! Form::label("account_$row_index" , __('lang_v1.payment_account') . ':') !!}

                <div class="input-group">

                    <span class="input-group-addon">

                        <i class="fa fa-money"></i>

                    </span>


                    {!! Form::select("payment[$row_index][account_id]", $accounts, !empty($payment_line['account_id']) ? $payment_line['account_id'] : '' , ['class' => 'form-control select2 account-dropdown', 'id' => !$readonly ? "account_$row_index" : "account_advance_$row_index", 'style' => 'width:100%;', 'disabled' => $readonly]); !!}

                </div>

            </div>

        </div>
    </div>

    <!-- New Accounting Module Dropdown -->
    <!-- the form removed by tamim-->

    @php
    $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
    $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
    @endphp

    @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens' && !empty($show_denomination))
    <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="{{json_encode($enable_cash_denomination_for_payment_methods)}}">
    <div class="clearfix"></div>
    <div class="col-md-12 cash_denomination_div @if(!in_array($payment_line['method'], $enable_cash_denomination_for_payment_methods)) hide @endif">
        <hr>
        <strong>@lang( 'lang_v1.cash_denominations' )</strong>
        @if(!empty($pos_settings['cash_denominations']))
        <table class="table table-slim">
            <thead>
                <tr>
                    <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                    <th width="20%">&nbsp;</th>
                    <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                    <th width="20%">&nbsp;</th>
                    <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                @endphp
                @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
                @php
                $count = 0;
                $sub_total = 0;
                if(!empty($payment_line['denominations'])){
                foreach($payment_line['denominations'] as $d) {
                if($d['amount'] == $dnm) {
                $count = $d['total_count'];
                $sub_total = $d['total_count'] * $d['amount'];
                $total += $sub_total;
                }
                }
                }
                @endphp
                <tr>
                    <td class="text-right">{{$dnm}}</td>
                    <td class="text-center">X</td>
                    <td>{!! Form::number("payment[$row_index][denominations][$dnm]", $count, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;' ]); !!}</td>
                    <td class="text-center">=</td>
                    <td class="text-left">
                        <span class="denomination_subtotal">{{number_format($sub_total, $currency_precision)}}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center">@lang('sale.total')</th>
                    <td>
                        <span class="denomination_total">{{number_format($total, $currency_precision)}}</span>
                        <input type="hidden" class="denomination_total_amount" value="{{number_format($total, $currency_precision)}}">
                        <input type="hidden" class="is_strict" value="{{$pos_settings['cash_denomination_strict_check'] ?? ''}}">
                    </td>
                </tr>
            </tfoot>
        </table>
        <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
        @else
        <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
        @endif
    </div>
    <div class="clearfix"></div>
    @endif
    <div class="clearfix"></div>
    @include('hms::partials.payment_type_details')
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
            {!! Form::textarea("payment[$row_index][note]", $payment_line['note'], ['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
        </div>
    </div>
</div>

<style>
    .disabled {
        opacity: 0.5;
        pointer-events: none;
        /* empêche les clics */
    }
</style>

<script>
    $(document).ready(function() {

        $(document).on('change', '.payment_types_dropdown', function() {
            $(this).closest('.payment_row').find('.account_module').removeClass('hide');
            $(this).closest('.payment_row').find('#select_account_div').addClass('disabled');
            var payment_type = $(this).val();
            var to_show = null;
            var cheque_field = null;
            var location_id = @json($location_id);
            var row_id = parseInt($(this).closest('.payment_row').data('rowId'));
            console.log("the row id is: ", row_id);

            $('.payment_details_div').addClass('hide');
            $('.payment_details_div[data-type="' + payment_type + '"]').removeClass('hide');

            $(this)
                .closest('.payment_row')
                .find('.payment_details_div')
                .each(function() {
                    if ($(this).attr('data-type') == 'cheque') {
                        cheque_field = $(this);
                    }
                    if ($(this).attr('data-type') == payment_type) {
                        to_show = $(this);
                    } else {
                        if (!$(this).hasClass('hide')) {
                            // $(this).addClass('hide');
                        }
                    }
                });
            // $('.card_type_div').addClass('hide');
            if (to_show && to_show.hasClass('hide')) {
                to_show.removeClass('hide');
                to_show.find('input').filter(':visible:first').focus();
            }
            if (payment_type == 'bank_transfer') {
                if (cheque_field) {
                    cheque_field.removeClass('hide');
                    cheque_field.find('input').filter(':visible:first').focus();
                }
            }


            if (payment_type == 'bank_transfer' || payment_type == 'direct_bank_deposit') {
                $(this).closest('.payment_row').find('.account_module').removeClass('hide');
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/get-account-group-name-dp',
                    data: {
                        group_name: payment_type,
                        location_id: location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        if (row_id >= 0) {
                            $('#account_' + row_id)
                                .empty()
                                .append(result);
                            $('#account_' + row_id).attr('required', true);
                        } else {
                            $('#account_id').attr('required', true);
                            $('#account_id').empty().append(result);
                        }
                        $('#select_account_div').removeClass('disabled');
                    },
                });
            } else {
                $(this).closest('.payment_row').find('.account_id').attr('required', false);
                // $(this).closest('.payment_row').find('.account_module').addClass('hide');
                $('#account_id').attr('required', false);
            }
            if (payment_type == 'cheque') {
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/get-account-group-name-dp',
                    data: {
                        group_name: payment_type,
                        location_id: location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        if (row_id >= 0) {
                            $('#account_' + row_id)
                                .empty()
                                .append(result);
                            check_insufficient_balance_row(row_id);
                        } else {
                            $('#account_id').empty().append(result);
                            $('#account_id')
                                .val($('#account_id option:contains("Cheques in Hand")').val())
                                .trigger('change');
                        }
                        $('#select_account_div').removeClass('disabled');
                    },
                });
            }
            if (payment_type == 'cash') {
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/get-account-group-name-dp',
                    data: {
                        group_name: payment_type,
                        location_id: location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        if (row_id >= 0) {
                            $('#account_' + row_id)
                                .empty()
                                .append(result);
                            check_insufficient_balance_row(row_id);
                        } else {
                            $('#account_id').empty().append(result);
                            $('#account_id')
                                .val($('#account_id option:contains("Cash")').val())
                                .trigger('change');
                        }
                        $('#select_account_div').removeClass('disabled');
                    },
                });
            }
            if (payment_type == 'card') {
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/get-account-group-name-dp',
                    data: {
                        group_name: payment_type,
                        location_id: location_id
                    },
                    contentType: 'html',
                    success: function(result) {
                        if (row_id >= 0) {
                            $('#account_' + row_id)
                                .empty()
                                .append(result);
                            check_insufficient_balance_row(row_id);
                        } else {
                            $('#account_id').empty().append(result);
                            $('#account_id')
                                .val(
                                    $(
                                        '#account_id option:contains("Cards (Credit Debit)  Account")'
                                    ).val()
                                )
                                .trigger('change');
                        }
                        $('#select_account_div').removeClass('disabled');
                    },
                });
            }


            edit_cheque_date = $(this)
                .closest('.payment_row')
                .find('.payment_details_div')
                .find('#payment_edit_cheque')
                .val();
            if (edit_cheque_date) {
                $(this)
                    .closest('.payment_row')
                    .find('.payment_details_div')
                    .find('.cheque_date')
                    .datepicker('setDate', edit_cheque_date);
            } else {
                $(this)
                    .closest('.payment_row')
                    .find('.payment_details_div')
                    .find('.cheque_date')
                    .datepicker('setDate', new Date());
            }
        });
    });
</script>