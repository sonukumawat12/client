@extends('layouts.app')
@section('title', __('autorepairservices::lang.vehicle_brand'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('autorepairservices::lang.vehicle_brand')
        <small>@lang( 'autorepairservices::lang.manage_your_vehicle_brands' )</small>
    </h1>
</section>

<!-- Main content -->
<div id="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'autorepairservices::lang.all_your_vehicle_brands' )])
        @can('vehicle_brand.create')
            @slot('tool')
                <div class="box-tools">
                    <a href="#" class="btn btn-block btn-primary btn-modal"
                            data-toggle="modal" data-target="#addBrandService" >
                            <i class="fa fa-plus"></i>
                            <span class="ul-btn__text">@lang( 'autorepairservices::lang.add' )</span>
                        </a>
                </div>
            @endslot
        @endcan
        @can('brand.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped dt">
                    <thead>
                        <tr>
                            <th>@lang( 'autorepairservices::lang.vehicle_brand' )</th>
                            <th>@lang( 'autorepairservices::lang.vehicle_type' )</th>
                            <th>@lang( 'autorepairservices::lang.vehicle_model' )</th>
                            <th>@lang( 'autorepairservices::lang.chassis_no' )</th>
                            <th>@lang( 'autorepairservices::lang.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</div>
<!-- /.content -->
@endsection
@include('autorepairservices::vehiclebrand.modals.add')
@include('autorepairservices::vehiclebrand.js.index')
