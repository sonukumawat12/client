<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.product_loss_excess_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => action('ReportController@getProductTransactionReport'), 'method' => 'get', 'id' =>
            'product_loss_excess_report_filter_form' ]) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'product_loss_excess_location_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product', __('report.product') . ':') !!}
                    {!! Form::select('product', $products, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_loss_excess_product']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('report.type') . ':') !!}
                    {!! Form::select('type', ['loss' => 'Weight Loss', 'excess' => 'Weight Excess'], null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_loss_excess_type']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer',__('report.customer') . ':') !!}
                    {!! Form::select('customer', $customers, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_loss_excess_customer']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit',__('product.unit') . ':') !!}
                    {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_loss_excess_unit']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_loss_excess_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'product_loss_excess_date_range', 'readonly']); !!}
                </div>
            </div>
            <!-- Modal for Custom Date Range -->
            <div class="modal fade" id="product_loss_excess_customDateRangeModal" tabindex="-1" aria-labelledby="product_loss_excess_customDateRangeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="product_loss_excess_customDateRangeModalLabel">Select Custom Date Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                            <label for="product_loss_excess_start_date">From:</label>
                            <input type="date" id="product_loss_excess_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                                <div class="col-md-6">
                            
                            <label for="product_loss_excess_end_date" class="mt-2">To:</label>
                            <input type="date" id="product_loss_excess_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="product_loss_excess_applyCustomRange">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div id='table_div'>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="product_loss_excess_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('report.date_and_time')</th>
                                <th>@lang('report.location')</th>
                                <th>@lang('report.product')</th>
                                <th>@lang('report.unit')</th>
                                <th>@lang('report.qty')</th>
                                <th>@lang('report.type')</th>
                                <th>@lang('report.customer')</th>
                                <th>@lang('report.sale_bill_no')</th>
                                <th>@lang('report.sale_amount')</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
