<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('lang_v1.aging_report')}}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'aging_report_form' ]) !!}
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'), 'id' => 'aging_customer_id', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('route', 'Route :') !!}
                    {!! Form::select('route[]', $routes, null, [
                        'class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'),
                        'id' => 'aging_route_id',
                        'style' => 'width: 100%;',
                        'multiple' => 'multiple'
                    ]); !!}

                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('no_of_days_over', __('report.enter_no_of_days_over') . ':') !!}
                    {!! Form::text('no_of_days_over', null, ['class' => 'form-control','placeholder' =>
                    __('report.enter_no_of_days_over'), 'id' => 'no_of_days_over']); !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_filter_by', __('report.date_filter_by')) !!}
                    {!! Form::select('date_filter_by', ['transaction_date' => __('report.transaction_date'),'all_days' => __('report.all_days')], null, [
                        'class' => 'form-control select2',
                        'id' => 'date_filter_by',
                        'style' => 'width: 100%;',
                    ]); !!}

                </div>
            </div>

            <div class="col-md-4 ppr_date_filter_filed">
                <div class="form-group">

                    {!! Form::label('ppr_date_filter1', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'ppr_date_filter1', 'readonly']); !!}
                </div>
            </div>
            <!-- Modal for Custom Date Range -->
            <div class="modal fade" id="ppr_1_customDateRangeModal" tabindex="-1" aria-labelledby="ppr_1_customDateRangeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ppr_1_customDateRangeModalLabel">Select Custom Date Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                            <label for="ppr_1_start_date">From:</label>
                            <input type="date" id="ppr_1_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                                <div class="col-md-6">
                            
                            <label for="ppr_1_end_date" class="mt-2">To:</label>
                            <input type="date" id="ppr_1_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="ppr_1_applyCustomRange">Apply</button>
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
                <table class="table table-bordered table-striped" id="aging_report_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>@lang('report.date')</th>
                            <th>@lang('report.customer')</th>
                            <th>Days</th>
                            <th>Route</th>
                            <th>@lang('lang_v1.invoice_no')</th>
                            <th>@lang('report.1_30_days')</th>
                            <th>@lang('report.31_45_days')</th>
                            <th>@lang('report.46_60_days')</th>
                            <th>@lang('report.61_90_days')</th>
                            <th>@lang('report.over_90_days')</th>
                            <th>Total</th>
                            <th class="notexport">@lang('report.action')</th> 
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td></td><td></td><td></td><td></td>
                            <td colspan="1"><strong>@lang('sale.total'):</strong></td>
                            
                            <td><span class="display_currency" id="footer_total_1_30" data-currency_symbol ="true"></span></td>
                            
                            <td><span class="display_currency" id="footer_total_31_45" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_46_60" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_61_90" data-currency_symbol ="true"></span></td>
                            
                            <td><span class="display_currency" id="footer_total_90" data-currency_symbol ="true"></span></td>
                            
                            <td><span class="display_currency" id="footer_total_amount_aging" data-currency_symbol ="true"></span></td>
                            
                            <td colspan="1"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
