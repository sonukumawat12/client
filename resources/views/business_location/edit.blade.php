<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
    $business_or_entity = App\System::getProperty('business_or_entity');
    @endphp
    {!! Form::open(['url' => action('BusinessLocationController@update', [$location->id]), 'method' => 'PUT', 'id' =>
    'business_location_add_form' ]) !!}

    {!! Form::hidden('hidden_id', $location->id, ['id' => 'hidden_id']); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@if($business_or_entity == 'business'){{ __('business.edit_business_location') }} @endif @if($business_or_entity == 'entity'){{ __('lang_v1.edit_entity_location') }} @endif</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __( 'invoice.name' ) . ':*') !!}
            {!! Form::text('name', $location->name, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
            'invoice.name' ) ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('location_id', __( 'lang_v1.location_id' ) . ':') !!}
            {!! Form::text('location_id', $location->location_id, ['class' => 'form-control',  'readonly', 'placeholder' => __(
            'lang_v1.location_id' ) ]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('landmark', __( 'business.landmark' ) . ':') !!}
            {!! Form::text('landmark', $location->landmark, ['class' => 'form-control', 'placeholder' => __(
            'business.landmark' ) ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('address_1', __( 'business.address_1' ) . ':') !!}
            {!! Form::text('address_1', $location->address_1, ['class' => 'form-control', 'placeholder' => __(
            'business.address_1' ) ]); !!}
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('address_2', __( 'business.address_2' ) . ':') !!}
            {!! Form::text('address_2', $location->address_2, ['class' => 'form-control', 'placeholder' => __(
            'business.address_2' ) ]); !!}
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('address_3', __( 'business.address_3' ) . ':') !!}
            {!! Form::text('address_3', $location->address_3, ['class' => 'form-control', 'placeholder' => __(
            'business.address_3' ) ]); !!}
          </div>
        </div>
        
        
        
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('city', __( 'business.city' ) . ':*') !!}
            {!! Form::text('city', $location->city, ['class' => 'form-control',  'readonly', 'placeholder' => __( 'business.city'),
            'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('zip_code', __( 'business.zip_code' ) . ':*') !!}
            {!! Form::text('zip_code', $location->zip_code, ['class' => 'form-control',  'readonly', 'placeholder' => __(
            'business.zip_code'), 'required' ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('state', __( 'business.state' ) . ':*') !!}
            {!! Form::text('state', $location->state, ['class' => 'form-control',  'readonly', 'placeholder' => __(
            'business.state'), 'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('country', __( 'business.country' ) . ':*') !!}
            {!! Form::text('country', $location->country, ['class' => 'form-control',  'readonly', 'placeholder' => __(
            'business.country'), 'required' ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('mobile', __( 'business.mobile' ) . ':') !!}
            {!! Form::text('mobile', $location->mobile, ['class' => 'form-control', 'placeholder' => __(
            'business.mobile')]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('alternate_number', __( 'business.alternate_number' ) . ':') !!}
            {!! Form::text('alternate_number', $location->alternate_number, ['class' => 'form-control', 'placeholder' =>
            __( 'business.alternate_number')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('email', __( 'business.email' ) . ':') !!}
            {!! Form::email('email', $location->email, ['class' => 'form-control', 'placeholder' => __(
            'business.email')]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('website', __( 'lang_v1.website' ) . ':') !!}
            {!! Form::text('website', $location->website, ['class' => 'form-control', 'placeholder' => __(
            'lang_v1.website')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        {{-- add by sakhawat --}}
        <div class="col-sm-6">
          <div class="form-group">
              <div class="input-group">
                {!! Form::label('currency_id', __('invoice.currency') . ':*') !!}
                {!! Form::select('currency_id', $currency,$location->currency_id, ['class' => 'form-control select2', 'required']); !!}
              </div>
          </div>
        </div>
        {{-- end add by sakhawat --}}

        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':*') !!}
            @show_tooltip(__('tooltip.invoice_scheme'))
            {!! Form::select('invoice_scheme_id', $invoice_schemes, $location->invoice_scheme_id, ['class' =>
            'form-control', 'required',
            'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('invoice_layout_id', __('invoice.invoice_layout') . ':*') !!}
            @show_tooltip(__('tooltip.invoice_layout'))
            {!! Form::select('invoice_layout_id', $invoice_layouts, $location->invoice_layout_id, ['class' =>
            'form-control', 'required',
            'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('selling_price_group_id', __('lang_v1.default_selling_price_group') . ':') !!}
            @show_tooltip(__('lang_v1.location_price_group_help'))
            {!! Form::select('selling_price_group_id', $price_groups, $location->selling_price_group_id, ['class' =>
            'form-control',
            'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('custom_field1', __('lang_v1.location_custom_field1') . ':') !!}
            {!! Form::text('custom_field1', $location->custom_field1, ['class' => 'form-control',
            'placeholder' => __('lang_v1.location_custom_field1')]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('custom_field2', __('lang_v1.location_custom_field2') . ':') !!}
            {!! Form::text('custom_field2', $location->custom_field2, ['class' => 'form-control',
            'placeholder' => __('lang_v1.location_custom_field2')]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('custom_field3', __('lang_v1.location_custom_field3') . ':') !!}
            {!! Form::text('custom_field3', $location->custom_field3, ['class' => 'form-control',
            'placeholder' => __('lang_v1.location_custom_field3')]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('custom_field4', __('lang_v1.location_custom_field4') . ':') !!}
            {!! Form::text('custom_field4', $location->custom_field4, ['class' => 'form-control',
            'placeholder' => __('lang_v1.location_custom_field4')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->