<div class="pos-tab-content @if(session('status.product_categories')) active @endif">

    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('account.default_payment_accounts')
            <small>@lang('account.manage_default_account')</small>
        </h1>
    </section>
    
    <!-- Main content -->
    <section class="content">
        @can('account.access')
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="@if(!session('status.product_categories')) active @endif">
                            <a href="#product_categories" data-toggle="tab">
                                <i class="fa fa-book"></i> <strong>@lang('superadmin::lang.product_categories')</strong>
                            </a>
                        </li>
                        <li class="@if(session('status.expense_categories')) active @endif">
                            <a href="#expense_categories" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                @lang('superadmin::lang.expense_categories') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane @if(!session('status.product_categories')) active @endif" id="product_categories">
                            <div class="row">
                                <div class="col-md-12">
                                    @component('components.widget')
                                        @slot('tool')
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-primary btn-modal" 
                                                data-href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@addProductCategory')}}" 
                                                data-container=".category_modal">
                                                <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                            </div>
                                        @endslot
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="all_category_table" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>@lang( 'category.category' )</th>
                                                        <th>@lang( 'category.code' )</th>
                                                        <th>@lang( 'category.sub_category' )</th>
                                                        <th>@lang( 'category.sub_cat_code' )</th>
                                                        <th>@lang( 'category.cogs' )</th>
                                                        <th>@lang( 'category.sales_accounts' )</th>
                                                        
                                                        <th>@lang( 'category.price_reduction_acc' )</th>
                                                        <th>@lang( 'category.price_increment_acc' )</th>
                                                        <th>@lang( 'category.remaining_stock_adjusts' )</th>
                                                        <th class="notexport">@lang( 'messages.action' )</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                         
                        <div class="tab-pane @if(session('status.expense_categories')) active @endif" id="expense_categories">
                            <div class="row">
                                <div class="col-md-12">
                                   @component('components.widget')
                                        @slot('tool')
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-primary btn-modal" 
                                                data-href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@addExpenseCategory')}}" 
                                                data-container=".expense_category_modal">
                                                <i class="fa fa-plus"></i> @lang( 'messages.add')</button>
                                            </div>
                                        @endslot
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="expense_categories_table" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>@lang( 'expense.category_name' )</th>
                                                        <th>@lang( 'expense.category_code' )</th>
                                                        <th>@lang( 'expense.expense_account' )</th>
                                                        <th>Payee Name</th>
                                                        <th>@lang( 'messages.action' )</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </section>

    <!-- /.content -->
</div>
