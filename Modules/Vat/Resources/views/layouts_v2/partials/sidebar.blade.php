
@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<!-- Start VAT Module -->
<li class="nav-item {{ in_array($request->segment(1), ['vat-module']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#vat-menu" aria-expanded="true" aria-controls="vat-menu">
        <i class="ti-id-badge"></i>
        <span>@lang('vat::lang.vat_module')</span>
    </a>
    <div id="vat-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('vat::lang.vat_module'):</h6>

            <!-- VAT -->
            <a class="collapse-item {{ $request->segment(3) == 'vat-invoice' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@index')}}">@lang('vat::lang.vat_invoice')</a>
           
            <!-- VAT-2 -->
            <a class="collapse-item {{ $request->segment(3) == 'vat-invoice2' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@index')}}">@lang('vat::lang.vat_invoice')-2</a>
            
            @if(!empty($pacakge_details['fleet_vat_invoice2']))
                <!-- VAT-2 -->
                <a class="collapse-item {{ $request->segment(3) == 'fleet-vat-invoice2' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@index')}}">@lang('vat::lang.fleet_vat_invoice')</a>
            @endif

             <!-- VAT-SALE -->
            @if(auth()->user()->can('list_vat_sale') && !empty($pacakge_details['list_vat_sale']))  
                <a class="collapse-item" href="{{action('\Modules\Vat\Http\Controllers\VatSettlementController@index')}}">@lang('vat::lang.vat_sale')</a>
            @endif
          
            <!-- VAT-PURCHASE -->
            @if(auth()->user()->can('list_vat_purchase') && !empty($pacakge_details['list_vat_purchase']))  
                <a class="collapse-item" href="{{action('\Modules\Vat\Http\Controllers\VatPurchaseController@index')}}">@lang('vat::lang.vat_purchase')</a>
            @endif
          
            <!-- VAT-EXPENSES -->
             @if(auth()->user()->can('list_vat_expense') && !empty($pacakge_details['list_vat_expense']))  
                <a class="collapse-item" href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@index')}}">@lang('vat::lang.vat_expenses')</a>
            @endif

            <!-- VAT-CONTACTS -->
            @if(auth()->user()->can('vat_contacts') && !empty($pacakge_details['vat_contacts']))  
                <a class="collapse-item " href="{{action('\Modules\Vat\Http\Controllers\VatContactController@index',['type' => 'customer'])}}">@lang('vat::lang.vat_contacts')</a>
            @endif
            
            <!-- VAT-PRODUCSTS -->
            {{--@if(auth()->user()->can('vat_products') && !empty($pacakge_details['vat_products']))  
                <a class="collapse-item " href="{{action('\Modules\Vat\Http\Controllers\VatProductController@index')}}">@lang('vat::lang.vat_products')</a>
            @endif --}}

            @if(auth()->user()->can('tax_report.view'))
            <a class="collapse-item {{ $request->segment(2) == 'reports' ? 'active' : '' }}" href="{{ action('\Modules\Vat\Http\Controllers\VatController@getVatReport') }}">@lang('report.tax_report')</a>
            @endif
    
            <a class="collapse-item {{ $request->segment(2) == 'reports-ledger' ? 'active' : '' }}" href="{{ action('\Modules\Vat\Http\Controllers\VatReportController@index') }}">@lang('vat::lang.vat_report_ledger')</a>
           
            <a class="collapse-item {{ $request->segment(2) == 'customer-statement' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\CustomerStatementController@index')}}">@lang('vat::lang.vat_statement')</a>
            
            @if(!empty($pacakge_details['customized_vat_invoices']))
                <!--VAT 127-->
                <a class="collapse-item {{ $request->segment(3) == 'invoices-127' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@index127')}}">@lang('vat::lang.list_vat_invoice_127')</a>
            @endif    
            
            @if(!empty($pacakge_details['customized_vat_invoices']))
                <a class="collapse-item {{ $request->segment(2) == 'vat-custom-invoices' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\VatBankDetailController@index')}}">@lang('vat::lang.customized_invoices')</a>
            @endif

            <a class="collapse-item " href="{{action('\Modules\Vat\Http\Controllers\ImportContactsController@index')}}">@lang('vat::lang.import_contacts')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'vat-settings' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\SettingsController@index')}}">@lang('vat::lang.vat_settings')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'customer-vat-schedule' ? 'active' : '' }}" href="{{action('\Modules\Vat\Http\Controllers\VatController@getCustomerVatSchedule')}}">@lang('vat::lang.vat_schedule')</a>
        </div>
    </div>
</li>
<!-- End VAT Module -->