 <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="@if(request()->tab == 'list') active @endif  @if(empty(request()->tab )) active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@index')}}?tab=list"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_invoice')-2</strong>
                        </a>
                    </li>
                    
                   <li class="@if(request()->tab == 'add') active @endif">
                        <a href="{{action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@create')}}?tab=add" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.add_vat_invoice')-2</strong>
                        </a>
                    </li>
                  
                </ul>
                </div>
            </div>
        </div>