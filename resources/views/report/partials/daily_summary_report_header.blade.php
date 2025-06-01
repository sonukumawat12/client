<!-- Content Header (Page header) -->
<section class="content-header"  style="padding: 5px !important">
    <h1>{{ __('report.daily_summary_report')}}</h1>
</section>

<div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('daily_summary_report_location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('daily_summary_report_location_id', $business_locations, !empty($location_id) ? $location_id : null, ['class' =>
            'form-control select2 daily_summary_report_change',
            'placeholder' => __('petro::lang.all'), 'id' => 'daily_summary_report_location_id', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('daily_summary_report_work_shift', __('hr.work_shift') . ':') !!}
            {!! Form::select('daily_summary_report_work_shift', $work_shifts, !empty($work_shift_id) ? $work_shift_id : null , ['class' =>
            'form-control select2 daily_summary_report_change', 'placeholder'
            => __('petro::lang.all'), 'id' => 'daily_summary_report_work_shift', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('daily_summary_report_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('daily_summary_report_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
            day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control daily_summary_report_change', 'id' => 'daily_summary_report_date_range', 'readonly']); !!}
        </div>
    </div>
    <!-- Modal for Custom Date Range -->
    <div class="modal fade" id="daily_summary_report_customDateRangeModal" tabindex="-1" aria-labelledby="daily_summary_report_customDateRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="daily_summary_report_customDateRangeModalLabel">Select Custom Date Range</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                    <label for="daily_summary_report_start_date">From:</label>
                    <input type="date" id="daily_summary_report_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                    
                    <label for="daily_summary_report_end_date" class="mt-2">To:</label>
                    <input type="date" id="daily_summary_report_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="daily_summary_report_applyCustomRange">Apply</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="margin-top: 25px;">
        <div class="box-tools pull-right">
            <button class="btn btn-primary print_report" onclick="printDailySummaryDiv()">
                <i class="fa fa-print"></i> @lang('messages.print')</button>
        </div>
    </div>
    @endcomponent
</div>
<div class="daily_summary_report_content"></div>