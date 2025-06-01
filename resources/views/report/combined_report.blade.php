<!-- Content Header (Page header) -->
<section class="content-header" style="padding: 5px !important">
    <h1>Combined Reports</h1>
</section>



<!-- Main content -->
<section class="content" style="padding-top: 0px !important">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('combined_report_date', 'Combined date:') !!}
                        {!! Form::text('combined_report_date', null, ['class' => 'form-control', 'readonly', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <!-- Modal for Custom Date Range -->
                <div class="modal fade" id="combined_report_customDateRangeModal" tabindex="-1" aria-labelledby="combined_report_customDateRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="combined_report_customDateRangeModalLabel">Select Custom Date Range</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-6">
                                <label for="combined_report_start_date">From:</label>
                                <input type="date" id="combined_report_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                                
                                <label for="combined_report_end_date" class="mt-2">To:</label>
                                <input type="date" id="combined_report_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="combined_report_applyCustomRange">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="stock_report" style="width: 100%;">
                        <thead>
                            <tr><th colspan="8" style="text-align: center;">@lang('report.stock_summary')</th></tr>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Opening Stock</th>
                                <th>Total Purchase Stock</th>
                                <th>Total Sold Qty</th>
                                <th style="color: red;">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated via JavaScript or server-side rendering -->
                        </tbody>
                    </table>
                </div>
            @endcomponent
        </div>
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="customer_outstanding_report" style="width: 100%;">
                        <thead>
                            <tr><th colspan="4" style="text-align: center;">@lang('report.customer_outstanding')</th></tr>
                            <tr>
                                <th>Customer Name</th>
                                <th>Opening Balance</th>
                                <th>Total Credit Sale</th>
                                <th>Total Payment Received</th>
                                <th style="color: red;">Balance Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated via JavaScript or server-side rendering -->
                        </tbody>
                    </table>
                </div>
            @endcomponent
        </div>
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="supplier_outstanding_report" style="width: 100%;">
                        <thead>
                            <tr><th colspan="4" style="text-align: center;">@lang('report.supplier_outstanding')</th></tr>
                            <tr>
                                <th>Customer Name</th>
                                <th>Opening Balance</th>
                                <th>Total Credit Purchase</th>
                                <th>Total Payment Paid</th>
                                <th style="color: red;">Balance Due</th>
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
<div class="modal fade view_combined_report" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <!-- Modal content will be loaded here -->
</div>
