<div class="row">
      <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="@if(request()->tab == 'add') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@create')}}?tab=add" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.add_vat_invoice')</strong>
                        </a>
                    </li>
                  
                    <li class="@if(request()->tab == 'list') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@index')}}?tab=list"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_invoice')</strong>
                        </a>
                    </li>

                    <li class="@if(request()->tab == 'products') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatInvoiceController@productsSold')}}?tab=products"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_products_sold')</strong>
                        </a>
                    </li>
                    
                     <li class="@if(request()->tab == 'prefix') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatPrefixController@index')}}?tab=prefix"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.prefix_and_starting_nos')</strong>
                        </a>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    </div>