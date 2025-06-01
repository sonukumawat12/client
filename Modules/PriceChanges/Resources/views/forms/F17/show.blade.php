@extends('layouts.app')
@section('title', __('mpcs::lang.F17_form'))

@section('content')
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'mpcs::lang.f17_from')])
    @slot('tool')
    
    @endslot
    <div class="col-md-12">
        <div class="table-responsive">
            <!--<table class="table table-bordered table-striped" id="form_17_table" style="width:100%;">
                <thead>
                    <tr>
                        <th>@lang('mpcs::lang.index')</th>
                        <th>@lang('mpcs::lang.product_code')</th>
                        <th>@lang('mpcs::lang.product')</th>
                        <th>@lang('mpcs::lang.current_stock')</th>
                        <th>@lang('mpcs::lang.unit_price')</th>
                        <th>@lang('mpcs::lang.select_mode')</th>
                        <th>@lang('mpcs::lang.new_price')</th>
                        <th>@lang('mpcs::lang.unit_price_difference')</th>
                        <th>@lang('mpcs::lang.price_changed_loss')</th>
                        <th>@lang('mpcs::lang.price_changed_gain')</th>
                        <th>@lang('mpcs::lang.signature')</th>
                        <th>@lang('mpcs::lang.page_no')</th>
    
                    </tr>
                </thead>
            </table>-->
            <table class="table table-bordered table-striped" id="form_17_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('pricechanges::lang.index')</th>
                    <th>@lang('pricechanges::lang.product_code')</th>
                    <th>@lang('pricechanges::lang.product')</th>
                    <th>@lang('pricechanges::lang.current_stock')</th>
                    <th>@lang('pricechanges::lang.unit_price')</th>
                    <th>@lang('pricechanges::lang.new_price')</th>
                    <th>@lang('pricechanges::lang.unit_price_difference')</th>
                    <th>@lang('pricechanges::lang.price_changed_loss')</th>
                    <th>@lang('pricechanges::lang.price_changed_gain')</th>
                    <th>@lang('pricechanges::lang.current_sale_price')</th>
                    <th>@lang('pricechanges::lang.new_sale_price')</th>
                    <th>@lang('pricechanges::lang.total_sale_diff')</th>

                </tr>
            </thead>
        </table>
        </div>
    </div>
    
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    $(document).ready(function(){
    //form_17_table 
    form_17_table = $('#form_17_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/pricechanges/{{$id}}',
            data: function(d) {
                var start_date = $('input#f17_date').val()
                d.start_date = start_date;
                d.category_id = $('#product_list_filter_category_id').val();
                d.unit_id = $('#product_list_filter_unit_id').val();
                d.brand_id = $('#product_list_filter_brand_id').val();
                d.location_id = $('#location_id').val();
                d.store_id = $('#store_id').val();
            }
        },
        columns: [
            { data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
            { data: 'sku', name: 'products.sku' },
            { data: 'product', name: 'products.name' },
            { data: 'current_stock', name: 'vld.qty_available' },
            { data: 'unit_price', name: 'variations.default_sell_price' },
            //{ data: 'select_mode', name: 'select_mode' },
            { data: 'new_price', name: 'new_price' },
            { data: 'unit_price_difference', name: 'unit_price_difference' },
            { data: 'price_changed_loss', name: 'price_changed_loss' },
            { data: 'price_changed_gain', name: 'price_changed_gain' },
            //{ data: 'total_sale_diff', name: 'total_sale_diff' },
            { data: 'signature', name: 'signature' },
            { data: 'page_no', name: 'page_no' },
        ],
        columnDefs: [
            { width: 20, targets: 6 }
        ],
    });

});
</script>
@endsection