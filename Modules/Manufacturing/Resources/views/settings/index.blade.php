<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Manufacturing\Http\Controllers\SettingsController@store'), 'method' => 'post', 'id' => 'manufacturing_settings_form' ]) !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('messages.settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('manufacturing::lang.wastage')</a>
                        <a href="#" class="list-group-item text-center">@lang('manufacturing::lang.extra_cost')</a>
                        <a href="#" class="list-group-item text-center">@lang('manufacturing::lang.by_products')</a>
                        <a href="#" class="list-group-item text-center">@lang('manufacturing::lang.lot_numbers')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <div class="pos-tab-content active">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('ref_no_prefix', __('manufacturing::lang.mfg_ref_no_prefix') . ':' ) !!}
                                    {!! Form::text('ref_no_prefix', !empty($manufacturing_settings['ref_no_prefix']) ? $manufacturing_settings['ref_no_prefix'] : null, ['placeholder' => __('manufacturing::lang.mfg_ref_no_prefix'), 'class' => 'form-control']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="checkbox">
                                        <label>
                                        {!! Form::checkbox('disable_editing_ingredient_qty', 1, !empty($manufacturing_settings['disable_editing_ingredient_qty']), ['class' => 'input-icheck', 'id' => 'disable_editing_ingredient_qty']); !!} @lang('manufacturing::lang.disable_editing_ingredient_qty')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="checkbox">
                                        <label>
                                        {!! Form::checkbox('enable_updating_product_price', 1, !empty($manufacturing_settings['enable_updating_product_price']), ['class' => 'input-icheck', 'id' => 'enable_updating_product_price']); !!} @lang('manufacturing::lang.enable_editing_product_price_after_production')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                    @include('manufacturing::settings.partials.wastage')
                    @include('manufacturing::settings.partials.extra_cost')
                    @include('manufacturing::settings.partials.by_products')
                    @include('manufacturing::settings.partials.lot_numbers')
                
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
        </div>
    </div>

    
    {!! Form::close() !!}
</section>