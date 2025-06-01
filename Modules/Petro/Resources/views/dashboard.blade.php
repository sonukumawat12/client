@extends('layouts.app')
@section('title', __('petro::lang.dashboard'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        @foreach($fuel_tanks as $key => $tank)
        <div class="col-md-4 text-center" style="margin-bottom: 40px;">
            <div id="chart_div_{{$key}}" style=" width: auto;  margin: 0 auto !important; display: inline-block;"></div>
            <div class="clearfix"></div>
            <h4>{{$tank->fuel_tank_number}}</h4>
            @php
                $currentDate = new DateTime('now');
                $tomorrowDate = $currentDate->modify('+1 day');
                $tomorrowDateString = $tomorrowDate->format('Y-m-d');
                
                $moduleUtil = new \App\Utils\ModuleUtil();
                $contactUtil = new \App\Utils\ContactUtil();
                
                $transactionUtil = new App\Utils\TransactionUtil($moduleUtil,$contactUtil);
                $balance = $transactionUtil->getTankBalanceByDateInclude($tank->id,$tomorrowDateString);
            @endphp
            <p>@lang('petro::lang.current_balance'): {{@num_format($balance)}}</p>

            <p>@lang('petro::lang.storage_volume'): {{@num_format((float)$tank->storage_volume ?? 0)}}</p>

        </div>
        @endforeach
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@foreach($fuel_tanks as $key => $tank)
@php
$balance = 0;
    $currentDate = new DateTime('now');
    $tomorrowDate = $currentDate->modify('+1 day');
    $tomorrowDateString = $tomorrowDate->format('Y-m-d');

    $moduleUtil = new \App\Utils\ModuleUtil();
    $contactUtil = new \App\Utils\ContactUtil();
    $transactionUtil = new App\Utils\TransactionUtil($moduleUtil,$contactUtil);
    $balance = $transactionUtil->getTankBalanceByDateInclude($tank->id,$tomorrowDateString);
@endphp
<script type="text/javascript">
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);
        google.charts.load('current', {
        'packages': ['gauge']
        });
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['', <?php 
                $storage_volume = (float)$tank->storage_volume;
                $balance_value = (float)$balance;
                echo ($storage_volume > 0) ? round(($balance_value / $storage_volume) * 100) : 0;
            ?>],
        ]);

        var options = {
            width: 400,
            height: 200,
            redFrom: 0,
            redTo: 35,
            yellowFrom: 35,
            yellowTo: 70,
            minorTicks: 5
        };
        var chart = new google.visualization.Gauge(document.getElementById('chart_div_{{$key}}'));
        chart.draw(data, options);
        }
        
</script>
@endforeach
@endsection