<li
class="treeview {{ in_array($request->segment(1), ['Stocktaking']) ? 'active active-sub' : '' }}"
id="tour_step5">
<a href="#" id="tour_step5_menu"><i class="fa fa-calculator"></i> <span>@lang('Stocktaking::lang.Stocktaking')</span>
  <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
  
  @if(auth()->user()->can('f22_stock_taking_form'))
  <li class="{{ $request->segment(1) == 'Stocktaking' && $request->segment(2) == 'F22_stock_taking' ? 'active' : '' }}"><a
      href="{{action('\Modules\Stocktaking\Http\Controllers\F22FormController@F22StockTaking')}}"><i class="fa fa-file-text-o"></i>@lang('Stocktaking::lang.F22StockTaking_form')</a>
  </li>
  @endif
  <li class="{{ $request->segment(1) == 'Stocktaking' && $request->segment(2) == 'forms-setting' ? 'active' : '' }}"><a
      href="{{action('\Modules\Stocktaking\Http\Controllers\FormsSettingController@index')}}"><i class="fa fa-cogs"></i>@lang('Stocktaking::lang.Stocktaking_forms_setting')</a>
  </li>
  
</ul>
</li>