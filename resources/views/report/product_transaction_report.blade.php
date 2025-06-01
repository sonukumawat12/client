<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.product_transaction_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => action('ReportController@getProductTransactionReport'), 'method' => 'get', 'id' =>
            'product_transaction_report_filter_form' ]) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'product_transaction_location_id']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('store_id', __('lang_v1.store_id').':*') !!}
                    <select name="store_id" id="product_transaction_store_id" class="form-control select2" style="width: 100%;" required>
                        <option value="">@lang('messages.please_select')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('category.category') . ':') !!}
                    {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_transaction_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_transaction_sub_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product', __('report.product') . ':') !!}
                    {!! Form::select('product', $products, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_transaction_product']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('brand', __('product.brand') . ':') !!}
                    {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_transaction_brand']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit',__('product.unit') . ':') !!}
                    {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'id' => 'product_transaction_unit']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_transaction_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'product_transaction_date_range', 'readonly']); !!}
                </div>
            </div>
            <!-- Modal for Custom Date Range -->
            <div class="modal fade" id="product_transaction_customDateRangeModal" tabindex="-1" aria-labelledby="product_transaction_customDateRangeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="product_transaction_customDateRangeModalLabel">Select Custom Date Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                            <label for="product_transaction_start_date">From:</label>
                            <input type="date" id="product_transaction_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                                <div class="col-md-6">
                            
                            <label for="product_transaction_end_date" class="mt-2">To:</label>
                            <input type="date" id="product_transaction_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="product_transaction_applyCustomRange">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    @include('report.partials.report_summary_section')

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div id='table_div'>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="product_transaction_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th  class="notexport">@lang('messages.action')</th>
                                <th>@lang('report.date_and_time')</th>
                                <th>@lang('report.sku')</th>
                                <th>@lang('report.product')</th>
                                <th>@lang('report.description')</th>
                                <th>@lang('report.starting_qty')</th>
                                <th>@lang('report.purchase_qty')</th>
                                <th>@lang('report.bonus_qty')</th>
                                <th>@lang('report.sold_qty')</th>
                                <th>@lang('report.balance_qty')</th>
                                <th>@lang('report.balance_qty_value')</th>
                                <th>@lang('report.stock_value_date_wise')</th>
                                <th>@lang('lang_v1.product_added_date')</th>

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
