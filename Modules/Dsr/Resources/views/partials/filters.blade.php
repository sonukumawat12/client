<div class="row" >
<div class="col-sm-3">
    <div class="form-group">

        {!! Form::label('date_range', __('report.date_range') . ':') !!}

        {!! Form::text('report_date_range', @format_date('first day of this month') . ' ~ ' .

        @format_date('last

        day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>

        'form-control', 'id' => 'report_date_range', 'readonly']); !!}

    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('country_id', __('dsr::lang.country') . ':') !!}
        {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'id' => 'country_id', 'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('province_id', __('dsr::lang.province') . ':') !!}
        {!! Form::select('province_id[]', [], null, ['class' => 'form-control select2','multiple', 'id' => 'province_id', 'style' => 'width:100%']); !!}
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('district_id', __('dsr::lang.district') . ':') !!}
        {!! Form::select('district_id[]', [], null, ['class' => 'form-control
        select2',
        'id' => 'district_id','multiple', 'style' => 'width:100%']); !!}
    </div>
</div>
</div>

<div class="row" >
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('area_id', __('dsr::lang.area') . ':') !!}
        {!! Form::select('area_id[]', [], null, ['class' => 'form-control select2','multiple', 'id' => 'area_id', 'style' => 'width:100%']); !!}
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('dealer_id', __('dsr::lang.dealers') . ':') !!}
        {!! Form::select('dealer_id[]', [], null, ['class' => 'form-control select2','multiple', 'id' => 'dealer_id', 'style' => 'width:100%']); !!}

    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('product_id', __('dsr::lang.product') . ':') !!}
        {!! Form::select('product_id', [], null, ['class' => 'form-control select2', 'id' => 'product_id', 'style' => 'width:100%']); !!}

    </div>
</div>
</div>
