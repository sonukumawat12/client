@extends('layouts.app')
@section('title', __('restaurant.table_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('restaurant.table_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tr_location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('tr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tr_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'tr_date_range', 'readonly']); !!}
                        </div>
                    </div>

                    <!-- Modal for Custom Date Range -->
                    <div class="modal fade" id="tr_customDateRangeModal" tabindex="-1" aria-labelledby="tr_customDateRangeModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="tr_customDateRangeModalLabel">Select Custom Date Range</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                <div class="col-md-6">
                                    <label for="tr_start_date">From:</label>
                                    <input type="date" id="tr_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                    </div>
                                <div class="col-md-6">
                                    
                                    <label for="tr_end_date" class="mt-2">To:</label>
                                    <input type="date" id="tr_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                    </div>
                            </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="tr_applyCustomRange">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table_report">
                        <thead>
                            <tr>
                                <th>@lang('restaurant.table')</th>
                                <th>@lang('report.total_sell')</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            if($('#tr_date_range').length == 1){
                var my_ranges = Object.assign({}, ranges);
                my_ranges['Custom Date Range'] = [moment(), moment()];
                $('#tr_date_range').daterangepicker({
                    ranges: my_ranges,
                    autoUpdateInput: false,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                    locale: {
                        format: moment_date_format
                    }
                });
                $('#custom_date_apply_button').on('click', function() {
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);

                        $('#tr_date_range').val(formattedStartDate + ' ~ ' + formattedEndDate);

                        $('#tr_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#tr_date_range').data('daterangepicker').setEndDate(moment(endDate));

                        $('.custom_date_typing_modal').modal('hide');
                        table_report.ajax.reload();
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });

                $('#tr_date_range').on('apply.daterangepicker', function(ev, picker) {
                    if (picker.chosenLabel === 'Custom Date Range') {
                        $('.custom_date_typing_modal').modal('show');
                    } else {
                        $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                        table_report.ajax.reload();
                    }
                });

                $('#tr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    table_report.ajax.reload();
                });
            }

            table_report = $('#table_report').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/table-report",
                                "data": function ( d ) {
                                    d.location_id = $('#tr_location_id').val();
                                    d.start_date = $('#tr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#tr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                {data: 'table', name: 'res_tables.name'},
                                {data: 'total_sell', name: 'total_sell', searchable: false}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#table_report'));
                            }
                        });
            //Customer Group report filter
            $('select#tr_location_id, #tr_date_range').change( function(){
                table_report.ajax.reload();
            });
        })
    </script>
@endsection