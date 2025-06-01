
<div class="modal-dialog modal-sm" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ContactController@updateVatNumber', [$contact->id]), 'method' => 'PUT', 'id' =>
    'contact_vat_number_form']) !!}
    
    <input type="hidden" id="update_fields_type" value="{{ request()->type }}">

    <div class="modal-header">
      <button type="button" class="close close-passenger-modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>

    <div class="modal-body">
        
        <input type="hidden" id="is_single_field" value="{{ request()->is_single }}">

      <div class="row">
        @if(request()->type == 'vat_number')
        <div class="col-md-12 ">
          <div class="form-group">
            {!! Form::label('vat_number', __('contact.vat_number') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('vat_number', $contact->vat_number, ['class' => 'form-control','placeholder' => __('contact.vat_number'),'id' => 'main_add_vat_number',
              'required']); !!}
            </div>
          </div>
        </div>
        @endif
        
        @if(request()->type == 'mobile')
        <div class="col-md-12 ">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control','placeholder' => __('contact.mobile'),'id' => 'add_mobile',
              'required']); !!}
            </div>
          </div>
        </div>
        @endif
        
        @if(request()->type == 'nic_number')
        <div class="col-md-12 ">
          <div class="form-group">
            {!! Form::label('nic_number', __('airline::lang.passport_number') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('nic_number', $contact->nic_number, ['class' => 'form-control','placeholder' => __('airline::lang.passport_number'),'id' => 'add_nic_number',
              'required']); !!}
            </div>
          </div>
        </div>
        @endif
        
        
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="update_vat_number">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default close-passenger-modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div>