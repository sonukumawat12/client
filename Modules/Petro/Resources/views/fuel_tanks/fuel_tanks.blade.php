<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('fueltanks_tank_number', __('petro::lang.fuel_tank_number') . ':') !!}
                        {!! Form::select('fueltanks_tank_number', $tank_numbers, null, ['class' => 'form-control
                        select2 daily_report_change',
                        'placeholder' => __('petro::lang.all'), 'id' => 'fueltanks_tank_number', 'style' =>
                        'width:100%']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('fueltanks_tank_location_id', __('petro::lang.location') . ':') !!}
                        {!! Form::select('fueltanks_tank_location_id', $business_locations, null, ['class' => 'form-control
                        select2 ', 'id' => 'fueltanks_location_id', 'style' =>
                        'width:100%']); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    
    
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_fuel_tanks')])
    @slot('tool')
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary pull-right btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
        <a class="btn  btn-danger"
                href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@import')}}">
                <i class="fa fa-download "></i> @lang('petro::lang.import')</a> &nbsp;
    </div>
    <hr>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="fuel_tanks_table" width="100%">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.fuel_tank_number')</th>
                    <th>@lang('petro::lang.product_name')</th>
                    <th>@lang('petro::lang.storage_volume')</th>
                    <th>@lang('petro::lang.current_balance')</th>
                    <th>@lang('petro::lang.bulk_tank')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->