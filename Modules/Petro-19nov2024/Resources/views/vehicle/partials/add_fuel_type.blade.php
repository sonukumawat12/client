<div class="pos-tab-content active">
    <div class="row">
    {!! Form::open(['action' => '\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@update', 'method' =>
    'put' , 'id' => 'setting_form', 'enctype' => 'multipart/form-data']) !!}
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('fuel_type', __('vehicle.fuel_type') . ':') !!}
                {!! Form::text('fuel_type',  null, ['id' => 'fuel_type', 'class' => 'form-control', 'placeholder' => __('vehicle.fuel_type') ]); !!}
            </div>
        </div>
        <div class="col-xs-2">
            <div class="form-group">
                {!! Form::label('fuel_sub_type', __('vehicle.fuel_sub_type') . ':') !!}
                <div class="input-group">
                    {!! Form::text('fuel_sub_type', null, ['class' =>
                    'form-control','placeholder' => __('vehicle.fuel_sub_type'), 'id' => 'fuel_sub_typeegory']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <button class="btn btn-primary" type="submit" style="margin-top: 22px;" id="add_fuel_type">@lang('messages.add')</button>
        </div>
        {!! Form::close() !!}
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('vehicle.all_fuel_types')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="fuel_types_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang( 'vehicle.fuel_type' )</th>
                    <th>@lang( 'vehicle.fuel_sub_type' )</th>
                    <th>@lang( 'lang_v1.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</div>
