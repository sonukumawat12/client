 <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="@if(request()->tab == 'add') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@create')}}?tab=add" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.add_vat_invoice')-2</strong>
                        </a>
                    </li>
                  
                    <li class="@if(request()->tab == 'list') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@index')}}?tab=list"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_invoice')-2</strong>
                        </a>
                    </li>

                    <li class="@if(request()->tab == 'products_sold') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@productsSold')}}?tab=products_sold"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_products_sold')-2</strong>
                        </a>
                    </li>
                    <li class="@if(request()->tab == 'invoice_setting') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@invoicesSetting')}}?tab=invoice_setting"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_invoice_setting')</strong>
                        </a>
                    </li>
                    
                    <li class="@if(request()->tab == 'prefixes') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2PrefixController@index')}}?tab=prefixes"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.prefix_and_starting_nos')</strong>
                        </a>
                    </li>
                    
                </ul>
                </div>
            </div>
        </div>