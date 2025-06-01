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
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
            </div>
        </div>
       

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
                                    <td>@lang('essentials::lang.salaries_income')</td>
                                    <td id="total_income">0.00</td>
                                </tr>
                                
                                <tr>
                                    <td>@lang('essentials::lang.loans_advances')</td>
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
    <table class="table table-bordered table-striped" id="fleet_ledger_table">
        <thead>
            <tr>
                <th>@lang('fleet::lang.date')</th>
                <th>@lang('fleet::lang.description')</th>
                <th>@lang('essentials::lang.salaries_income')</th>
                <th>@lang('essentials::lang.loans_advances')</th>
                <th>@lang('fleet::lang.balance')</th>
                <th>@lang('fleet::lang.payment')</th>
                
            </tr>
        </thead>
    </table>
</div>