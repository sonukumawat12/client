<style>
    .bg_color {
        background: #357ca5;
        font-size: 20px;
        color: #fff;
    }

    .text-center {
        text-align: center;
    }

    #customer_detail_table th {
        background: #357ca5;
        color: #fff;
    }

    #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
    #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
        background-color: rgba(89, 129, 255, 0.3);
    }
</style>
<div class="row">
    <div class="col-md-12">

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control', 'readonly']); !!}
            </div>
        </div>

    </div>
    <div class="col-md-12">

        <div id="contact_ledger_div"></div>

    </div>
    <div class="col-md-12">
        
        <div class="col-md-6"></div>

        <div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
            <p class=" bg_color"
                style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
                @lang('lang_v1.account_summary')</p>
            <hr>
            <table
                class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif"
                id="customer_detail_table">
                <tr>
                    <td>@lang('lang_v1.beginning_balance')</td>
                    <td id="bf_balance">0.00</td>
                </tr>
                
                <tr>
                    <td>@lang('fleet::lang.invoice_amount')</td>
                    <td id="total_income">0.00</td>
                </tr>
                
                <tr>
                    <td>@lang('fleet::lang.payment_received')</td>
                    <td id="total_paid">0.00</td>
                </tr>
                <tr>
                    <td><strong>@lang('lang_v1.balance_due')</strong></td>
                    <td id="balance_due">0.00</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="ledger_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang('fleet::lang.date')</th>
                <th>@lang('fleet::lang.description')</th>
                <th>@lang('fleet::lang.original_location')</th>
                <th>@lang('fleet::lang.destination')</th>
                <th>@lang('fleet::lang.milage')</th>
                <th>@lang('fleet::lang.invoice_no')</th>
                <th>@lang('fleet::lang.invoice_amount')</th>
                <th>@lang('fleet::lang.payment_received')</th>
                <th>@lang('fleet::lang.balance')</th>
                <th>@lang('fleet::lang.payment_method')</th>
            </tr>
        </thead>
        <tr>
        </tr>
    </table>
</div>