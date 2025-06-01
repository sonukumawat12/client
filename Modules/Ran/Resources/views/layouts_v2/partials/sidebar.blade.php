<li class="nav-item {{ in_array($request->segment(1), ['ran']) && $request->segment(2) != 'issue-customer-bill'? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#ran-menu"
        aria-expanded="true" aria-controls="ran-menu">
        <i class="ti-world"></i>
        <span>@lang('ran::lang.ran')</span>
    </a>
    <div id="ran-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('ran::lang.ran'):</h6>
            <a class="collapse-item {{ $request->segment(2) == 'gold' ? 'active' : '' }}" href="{{action('\Modules\Ran\Http\Controllers\GoldController@index')}}">@lang('ran::lang.gold')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'production' ? 'active' : '' }}" href="{{action('\Modules\Ran\Http\Controllers\ProductionController@index')}}">@lang('ran::lang.production')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'goldsmith' ? 'active' : '' }}" href="{{action('\Modules\Ran\Http\Controllers\GoldSmithController@index')}}">@lang('ran::lang.goldsmith')</a>
        </div>
    </div>
</li>