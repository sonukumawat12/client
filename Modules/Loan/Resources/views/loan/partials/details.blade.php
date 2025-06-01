
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="contact_type" class="control-label">{{ trans_choice('core.contact', 1) }}
                {{ trans_choice('core.type', 1) }}</label>
            <select class="form-control @error('contact_type') is-invalid @enderror" name="contact_type" id="contact_type"
                required>
                <option value="">Select</option>
                <option value = "customer">
                    Customer
                </option>
                <option value = "supplier">
                    Supplier
                </option>
            </select>
            @error('contact_type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        {!! Form::label('contact_id', __('contact.contact'). ':', []) !!}
        {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
        'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
    </div>
    <div class="col-md-3" id="location_filter">
        <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id', $business_locations, null, ['class' =>
            'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
          {!! Form::label('loan_product_id', __('lang_v1.products') . ':') !!}
          {!! Form::select('loan_product_id', $loan_products, null, ['class' => 'form-control select2 loan_product_id',
          'style' =>
          'width:100%', 'id' => 'loan_product_id', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
      </div>
      
    
</div>

