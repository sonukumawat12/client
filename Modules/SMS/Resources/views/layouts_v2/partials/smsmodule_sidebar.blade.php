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


<li class="nav-item {{ in_array($request->segment(1), ['smsmodule']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#smsmodule-menu"
        aria-expanded="true" aria-controls="smsmodule-menu">
        <i class="fa fa-comments"></i>
        <span>@lang('lang_v1.smsmodule')</span>
    </a>
    <div id="smsmodule-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('lang_v1.smsmodule'):</h6>
            
            
               <a class="collapse-item {{ $request->segment(1) == 'smsmodule' && $request->segment(2) == 'view-ledger'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SMSController@smsGroups')}}">@lang('lang_v1.sms_groups')</a>
             
               
                @if(!empty($pacakge_details['sms_quick_send']) && auth()->user()->can('sms_quick_send'))
                    <a class="collapse-item {{ $request->segment(1) == 'smsmodule' && $request->segment(2) == 'view-ledger'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SmsSendController@quickSend')}}">@lang('lang_v1.sms_quick_send')</a>
                @endif
                
                @if(!empty($pacakge_details['sms_from_file']) && auth()->user()->can('sms_from_file'))
                    <a class="collapse-item {{ $request->segment(1) == 'smsmodule' && $request->segment(2) == 'sms-delivery'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SmsSendController@smsCampaign')}}">@lang('lang_v1.sms_campaign')</a>
                @endif
                
                @if(!empty($pacakge_details['sms_campaign']) && auth()->user()->can('sms_campaign'))
                    <a class="collapse-item {{ $request->segment(1) == 'smsmodule' && $request->segment(2) == 'sms-from-file'? 'active' : '' }}" href="{{action('\Modules\SMS\Http\Controllers\SmsSendController@smsFromFile')}}">@lang('lang_v1.sms_from_file')</a>
                @endif
        </div>
    </div>
</li>