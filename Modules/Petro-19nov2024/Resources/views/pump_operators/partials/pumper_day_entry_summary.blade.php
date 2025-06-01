<div class="col-md-12">
    @component('components.widget')
    
    <div class="col-md-5 col-md-offset-2">
        
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
        
        
    </div>

    @endcomponent
</div>

