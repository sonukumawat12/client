<li class="nav-item {{ in_array($request->segment(1), ['Stocktaking']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#stocktaking-menu"
        aria-expanded="true" aria-controls="stocktaking-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('Stocktaking::lang.Stocktaking')</span>
    </a>
    <div id="stocktaking-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Stock Taking:</h6>
            @if(auth()->user()->can('f22_stock_taking_form'))
                <a class="collapse-item {{ $request->segment(1) == 'Stocktaking' && $request->segment(2) == 'F22_stock_taking' ? 'active' : '' }}" href="{{action('\Modules\Stocktaking\Http\Controllers\F22FormController@F22StockTaking')}}">@lang('Stocktaking::lang.F22StockTaking_form')</a>
            @endif
                <a class="collapse-item {{ $request->segment(1) == 'Stocktaking' && $request->segment(2) == 'forms-setting' ? 'active' : '' }}" href="{{action('\Modules\Stocktaking\Http\Controllers\FormsSettingController@index')}}">@lang('Stocktaking::lang.Stocktaking_forms_setting')</a>
        </div>
    </div>
</li>