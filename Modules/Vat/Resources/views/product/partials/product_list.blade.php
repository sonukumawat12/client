@php 
    $colspan = 15;
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<table class="table table-bordered table-striped ajax_view " id="vat_product_table" style="width: 100% ">
    
    <thead>
        <tr>
            <td colspan="{{$colspan}}">
            <div style="display: flex; width: 100%;">
                @can('product.delete')
                    {!! Form::open(['url' => action([\Modules\Vat\Http\Controllers\VatProductController::class, 'massDestroy']), 'method' => 'post', 'id' => 'mass_delete_form' ]) !!}
                    {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                    {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger', 'id' => 'delete-selected')) !!}
                    {!! Form::close() !!}
                @endcan

                
                
                &nbsp;
                {!! Form::open(['url' => action([\Modules\Vat\Http\Controllers\VatProductController::class, 'massDeactivate']), 'method' => 'post', 'id' => 'mass_deactivate_form' ]) !!}
                {!! Form::hidden('selected_products', null, ['id' => 'selected_products']); !!}
                {!! Form::submit(__('lang_v1.deactivate_selected'), array('class' => 'btn btn-xs btn-warning', 'id' => 'deactivate-selected')) !!}
                {!! Form::close() !!} @show_tooltip(__('lang_v1.deactive_product_tooltip'))
                &nbsp;
                
                </div>
            </td>
        </tr>
        <tr>
            <th><input type="checkbox" id="select-all-row" data-table-id="vat_product_table"></th>
            <th>&nbsp;</th>
            <th>@lang('messages.action')</th>
            <th>@lang('sale.product')</th>
            <th>@lang('lang_v1.unit_perchase_price')</th>
            <th>@lang('lang_v1.selling_price')</th>
            <th>@lang('product.product_type')</th>
            <th>@lang('product.tax')</th>
            <th>@lang('product.sku')</th>
            <th>@lang('unit.semi_finished')</th>
        </tr>
    </thead>
    <tfoot>
        
    </tfoot>
</table>
