@can('visitor.registration.create')
<li class="nav-item {{ in_array($request->segment(1), ['visitor-module', 'visitor']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#visitors-menu"
        aria-expanded="true" aria-controls="visitors-menu">
        <i class="fa fa-group"></i>
        <span>@lang('visitor::lang.visitor_module')</span>
    </a>
    <div id="visitors-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('visitor::lang.visitor_module'):</h6>
            @if($visitors)
                <a class="collapse-item {{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'visitor' ? 'active' : '' }}" href="{{action('\Modules\Visitor\Http\Controllers\VisitorController@index')}}">@lang('visitor::lang.list_visitors')</a>
            @endif
            @if($visitors_registration)
                <a class="collapse-item {{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'registration' && $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Visitor\Http\Controllers\VisitorRegistrationController@create')}}">@lang('visitor::lang.visitor_registration')</a>
            @endif
            @if($visitors_registration_setting)
                <a class="collapse-item {{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Visitor\Http\Controllers\VisitorSettingController@index')}}">@lang('visitor::lang.visitor_registration_settings').</a>
            @endif
                <a class="collapse-item {{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'qr-visitor-reg' ? 'active' : '' }}" href="{{action('\Modules\Visitor\Http\Controllers\VisitorController@generateQr')}}">@lang('visitor::lang.qr_visitor_reg').</a>
        </div>
    </div>
</li>
@endcan