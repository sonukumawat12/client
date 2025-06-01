@extends('layouts.app')
@section('title', __('petro::lang.list_commission'))
<style>
.disabled {
    pointer-events:none; //This makes it not clickable
    opacity:0.6;         //This grays it out to look disabled
}
</style>
@section('content')


<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('petro::lang.type') . ':') !!}
                    {!! Form::select('type', ['fixed' => __('petro::lang.fixed'),'percentage' =>
                    __('petro::lang.percentage')], null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.list_commission')])
    @slot('tool')
    
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_commissions_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.transaction_date')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.sale_amount')</th>
                    <th>@lang('petro::lang.commission_type')</th>
                    <th>@lang('petro::lang.commission_value')</th>
                    <th>@lang('petro::lang.commission_amount')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->


@endsection
@section('javascript')
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $(document).ready( function(){
    if ($('#date_range').length == 1) {
        $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            
            list_commissions_table.ajax.reload();
        });
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
        });
        $('#date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    var columns = [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'settlement_no', name: 'settlement_no' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'sale_amount', name: 'sale_amount' },
            { data: 'type', name: 'type' },
            { data: 'value', name: 'value' },
            { data: 'commission_amount', name: 'commission_amount'  }
        ];

    list_commissions_table = $('#list_commissions_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@listCommission',[$id])}}',
            data: function(d) {
                d.type = $('select#type').val();
                
                d.start_date = $('input#date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columns: columns,
    });

    $('#type').change(function(){
        list_commissions_table.ajax.reload();
    });
});


</script>
@endsection
