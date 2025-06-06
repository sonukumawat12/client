@extends('layouts.app')
@section('title', __('lang_v1.user_activity'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.user_activity')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                   
                   <div class="form-group">

                        {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'date_range_filter', 'readonly']); !!}
                    </div>
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('subject', __('Subject') . ':') !!}
                     
                        {!! Form::select('subject', $subject, null, ['id' => 'subject' ,'class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]); !!}
                     
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('Type') . ':') !!}
                     
                        {!! Form::select('user', $type, null, ['id' => 'type' ,'class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]); !!}
                     
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('user', __('report.users') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('user', $users, null, ['id' => 'users' ,'class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]); !!}
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
                    <table class="table table-bordered table-striped" 
                    id="user_activity_report_table" width="100%">
                        <thead>
                            <tr>
                                <th>@lang('report.date_time')</th>
                                <th>@lang('report.username')</th>
                                <th>@lang('report.activity_subject')</th>
                                <th>@lang('report.subject_id')</th>
                                <th>@lang('report.activity_type')</th>
                                <th>@lang('report.description')</th>
                            </tr>
                        </thead>
                        <tfoot>
                         
                        </tfoot>
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


@endsection

@section('javascript')
<script>
    //  if ($('#date_range_filter').length == 1) {
    //     $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
    //         $('#date_range_filter').val(
    //            start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
    //         );
    //     });
    //     $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
    //         $('#product_sr_date_filter').val('');
    //     });
    //     $('#date_range_filter')
    //         .data('daterangepicker')
    //         .setStartDate(moment().startOf('month'));
    //     $('#date_range_filter')
    //         .data('daterangepicker')
    //         .setEndDate(moment().endOf('month'));
    // }
    $('#date_range_filter').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#date_range_filter').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        user_activity_report_table.ajax.reload();
                    }
                });

                $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#date_range_filter').length) {
                        $('#date_range_filter').val(fullRange);
                        $('#date_range_filter').data('daterangepicker').setStartDate(moment(startDate));
                        $('#date_range_filter').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        user_activity_report_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
</script>
<script>
//User Activity report

    user_activity_report_table = $('#user_activity_report_table').DataTable({
    processing: true,
    serverSide: true,
    order: [[0, 'desc']],
    ajax: {
        url: '{{action("CustomerStatementController@getUserActivityReport")}}',
        data: function (d) {
            var user = $('#users').val();
            var type = $('#type').find(":selected").text();
            var start = '';
            var end = '';
            if ($('#date_range_filter').val()) {
              
            }
            start = $('#date_range_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('#date_range_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            var subject = $('#subject').find(":selected").text();

            d.user = user;
            d.type = type;
            d.subject = subject;
            d.startDate = start;
            d.endDate = end;
        },
    },
    columns: [
        { data: 'created_at', name: 'created_at' },
        { data: 'causer_id', name: 'causer_id' },
        { data: 'log_name', name: 'log_name' },
        { data: 'subject_id', name: 'subject_id' },
        { data: 'description', name: 'description' },
        { data: 'description_details', name: 'description' }
    ],
    buttons: [
          {
                extend: 'csv',
                text: '<i class="fa fa-file"></i> Export to CSV',
                className: 'btn btn-default btn-sm',
                title: 'User Activity Report',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                className: 'btn btn-default btn-sm',
                title: 'User Activity Report',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i> Column Visibility',
                className: 'btn btn-default btn-sm',
                title: 'User Activity Report',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                className: 'btn btn-default btn-sm',
                title: 'User Activity Report',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-default btn-sm',
                title: 'User Activity Report',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                }
            }
    ],
    dom: "<'row'<'col-sm-3'l><'col-sm-6'B><'col-sm-3'f>>" +  
        "<'row'<'col-sm-12'tr>>" + 
        "<'row'<'col-sm-5'i><'col-sm-7'p>>", 
    lengthMenu: [10, 25, 50, 75, 100],
    fnDrawCallback: function (oSettings) {

    },
});
$('#users').change(function () {
    user_activity_report_table.ajax.reload();

});
$('#type').change(function () {
    user_activity_report_table.ajax.reload();

});
$('#subject').change(function () {
    user_activity_report_table.ajax.reload();

});
$('#date_range_filter').on('apply.daterangepicker', function () {
    user_activity_report_table.ajax.reload();
});
</script>
@endsection