<div class="container">
    <div class="spacer"></div>
    <div class="row">
        <div class="col-md-8 col-md-offset-1 col-sm-12" style="border: 1px solid #ccc;">
            <div id="invoice_content">
                <!-- business information here -->

                <div class="row">

                    <!-- Logo -->
                    @if (session('business.logo') && empty($is_pdf))
                        <div class="text-center">
                            <img style="width: 160px; height: 90px" src="{{ asset('uploads/business_logos/' . session('business.logo')) }}"
                                alt="Logo">
                        </div>
                    @endif

                    <!-- Header text -->

                    <!-- business information here -->
                    <div class="col-xs-12 text-center">
                        <h2 class="text-center">
                            <!-- Shop & Location Name  -->
                            {{ session('business.name') }}
                        </h2>

                        <!-- Address -->
                        <p>
                            <small class="text-center">
                                {!! $loan_transaction->business_location->location_address !!}
                            </small>
                            <br>
                            @if (!empty($loan_transaction->business_location->mobile))
                                <b>Mobile:</b> {{ $loan_transaction->business_location->mobile }}
                            @endif
                        </p>

                        <!-- Title -->
                        <h3 class="text-center">
                            {{ trans_choice('loan::general.transaction', 1) }}
                            {{ trans_choice('core.detail', 2) }}
                        </h3>

                        <p style="width: 100% !important" class="word-wrap">
                            <span class="pull-left text-left word-wrap">
                                <b>Transaction #{{ $loan_transaction->id }}</b>

                                <!-- customer info -->
                                <br>
                                <b>Customer</b> <br>{{ $contact->name }}<br>{{ $contact->mobile }} <br>
                                <br>
                                {!! $contact->address_line_1 !!}
                            </span>

                            <span class="pull-right text-left">
                                <b>Date</b> {{ @format_date($loan_transaction->submitted_on) }}:
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <!-- /.col -->
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <br>
                        <table class="table table-responsive table-slim">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ trans_choice('core.transaction', 1) }}
                                        {{ trans_choice('core.type', 1) }}</th>
                                    <th class="text-right">{{ trans_choice('core.payment', 1) }}
                                        {{ trans_choice('core.type', 1) }}</th>
                                    <th class="text-right">{{ trans('core.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-left">{{ $loan_transaction->loan_transaction_type }}</td>
                                    <td class="text-right">{{ $loan_transaction->payment_detail->payment_type->name }}</td>
                                    <td class="text-right">{{ number_format($loan_transaction->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">

                    <div class="col-xs-12">
                        <div class="table-responsive">
                            @if ($loan_transaction->payment_detail->has_more_info)
                                <div class="col-md-12">
                                    <hr>
                                </div>

                                <table class="table table-slim">
                                    <tr>
                                        <td colspan="2">
                                            <b>{{ trans_choice('core.payment', 1) }}
                                                {{ trans_choice('core.detail', 2) }}</b>
                                        </td>
                                    </tr>
                                    @if (!empty($loan_transaction->payment_detail->account_number))
                                        <tr>
                                            <td>{{ trans_choice('core.account', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->account_number }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->cheque_number))
                                        <tr>
                                            <td>{{ trans_choice('core.cheque', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->cheque_number }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->routing_code))
                                        <tr>
                                            <td>{{ trans_choice('core.routing_code', 1) }}</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->routing_code }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->receipt))
                                        <tr>
                                            <td>{{ trans_choice('core.receipt', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->receipt }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->bank_name))
                                        <tr>
                                            <td>{{ trans_choice('core.bank', 1) }}#</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->bank_name }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($loan_transaction->payment_detail->description))
                                        <tr>
                                            <td>{{ trans_choice('core.description', 1) }}</td>
                                            <td>
                                                {{ $loan_transaction->payment_detail->description }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <p></p>
                    </div>
                </div>
                <div class="row">
                </div>

            </div>
            <div class="spacer"></div>
        </div>
    </div>
    <div class="spacer"></div>
</div>
