 <li class="nav-item {{ in_array($request->segment(1), ['shipping']) ? 'active active-sub' : '' }}">
     <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingmanagement-menu"
         aria-expanded="true" aria-controls="shippingmanagement-menu">
         <i class="fa fa-car"></i>
         <span>@lang('Shipping')</span>
     </a>
     <div id="shippingmanagement-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
         <div class="bg-white py-2 collapse-inner rounded">
             <h6 class="collapse-header">@lang('shipping_management'):</h6>

             
             <a class="collapse-item {{ $request->segment(2) == 'agents' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\AgentController@index') }}">
                 @lang('shipping::lang.agents')
             </a>
             
             <a class="collapse-item {{ $request->segment(2) == 'recipients' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\RecipientController@index') }}">
                 @lang('shipping::lang.recipients')
             </a>
             
              <a class="collapse-item {{ $request->segment(2) == 'partners' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\PartnerController@index') }}">
                 @lang('shipping::lang.partners')
             </a>
             
             <a class="collapse-item {{ $request->segment(2) == 'add-shipment' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@index') }}">
                 @lang('shipping::lang.add_shipment')
             </a>
             <a class="collapse-item {{ $request->segment(2) == 'add-shipment-sw' ? 'active' : '' }}"
                href="{{ action('\Modules\Shipping\Http\Controllers\AddShipmentSWController@index') }}">
                    @lang('shipping::lang.add_shipment_sw')
             </a>
             
             <a class="collapse-item {{ $request->segment(2) == 'shipment' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\ShippingController@index') }}">
                 @lang('shipping::lang.list_shipment')
             </a>
              <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\SettingController@index') }}">
                 @lang('shipping::lang.shipping_settings')
             </a>
              <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}"
                 href="{{ action('\Modules\Shipping\Http\Controllers\LocationsController@index') }}">
                 @lang('shipping::lang.location_settings')
             </a>
             
         </div>
     </div>
 </li>
