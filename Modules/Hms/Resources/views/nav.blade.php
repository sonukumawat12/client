<li class="nav-item {{ in_array($request->segment(1), ['hms']) ? 'active active-sub' : '' }}">
    <a 
        class="nav-link collapsed" 
        href="{{action([\Modules\Hms\Http\Controllers\HmsController::class, 'index'])}}"
        data-toggle="collapse"
        data-target="#hmsmanagement-menu"
        aria-expanded="true"
        aria-controls="hmsmanagement-menu"
    >
        <i class="fas fa-hotel"></i>
        <span>@lang('hms::lang.hms')</span>
    </a>
    <div id="hmsmanagement-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('hms'):</h6>
            
                <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'dashboard' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsController::class, 'index'])}}">Dashboard</a>
            @can('hms.manage_rooms')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'rooms' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\RoomController::class, 'index'])}}">@lang('hms::lang.rooms')</a>
            @endcan
            @can('hms.manage_price')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'room' && $request->segment(3) == 'pricing' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\RoomController::class, 'pricing'])}}">@lang('hms::lang.prices')</a>
            @endcan
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'bookings' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsBookingController::class, 'index'])}}">@lang('hms::lang.bookings')</a>
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'calendar' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsBookingController::class, 'calendar'])}}">@lang('hms::lang.calendar')</a>
            @can('hms.manage_extra')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'extras' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\ExtraController::class, 'index'])}}">@lang('hms::lang.extras')</a>
            @endcan
            @can('hms.manage_unavailable')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'unavailables' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\UnavailableController::class, 'index'])}}">@lang('hms::lang.unavailable')</a>
            @endcan
            @can('hms.manage_coupon')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'coupons' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsCouponController::class, 'index'])}}">@lang('hms::lang.coupons')</a>
            @endcan
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'reports' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsReportController::class, 'index'])}}">@lang('hms::lang.reports')</a>
            @can('hms.manage_amenities')
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'amenities' ? 'active' : '' }}" href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=amenities'}}">@lang('hms::lang.amenities')</a>
            @endcan
            <a class="collapse-item {{ $request->segment(1) == 'hms' && $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsSettingController::class, 'index'])}}">@lang('messages.settings')</a>
                
               
              
        </div>
    </div>
</li>