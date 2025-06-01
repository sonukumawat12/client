<li class="nav-item {{ in_array($request->segment(1), ['property']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#projects-menu"
        aria-expanded="true" aria-controls="projects-menu">
        <i class="ti-layout-media-right-alt"></i>
        <span>@lang('property::lang.property')</span>
    </a>
    <div id="projects-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('property::lang.property'):</h6>
            <a class="collapse-item {{ $request->segment(2) == 'list-price-changes' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\PriceChangesController@index')}}">@lang('property::lang.list_price_changes')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'sale-and-customer-payment' && $request->segment(3) == 'dashboard' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard', ['type' => 'customer'])}}">@lang('property::lang.sales_dashboard')</a>
            @can('property.customer.view')
                <a class="collapse-item {{ $request->segment(2) == 'contacts' && $request->input('type') == 'customer' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\ContactController@index', ['type' => 'customer'])}}">@lang('property::lang.property_customer')</a>
            @endcan
            @can('property.list.view')
                <a class="collapse-item {{ $request->segment(2) == 'properties' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\PropertyController@index')}}">@lang('property::lang.list_properties')</a>
            @endcan
            @can('property.purchase.view')
                <a class="collapse-item {{ $request->segment(2) == 'purchases' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\PurchaseController@index')}}">@lang('property::lang.property_purchase')</a>
            @endcan
            @can('property.purchase.view')
                <a class="collapse-item {{ $request->segment(2) == 'reports' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\ReportController@index')}}"> @lang('property::lang.reports')</a>
            @endcan
            @can('property.settings.access')
                <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Property\Http\Controllers\SettingController@index')}}">@lang('property::lang.settings')</a>
            @endcan
        </div>
    </div>
</li>

@if($list_easy_payment)
@if(auth()->user()->can('list_easy_payments.access'))
    <li class="nav-item {{  in_array( $request->segment(2), ['easy-payments']) ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{action('\Modules\Property\Http\Controllers\EasyPaymentController@index')}}">
            <i class="fa fa-money"></i>
            <span>@lang('property::lang.list_easy_payments')</span></a>
    </li>

@endif
@endif
