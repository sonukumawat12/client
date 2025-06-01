<div class="row">

<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('airports_filter_date_range', __('airline::lang.date_range') . ':') !!}
        {!! Form::text('airports_filter_date_range', null, [
            'placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control',
            'readonly',
        ]) !!}
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('airports_filter_country_select', __('airline::lang.country_select') . ':') !!}
        {!! Form::select('airports_filter_country_select', [], null, [
            'class' => 'form-control select2',
            'id' => 'airports_filter_country_select',
            'placeholder' => __('messages.please_select'),
            'onchange' => 'airport_module.airport_table.ajax.reload()',
        ]) !!}
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('airports_filter_province_select', __('airline::lang.province_select') . ':') !!}
        {!! Form::select('airports_filter_province_select', [], null, [
            'class' => 'form-control',
            'id' => 'airports_filter_province_select',
            'placeholder' => __('airline::lang.province'),
            'onchange' => 'airport_module.airport_table.ajax.reload()',
        ]) !!}
        {{-- {!! Form::text('airports_filter_province_select', null, [
            'class' => 'form-control',
            'id' => 'airports_filter_province_select',
            'placeholder' => __('airline::lang.province'),
            'onchange' => 'airport_module.airport_table.ajax.reload()',
        ]) !!} --}}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('airports_filter_airport_name', __('airline::lang.airport_name') . ':') !!}

        {!! Form::select('airports_filter_airport_name', [], null, [
            'class' => 'form-control',
            'id' => 'airports_filter_airport_name',
            'placeholder' => __('airline::lang.enter_airport_name'),
            'onchange' => 'airport_module.airport_table.ajax.reload()',
        ]) !!}
    </div>
</div>
</div>
