<div class="pos-tab-content @if(session('status.tank_dip_chart')) active @endif">
    <!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mbt_sheet_name', __('superadmin::lang.sheet_name') . ':') !!}
                    {!! Form::select('mbt_sheet_name', $mbt_sheet_names, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'mbt_sheet_name', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mbt_tank_manufacturer', __('superadmin::lang.tank_manufacturer') . ':') !!}
                    {!! Form::select('mbt_tank_manufacturer', $mbt_tank_manufacturers, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'mbt_tank_manufacturer', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mbt_business_id', __('superadmin::lang.business') . ':') !!}
                    {!! Form::select('mbt_business_id', $businesses, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'mbt_business_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
           
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'superadmin::lang.tank_dip_chart')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" data-container=".tank_dip_chart_model"
                    data-href="{{action('\Modules\Superadmin\Http\Controllers\MapBusinessTankController@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                </button> &nbsp;
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="map_business_tanks_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'superadmin::lang.business' )</th>
                                <th>@lang( 'superadmin::lang.sheet_name' )</th>
                                <th>@lang( 'superadmin::lang.tank_manufacturer' )</th>
                                <th>@lang( 'superadmin::lang.tank_capacity' )</th>
                                <th>@lang( 'superadmin::lang.user_added' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                            
                    </table>

                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
</div>