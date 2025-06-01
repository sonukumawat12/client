<li class="nav-item {{ in_array($request->segment(1), ['Sales-Discounts']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Sales-Discounts-menu" aria-expanded="true" aria-controls="Sales-Discounts-menu">
        <i class="fa fa-percent"></i>
        <span>Sales Discounts</span>
    </a>
    <div id="Sales-Discounts-menu" class="collapse" aria-labelledby="headingPages"data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Sales Discounts:</h6>
             <a class="collapse-item {{$request->segment(1) == 'Sales-Discounts' && $request->segment(2) == 'List_Sales_Discounts' ? 'active' : '' }}"
                href="{{ action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@index') }}">List Sales Discount</a>
           
        </div>
    </div>
</li>