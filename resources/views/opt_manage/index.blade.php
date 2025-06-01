@extends('layouts.app')
@section('title', __('lang_v1.OTP_verifications'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.OTP_verifications' ) @show_tooltip(__('lang_v1.OTP_verifications'))
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                        @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                        'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('user', __( 'lang_v1.user' )) !!}
                        {!! Form::select('user', $names, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'airline::lang.please_select' ), 'id' => 'user']);
                        !!}
                    </div>
                </div>
            </div>
           
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            
        @endslot
        @can('brand.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="opt_activity_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.date' )</th>
                            <th>@lang( 'lang_v1.time' )</th>
                            <th>@lang( 'lang_v1.user' )</th>
                            <th>@lang( 'lang_v1.mobile_number' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    

</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
 if ($('#date_range_filter').length == 1) {
    $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
        $('#date_range_filter').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );

        opt_activity_table.ajax.reload();
    });
    $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#product_sr_date_filter').val('');
    });
    $('#date_range_filter')
        .data('daterangepicker')
        .setStartDate(moment().startOf('year'));
    $('#date_range_filter')
        .data('daterangepicker')
        .setEndDate(moment().endOf('year'));
}

    //Roles table
    $(document).ready( function(){
     opt_activity_table = $('#opt_activity_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                    url:"{{ action('\App\Http\Controllers\UsersOPTController@index') }}",
                    data: function(d) {
                        d.user = $('#user').val();
                        var start_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                    "columns":[
                        {"data":"date"},
                        {"data":"time"},
                        {"data":"user"},
                        {"data":"mobile"}
                    ]
                });
                
        
        
    });
    
    

$('#date_range_filter, #user').change(function() {
    opt_activity_table.ajax.reload();
})   
</script>
@endsection
