<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f15a9ab_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('f15a9ab_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_15a9ab_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_15a9ab_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.from_no') . ':') !!}
                    {!! Form::text('F15a9ab_from_no', $F15a9ab_from_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 text-red" style="margin-top: 14px;">
                        <b>@lang('petro::lang.date_range'): <span class="15a9ab_from_date"></span> @lang('petro::lang.to') <span class="15a9ab_to_date"></span> </b>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                                <span class="f15a9ab_location_name">@lang('petro::lang.all')</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.9b_form') @lang('mpcs::lang.form_no') : {{$F15a9ab_from_no}}</h5>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <h4>@lang('mpcs::lang.sales_status_section')</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="form_9b_table">
                                        <thead>
                                            <tr>
                                                <th>@lang('mpcs::lang.description')</th>
                                                <td>Petrol/Premium</td>
                                                <td>Diesel/Mile</td>
                                                <td>Loose Oil</td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td>Sub total</td>
                                                <td>Official use only</td>
                                                <td>After check</td>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Cash for the date</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td rowspan="2">................. Receive date</td>
                                                <th>Excess Cash</th>
                                            </tr>
                                            <tr>
                                                <th>Up to previous day cash</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Per date ...................</td>
                                            </tr>
                                            <tr>
                                                <th>Total cash</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>..........</td>
                                                <td>Per date ......................</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Given</td>
                                                <td>Today ......................</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>...............</td>
                                                <th>Excess Stamp</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Checked</td>
                                                <td>Per date ................</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>..........</td>
                                                <td>Per date ......................</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Approved</td>
                                                <td>Today ......................</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="table-responsive col-md-6">
                                    <table class="table table-bordered table-striped" id="form_15a9ab_table">
                                        <thead>
                                            <tr>
                                                <th colspan="1000">
                                                    <h4 class="text-center">Receipts</h4>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Up to Previous date</th>
                                                <th>Description</th>
                                                <th>Today</th>
                                                <th>Today's subtotal</th>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive col-md-6">
                                    <table class="table table-bordered table-striped" id="form_15a9ab_table">
                                        <thead>
                                            <tr>
                                                <th colspan="1000">
                                                    <h4 class="text-center">Payments</h4>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Up to Previous date</th>
                                                <th>Description</th>
                                                <th>Today</th>
                                                <th>Today's subtotal</th>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->