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
        {!! Form::label('product_id', __('dsr::lang.product') . ':') !!}
        {!! Form::select('product_id', $products, null, ['class' => 'form-control select2', 'id' => 'product_id','placeholder' => __('lang_v1.all'), 'style' => 'width:100%']); !!}

    </div>
</div>
</div>
