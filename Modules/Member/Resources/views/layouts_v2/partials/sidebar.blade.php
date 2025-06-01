<li class="nav-item {{ in_array($request->segment(1), ['member-module', 'member']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#membermodule-menu"
        aria-expanded="true" aria-controls="membermodule-menu">
        <i class="fa fa-child"></i>
        <span>@lang('member::lang.member_module')</span>
    </a>
    <div id="membermodule-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Member Module:</h6>
            <a class="collapse-item {{ $request->segment(1) == 'member-module' && $request->segment(2) == 'members' ? 'active' : '' }}" href="{{action('\Modules\Member\Http\Controllers\MemberController@index')}}">List Member</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'member' && $request->segment(2) == 'suggestions' && $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Member\Http\Controllers\SuggestionController@index', 'en')}}">List Suggestions</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'member-module' && $request->segment(2) == 'member-settings' ? 'active' : '' }}" href="{{action('\Modules\Member\Http\Controllers\MemberSettingController@index')}}">Member Settings</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'member-module' && $request->segment(2) == 'users-activity' ? 'active' : '' }}" href="{{action('\Modules\Member\Http\Controllers\MemberController@memberUserActivity')}}">@lang('member::lang.member_user_activity')</a>
            
             <a class="collapse-item {{ $request->segment(1) == 'member-module' && $request->segment(2) == 'member-sms-settings ' ? 'active' : '' }}" href="{{action('\Modules\Member\Http\Controllers\MemberController@smsSettings')}}">@lang('member::lang.sms_settings')</a>
            
        </div>
    </div>
</li>