@php

use Modules\Distribution\Entities\Distribution_provinces;
use Modules\Distribution\Entities\Distribution_districts;
use Modules\Distribution\Entities\Distribution_areas;


$business_id = request()->session()->get('user.business_id');

$provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');
$districts = Distribution_districts::leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')->where('distribution_provinces.business_id', $business_id)->pluck('distribution_districts.name', 'distribution_districts.id', 'distribution_districts.province_id');
$areas = Distribution_areas::leftjoin('distribution_districts','distribution_areas.district_id','distribution_districts.id')
        ->leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')
        ->leftjoin('users', 'distribution_provinces.added_by', 'users.id')
        ->where('distribution_provinces.business_id', $business_id)
        ->pluck('distribution_areas.name','distribution_areas.id','distribution_areas.district_id');


@endphp






@extends('layouts.app')
@section('title', __('distribution::lang.settings'))

<style>
    .select2 {
        width: 100% !important;
    }
</style>
@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#provinces" id="provinces-link" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>Provinces</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'districts') active @endif">
                        <a style="font-size:13px;" href="#districts" id="districts-link" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>Districts</strong>
                        </a>
                    </li>

                    <li class=" @if(session('status.tab') == 'areas') active @endif">
                        <a style="font-size:13px;" href="#areas"  id="areas-link" data-toggle="tab">
                            <i class="fa fa-user-secret"></i> <strong>Areas</strong>
                        </a>
                    </li>
                    
                    <li class=" @if(session('status.tab') == 'routes') active @endif">
                        <a style="font-size:13px;" href="#routes" id="routes-link" data-toggle="tab">
                            <i class="fa fa-user-secret"></i> <strong>Routes</strong>
                        </a>
                    </li>

                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="provinces">
            @include('distribution::settings.provinces.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'districts') active @endif" id="districts">
            @include('distribution::settings.districts.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'areas') active @endif" id="areas">
             @include('distribution::settings.areas.index')
        </div>
        
        <div class="tab-pane  @if(session('status.tab') == 'routes') active @endif" id="routes">
             @include('distribution::settings.routes.index')
        </div>

    </div>
</section>

@endsection


@section('javascript')

<script>
    
    // select provinces
    $(document).ready(function () {
        var page = "{{session('page')}}";
        
        if(page != ""){
            var page = "#"+page+"-link";
            console.log(page);
            $(page).click();
            
        }else{
            $("#provinces").click();
        }
        
        $(".select2").select2();
        provinces_table = $('#provinces_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action("\Modules\Distribution\Http\Controllers\DistributionProvincesController@index")}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'added_by', name: 'added_by' },
                { data: 'created_at', name: 'created_at' }
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        
        // select districts
        districts_table = $('#districts_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action("\Modules\Distribution\Http\Controllers\DistributionDistrictsController@index")}}',
                data: function (d) {
                    d.province_id = $('#districts_province_id').val()
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'province_name', name: 'province_name' },
                { data: 'added_by', name: 'added_by' },
                { data: 'date', name: 'date' }
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        
        // select areas
        areas_table = $('#areas_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action("\Modules\Distribution\Http\Controllers\DistributionAreasController@index")}}',
                data: function (d) {
                    d.province_id = $('#area_province_id').val()
                    d.district_id = $('#area_district_id').val()
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'name', name: 'name' },
                { data: 'district_name', name: 'district_name' },
                { data: 'province_name', name: 'province_name' },
                { data: 'added_by', name: 'added_by' },
                { data: 'date', name: 'date' }
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        // select routes
        routes_table = $('#routes_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action("\Modules\Distribution\Http\Controllers\DistributionRoutesController@index")}}',
                data: function (d) {
                    d.province_id = $('#route_province_id').val()
                    d.district_id = $('#route_district_id').val()
                    d.area_id = $('#route_area_id').val()
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'route_no', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'district_name', name: 'distribution_districts.name' },
                { data: 'province_name', name: 'distribution_provinces.name' },
                { data: 'area_name', name: 'distribution_areas.name' },
                { data: 'user_names', name: 'users.username' },
                { data: 'date', name: 'distribution_routes.created_at' }
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        $('#districts_province_id').change(function () {
            console.log("districts");
            districts_table.ajax.reload();
        })
        
        $('#area_province_id,#area_district_id').change(function () {
            console.log("areas");
            areas_table.ajax.reload();
        })
        
        $('#route_province_id,#route_district_id,#route_area_id').change(function () {
            console.log("routes");
            routes_table.ajax.reload();
        })
        
        
        
    })

   

    $(document).on('click', 'a.delete_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        provinces_table.ajax.reload();
                        districts_table.ajax.reload();
                        areas_table.ajax.reload();
                        routes_table.ajax.reload();
                    },
                });
            }
        });
    });
    
    
    



</script>
@endsection