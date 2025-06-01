@extends('layouts.app')
@section('title', __('product.edit_product'))

@section('content')

@php
  $is_image_required = !empty($common_settings['is_product_image_required']) && empty($product->image);
@endphp
<style>
    .select2{
        width: 100% !important;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('product.edit_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\Modules\Vat\Http\Controllers\VatProductController::class, 'update'] , [$product->id] ), 'method' => 'PUT', 'id' => 'vat_product_add_form',
        'class' => 'vat_product_form', 'files' => true ]) !!}
    <input type="hidden" id="product_id" value="{{ $product->id }}">

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('name', __('product.product_name') . ':*') !!}
                  {!! Form::text('name', $product->name, ['class' => 'form-control', 'required',
                  'placeholder' => __('product.product_name')]); !!}
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('sku', __('product.sku')  . ':*') !!} @show_tooltip(__('tooltip.sku'))
                {!! Form::text('sku', $product->sku, ['class' => 'form-control',
                'placeholder' => __('product.sku'), 'required']); !!}
              </div>
            </div>

            
            <div class="col-sm-2">
                <div class="form-group">
                  {!! Form::label('semi_finished', __( 'unit.semi_finished' ) . ':*') !!}
                  {!! Form::select('semi_finished', ['1' => __('messages.yes'), '0' => __('messages.no')], $product->semi_finished, ['placeholder'
                  => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
                </div>
            </div>
            
             <div class="col-sm-2">
              <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                {!! Form::select('unit_id', $units, $product->unit_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                  
              </div>
            </div>
            
             <div class="col-sm-2">
                <div class="form-group">
                    <br>
                    <label>
                        {!! Form::checkbox('vat_claimed', 1, !empty($product) ? $product->vat_claimed : true, ['class' => 'input-icheck', 'id' => 'vat_claimed']); !!} <strong>@lang('product.vat_input_claimed')</strong>
    
                </div>
            </div>
          

        </div>
        
        <div class="row">
            <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
              <div class="form-group">
                {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                  {!! Form::select('tax', $taxes, $product->tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
              </div>
            </div>

            <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
              <div class="form-group">
                {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                  {!! Form::select('tax_type',['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], $product->tax_type,
                  ['class' => 'form-control select2', 'required']); !!}
              </div>
            </div>
            
            <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
                <div class="form-group">
                    {!! Form::label('sale_tax', __('product.sale_tax') . ':') !!}
                    {!! Form::select('sale_tax', $taxes, $product->sale_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
                </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                {!! Form::select('type', $product_types, $product->type, ['class' => 'form-control select2',
                  'required','disabled', 'data-action' => 'edit', 'data-product_id' => $product->id ]); !!}
              </div>
            </div>

            <div class="form-group col-sm-12" id="product_form_part"></div>
            <input type="hidden" id="variation_counter" value="0">
            <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
        </div>
            
        
        <div class="row">
            
            <div class="col-sm-6">
              <div class="form-group">
                {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                  {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control']); !!}
              </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('added_date', __('lang_v1.product_added_date') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('date', date('m/d/Y', strtotime($product->date)), ['class' => 'form-control required input_number', 'id' => 'product_added_date']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*', 'required' => $is_image_required]); !!}
                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif</p></small>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
                {!! Form::file('product_brochure', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <small>
                    <p class="help-block">
                        @lang('lang_v1.previous_file_will_be_replaced')<br>
                        @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                        @includeIf('components.document_help_text')
                    </p>
                </small>
              </div>
            </div>
    @endcomponent

  <div class="row">
    <input type="hidden" name="submit_type" id="submit_type">
        <div class="col-sm-12">
          <div class="text-center">
            <div class="btn-group">
              
              <button type="submit" value="submit" class="btn btn-primary vat_submit_product_form btn-big">@lang('messages.update')</button>
            </div>
          </div>
        </div>
  </div>
{!! Form::close() !!}
</section>
<!-- /.content -->

@endsection

@section('javascript')
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
  <script type="text/javascript">
    $(document).ready( function(){
        $("#product_added_date").datepicker({
            format: "mm/dd/yyyy",
        });
    
      __page_leave_confirmation('#product_add_form');
    });
  </script>
@endsection