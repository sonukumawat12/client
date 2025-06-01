 <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#ezyinvoice-menu"
        aria-expanded="true" aria-controls="ezyinvoice-menu">
        <i class="fa fa-car"></i>
        <span>@lang('ezyinvoice::lang.ezy_invoice')</span>
    </a>
    <div id="ezyinvoice-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('ezyinvoice::lang.ezy_invoice'):</h6>
            <a class="collapse-item {{ $request->segment(2) == 'invoices' ? 'active' : '' }}" href="{{ action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@index') }}">
                @lang('ezyinvoice::lang.invoices')
            </a>
        </div>
    </div>
</li>
