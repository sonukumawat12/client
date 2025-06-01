@extends('layouts.printlayout')
@section('content')
    <script src="{{ asset('AdminLTE/plugins/jQuery/jquery-2.2.3.min.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js?v=' . $asset_v) }}"></script>

    <div class="row">
        <div class="col-md-12">
            <span style="float: right">
                <button class="btn btn-primary" onclick="printContent('printing-page')">Print</button>
                <a href="javascript:void(0)" class="btn btn-danger back-to-sales-dashboard" style="margin-left: 15px">Back to Sales Dashboard</a>
            </span>
        </div>
    </div>
    <div id="printing-page">
        <div class="col-md-12">
            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                <h1>@lang( 'property::lang.finalize' )</h1>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-6 col-sm-6 col-xs-6 text-left">
                <p>
                    <strong>Reservation Payment:</strong>
                </p>
                <p>
                    <strong>Invoice No: {{request()->get('invoice_no') ?? ''}}</strong>
                </p>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-left">
                <p>
                    <strong>Sold Date:{{request()->get('transaction_date') ?? ''}}</strong>
                </p>
                <p>
                    <strong>Block Sold Price: {{request()->get('block_sold_price') ?? '0'}}</strong>
                </p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                    <p><strong>Date:</strong> <br> {{request()->get('date') ?? ''}}</p>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Block Sold Price:</strong> <br> {{request()->get('block_sold_price') ?? '0'}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Other Charges:</strong> <br> {{request()->get('other_charges') ?? '0'}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Total Payable:</strong> <br> {{request()->get('total_payable') ?? '0'}}</p>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Reserved Amount:</strong> <br> {{request()->get('reserved_amount') ?? '0'}}</p>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Down Payment:</strong> <br> {{request()->get('down_payment') ?? '0'}}</p>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Balance Amount:</strong> <br> {{request()->get('balance_amount') ?? '0'}}</p>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Finance Option:</strong> <br> {{request()->get('finance_option') ?? ''}}</p>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Easy Payment:</strong> <br> {{request()->get('easy_payment') ?? ''}}</p>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Installment Cycle:</strong> <br> {{request()->get('installment_cycle') ?? ''}}</p>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>No Of Installment:</strong> <br> {{request()->get('no_of_installment') ?? ''}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Installment Amount:</strong> <br> {{request()->get('installment_amount') ?? '0'}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Loan Capital:</strong> <br> {{request()->get('loan_capital') ?? '0'}}</p>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Total Interest:</strong> <br> {{request()->get('total_interest') ?? '0'}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>First Installment Date:</strong> <br> {{request()->get('first_installment_date') ?? ''}}</p>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <p><strong>Last Installment Date:</strong> <br> {{request()->get('last_installment_date') ?? ''}}</p>
            </div>
        </div>
        <div class="col-md-12">

            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                <p><strong>Attachments:</strong></p>
                @if(request()->get('docoment_name'))
                    <p>
                        <a href="{{request()->get('docoment_name')}}">{{request()->get('docoment_name')}}</a>
                    </p>
                @endif

            </div>

            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                <p><strong>Note:</strong></p>
                <p>{{request()->get('note') ?? ''}}</p>
            </div>

        </div>
    </div>
@endsection
@section('javascript')
    <script>
        function printContent(el) {
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
        }

        $(".back-to-sales-dashboard").click(function () {
            window.location.href = "sale-and-customer-payment/dashboard?type=customer";
        });
    </script>
@endsection
