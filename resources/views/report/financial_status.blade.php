<!-- Content Header (Page header) -->
<section class="content-header"  style="padding: 5px !important">
    <h1>Review Changes</h1>
</section>

<!-- Main content -->
<section class="content" style="padding-top: 0px !important">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('financial_status_date_range',  'Review date:') !!}
                        {!! Form::text('financial_status_date_range',  null, ['class' => 'form-control', 'readonly' ,'style' => 'width:100%']); !!}
                    </div>
                </div>
                <!-- Modal for Custom Date Range -->
                <div class="modal fade" id="financial_status_customDateRangeModal" tabindex="-1" aria-labelledby="financial_status_customDateRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="financial_status_customDateRangeModalLabel">Select Custom Date Range</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-6">
                                <label for="financial_status_start_date">From:</label>
                                <input type="date" id="financial_status_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                                
                                <label for="financial_status_end_date" class="mt-2">To:</label>
                                <input type="date" id="financial_status_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="financial_status_applyCustomRange">Apply</button>
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
                <div class="table-responsive" id="financial_status_report_table">
                    
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_review_change" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
