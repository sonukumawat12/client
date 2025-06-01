<div class="col-md-12">
    @component('components.widget')
    
    <div class="col-md-5 col-md-offset-2">
        <div class="row">
            @if ($this_shift->status == 2)
                <div class="">
                    <div class="col-md-6 text-red">
                        <h3 id="shift_text"  class="text-danger">@lang('petro::lang.shift_closed'):</h3>
                    </div>
                    <div class="col-md-6">
                        @php
                            $pud = $day_entries->sum('amount') + $other_sale - $payments->total_paid;
                            $status = $pud > 0 ? 'SH' : ($pud < 0 ? 'EX' : '');
                            @endphp
                        <h3>PUD-{{ $status }} {{ $shift_number }}</h3>
                    </div>
                </div>    
            @endif
        </div>
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
                <h3 id="balance_to_settle">
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
                <h3 id="current_balance_to_operator">{{ @num_format($payments->shortage_excess) }}</h3>
            </div>
        </div>
        
        @if(!empty(auth()->user()->pump_operator_id) && $this_shift->status == 0)
        <div class="row">
            <div class="col-md-8">
                @if($unconfirmed_pumps_count == 0 && $unclosed_pumps_count == 0)
                    <a id="close_shift_btn" 
                    class="@if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0) hide @endif btn btn-flat pull-right" 
                    style="background: #3f48cc; color: #fff;"
                    href="{{ action('\Modules\Petro\Http\Controllers\ClosingShiftController@closeShift', $this_shift->id) }}">
                        @lang('petro::lang.close_shift')
                    </a>
                @elseif($unconfirmed_pumps_count != 0)
                    <a onclick="alert_pending_receive()"
                    class="@if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0) hide @endif btn btn-flat pull-right" 
                    style="background: #3f48cc; color: #fff;"
                    href="#">
                        @lang('petro::lang.close_shift')
                    </a>
                    <script>
                        function alert_pending_receive(){
                            toastr.error('Pending @lang('petro::lang.receive_pump')');
                        }
                    </script>
                @else
                    <a onclick="alert_pending_close()"
                    class="@if(($day_entries->sum('amount') + $other_sale - $payments->total) != 0) hide @endif btn btn-flat pull-right" 
                    style="background: #3f48cc; color: #fff;"
                    href="#">
                        @lang('petro::lang.close_shift')
                    </a>
                    <script>
                        function alert_pending_close(){
                            toastr.error('Please close the Meters first');
                        }
                    </script>
                @endif
            </div>
        </div>        
        @endif
        @if($unconfirmed_pumps_count == 0 && $unclosed_pumps_count == 0)
         <div class="row mt-5">
             <div class="col-md-4">
                 @if ($day_entries->sum('amount') + $other_sale - $payments->total_paid > 0 )
                     <button
                         @if($this_shift->status == 2 || $day_entries->sum('amount') + $other_sale - $payments->total == 0)
                             disabled="disabled"
                         @endif

                         type="button"
                         id="settleBalance" 
                         class="btn btn-flat"
                         style="background: #3CA2E8; color:#fff; border:none;padding:15px 25px;border-radius:4px"
                     >
                         @lang('petro::lang.shortage')
                     </button>
                 @endif
             </div>
             @if ($day_entries->sum('amount') + $other_sale - $payments->total_paid < 0)
                 <div class="col-md-6">
                     <button
                         type="button"
                         @if($this_shift->status == 2 || $day_entries->sum('amount') + $other_sale - $payments->total == 0)
                             disabled="disabled"
                         @endif
                         id="settleBalance"
                         style="background: #25B24E; color:#fff; border:none;padding:15px 25px;border-radius:4px"
                         class="btn btn-flat pull-right" 
                     >
                         @lang('petro::lang.excess')
                     </button>
                 </div>
             @endif
         </div>
        @endif
    </div>

    @endcomponent
</div>

