<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('list_filter_location_id', __('purchase.business_location') . ':') !!}

        {!! Form::select('list_filter_location_id', $business_locations, null, ['class' => 'form-control select2',
        'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('list_filter_customer_id', __('contact.customer') . ':') !!}
        {!! Form::select('list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style'
        => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('list_filter_status', __('tpos.status') . ':') !!}
        {!! Form::select('list_filter_status', ['pending' => __('lang_v1.pending'), 'completed' => __('tpos.completed')], null, ['class' => 'form-control
        select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('list_filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
        => 'form-control', 'readonly']); !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('tpos_no', __('tpos.tpos_no') . ':') !!}
        {!! Form::select('tpos_no', $tpos_no, null, ['class' => 'form-control select2', 'style' =>
        'width:100%','placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('fpos_no', __('tpos.fpos_no') . ':') !!}
        {!! Form::select('fpos_no', $fpos_no, null, ['class' => 'form-control select2', 'style' =>
        'width:100%','placeholder' => __('lang_v1.all')]); !!}
    </div>
</div>

