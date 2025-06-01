@php
   
   $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<li class="nav-item {{ in_array($request->segment(1), ['sms']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#sms-menu"
        aria-expanded="true" aria-controls="sms-menu">
        <i class="fa fa-comments"></i>
        <span>@lang('sms::lang.sms')</span>
    </a>
    <div id="sms-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('sms::lang.sms'):</h6>
            
                @if(!empty($pacakge_details['sms_history']) || !empty($pacakge_details['list_sms']))
                <a class="collapse-item {{ $request->segment(1) == 'sms' && $request->segment(2) == ''? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SMSController@index')}}">@lang('sms::lang.list_sms')</a>
                @endif
                
                @if(!empty($pacakge_details['sms_ledger']) && auth()->user()->can('sms_ledger'))
                    <a class="collapse-item {{ $request->segment(1) == 'sms' && $request->segment(2) == 'view-ledger'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SmsLedger@viewLedger')}}">@lang('sms::lang.sms_ledger')</a>
                @endif
                
                @if(!empty($pacakge_details['sms_delivery_report']) && auth()->user()->can('sms_delivery_report'))
                <a class="collapse-item {{ $request->segment(1) == 'sms' && $request->segment(2) == 'sms-delivery'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SmsLedger@smsDelivery')}}">@lang('sms::lang.sms_delivery_report')</a>
                @endif
        </div>
    </div>
</li>