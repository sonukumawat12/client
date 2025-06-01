@extends('layouts.app')

@section('content')
    <section class="content-header main-content-inner">
        <div class="row">

            <div class="col-md-12 dip_tab">

                <div class="settlement_tabs">

                    <ul class="nav nav-tabs">

                        <li class="active" style="margin-left: 20px;">

                            <a style="font-size:13px;" href="#countries" class="" data-toggle="tab">

                                <i class="fa fa-file-o"></i> <strong>@lang('shipping::lang.countries')</strong>

                            </a>

                        </li>
                        <li class="">
                                <a style="font-size:13px;" href="#provinces" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('shipping::lang.provinces')</strong>

                                </a>

                            </li>
                        <li class="">
                                <a style="font-size:13px;" href="#districts" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('shipping::lang.districts')</strong>

                                </a>

                        </li>
                        <li class="">
                                <a style="font-size:13px;" href="#areas" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('shipping::lang.areas')</strong>

                                </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="countries">
                @include('shipping::locations.countries.index')
            </div>
            <div class="tab-pane" id="provinces">
                @include('shipping::locations.provinces.index')
            </div>
            <div class="tab-pane" id="districts">
                @include('shipping::locations.districts.index')
            </div>
            <div class="tab-pane" id="areas">
               @include('shipping::locations.areas.index')
            </div>
        </div>
        <div class="modal fade dip_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@endsection
@push('javascript')

    <script type="text/javascript">
        $(document).ready( function(){
            $('#countries_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],

                ajax: "{{ action('\Modules\Shipping\Http\Controllers\LocationsController@countries') }}",
                
                columns: [
                    { data: 'country', name: 'country' },
                    { data: 'country_code', name: 'country_code' },
                    { data: 'currency_code', name: 'currency_code' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
            $('#provinces_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],

                ajax: "{{ action('\Modules\Shipping\Http\Controllers\LocationsController@provinces') }}",
                
                columns: [
                    { data: 'country', name: 'countries.country' },
                    { data: 'province', name: 'provinces.name' },
                    { data: 'created_at', name: 'created_at' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
            $('#district_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],

                ajax: "{{ action('\Modules\Shipping\Http\Controllers\LocationsController@districts') }}",
                
                columns: [
                    { data: 'country', name: 'countries.country' },
                    { data: 'province', name: 'provinces.name' },
                    { data: 'district', name: 'districts.name' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
            $('#area_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],
                ajax: "{{ action('\Modules\Shipping\Http\Controllers\LocationsController@areas') }}",
                
                columns: [
                    { data: 'country', name: 'countries.country' },
                    { data: 'province', name: 'provinces.name' },
                    { data: 'district', name: 'districts.name' },
                    { data: 'area', name: 'area' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
        })
    </script>

    @endpush
