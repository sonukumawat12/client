@extends('layouts.app')

@section('content')
    <section class="content-header main-content-inner">
        <div class="row">

            <div class="col-md-12 dip_tab">

                <div class="settlement_tabs">

                    <ul class="nav nav-tabs">

                        <li class="active" style="margin-left: 20px;">

                            <a style="font-size:13px;" href="#countries" class="" data-toggle="tab">

                                <i class="fa fa-file-o"></i> <strong>@lang('dsr::lang.countries')</strong>

                            </a>

                        </li>
                        <li class="">
                                <a style="font-size:13px;" href="#provinces" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('dsr::lang.provinces')</strong>

                                </a>

                            </li>
                        <li class="">
                                <a style="font-size:13px;" href="#districts" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('dsr::lang.districts')</strong>

                                </a>

                        </li>
                        <li class="">
                                <a style="font-size:13px;" href="#areas" data-toggle="tab">

                                    <i class="fa fa-gear"></i> <strong>@lang('dsr::lang.areas')</strong>

                                </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="countries">
                @include('dsr::locations.countries.index')
            </div>
            <div class="tab-pane" id="provinces">
                @include('dsr::locations.provinces.index')
            </div>
            <div class="tab-pane" id="districts">
                @include('dsr::locations.districts.index')
            </div>
            <div class="tab-pane" id="areas">
               @include('dsr::locations.areas.index')
            </div>
        </div>
        <div class="modal fade dip_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@endsection
@push('javascript')

    <script type="text/javascript">
        $(document).ready( function(){
            $(()=>{
                $('.select2').select2();
            })
            $('#countries_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],

                ajax: "{{ action('\Modules\Dsr\Http\Controllers\LocationsController@countries') }}",
                columnDefs: [{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
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

                ajax: "{{ action('\Modules\Dsr\Http\Controllers\LocationsController@provinces') }}",
                columnDefs: [{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    { data: 'country', name: 'country' },
                    { data: 'province', name: 'province' },
                    { data: 'created_at', name: 'created_at' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
            $('#district_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],

                ajax: "{{ action('\Modules\Dsr\Http\Controllers\LocationsController@districts') }}",
                columnDefs: [{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    { data: 'country', name: 'country' },
                    { data: 'province', name: 'province' },
                    { data: 'district', name: 'district' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
            $('#area_table').DataTable({
                processing: true,
                serverSide: false,
                buttons: [],
                ajax: "{{ action('\Modules\Dsr\Http\Controllers\LocationsController@areas') }}",
                columnDefs: [{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    { data: 'country', name: 'country' },
                    { data: 'province', name: 'province' },
                    { data: 'district', name: 'district' },
                    { data: 'area', name: 'area' },
                ],
                "fnDrawCallback": function(oSettings) {
                }
            });
        })
    </script>

    @endpush
