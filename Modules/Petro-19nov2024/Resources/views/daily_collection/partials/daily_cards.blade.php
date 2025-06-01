


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'petro::lang.daily_cards', ['contacts' => __('petro::lang.mange_daily_cards') ])</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.daily_cards')</a></li>
                    <li><span>@lang( 'petro::lang.daily_cards', ['contacts' => __('petro::lang.mange_daily_cards') ])</span></li>
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
               
            <div class="row">
                 <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dc_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('dc_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                 <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('dc_pump_operator', __('petro::lang.pump_operator').':') !!}<br>
                        {!! Form::select('dc_pump_operator', $pump_operators, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dc_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('dc_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'dc_date_range', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('petro::lang.customer').':') !!}
                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;','required','placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-3">
                        <div class="form-group">
                        {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                        {!! Form::select('card_type', $card_types, null, ['class' => 'form-control card_fields
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.all' ) ,'required']); !!}
                    </div>
                </div>
            
                <div class="col-md-3">
                        <div class="form-group">
                        {!! Form::label('slip_no', __('petro::lang.slip_no').':') !!}
                        {!! Form::select('slip_no', $slip_nos, null, ['class' => 'form-control card_fields
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.all' ) ,'required']); !!}
                    </div>
                </div>
                
                 <div class="col-md-3">
                        <div class="form-group">
                        {!! Form::label('card_number', __('petro::lang.card_number').':') !!}
                        {!! Form::select('card_number', $card_numbers, null, ['class' => 'form-control card_fields
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.all' ) ,'required']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dc_settlement_id',  __('petro::lang.settlement_no') . ':') !!}
                        {!! Form::select('dc_settlement_id', $daily_card_settlements, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                 <div class="clearfix"></div>
                
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('dc_status', __('petro::lang.status').':') !!}<br>
                        {!! Form::select('dc_status', array('pending' => 'Pending', 'completed' => 'Completed'), null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
            </div>
                
                
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_daily_cards')])
    @slot('tool')
    <div class="box-tools pull-right">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\DailyCardController@create')}}"
                data-container=".pump_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="daily_card_table" width="100%">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.collection_form_no')</th>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.card_type' )</th>
                    <th>@lang('petro::lang.card_number' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.total_collection')</th>
                    <th>@lang('petro::lang.slip_no' )</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('lang_v1.note') </th>
                    <th>@lang('petro::lang.status')</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div id="daily_card_print"></div>

</section>
<!-- /.content -->
