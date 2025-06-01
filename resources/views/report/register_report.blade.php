<!-- Content Header (Page header) -->
<section class="content-header"  style="padding: 5px !important">
    <h1>{{ __('report.register_report')}}</h1>
</section>

<!-- Main content -->
<section class="content" style="padding-top: 0px !important">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('register_user_id',  __('report.user') . ':') !!}
                        {!! Form::select('register_user_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all_users')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('register_status',  __('sale.status') . ':') !!}
                        {!! Form::select('register_status', ['open' => __('cash_register.open'), 'close' => __('cash_register.close')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('close_date',  __('sale.close_date') . ':') !!}
                        {!! Form::text('close_date',  null, ['class' => 'form-control', 'readonly' ,'style' => 'width:100%']); !!}
                    </div>
                </div>
                <!-- Modal for Custom Date Range -->
                <div class="modal fade" id="register_report_customDateRangeModal" tabindex="-1" aria-labelledby="register_report_customDateRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="register_report_customDateRangeModalLabel">Select Custom Date Range</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-6">
                                <label for="register_report_start_date">From:</label>
                                <input type="date" id="register_report_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                                
                                <label for="register_report_end_date" class="mt-2">To:</label>
                                <input type="date" id="register_report_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="register_report_applyCustomRange">Apply</button>
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
                    <table class="table table-bordered table-striped" id="register_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('report.open_time')</th>
                                <th>@lang('report.close_time')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('report.user')</th>
                                <th>@lang('cash_register.total_card_slips')</th>
                                <th>@lang('cash_register.total_cheques')</th>
                                <th>@lang('cash_register.total_cash')</th>
                                <th>@lang('cash_register.total_credit_sale')</th>
                                <th class="notexport">@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
