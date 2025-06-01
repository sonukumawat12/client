<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.customer_groups_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

              {!! Form::open(['url' => action('ReportController@getCustomerGroup'), 'method' => 'get', 'id' => 'cg_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('cg_customer_group_id', __( 'lang_v1.customer_group_name' ) . ':') !!}
                        {!! Form::select('cg_customer_group_id', $customer_group, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'cg_customer_group_id']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('cg_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('cg_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('cg_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'cg_date_range', 'readonly']); !!}
                    </div>
                </div>

                <!-- Modal for Custom Date Range -->
                <div class="modal fade" id="cg_customDateRangeModal" tabindex="-1" aria-labelledby="cg_customDateRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cg_customDateRangeModalLabel">Select Custom Date Range</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-6">
                                <label for="cg_start_date">From:</label>
                                <input type="date" id="cg_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                                
                                <label for="cg_end_date" class="mt-2">To:</label>
                                <input type="date" id="cg_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="cg_applyCustomRange">Apply</button>
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="cg_report_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.customer_group')</th>
                            <th>@lang('report.total_sell')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
