<li class="nav-item {{ in_array($request->segment(1), ['airline/settings']) ? 'active active-sub' : '' }}">

    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#airline-menu" aria-expanded="true" aria-controls="airline-menu">

        <i class="ti-id-badge"></i>

        <span>Airline Ticketing</span>

    </a>

    <div id="airline-menu" class="collapse" aria-labelledby="headingPages"data-parent="#accordionSidebar">

        <div class="bg-white py-2 collapse-inner rounded">

            <h6 class="collapse-header">Airline Ticketing:</h6>

             <a class="collapse-item {{ $request->segment(2) == 'airline_suppliers' ? 'active' : '' }}"

                href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_suppliers') }}">Suppliers</a>

            <a class="collapse-item {{ $request->segment(2) == 'create_invoice' ? 'active' : '' }}"

                href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@create_invoice') }}">Create Invoice</a>

            

            <a class="collapse-item {{ $request->segment(2) == 'agents' ? 'active' : '' }}"

                    href="{{ action('\Modules\Airline\Http\Controllers\AirlineAgentController@index') }}">@lang('airline::lang.arilines_agents_sidebar')</a>  

                      

            <a class="collapse-item {{ $request->segment(2) == 'create' ? 'active' : '' }}"

            href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@add_commission') }}">Add Commission</a>   

        <a class="collapse-item {{ $request->segment(2) == 'ticketing' ? 'active' : '' }}"

        <a class="collapse-item {{ $request->segment(2) == 'list_Commission' ? 'active' : '' }}"

            href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@list_commission') }}">List Commission</a>   

        <a class="collapse-item {{ $request->segment(2) == 'ticketing' ? 'active' : '' }}"

                href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@index') }}">List Invoices</a>

           



            <a class="collapse-item {{ $request->segment(2) == 'airline_settings' ? 'active' : '' }}"

                href="{{ action('\Modules\Airline\Http\Controllers\AirlineSettingController@index') }}">Settings</a>


            <a class="collapse-item {{ $request->segment(2) == 'form_settings' ? 'active' : '' }}"

                href="{{ action('\Modules\Airline\Http\Controllers\FormSettingsController@index') }}">Form Settings</a>

        </div>

    </div>

</li>