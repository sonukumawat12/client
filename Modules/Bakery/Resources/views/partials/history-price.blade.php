<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
        'pricechanges::lang.price_changed_details')])
    <div class="row">

        <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control reload_price_details', 'id' => 'price_changed_date_range', 'readonly']); !!}
                </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category_details_id', __('pricechanges::lang.product_category') . ':') !!}
                {!! Form::select('category_details_id', $categories, null, ['class' => 'form-control category_changed_details_filter select2 reload_price_details', 'style' =>
                'width:100%', 'id' => 'price_changed_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
       
        <div class="col-md-3" id="subcategory_details_id_filter">
            <div class="form-group">
                {!! Form::label('subcategory_details_id', __('pricechanges::lang.product_subcategory') . ':') !!}
                {!! Form::select('subcategory_details_id', $subCategories, null, ['class' => 'form-control subcategory_details_filter select2 reload_price_details',
                'id' => 'subcategory_details_id',
                'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('price_changed_product_id', __('lang_v1.products') . ':') !!}
                {!! Form::select('price_changed_product_id',$products, null, ['class' => 'form-control price_changed_product_details_filter select2 reload_price_details', 'style' =>
                'width:100%', 'id' => 'price_changed__filter_product_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
    </div>
    <div class = "row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_details_id', __('pricechanges::lang.users') . ':') !!}
                {!! Form::select('user_details_id', $usersInChangeDetails, null, ['class' => 'form-control price_changed_users_filter reload_price_details select2', 'style' =>
                'width:100%', 'id' => 'price_changed_filter_user_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'pricechanges::lang.price_changed_details')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="price_changed_details_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('pricechanges::lang.date_and_time')</th>
                    <th>@lang('pricechanges::lang.current_price')</th>
                    <th>@lang('pricechanges::lang.new_price')</th>
                    <th>@lang('pricechanges::lang.user')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->