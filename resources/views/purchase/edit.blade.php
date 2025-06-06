@extends('layouts.app')
@section('title', __('purchase.edit_purchase'))

@section('content')



    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">@lang('purchase.edit_purchase') <i class="fa fa-keyboard-o hover-q text-muted"
                            aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom"
                            data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover"
                            data-original-title="" title=""></i></h4>
                    <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                        <li><a href="#">Purchases</a></li>
                        <li><span>@lang('purchase.edit_purchase')</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content main-content-inner">

        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
        <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
        <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
        <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">

        @include('layouts.partials.error')

        {!! Form::open([
            'url' => action('PurchaseController@update', [$purchase->id]),
            'method' => 'PUT',
            'id' => 'add_purchase_form',
            'files' => true,
        ]) !!}

        @php
            $currency_precision = config('constants.currency_precision', 2);
        @endphp

        <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('purchase_no', __('purchase.purchase_no') . ':') !!}
                        {!! Form::text('invoice_no', !empty($purchase->invoice_no) ? $purchase->invoice_no : 1, [
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>

                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('contact_id', [$purchase->contact_id => $purchase->contact->name], $purchase->contact_id, [
                                'class' => 'form-control',
                                'placeholder' => __('messages.please_select'),
                                'required',
                                'id' => 'supplier_id',
                            ]) !!}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier"
                                    data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no') . '*') !!}
                        {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-sm-3 @if (!empty($default_purchase_status)) hide @endif">
                    <div class="form-group">
                        {!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
                        @show_tooltip(__('tooltip.order_status'))
                        {!! Form::select('status', $orderStatuses, $purchase->status, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_datetime($purchase->transaction_date), [
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('invoice_date', __('purchase.invoice_date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::date('invoice_date', $purchase->invoice_date, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>



                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('is_vat', __('lang_v1.is_vat')) !!}
                        {!! Form::select('is_vat', ['0' => __('lang_v1.no'), '1' => __('lang_v1.yes')], $purchase->is_vat, [
                            'class' => 'form-control
                                                                                                			select2',
                            'required',
                        ]) !!}
                    </div>
                </div>


                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                        @show_tooltip(__('tooltip.purchase_location'))
                        {!! Form::select('location_id', $business_locations, $purchase->location_id, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'disabled',
                        ]) !!}
                    </div>
                </div>

                <!-- Currency Exchange Rate -->
                <div class="col-sm-3 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                    <div class="form-group">
                        {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                        @show_tooltip(__('tooltip.currency_exchange_factor'))
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! Form::number('exchange_rate', $purchase->exchange_rate, [
                                'class' => 'form-control',
                                'required',
                                'step' => 0.001,
                            ]) !!}
                        </div>
                        <span class="help-block text-danger">
                            @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                        </span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <div class="multi-input">
                            {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                            <br />
                            {!! Form::number('pay_term_number', $purchase->pay_term_number, [
                                'class' => 'form-control width-40 pull-left',
                                'placeholder' => __('contact.pay_term'),
                            ]) !!}

                            {!! Form::select(
                                'pay_term_type',
                                ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')],
                                $purchase->pay_term_type,
                                [
                                    'class' => 'form-control width-60 pull-left',
                                    'placeholder' => __('messages.please_select'),
                                    'id' => 'pay_term_type',
                                ],
                            ) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', ['id' => 'upload_document']) !!}
                        <p class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])</p>
                    </div>
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row" hidden>
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            {!! Form::text('search_product', null, [
                                'class' => 'form-control mousetrap',
                                'id' => 'search_product',
                                'placeholder' => __('lang_v1.search_product_placeholder'),
                                'autofocus',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <button tabindex="-1" type="button" class="btn btn-link btn-modal"
                            data-href="{{ action('ProductController@quickAdd') }}" data-container=".quick_add_product_modal"><i
                                class="fa fa-plus"></i> @lang('product.add_new_product') </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    @include('purchase.partials.edit_purchase_entry_row')

                    <hr />
                    <div class="pull-right col-md-5">
                        <table class="pull-right col-md-12">
                            <tr class="hide">
                                <th class="col-md-7 text-right">@lang('purchase.total_before_tax'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_st_before_tax" class="display_currency"></span>
                                    <input type="hidden" id="st_before_tax_input" value=0>
                                </td>
                            </tr>
                            <tr>
                                <th class="col-md-7 text-right">@lang('purchase.net_total_amount'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_subtotal"
                                        class="display_currency">{{ $purchase->total_before_tax / $purchase->exchange_rate }}</span>
                                    <!-- This is total before purchase tax-->
                                    <input type="hidden" id="total_subtotal_input"
                                        value="{{ $purchase->total_before_tax / $purchase->exchange_rate }}"
                                        name="total_before_tax">
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-12">
                    <table class="table">
                        <tr>

                            <td class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
                                </div>
                            </td>
                            <td class="col-md-3">

                            </td>
                            <td class="col-md-3">
                                &nbsp;
                            </td>
                            <td class="col-md-3">
                                <b>Discount:</b>(-)
                                <span id="discount_calculated_amount" class="display_currency">0</span>
                                {!! Form::hidden(
                                    'discount_amount',
                                
                                    $purchase->discount_type == 'fixed'
                                        ? number_format(
                                            $purchase->discount_amount / $purchase->exchange_rate,
                                            $currency_precision,
                                            $currency_details->decimal_separator,
                                            $currency_details->thousand_separator,
                                        )
                                        : number_format(
                                            $purchase->discount_amount,
                                            $currency_precision,
                                            $currency_details->decimal_separator,
                                            $currency_details->thousand_separator,
                                        ),
                                    ['class' => 'form-control input_number'],
                                ) !!}
                            </td>
                        </tr>
                        <tr hidden>
                            <td>
                                <div class="form-group">
                                    {!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}

                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <b>@lang('purchase.purchase_tax'):</b>(+)
                                <span id="tax_calculated_amount" class="display_currency">0</span>
                                {!! Form::hidden('tax_amount', $purchase->tax_amount, ['id' => 'tax_amount']) !!}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="form-group">
                                    {!! Form::label('shipping_details', __('purchase.shipping_details') . ':') !!}
                                    {!! Form::text('shipping_details', $purchase->shipping_details, ['class' => 'form-control']) !!}
                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align: right;">
                                <div class="form-group" style="display: inline-flex; align-items: center; gap: 10px;">
                                    {!! Form::label('shipping_charges', '(+) ' . __('purchase.additional_shipping_charges') . ':', [
                                        'style' => 'margin-bottom: 0;',
                                    ]) !!}
                                    {!! Form::text(
                                        'shipping_charges',
                                        number_format(
                                            $purchase->shipping_charges / $purchase->exchange_rate,
                                            $currency_precision,
                                            $currency_details->decimal_separator,
                                            $currency_details->thousand_separator,
                                        ),
                                        ['class' => 'form-control input_number', 'style' => 'width: auto;'],
                                    ) !!}
                                </div>
                                <div class="form-group" style="display: inline-flex; align-items: center; gap: 10px;">
                                    {!! Form::label('price_adjustment', __('purchase.price_adjustment') . ':', [
                                        'style' => 'margin-bottom: 0; color:red;',
                                    ]) !!}
                                    {!! Form::text(
                                        'price_adjustment',
                                        number_format(
                                            $purchase->price_adjustment / $purchase->exchange_rate,
                                            $currency_precision,
                                            $currency_details->decimal_separator,
                                            $currency_details->thousand_separator,
                                        ),
                                        ['class' => 'form-control input_number', 'style' => 'width: auto; outline: 1px solid red; color:red;'],
                                    ) !!}
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                {!! Form::hidden('final_total', $purchase->final_total, ['id' => 'grand_total_hidden']) !!}
                                <b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency"
                                    data-currency_symbol='true'>{{ number_format(
                                        $purchase->final_total,
                                        $currency_precision,
                                        $currency_details->decimal_separator,
                                        $currency_details->thousand_separator,
                                    ) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="form-group">
                                    {!! Form::label('additional_notes', __('purchase.additional_notes')) !!}
                                    {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        @endcomponent

        @component('components.widget', [
            'class' => 'box-primary unload_div hide',
            'title' => __('purchase.unload_tanks'),
        ])
            <div class="box-body unload_tank">
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="box-body payment_row" data-row_id="0">
                @if (!empty($is_admin))
                    @if (!empty($purchase->payment_lines))
                        @foreach ($purchase->payment_lines as $index => $one)
                            @include('sale_pos.partials.payment_row_form_expense', [
                                'row_index' => $index,
                                'payment' => $one,
                                'edit' => 1,
                            ])
                        @endforeach
                    @else
                        @include('sale_pos.partials.payment_row_form_expense', [
                            'row_index' => 0,
                            'edit' => 1,
                        ])
                    @endif
                @endif
                <hr>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
                        <a id="" href="{{ url('purchases') }}"
                            class="btn btn-danger pull-left btn-flat">@lang('lang_v1.back')</a>
                    </div>
                    <div class="col-sm-6">
                        <button type="button" id="submit_purchase_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        @endcomponent
        <input type="hidden" name="cash_account_id" id="cash_account_id" value="{{ $cash_account_id }}">
        <input type="hidden" name="is_edit" id="is_edit" value="1">
        {!! Form::close() !!}
    </section>
    <!-- @eng START 15/2 -->
    <style>
        .swal-title {
            color: red;
        }
    </style>
    <!-- @eng END 15/2 -->
    <!-- /.content -->
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".purchase_line_tax_id").trigger('change');
            update_table_total();
            update_grand_total();
            $('#method_0').trigger('change');
        });
    </script>
    @include('purchase.partials.keyboard_shortcuts')

    @php
        $i = 0;
    @endphp
    @foreach ($purchase->purchase_lines as $purchase_line)
        @if (!empty($purchase_line->product->category->name) && $purchase_line->product->category->name == 'Fuel')
            <script>
                $(document).ready(function() {
                    $.ajax({
                        method: 'get',
                        url: '/purchases/get_edit_unload_tank_row',
                        data: {
                            product_id: {{ $purchase_line->product_id }},
                            transaction_id: {{ $purchase_line->transaction_id }},
                            location_id: $('#location_id').val(),
                            row_count: {{ $i }},
                        },
                        success: function(data) {
                            if (data) {
                                $('.unload_div').removeClass('hide');
                                $('.unload_tank').append(data);
                            }
                        },
                    });
                    $('.method').trigger('change');
                });
            </script>
        @endif
        @php
            $i++;
        @endphp
    @endforeach

    <script>
        $(document).ready(function() {
            $('.payment-amount').change(function() {

                paid = parseFloat($(this).val());
                var $row = $(this).closest('.row');
                var accid = parseInt($row.find('.payment_types_dropdown').val());

                $.ajax({
                    method: 'GET',
                    url: '/accounting-module/check-insufficient-balance-for-accounts',
                    success: function(result) {
                        var ids = result;

                        if (ids.includes(accid)) {

                            $.ajax({
                                method: 'GET',
                                url: '/accounting-module/get-account-balance/' + accid,
                                success: function(result) {

                                    if (parseFloat(paid) > parseFloat(result
                                            .balance) || result.balance == null) {
                                        swal({
                                            title: 'Insufficient Balance',
                                            icon: "error",
                                            buttons: true,
                                            dangerMode: true,
                                        })

                                        $('button#submit_purchase_form').prop(
                                            'disabled', true);
                                        return false;
                                    } else {
                                        $('button#submit_purchase_form').prop(
                                            'disabled', false);
                                    }
                                }
                            });
                        } else {
                            $('button#submit_purchase_form').prop('disabled', false);
                        }

                    }
                });

                // @eng END 15/2 

            });

            $('#transaction_date_range_cheque_deposit').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#transaction_date_range_cheque_deposit').val(start.format(moment_date_format) + ' ~ ' +
                        end.format(moment_date_format));

                    get_cheques_list();
                }
            );





            $('.account_id').change(function() {

                var accid = parseInt($(this).val());
                var $row = $(this).closest('.row');
                var paid = parseFloat($row.find('.payment-amount').val());

                if ($(this).val() == "cheque") {
                    $row.find('.payment-amount').prop('readonly', true);
                } else {
                    $row.find('.payment-amount').prop('readonly', false);
                }

                $.ajax({
                    method: 'GET',
                    url: '/accounting-module/check-insufficient-balance-for-accounts',
                    success: function(result) {
                        var ids = result;
                        if (ids.includes(accid)) {

                            $.ajax({
                                method: 'GET',
                                url: '/accounting-module/get-account-balance/' + accid,
                                success: function(result) {

                                    if (parseFloat(paid) > parseFloat(result
                                            .balance) || result.balance == null) {
                                        swal({
                                            title: 'Insufficient Balance',
                                            icon: "error",
                                            buttons: true,
                                            dangerMode: true,
                                        })

                                        $('button#submit_purchase_form').prop(
                                            'disabled', true);
                                        return false;
                                    } else {
                                        $('button#submit_purchase_form').prop(
                                            'disabled', false);
                                    }
                                }
                            });
                        } else {
                            $('button#submit_purchase_form').prop('disabled', false);
                        }

                    }
                });

            });
        });

        //   $(document).on('change', '.payment_types_dropdown', function(e) {
        //         var payment_type = $(this).val();
        //         if(payment_type == 'direct_bank_deposit' || payment_type == 'bank_transfer'){
        //           $('.account_module').removeClass('hide');
        //         }else{
        //           $('.account_module').addClass('hide');
        //         }
        //     });
    </script>
@endsection
