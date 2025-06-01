<li class="nav-item {{ in_array($request->segment(1), ['installment']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#installment-menu"
        aria-expanded="true" aria-controls="installment-menu">
        <i class="fa fa-calculator"></i>
        <span>Installment Module</span>
    </a>
    <div id="installment-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Installment Module:</h6>
             <a class="collapse-item {{ $request->segment(1) == 'installment' && $request->segment(2) == 'system' ? 'active' : '' }}" href="{{action('\Modules\Installment\Http\Controllers\InstallmentSystemController@index')}}">Installment Plans</a>
             <a class="collapse-item {{ $request->segment(1) == 'installment' && $request->segment(2) == 'sells' ? 'active' : '' }}" href="{{action('\Modules\Installment\Http\Controllers\SellController@index')}}">Sale Invoices</a>
             
              <a class="collapse-item {{ $request->segment(1) == 'installment' && $request->segment(2) == 'customer' ? 'active' : '' }}" href="{{action('\Modules\Installment\Http\Controllers\CustomerController@index')}}">Customer Installments</a>
              
               <a class="collapse-item {{ $request->segment(1) == 'installment' && $request->segment(2) == 'installment' ? 'active' : '' }}" href="{{action('\Modules\Installment\Http\Controllers\InstallmentController@index')}}">Installment Report</a>
               
                <a class="collapse-item {{ $request->segment(1) == 'installment' && $request->segment(2) == 'contacts' ? 'active' : '' }}" href="{{action('\Modules\Installment\Http\Controllers\CustomerController@contacts')}}">All installments</a>
        </div>
    </div>
</li>

