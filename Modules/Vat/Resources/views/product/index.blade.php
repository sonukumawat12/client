@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.products')
        <small>@lang('lang_v1.manage_products')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
<div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="active">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatProductController@index')}}" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.products')</strong>
                        </a>
                    </li>
                  
                  
                    <li class="">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatUnitController@index')}}"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.units')</strong>
                        </a>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    <div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':') !!}
                {!! Form::select('type', ['single' => __('lang_v1.single')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
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
            <br>
            <div class="form-group">
                {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

    @endcomponent
    </div>
</div>
@can('product.view')
    <div class="row">
        <div class="col-md-12">
           <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="product_list_tab">
                        
                        @can('product.create')                            
                            <a class="btn btn-primary pull-right" href="{{action([\Modules\Vat\Http\Controllers\VatProductController::class, 'create'])}}" style="margin-left: 10px;">
                                        <i class="fa fa-plus"></i> @lang('messages.add')</a> 
                                        
                            <a class="btn btn-primary pull-right" href="{{action([\Modules\Vat\Http\Controllers\ImportProductsController::class, 'index'])}}">
                                        <i class="fa fa-plus"></i> @lang('vat::lang.import')</a>
                            <br><br>
                        @endcan
                        @include('vat::product.partials.product_list')
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endcan

<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>


</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready( function(){
            vat_product_table = $('#vat_product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[3, 'asc']],
                "ajax": {
                    "url": "/vat-module/vat-products",
                    "data": function ( d ) {
                        d.type = $('#product_list_filter_type').val();
                        d.product_id = $('#product_list_filter_product_id').val();
                        d.semi_finished = $('#product_list_filter_semi_finished').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.tax_id = $('#product_list_filter_tax_id').val();
                        d.active_state = $('#active_state').val();
                        d = __datatable_ajax_callback(d);
                    }
                },
                columnDefs: [ {
                    "targets": [0, 1, 2],
                    "orderable": false,
                    "searchable": false
                } ],
                columns: [
                        { data: 'mass_delete'  },
                        { data: 'image', name: 'vat_products.image'  },
                        { data: 'action', name: 'action'},
                        { data: 'product', name: 'vat_products.name'  },
                        { data: 'purchase_price', name: 'max_purchase_price', searchable: false},
                        { data: 'selling_price', name: 'max_price', searchable: false},
                        { data: 'type', name: 'vat_products.type'},
                        { data: 'tax', name: 'tax_rates.name', searchable: false},
                        { data: 'sku', name: 'vat_products.sku'},
                        { data: 'semi_finished', name: 'vat_products.semi_finished'}
                    ],
                    createdRow: function( row, data, dataIndex ) {
                        $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                    },
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#product_table'));
                    },
            });
            

            $('table#vat_product_table tbody').on('click', 'a.delete-product', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    vat_product_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#delete-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_rows').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('form#mass_delete_form').submit();
                        }
                    });
                } else{
                    $('input#selected_rows').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            });

            $(document).on('click', '#deactivate-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_products').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            var form = $('form#mass_deactivate_form')

                            var data = form.serialize();
                                $.ajax({
                                    method: form.attr('method'),
                                    url: form.attr('action'),
                                    dataType: 'json',
                                    data: data,
                                    success: function(result) {
                                        if (result.success == true) {
                                            toastr.success(result.msg);
                                            vat_product_table.ajax.reload();
                                            form
                                            .find('#selected_products')
                                            .val('');
                                        } else {
                                            toastr.error(result.msg);
                                        }
                                    },
                                });
                        }
                    });
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            })

            

            $('table#vat_product_table tbody').on('click', 'a.activate-product', function(e){
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            vat_product_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change', '#product_list_filter_product_id,#product_list_filter_semi_finished,#product_list_filter_type, #product_list_filter_category_id,#product_list_filter_sub_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id', 
                function() {
                    vat_product_table.ajax.reload();
                   
            });
        });
    </script>
@endsection