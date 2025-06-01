

@php
    $business_id = request()->session()->get('user.business_id');
    $subscription = Modules\Superadmin\Entities\Subscription::current_subscription($business_id);
    $pacakge_details = array();
    
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }
@endphp
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':') !!}
                {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category_id', __('product.category') . ':') !!}
                {!! Form::select('category_id', $categories, null, ['class' => 'category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                  {!! Form::select('sub_category_id', $sub_categories, null, ['class' => 'form-control select2
                  sub_category_id', 'style' =>
                  'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]);
                  !!}
              </div>
          </div>
          
          <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('semi_finished', __('unit.semi_finished') . ':') !!}
                  {!! Form::select('semi_finished', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['class' => 'form-control select2 semi_finished',
                  'style' =>
                  'width:100%', 'id' => 'product_list_filter_semi_finished', 'placeholder' => __('lang_v1.all')]); !!}
              </div>
          </div>
          
          <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('product_id', __('lang_v1.products') . ':') !!}
                  {!! Form::select('product_id', $products, null, ['class' => 'form-control select2 product_id',
                  'style' =>
                  'width:100%', 'id' => 'product_list_filter_product_id', 'placeholder' => __('lang_v1.all')]); !!}
              </div>
          </div>

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':') !!}
                {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('tax_id', __('product.tax') . ':') !!}
                {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <br>
            <div class="form-group">
                {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

        <!-- include module filter -->
        @if(!empty($pos_module_data))
            @foreach($pos_module_data as $key => $value)
                @if(!empty($value['view_path']))
                    @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                @endif
            @endforeach
        @endif

        <div class="col-md-3">
          <div class="form-group">
            <br>
            <label>
              {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck', 'id' => 'not_for_selling']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
            </label>
          </div>
        </div>
        @if($is_woocommerce)
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <label>
                      {!! Form::checkbox('woocommerce_enabled', 1, false, 
                      [ 'class' => 'input-icheck', 'id' => 'woocommerce_enabled']); !!} {{ __('lang_v1.woocommerce_enabled') }}
                    </label>
                </div>
            </div>
        @endif
    @endcomponent
    </div>
</div>
@can('product.view')
    <div class="row">
        <div class="col-md-12">
           <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    
                    @if((array_key_exists('products_all_products',$pacakge_details) && !empty($pacakge_details['products_all_products'])) || !array_key_exists('products_all_products',$pacakge_details) )
                        <li class="active">
                            <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
                        </li>
                    @endif
                    
                    @if((array_key_exists('products_stock_report',$pacakge_details) && !empty($pacakge_details['products_stock_report'])) || !array_key_exists('products_stock_report',$pacakge_details) )
                        @can('stock_report.view')
                        <li>
                            <a href="#product_stock_report" data-toggle="tab" aria-expanded="true"><i class="fa fa-hourglass-half" aria-hidden="true"></i> @lang('report.stock_report')</a>
                        </li>
                        @endcan
                    @endif
                </ul>

                <div class="tab-content">
                    @if((array_key_exists('products_all_products',$pacakge_details) && !empty($pacakge_details['products_all_products'])) || !array_key_exists('products_all_products',$pacakge_details) )
                        <div class="tab-pane active" id="product_list_tab">
                            @if($is_admin)
                                <a class="btn btn-success pull-right margin-left-10" href="{{action([\App\Http\Controllers\ProductController::class, 'downloadExcel'])}}"><i class="fa fa-download"></i> @lang('lang_v1.download_excel')</a>
                            @endif
                            @can('product.create')                            
                                <a class="btn btn-primary pull-right" href="{{action([\App\Http\Controllers\ProductController::class, 'create'])}}">
                                            <i class="fa fa-plus"></i> @lang('messages.add')</a>
                                <br><br>
                            @endcan
                            @include('product.partials.product_list')
                        </div>
                    @endif
                    
                    @if((array_key_exists('products_stock_report',$pacakge_details) && !empty($pacakge_details['products_stock_report'])) || !array_key_exists('products_stock_report',$pacakge_details) )
                        @can('stock_report.view')
                        <div class="tab-pane" id="product_stock_report">
                            @include('report.partials.stock_report_table')
                        </div>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
@endcan
<input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@if($is_woocommerce)
    @include('product.partials.toggle_woocommerce_sync_modal')
@endif
@include('product.partials.edit_product_location_modal')

</section>
<!-- /.content -->