<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('sr_location_id', __('purchase.business_location') . ':') !!}

                {!! Form::select('sr_location_id', $business_locations, null, ['class' => 'form-control select2',
                'style' => 'width:100%']); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('sr_product_id', __('sale.product') . ':') !!}

                {!! Form::select('sr_product_id', $products, null, ['class' => 'form-control select2',
                'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        @endcomponent
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="supplier_stock_report_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang('sale.product')</th>
                        <th>@lang('product.sku')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('purchase.purchase_quantity')</th>
                        <th>@lang('lang_v1.total_sold')</th>
                        <th>@lang('lang_v1.total_returned')</th>
                        <th>@lang('report.current_stock')</th>
                        <th>@lang('lang_v1.total_stock_price')</th>
                    </tr>
                </thead>
            </table>
        </div>
        @endcomponent
    </div>
</div>