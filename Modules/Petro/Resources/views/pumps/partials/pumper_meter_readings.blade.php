<!-- Main content -->
<section class="content">
    @if(empty($pump_operator_dashboard))
        <div class="alert alert-danger">
            @lang('petro::lang.not_subscribed_to_pumper_dashboard')
        </div>
    @else
    
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('pump_operators', __('petro::lang.pump_operator') . ':') !!}
                        {!! Form::select('pumper_meter_sales_pump_operators', $pump_operators, null, ['class' => 'form-control
                        select2', 'placeholder'
                        => __('petro::lang.all'), 'id' => 'pumper_meter_sales_pump_operators', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                        {!! Form::select('pumper_meter_sales_pumps', $pumps, null, ['class' => 'form-control select2',
                        'placeholder'
                        => __('petro::lang.all'), 'id' => 'pumper_meter_sales_pumps', 'style' => 'width:100%']); !!}
                    </div>
                </div>
               
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('pumper_meter_sales_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('pumper_meter_sales_date_range', @format_date('first day of this month') . ' ~ ' .
                        @format_date('last
                        day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                        'form-control', 'id' => 'pumper_meter_sales_date_range', 'readonly']); !!}
                    </div>
                </div>
                @endcomponent
            </div>
        </div>
    
        @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_list_pumps')])
       
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="pumper_meter_reading_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang('petro::lang.date')</th>
                        <th>@lang('petro::lang.time')</th>
                        <th>@lang('petro::lang.pump_operator')</th>
                        <th>@lang('petro::lang.pump_no')</th>
                        <th>@lang('petro::lang.received_meter')</th>
                        <th>@lang('petro::lang.new_meter')</th>
                        <th>@lang('petro::lang.sold_qty')</th>
                        <th>@lang('petro::lang.unit_price')</th>
                        <th>@lang('petro::lang.amount')</th>
                        <th>@lang('petro::lang.total_amount')</th>
                        <th>@lang('petro::lang.today_deposited')</th>
                        <th>@lang('petro::lang.balance_to_deposit')</th>
    
                    </tr>
                </thead>
            </table>
        </div>
        @endcomponent
    @endif
    


</section>
<!-- /.content -->