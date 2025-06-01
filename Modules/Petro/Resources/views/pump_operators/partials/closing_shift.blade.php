@php
    $date = date('Y-m-d');
    $pump_operator_id = Auth::user()->pump_operator_id;
    $open_meters = \Modules\Petro\Entities\PumpOperatorAssignment::where('pump_operator_id',$pump_operator_id)
                        ->whereDate('date_and_time',$date)
                        ->where('is_manually_closed','0')
                        ->count();
@endphp
                

<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
               
            <div class="row">
                 <div class="col-md-4 px-4" style="margin-left: 35px;">
                    <div class="form-group">
                        {!! Form::label('shift_id',  __('petro::lang.shift') . ':') !!}
                        <select class="form-control select2" style = 'width:100%' id="closing_shift_id">
                            @foreach($shifts as $shift)
                                <option value="{{$shift->id}}">{{$shift->name." (".@format_date($shift->shift_date)}} to {{!empty($shift->closed_time) ? @format_datetime($shift->closed_time) : 'Open'}} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
            </div>
                
                
            @endcomponent
        </div>
    </div>
    
    <div class="row" id="closing_shift_summary">
        
    </div>

    @if(empty(auth()->user()->pump_operator_id))
    {{-- <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('close_shift_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'close_shift_location_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('close_shift_pump_operators', $pump_operators, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('close_shift_pumps', $pumps->pluck('pump_name', 'id'), null, ['class' =>
                    'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('close_shift_payment_method', $payment_types, null, ['class' => 'form-control
                    select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('close_shift_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'close_shift_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div> --}}
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_closing_shift_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.time')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.shift_number')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.starting_meter')</th>
                    <th>@lang('petro::lang.closing_meter')</th>
                    <th>@lang('petro::lang.test_qty')</th>
                    <th>@lang('petro::lang.sold_ltr')</th>
                    <th>@lang('petro::lang.amount')</th>
                    <th>@lang('petro::lang.short_amount')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total">
                    <td colspan="9" class="text-right"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_cs_testing_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_cs_sold_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_cs_sold_amount" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_cs_short_amount" data-currency_symbol="true"></span></td>

                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->