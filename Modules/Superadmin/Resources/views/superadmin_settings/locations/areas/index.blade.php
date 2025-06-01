<!-- Main content -->
<section class="content">

    @component('components.widget', ['class' => 'box-primary', 'title' => __('dsr::lang.areas')])

        @slot('tool')

            <button type="button" class="btn  btn-primary btn-modal pull-right"

                    data-href="{{action('\Modules\Superadmin\Http\Controllers\LocationsController@addarea')}}"

                    data-container=".areas">

                <i class="fa fa-flag"></i> @lang('dsr::lang.add_areas')</button>

        @endslot
            <div class="col-md-12">

            <div class="row" style="margin-top: 20px;">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped" id="area_table" style="width: 100% !important;">

                        <thead>

                        <tr>

                            <th>@lang('dsr::lang.country')</th>

                            <th>@lang('dsr::lang.province')</th>

                            <th>@lang('dsr::lang.district')</th>

                            <th>@lang('dsr::lang.area')</th>

                        </tr>

                        </thead>

                    </table>

                </div>

            </div>

            </div>

    @endcomponent
</section>

