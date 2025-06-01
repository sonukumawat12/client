<!-- Main content -->
<section class="content">

    @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.provinces')])

        @slot('tool')

            <button type="button" class="btn  btn-primary btn-modal pull-right"

                    data-href="{{action('\Modules\Shipping\Http\Controllers\LocationsController@addProvince')}}"

                    data-container=".provinces">

                <i class="fa fa-flag"></i> @lang('shipping::lang.add_province')</button>

        @endslot

            <div class="col-md-12">

            <div class="row" style="margin-top: 20px;">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped" id="provinces_table" style="width: 100% !important;">

                        <thead>

                        <tr>

                            <th>@lang('shipping::lang.country')</th>

                            <th>@lang('shipping::lang.province')</th>

                            <th>@lang('shipping::lang.created_at')</th>

                        </tr>

                        </thead>

                    </table>

                </div>

            </div>

            </div>


    @endcomponent

    <div class="modal fade provinces" role="dialog" size="modal-md" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

