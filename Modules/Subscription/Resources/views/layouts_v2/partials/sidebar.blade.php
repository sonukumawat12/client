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

@if(!empty($pacakge_details['subscriptions_module']))
<li class="nav-item {{ in_array($request->segment(1), ['subscription']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#subscription-menu"
        aria-expanded="true" aria-controls="subscription-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('subscription::lang.subscription')</span>
    </a>
    <div id="subscription-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('subscription::lang.subscription'):</h6>
            @if(!empty($pacakge_details['list_subscriptions']))
                <a class="collapse-item {{ $request->segment(1) == 'list' ? 'active' : '' }}" href="{{action('\Modules\Subscription\Http\Controllers\SubscriptionListController@index')}}">@lang('subscription::lang.subscription_list')</a>
            @endif
            
            @if(!empty($pacakge_details['subscriptions_settings']))
                <a class="collapse-item {{ $request->segment(1) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@index')}}">@lang('subscription::lang.subscription_settings')</a>
            @endif
            
            @if(!empty($pacakge_details['subscriptions_sms_template']))
                <a class="collapse-item {{ $request->segment(1) == 'templates' ? 'active' : '' }}" href="{{action('\Modules\Subscription\Http\Controllers\SubscriptionSmsTemplateController@index')}}">@lang('subscription::lang.sms_templates')</a>
            @endif
            
            @if(!empty($pacakge_details['subscriptions_user_activity']))
                <a class="collapse-item {{ $request->segment(1) == 'user-activity' ? 'active' : '' }}" href="{{action('\Modules\Subscription\Http\Controllers\SubscriptionUserActivityController@index')}}">@lang('subscription::lang.user_activity')</a>
            @endif
        </div>
    </div>
</li>
@endif

