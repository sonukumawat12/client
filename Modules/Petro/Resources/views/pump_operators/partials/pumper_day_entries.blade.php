
                
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
               
            <div class="row">
                 <div class="col-md-4 px-4" style="margin-left: 35px;">
                    <div class="form-group">
                        {!! Form::label('shift_id',  __('petro::lang.shift') . ':') !!}
                        <select class="form-control select2" style = 'width:100%' id="shift_id">
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


    <div class="row" id="pumper_day_entry_summary">
        
    </div>

    @if(empty(auth()->user()->pump_operator_id))
    {{-- <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('day_entries_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'day_entries_location_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('day_entries_pump_operators', $pump_operators, null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('day_entries_pumps', $pumps->pluck('pump_name', 'id'), null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('day_entries_payment_method', $payment_types, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_difference', __('petro::lang.difference') . ':') !!}
                    {!! Form::select('day_entries_difference', ['positive' => 'Positive', 'negative' => 'Negative'], null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_difference', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div> --}}
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_day_entries_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    
                    @if(empty(auth()->user()->pump_operator_id))
                    <th>@lang('petro::lang.settlement_no')</th>
                    @endif
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.shift')</th>
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
                    <td colspan="@if(!empty(auth()->user()->pump_operator_id)) 8 @else 9 @endif" class="text-right"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_sold_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_sold_amount" data-currency_symbol="true"></span></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->