

@component('components.filters', ['title' => __('report.filters')])
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('history_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('history_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                => 'form-control', 'readonly']); !!}
            </div>
        </div>
        
       
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('username', __('superadmin::lang.username') . ':') !!}
                {!! Form::select('username', $usernames, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sender_name', __('superadmin::lang.sender_name') . ':') !!}
                {!! Form::select('sender_name', $sender_names, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sms_type_', __('superadmin::lang.sms_type') . ':') !!}
                {!! Form::select('sms_type_', $sms_type, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sms_status', __('superadmin::lang.sms_status') . ':') !!}
                {!! Form::select('sms_status', $sms_status, null, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
       
        
    </div>
@endcomponent


@component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.sms_history' )])

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="sms_history_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang( 'superadmin::lang.date' )</th>
                <th>@lang( 'superadmin::lang.id_no' )</th>
                <th>@lang( 'superadmin::lang.username' )</th>
                <th>@lang( 'superadmin::lang.sender_name' )</th>
                <th>@lang( 'superadmin::lang.phone_no' )</th>
                <th>@lang( 'superadmin::lang.message' )</th>
                <th>@lang( 'superadmin::lang.sms_type' )</th>
                <th>@lang( 'superadmin::lang.no_of_sms' )</th>
                <th>@lang( 'superadmin::lang.sms_status' )</th
            </tr>
        </thead>
    </table>
</div>

@endcomponent