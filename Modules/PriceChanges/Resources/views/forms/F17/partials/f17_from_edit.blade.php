<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'pricechanges::lang.edit_f17_form')])
    <div class="row">
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('pricechanges::lang.date') . ':') !!}
                {!! Form::text('f17_date', null, ['class' => 'form-control', 'id' => 'f17_date', 'readonly', 'style' => 'height:28px']) !!}<!-- added style=height:28px to match select input height-->
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('pricechanges::lang.price_change_form_no') . ':') !!}
                {!! Form::text('F17_from_no', $F17_from_no, ['class' => 'form-control f17_filter', 'id' => 'F17_from_no', 'readonly',  'style' => 'height:28px']) !!}<!-- added style=height:28px-->
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category_id', __('product.category') . ':') !!}
                {!! Form::select('category_id', $categories, $price_change->category_id, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':') !!}
                {!! Form::select('unit_id', $units, $price_change->unit_id, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('product_id', __('lang_v1.products') . ':') !!}
                {!! Form::select('product_id',$products, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_product_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
      
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                {!! Form::select('brand_id', $brands, $price_change->brand_id, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, $price_change->location_id, ['class' => 'form-control f17_filter select2',
                'id' => 'location_id',
                'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3"> <!-- changed class from col-sm-3 to col-md-3 for uniform responsiveness-->
            <div class="form-group">
                {!! Form::label('store_id', __('lang_v1.store_id') .  ':') !!}
                {!! Form::select('store_id', $stores, $price_change->store_id, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_store_id', 'placeholder' => __('lang_v1.please_select')]); !!}
            </div>
        </div>
    </div>

    @endcomponent
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="col-md-3 pull-right mb-12">
        <button type="submit" name="submit_type" id="f17_save" value="save" class="btn btn-primary pull-right"
        style="margin-left: 20px">@lang('mpcs::lang.save')</button>
    </div>
    @endslot
    <!-- MPCS module f17 form should be full width -->
    <div class="">
        <div class="row">
            <div class="col-md-3 mb-2 pull-right">
           	    Total Price Loss:
           	    <div class="price_changed_total_loss_value" style="color:red;">0</div>  
            </div>
            <div class="col-md-3 mb-2 pull-right">
                Total Price Gain:
                <div class="price_changed_total_gain_value" style="color:red;">0</div>
            </div>
       	 
       	
       	
       	</div>
         
       	
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
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
<script>
    $(document).ready(function(){
        $('#product_list_filter_category_id').change(function(){
            var category_id = $(this).val();
            var unit_id  = $('#product_list_filter_unit_id').val();
            // // Update the Units dropdown
            // $.ajax({
            //     url: '/pricechanges/get-unit',
            //     type: 'get',
            //     data: {category_id: category_id},
            //     success: function(response){
            //         var len = response.length;
            //         $("#product_list_filter_unit_id").empty();
            //         $("#product_list_filter_unit_id").append("<option value=''>-- Select Unit --</option>");
            //         for(var i=0; i<len; i++){
            //             var id = response[i]['id'];
            //             var name = response[i]['name'];
            //             $("#product_list_filter_unit_id").append("<option value='"+id+"'>"+name+"</option>");
            //         }
            //     }
            // });
          
            $.ajax({
                url: '/pricechanges/get-product',
                type: 'get',
                data: {category_id: category_id,unit_id: unit_id},
                success: function(response){
                    var len = response.length;
                    $("#product_list_filter_product_id").empty();
                    $("#product_list_filter_product_id").append("<option value=''>-- Select Product --</option>");
                    for(var i=0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        $("#product_list_filter_product_id").append("<option value='"+id+"'>"+name+"</option>");
                    }

                    

                    
                }
            });
            $('#form_17_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/pricechanges/create',
                data: function(d) {
                    var start_date = $('input#f17_date').val()
                    d.start_date = start_date;
                    d.category_id = $('#product_list_filter_category_id').val();
                    d.unit_id = $('#product_list_filter_unit_id').val();
                    d.brand_id = $('#product_list_filter_brand_id').val();
                    d.location_id = $('#location_id').val();
                    d.store_id = $('#store_id').val();
                    d.product_id = $("#product_list_filter_product_id").val();
                }
            },
           
            columns: [
                { data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
                { data: 'sku', name: 'products.sku' },
                { data: 'product', name: 'products.name' },
                { data: 'current_stock', name: 'vld.qty_available' },
                { data: 'unit_price', name: 'variations.default_sell_price' },
                { data: 'new_price', name: 'new_price' },
                { data: 'unit_price_difference', name: 'unit_price_difference' },
                { data: 'price_changed_loss', name: 'price_changed_loss' },
                { data: 'price_changed_gain', name: 'price_changed_gain' },
                { data: 'current_sale_price', name: 'current_sale_price' },
                { data: 'new_sale_price', name: 'new_sale_price' },
                { data: 'total_sale_difference', name: 'total_sale_difference' }
            ],
            columnDefs: [
                { width: 20, targets: 6 }
            ]
        });
        });
    });
</script>
</section>
<!-- /.content -->