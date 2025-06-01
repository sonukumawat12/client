<!-- Content Header (Page header) -->



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Reports</a></li>
                    <li><span>@lang( 'report.profit_loss' )</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Main content -->

<section class="content">

    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang( 'report.profit_loss' )</h2></div>

    

    <div class="row no-print">

        <div class="col-md-3 col-md-offset-7 col-xs-6">

            <div class="input-group">

                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>

                 <select class="form-control select2" id="profit_loss_location_filter"  style="width: 100%;">

                    @foreach($business_locations as $key => $value)

                        <option value="{{ $key }}">{{ $value }}</option>

                    @endforeach

                </select>

            </div>

        </div>

        <div class="col-md-2 col-xs-6">

            <div class="form-group pull-right">

                <div class="input-group">

                  <button type="button" class="btn btn-primary" id="profit_tabs_filter">

                    <span>

                      <i class="fa fa-calendar"></i> {{ @format_date(date('Y-m-01'))." ~ ".@format_date(date('Y-m-t'))  }}

                    </span>

                    <i class="fa fa-caret-down"></i>

                  </button>

                </div>

            </div>

        </div>

        <!-- Modal for Custom Date Range -->
        <div class="modal fade" id="profit_tabs_customDateRangeModal" tabindex="-1" aria-labelledby="profit_tabs_customDateRangeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profit_tabs_customDateRangeModalLabel">Select Custom Date Range</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                                <div class="col-md-6">
                        <label for="profit_tabs_start_date">From:</label>
                        <input type="date" id="profit_tabs_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                        </div>
                                <div class="col-md-6">
                        
                        <label for="profit_tabs_end_date" class="mt-2">To:</label>
                        <input type="date" id="profit_tabs_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                        </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="profit_tabs_applyCustomRange">Apply</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        @include('report/partials/profit_loss_details')

    </div>

    



    <div class="row no-print">

        <div class="col-sm-12">

            <button type="button" class="btn btn-primary pull-right" 

            aria-label="Print" onclick="window.print();"

            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button>

        </div>

    </div>

    <br>

    <div class="row no-print">

    </div>

    <div class="row no-print">

        <div class="col-md-12">

           <!-- Custom Tabs -->

            <div class="nav-tabs-custom">

                <ul class="nav nav-tabs categorised_reports">

                    <li class="active">

                        <a href="#profit_by_products" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.profit_by_products')</a>

                    </li>



                    <li>

                        <a href="#profit_by_categories" data-toggle="tab" aria-expanded="true"><i class="fa fa-tags" aria-hidden="true"></i> @lang('lang_v1.profit_by_categories')</a>

                    </li>
                    
                    <li>

                        <a href="#profit_by_sub_categories" data-toggle="tab" aria-expanded="true"><i class="fa fa-tags" aria-hidden="true"></i> @lang('lang_v1.profit_by_sub_categories')</a>

                    </li>



                    <li>

                        <a href="#profit_by_brands" data-toggle="tab" aria-expanded="true"><i class="fa fa-diamond" aria-hidden="true"></i> @lang('lang_v1.profit_by_brands')</a>

                    </li>



                    <li>

                        <a href="#profit_by_locations" data-toggle="tab" aria-expanded="true"><i class="fa fa-map-marker" aria-hidden="true"></i> @lang('lang_v1.profit_by_locations')</a>

                    </li>



                    <li>

                        <a href="#profit_by_invoice" data-toggle="tab" aria-expanded="true"><i class="fa fa-file-text-o" aria-hidden="true"></i> @lang('lang_v1.profit_by_invoice')</a>

                    </li>



                    <li>

                        <a href="#profit_by_date" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('lang_v1.profit_by_date')</a>

                    </li>

                    <li>

                        <a href="#profit_by_customer" data-toggle="tab" aria-expanded="true"><i class="fa fa-user" aria-hidden="true"></i> @lang('lang_v1.profit_by_customer')</a>

                    </li>

                    <li>

                        <a href="#profit_by_day" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('lang_v1.profit_by_day')</a>

                    </li>

                </ul>



                <div class="tab-content">

                    <div class="tab-pane active" id="profit_by_products"> 

                        @include('report.partials.profit_by_products')

                    </div>



                    <div class="tab-pane" id="profit_by_categories">

                        @include('report.partials.profit_by_categories')

                    </div>
                    
                    <div class="tab-pane" id="profit_by_sub_categories">

                        @include('report.partials.profit_by_sub_categories')

                    </div>



                    <div class="tab-pane" id="profit_by_brands">

                        @include('report.partials.profit_by_brands')

                    </div>



                    <div class="tab-pane" id="profit_by_locations">

                        @include('report.partials.profit_by_locations')

                    </div>



                    <div class="tab-pane" id="profit_by_invoice">

                        @include('report.partials.profit_by_invoice')

                    </div>



                    <div class="tab-pane" id="profit_by_date">

                        @include('report.partials.profit_by_date')

                    </div>



                    <div class="tab-pane" id="profit_by_customer">

                        @include('report.partials.profit_by_customer')

                    </div>



                    <div class="tab-pane" id="profit_by_day">

                        

                    </div>

                </div>

            </div>

        </div>

    </div>

	



</section>

<!-- /.content -->

