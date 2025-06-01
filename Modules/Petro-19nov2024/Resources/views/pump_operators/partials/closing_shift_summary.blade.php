<div class="col-md-12">
    @component('components.widget')
    
    <div class="col-md-5 col-md-offset-2">
        @if($this_shift->status != 0)
            <div class="row">
                <h3 class="text-danger">@lang('petro::lang.shift_closed')</h3>
            </div>
        @endif
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.no_of_closed_pumps'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ $today_pumps }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.total_sale_closed_pumps'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($day_entries->sum('amount')) }}</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.total_other_sales'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($other_sale) }}</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.total_payments'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->total) }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.balance_to_settle'):</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    {{ @num_format($day_entries->sum('amount') + $other_sale - $payments->total) }}
                    @if(($day_entries->sum('amount') + $other_sale - $payments->total) < 0)
                        <span>@lang('petro::lang.excess')</span>
                    @endif
                </h3> 
            </div>
        </div>
        
        @if(!empty(auth()->user()->pump_operator_id) && $this_shift->status == 0)
        <div class="row">
            <div class="col-md-6">
                <a class="btn btn-flat btn-warning btn-modal"
                   style="font-family: 'Source Sans Pro', sans-serif; color: #fff;"
                   href="#" data-container=".view_modal"
                   data-href="{{ action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@otherSales', $this_shift->id) }}">
                    @lang('petro::lang.other_sales')
                </a>
            </div>
            
            @if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0)
                <div class="col-md-6">
                    <a class="btn btn-flat btn-primary"
                       style="font-family: 'Source Sans Pro', sans-serif; color: #fff;"
                       href="{{ action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@balanceToOperator', auth()->user()->pump_operator_id) }}">
                        @lang('petro::lang.balance_to_operator')
                    </a>
                </div>
            @endif
        </div>
        @endif
        
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.cash'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->cash) }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.credit_sales'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->credit) }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.credit_cards'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->card) }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.cheque_sales'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->cheque) }}</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.other'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format($payments->other) }}</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 text-red">
                <h3>@lang('petro::lang.current_balance_to_operator'):</h3>
            </div>
            <div class="col-md-6">
                <h3>{{ @num_format(-1 * $payments->shortage_excess) }}</h3>
            </div>
        </div>
        
        @if(!empty(auth()->user()->pump_operator_id) && $this_shift->status == 0)
        <div class="row">
            <div class="col-md-8">
                @if($unconfirmed_pumps_count == 0)
                    <a id="close_shift_btn" 
                    class="@if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0) hide @endif btn btn-flat pull-right" 
                    style="background: #3f48cc; color: #fff;"
                    href="{{ action('\Modules\Petro\Http\Controllers\ClosingShiftController@closeShift', $this_shift->id) }}">
                        @lang('petro::lang.close_shift')
                    </a>
                @else
                    <a onclick="alert('Pending @lang('petro::lang.receive_pump')')"
                    class="@if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0) hide @endif btn btn-flat pull-right" 
                    style="background: #3f48cc; color: #fff;"
                    href="#">
                        @lang('petro::lang.close_shift')
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    @endcomponent
</div>

