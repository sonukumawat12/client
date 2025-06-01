
<!-- Content Header (Page header) -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.items_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_supplier_id', __('purchase.supplier') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('ir_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_purchase_date_filter', __('purchase.purchase_date') . ':') !!}
                    {!! Form::text('ir_purchase_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <!-- Modal for Custom Date Range -->
            <div class="modal fade" id="ir_purchase_customDateRangeModal" tabindex="-1" aria-labelledby="ir_purchase_customDateRangeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ir_purchase_customDateRangeModalLabel">Select Custom Date Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                            <label for="ir_purchase_start_date">From:</label>
                            <input type="date" id="ir_purchase_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                                <div class="col-md-6">
                            
                            <label for="ir_purchase_end_date" class="mt-2">To:</label>
                            <input type="date" id="ir_purchase_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="ir_purchase_applyCustomRange">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_sale_date_filter', __('lang_v1.sell_date') . ':') !!}
                    {!! Form::text('ir_sale_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <!-- Modal for Custom Date Range -->
            <div class="modal fade" id="ir_sale_customDateRangeModal" tabindex="-1" aria-labelledby="ir_sale_customDateRangeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ir_sale_customDateRangeModalLabel">Select Custom Date Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                            <label for="ir_sale_start_date">From:</label>
                            <input type="date" id="ir_sale_start_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                                <div class="col-md-6">
                            
                            <label for="ir_sale_end_date" class="mt-2">To:</label>
                            <input type="date" id="ir_sale_end_date" class="form-control custom_start_end_date_range" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="ir_sale_applyCustomRange">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_id', __('lang_v1.products') . ':') !!}
                    {!! Form::select('product_id', $products, null, ['class' => 'form-control select2 product_id',
                    'style' =>
                    'width:100%', 'id' => 'ir_product_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_location_id', __('purchase.business_location').':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::select('ir_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required', 'style' => 'width: 100%']); !!}
                    </div>
                </div>
            </div>
            @if(Module::has('Manufacturing'))
                <div class="col-md-3">
                    <div class="form-group">
                        <br>
                        <div class="checkbox">
                            <label>
                              {!! Form::checkbox('only_mfg', 1, false, 
                              [ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!} {{ __('manufacturing::lang.only_mfg_products') }}
                            </label>
                        </div>
                    </div>
                </div>
            @endif
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" 
                    id="items_report_table">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.unit')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('purchase.purchase_date')</th>
                                <th>@lang('lang_v1.purchase')</th>
                                <th>@lang('purchase.supplier')</th>
                                <th>@lang('lang_v1.purchase_price')</th>
                                <th>@lang('lang_v1.sell_date')</th>
                                <th>@lang('business.sale')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                <th>@lang('lang_v1.selling_price')</th>
                                <th>@lang('sale.subtotal')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_pp" 
                                    class="display_currency" data-currency_symbol="true"></td>
                                <td colspan="4"></td>
                                <td id="footer_total_qty"></td>
                                <td id="footer_total_sp"
                                    class="display_currency" data-currency_symbol="true"></td>
                                <td id="footer_total_subtotal"
                                    class="display_currency" data-currency_symbol="true"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">