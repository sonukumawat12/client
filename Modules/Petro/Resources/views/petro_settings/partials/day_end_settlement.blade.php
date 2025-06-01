<!-- Main content -->

<section class="content main-content-inner">

    <div class="row">

        <div class="col-md-12">

            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">

                <div class="form-group">

                    {!! Form::label('daily_date_range', __('report.date_range') . ':') !!}

                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' .

                    @format_date('last

                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>

                    'form-control', 'id' => 'date_range', 'readonly']); !!}

                </div>

            </div>

            @endcomponent

        </div>

    </div>



    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.day_end_settlement')])

    @slot('tool')
    
    @can('add_day_end_settlement')
    <button type="button" class="btn  btn-primary btn-modal pull-right"

    data-href="{{action('\Modules\Petro\Http\Controllers\DayEndSettlementController@create')}}"

    data-container=".dip_modal">

    <i class="fa fa-plus"></i> @lang('petro::lang.add')</button>
    @endcan

    @endslot

    <div class="table-responsive">

                <table class="table table-bordered table-striped" id="day_end_settlement_table" width="100%">

                    <thead>

                        <tr>
                            
                            <th>@lang('petro::lang.action')</th>

                            <th>@lang('petro::lang.time_and_date')</th>
                            
                            <th>@lang('petro::lang.day_end_date')</th>

                            <th>@lang('petro::lang.no_operation')</th>
                            
                            <th>@lang('petro::lang.pumps_in_settlement')</th>

                            <th>@lang('petro::lang.user_added')</th>

                            <th>@lang('petro::lang.user_editted')</th>

                        </tr>

                    </thead>

                    
                   
                   

                </table>

            </div>

    @endcomponent



    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>

<!-- /.content -->