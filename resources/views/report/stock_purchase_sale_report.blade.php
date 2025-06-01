<!-- Content Header (Page header) -->
<section class="content-header" style="padding: 5px !important">
    <h1>@lang('report.stock_purchase_sale_report')</h1>
</section>

<!-- Main content -->
<section class="content" style="padding-top: 0px !important">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('stock_purchase_sale_report_date', 'Date Range:') !!}
                    {!! Form::text('stock_purchase_sale_report_date', null, ['class' => 'form-control', 'readonly', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="stock_purchase_sale_report_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th colspan="13" style="text-align: center;">@lang('report.stock_purchase_sale_report')</th>
                        </tr>
                        <tr>
                            <th>@lang('report.product_code')</th>
                            <th>@lang('report.product')</th>
                            <th>@lang('report.opening_stock')</th>
                            <th>@lang('report.purchase_qty')</th>
                            <th>@lang('report.purchase_cost')</th>
                            <th>@lang('report.p_returned_qty')</th>
                            <th>@lang('report.sold_qty')</th>
                            <th>@lang('report.sale_amount')</th>
                            <th>@lang('report.sold_returned_qty')</th>
                            <th>@lang('report.sale_return_amount')</th>
                            <th>@lang('report.total_sold_qty')</th>
                            <th>@lang('report.total_sale_amount')</th>
                            <th>@lang('report.avr_stock_qty')</th>
                            <th>@lang('report.avr_stock_amount')</th>
                            <th>@lang('report.profit')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated via JavaScript or server-side rendering -->
                    </tbody>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->