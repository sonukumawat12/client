<div class="pos-tab-content">
    {{-- <style>
        .select2-results__option[aria-selected="true"] {
            display: none;
        }

        .equal-column {
            min-height: 95px;
        }
    </style> --}}
    <!-- Main content -->


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
                @include('superadmin::superadmin_settings.locations.countries.index')
            </div>
            <div class="tab-pane" id="provinces">
                @include('superadmin::superadmin_settings.locations.provinces.index')
            </div>
            <div class="tab-pane" id="districts">
                @include('superadmin::superadmin_settings.locations.districts.index')
            </div>
            <div class="tab-pane" id="areas">
               @include('superadmin::superadmin_settings.locations.areas.index')
            </div>
        </div>
        
</div>