@extends('layouts.app')
@section('title', __('product.add_new_product'))

@section('content')
<style>
    .select2{
        width: 100% !important;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('product.add_new_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @php
        $is_image_required = !empty($common_settings['is_product_image_required']);
    @endphp
    {!! Form::open(['url' => action([\Modules\Vat\Http\Controllers\VatProductController::class, 'store']), 'method' => 'post',
    'id' => 'vat_product_add_form', 'files' => true ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('name', __('product.product_name') . ':*') !!}
                {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control', 'required',
                'placeholder' => __('product.product_name')]); !!}
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))
                {!! Form::text('sku', null, ['class' => 'form-control',
                'placeholder' => __('product.sku')]); !!}
            </div>
        </div>
       
        
        <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('semi_finished', __( 'unit.semi_finished' ) . ':*') !!}
              {!! Form::select('semi_finished', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder'
              => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                {!! Form::select('unit_id', $units, !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'), ['class' => 'form-control select2','style' => 'width: 100% !important;', 'required']); !!}
                
            </div>
        </div>
        
    </div>
    <div class="row">
        
        
        <div class="col-sm-3">
            <div class="form-group">
                <br>
                <label>
                    {!! Form::checkbox('vat_claimed', 1, !empty($duplicate_product) ? $duplicate_product->vat_claimed : true, ['class' => 'input-icheck', 'id' => 'vat_claimed']); !!} <strong>@lang('product.vat_input_claimed')</strong>
                
            </div>
        </div>
        <div class="col-sm-3">
            
            <div class="form-group">
                {!! Form::label('added_date', __('lang_v1.product_added_date') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('date', null, ['class' => 'form-control required input_number', 'id' => 'product_added_date']); !!}
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*',
                'required' => $is_image_required, 'class' => 'upload-element']); !!}
                <small>
                    <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
                </small>
            </div>
        </div>
        
        <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
            {!! Form::file('product_brochure', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <small>
                <p class="help-block">
                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    @includeIf('components.document_help_text')
                </p>
            </small>
        </div>
    </div>
        
    </div>
    
    @endcomponent

   

    @component('components.widget', ['class' => 'box-primary'])
    <div class="row">

        <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                {!! Form::select('tax', $taxes, !empty($duplicate_product->tax) ? $duplicate_product->tax : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
            </div>
        </div>

        <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
                ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        
        <div class="col-sm-3 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('sale_tax', __('product.sale_tax') . ':') !!}
                {!! Form::select('sale_tax', $taxes, null, ['placeholder' => __('messages.please_select'),  'class' => 'form-control select2'], $tax_attributes); !!}
            </div>
        </div>

        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                {!! Form::select('type', $product_types, !empty($duplicate_product->type) ? $duplicate_product->type : null, ['class' => 'form-control select2',
                'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
            </div>
        </div>

        <div class="form-group col-sm-12" id="product_form_part">
            @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent])
        </div>

        <input type="hidden" id="variation_counter" value="1">
        <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">

    </div>
    @endcomponent
    
    <div class="row">
    
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null, ['class' => 'form-control']); !!}
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" name="submit_type" id="submit_type">
            <div class="text-center">
                <div class="btn-group">
                    <button type="submit" value="submit" class="btn btn-primary btn-big vat_submit_product_form">@lang('messages.save')</button>
                </div>

            </div>
        </div>
    </div>
    {!! Form::close() !!}

</section>
<!-- /.content -->

@endsection

@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#product_added_date").datepicker("setDate", new Date());
        
        __page_leave_confirmation('#product_add_form');
        onScan.attachTo(document, {
            suffixKeyCodes: [13], // enter-key expected at the end of a scan
            reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
            onScan: function(sCode, iQty) {
                $('input#sku').val(sCode);
            },
            onScanError: function(oDebug) {
                console.log(oDebug);
            },
            minLength: 2,
            ignoreIfFocusOn: ['input', '.form-control']
            // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
            //     console.log('Pressed: ' + iKeyCode);
            // }
        });
    });
</script>
@endsection