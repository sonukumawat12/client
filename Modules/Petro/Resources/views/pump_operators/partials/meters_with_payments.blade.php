<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                @if(empty($only_pumper))
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('meters_with_payments_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                            {!! Form::select('meters_with_payments_pump_operators', $pump_operators, null, ['class' => 'form-control
                            select2', 'placeholder'
                            => __('petro::lang.all'), 'id' => 'meters_with_payments_pump_operators', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('meters_with_payments_pump_no', __('petro::lang.pumps') . ':') !!}
                            {!! Form::select('meters_with_payments_pump_no', $pumps->pluck('pump_name', 'id'), null, ['class' => 'form-control
                            select2',
                            'placeholder'
                            => __('petro::lang.all'), 'id' => 'meters_with_payments_pump_no', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('meters_with_payments_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('meters_with_payments_date_range', @format_date('first day of this month') . ' ~ ' .
                        @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'),
                        'class' =>
                        'form-control', 'id' => 'meters_with_payments_date_range', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_meters_with_payments')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="pump_operators_meters_with_payments_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('petro::lang.date')</th>
                        <th>@lang('petro::lang.time')</th>
                        <th>@lang('petro::lang.pump_operator')</th>
                        <th>@lang('petro::lang.collection_form_no')</th>
                        <th>Pumps</th>
                        <th>Unit Price</th>
                        <th>Last Meter</th>
                        <th>New Meter</th>
                        <th>Qty Sold</th>
                        <th>Total Sold Amount</th>
                        <th>@lang('petro::lang.payment_type')</th>
                        <th>Total Payment Entered</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

</section>