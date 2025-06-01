<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'pricechanges::lang.f17_from')])
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
                {!! Form::select('category_id', $categories, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':') !!}
                {!! Form::select('unit_id', $units, null, ['class' => 'form-control f17_filter select2', 'style' =>
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
                {!! Form::select('brand_id', $brands, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control f17_filter select2',
                'id' => 'location_id',
                'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3"> <!-- changed class from col-sm-3 to col-md-3 for uniform responsiveness-->
            <div class="form-group">
                {!! Form::label('store_id', __('lang_v1.store_id') .  ':') !!}
                {!! Form::select('store_id', $stores, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'product_list_filter_store_id', 'placeholder' => __('lang_v1.please_select')]); !!}
            </div>
        </div>
    </div>

    @endcomponent
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="col-md-3 pull-right mb-12">
        <button type="submit" name="submit_type" id="f17_save" value="save" class="btn btn-primary pull-right"
            style="margin-left: 20px">@lang('pricechanges::lang.save')</button>
    </div>
    @endslot
    <!-- MPCS module f17 form should be full width -->
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
        });
    });
</script>
</section>
<!-- /.content -->