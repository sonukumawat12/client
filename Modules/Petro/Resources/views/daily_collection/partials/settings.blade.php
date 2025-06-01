<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 25px">
                    <li><a href="#">@lang('petro::lang.settings')</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row mb-4">
        <div class="col-md-12">
            @component('components.filters', ['title' => 'Add New Settings'])
            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}
            {!! Form::open(['url' =>'save-settings', 'method' => 'post' , 'id' => 'save_settings_form']) !!} 
            {{-- {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCollectionController@saveSettings'), 'method' => 'post' ]) !!}  --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('day_counted_from', __('petro::lang.day_counted_from').':') !!}<br>
                        {!! Form::time('day_counted_from', null, ['class' => 'form-control', 'id' => 'day_counted_from']) !!}

                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('time_till', __('petro::lang.time_till') . ':') !!}
                        {!! Form::time('time_till', null, ['class' => 'form-control', 'id' => 'time_till']) !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ending_date_type', __('petro::lang.day_counted_from').':') !!}<br>
                        {!! Form::select('ending_date_type', 
                            [
                                '' => __('petro::lang.day_counted_from'), // This acts as placeholder
                                'same_day' => 'Same Day', 
                                'following_day' => 'Following Day'
                            ],
                            null, 
                            [
                                'class' => 'form-control select2', 
                                'style' => 'width:100%',
                                'required' => true
                            ]
                        ) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {{-- {!! Form::label('ending_date_type', __('petro::lang.day_counted_from').':') !!} <br> --}}
                        {!! Form::label('ending_date_type', 'Submit Changes ... :') !!} <br>
                        <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                    </div>
                </div>

            </div>
            {!! Form::close() !!}

            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.settings')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="settings_table" width="100%">
            <thead>
                <tr>
                    <th>@lang('petro::lang.action')</th>
                    <th>@lang('petro::lang.start_time')</th>
                    <th>@lang('petro::lang.time_till')</th>
                    <th>@lang('petro::lang.day_counted_from')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.user_entered' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    {{-- <div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div id="daily_card_print"></div> --}}

</section>
