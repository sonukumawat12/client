@extends('layouts.app')
@section('title', __('shipping::lang.agent'))

@section('content')
<style>
    .select2{
        width: 100% !important;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('lang_v1.ledger') }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-3 col-xs-12">
            {!! Form::label('airline_agent', __('airline::lang.agent') . ':') !!}
            {!! Form::select('fleet_id', $driver_dropdown, $driver->id , ['class' => 'form-control select2', 'id' =>
            'fleet_id']); !!}
        </div>
        <div class="col-md-3 col-xs-12">
            <div class="form-group">
                {!! Form::label('agent_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('agent_date_range_filter', @format_date('first day of this year') . ' ~ ' .
                @format_date('last
                day of this year') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'agent_date_range_filter', 'readonly']); !!}
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12" id="contact_ledger_div">
            
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        updateLedger();
    })
    
     $('#fleet_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/airline/agents')}}/" + $(this).val()+"?tab={{$view_type}}";
        }
    });
    
    if ($('#agent_date_range_filter').length == 1) {
       $('#agent_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#agent_date_range_filter').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );

            updateLedger();
        });
        $('#agent_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#agent_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('year'));
        $('#agent_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('year'));
    }
    
    
    function updateLedger(){
        var start_date = '';
        var end_date = '';
        
        if($('#agent_date_range_filter').val()) {
            start_date = $('#agent_date_range_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            end_date = $('#agent_date_range_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        }
    
        $.ajax({
        url: '/airline/agents/get-ledger/{{$id}}?start_date=' + start_date + '&end_date=' + end_date,
        dataType: 'html',
        success: function(result) {
                $('#contact_ledger_div')
                    .html(result);
                __currency_convert_recursively($('#contact_ledger_div'));
    
                $('#ledger_table').DataTable({
                    searching: true,
                    ordering:false,
                    paging:true,
                    // dom: 't'
                });
            },
        });
    }
    
</script>
    
@endsection