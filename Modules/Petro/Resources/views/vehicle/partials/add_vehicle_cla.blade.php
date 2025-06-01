<div class="pos-tab-content ">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-4">
            <div class="form-group">
            {!! Form::label('type', __('product.product_type') . ':') !!}
            {!! Form::text('filter_classification_by_date', null, ['class' => 'form-control date_picker', 'style' => 'width:100%', 'id' =>
            'filter_classification_by_date', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

        
        @endcomponent
        </div>
    </div>
    <div class="row">
    {!! Form::open(['action' => '\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@update', 'method' =>
    'put' , 'id' => 'setting_form', 'enctype' => 'multipart/form-data']) !!}

        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('vehicle_classification', __('vehicle.vehicle_classification') . ':') !!}
                <div class="input-group">
                    {!! Form::text('vehicle_classification', null, ['class' =>
                    'form-control','placeholder' => __('vehicle.vehicle_classification'), 'id' => 'vehicle_classification']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <button class="btn btn-primary" type="submit" style="margin-top: 22px;" id="add_vehicle_classification">@lang('messages.add')</button>
        </div>
        {!! Form::close() !!}
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('vehicle.all_vehicle_categories')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="vehicle_classification_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('vehicle.vehicle_classification')</th>
                    <th>@lang('vehicle.date_time')</th>

                    <th>@lang( 'lang_v1.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</div>
