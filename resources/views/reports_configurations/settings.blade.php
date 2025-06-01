@extends('layouts.app')
@section('title', __('reports_configurations.reports_configurations'))
@php
$business_or_entity = App\System::getProperty('business_or_entity');

@endphp
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('reports_configurations.reports_configurations') }} </h1>
    
</section>
<style>
    .select2-results__option[aria-selected="true"] {
        display: none;
    }

    .equal-column {
        min-height: 95px;
    }
</style>
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('ReportConfigurationsController@store'), 'method' => 'post',
    'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
            <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('reports_configurations.customer_statement_report')</a>
                 
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <!-- tab 1 start -->
                    @include('reports_configurations.partials.customer_statement_report')
                    <!-- tab 1 end -->
                    
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button"
                type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    
</script>
@endsection