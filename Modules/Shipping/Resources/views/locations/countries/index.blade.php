<!-- Main content -->
<section class="content">

    @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.countries')])

        @slot('tool')

            <button type="button" class="btn  btn-primary btn-modal pull-right"

                    data-href="{{action('\Modules\Shipping\Http\Controllers\LocationsController@addCountry')}}"

                    data-container=".countries">

                <i class="fa fa-flag"></i> @lang('shipping::lang.add_country')</button>



        @endslot

        <div class="col-md-12">

            <div class="row" style="margin-top: 20px;">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped" id="countries_table" style="width: 100% !important;">

                        <thead>

                        <tr>

                            <th>@lang('shipping::lang.country')</th>

                            <th>@lang('shipping::lang.country_code')</th>

                            <th>@lang('shipping::lang.currency_code')</th>

                        </tr>

                        </thead>

                    </table>

                </div>

            </div>

        </div>

    @endcomponent

    <div class="modal fade countries" role="dialog" size="modal-md" aria-labelledby="gridSystemModalLabel">

    </div>

</section>
