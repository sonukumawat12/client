<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('chart_tank_id', __('petro::lang.tanks') . ':') !!}
                    {!! Form::select('chart_tank_id', $tanks, null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'chart_tank_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.dip_chart')])
    @can('add_dip_resetting')
    @slot('tool')
    
    @can('dipmanagement.add_dip_chart')
    <button type="button" class="btn  btn-primary btn-modal pull-right"
                data-href="{{action('\Modules\Petro\Http\Controllers\DipManagementController@addDipChart')}}"
                data-container=".dip_modal">
                <i class="fa fa-balance-scale"></i> @lang('petro::lang.add_dip_chart')</button>
    @endcan
  
    @endslot
    @endcan
    <div class="col-md-12">
        
        <div class="row" style="margin-top: 20px;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dip_chart_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('petro::lang.date_time')</th>
                            <th>@lang('petro::lang.sheet_name')</th>
                            <th>@lang('petro::lang.tank_name')</th>
                            <th>@lang('petro::lang.tank_manufacturer')</th>
                            <th>@lang('petro::lang.tank_manufacturer_contact')</th>
                            <th>@lang('petro::lang.tank_capacity')</th>
                            <th>@lang('petro::lang.dip_reading')</th>
                            <th>@lang('petro::lang.dip_reading_lts')</th>
                            <th>@lang('petro::lang.user_added')</th>
                            <th>@lang('petro::lang.action')</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endcomponent

</section>
<!-- /.content -->