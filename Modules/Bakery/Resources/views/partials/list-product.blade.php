<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class=" @if(session('status.tab') == 'view') active @endif">
                        <a style="font-size:13px;" href="#view" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>View</strong>
                        </a>
                    </li>

                    <li class=" @if(session('status.tab') == 'add-price') active @endif">
                        <a style="font-size:13px;" href="#add-price" data-toggle="tab">
                            <i class="fa-solid fa-car"></i><strong>Add New Price</strong>
                        </a>
                    </li>

 
                    </li>
                     <li class=" @if(session('status.tab') == 'history-price') active @endif">
                        <a style="font-size:13px;" href="#history-price"  data-toggle="tab">
                            <i class="fa fa-cubes"></i> History price
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="view">
             @include('bakery::partials.view')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'add-price') active @endif" id="add-price">
             @include('bakery::partials.add-price')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'history-price') active @endif" id="history-price">
            @include('bakery::partials.history-price')
        </div>


    </div>

</section>