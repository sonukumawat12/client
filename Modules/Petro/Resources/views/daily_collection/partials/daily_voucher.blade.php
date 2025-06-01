

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'petro::lang.daily_voucher', ['contacts' => __('petro::lang.mange_daily_voucher') ])</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.daily_voucher')</a></li>
                    <li><span>@lang( 'petro::lang.daily_voucher', ['contacts' => __('petro::lang.mange_daily_voucher') ])</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('daily_voucher_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('daily_voucher_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
               
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('daily_voucher_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('daily_voucher_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'daily_voucher_date_range', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('dv_pump_operator', __('petro::lang.pump_operator').':') !!}
                        {!! Form::select('dv_pump_operator', $pump_operators, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dv_customer_id', __('petro::lang.customer').':') !!}
                        {!! Form::select('dv_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;','required','placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dv_settlement_id',  __('petro::lang.settlement_no') . ':') !!}
                        {!! Form::select('dv_settlement_id', $daily_voucher_settlements, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
               
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('dv_status', __('petro::lang.status').':') !!}<br>
                        {!! Form::select('dv_status', array('pending' => 'Pending', 'completed' => 'Completed'), null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
       
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_credit_sales')])
    @slot('tool')
    <div class="box-tools pull-right">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\DailyVoucherController@create')}}"
                data-container=".pump_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="daily_voucher_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.voucher_order_date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.daily_voucher_order_no')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.shift_number')</th>
                    <th>@lang('petro::lang.collection_form_no')</th>
                    <th>@lang('petro::lang.customer')</th>
                    
                    <th>@lang('petro::lang.credit_limit')</th>
                    <th>@lang('petro::lang.current_outstanding')</th>
                    <th>@lang('petro::lang.amount')</th>
                    <th>@lang('petro::lang.balance_amount')</th>
                    <th>@lang('petro::lang.total_collection')</th>
                    
                    <th>@lang('petro::lang.created_by')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.status')</th>
                    <th>@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->
