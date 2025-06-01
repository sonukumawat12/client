@php
    $business_type = array('client' => __('superadmin::lang.client'), 'business' => __('superadmin::lang.business'));
@endphp

@component('components.filters', ['title' => __('report.filters')])
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range', __('report.date_range') . ':') !!}
                {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                => 'form-control', 'readonly']); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('filter_business_id', __('superadmin::lang.business') . ':') !!}
                
                <select class="form-control select2"  id="filter_business_id" style = 'width:100%'>
                    <option value="">@lang('lang_v1.all')</option>
                    @foreach($business as $one)
                        <option value="{{$one->id}}" data-string="{{$one->type}}">{{$one->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('filter_package_id', __('superadmin::lang.package') . ':') !!}
                {!! Form::select('filter_package_id', $packages, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('filter_payment_method', __('superadmin::lang.payment_method') . ':') !!}
                {!! Form::select('filter_payment_method', $payment_methods, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('filter_created_by', __('superadmin::lang.user_added') . ':') !!}
                {!! Form::select('filter_created_by', $users, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('filter_type', __('superadmin::lang.type') . ':') !!}
                {!! Form::select('filter_type', $business_type, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
    </div>
@endcomponent


@component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.refill_business' )])
@slot('tool')
<div class="box-tools pull-right">
    <button type="button" class="btn btn-primary btn-modal"
        data-href="{{action('\Modules\Superadmin\Http\Controllers\RefillBusinessController@create')}}"
        data-container=".packages_modal">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
</div>
@endslot

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="refill_business_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang( 'superadmin::lang.date' )</th>
                <th>@lang( 'superadmin::lang.business_type' )</th>
                <th>@lang( 'superadmin::lang.business_client' )</th>
                <th>@lang( 'superadmin::lang.package_name' )</th>
                <th>@lang( 'superadmin::lang.amount' )</th>
                <th>@lang( 'superadmin::lang.no_of_sms' )</th>
                <th>@lang( 'superadmin::lang.expiry_date' )</th>
                <th>@lang( 'superadmin::lang.payment_method' )</th>
                <th>@lang( 'superadmin::lang.user_added' )</th>
                <th class="notexport">@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>

@endcomponent