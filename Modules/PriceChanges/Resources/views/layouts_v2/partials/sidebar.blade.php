<li class="nav-item {{ in_array($request->segment(1), ['pricechanges']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pricechanges-menu"
        aria-expanded="true" aria-controls="pricechanges-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('pricechanges::lang.mpcs')</span>
    </a>
    <div id="pricechanges-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('pricechanges::lang.mpcs'):</h6>
            
            @if(auth()->user()->can('f17_form'))
                <a class="collapse-item {{ $request->segment(1) == 'pricechanges' ? 'active' : '' }}" href="{{action('\Modules\PriceChanges\Http\Controllers\F17FormController@index')}}">@lang('pricechanges::lang.F17_form')</a>
            @endif
        </div>
    </div>
</li>

