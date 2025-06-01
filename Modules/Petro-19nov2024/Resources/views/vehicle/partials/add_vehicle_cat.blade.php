<div class="pos-tab-content ">
    <div class="row">
    {!! Form::open(['action' => '\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@update', 'method' =>
    'put' , 'id' => 'setting_form', 'enctype' => 'multipart/form-data']) !!}

        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('vehicle_category', __('vehicle.vehicle_category') . ':') !!}
                <div class="input-group">
                    {!! Form::text('vehicle_category', null, ['class' =>
                    'form-control','placeholder' => __('vehicle.vehicle_category'), 'id' => 'vehicle_category']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <button class="btn btn-primary" type="submit" style="margin-top: 22px;" id="add_vehicle_category">@lang('messages.add')</button>
        </div>
        {!! Form::close() !!}
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('vehicle.all_vehicle_categories')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="vehicle_category_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('vehicle.vehicle_category')</th>
                    <th>@lang( 'lang_v1.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</div>
