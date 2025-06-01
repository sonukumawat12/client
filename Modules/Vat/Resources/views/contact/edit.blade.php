<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
    $form_id = 'vat_contact_edit_form';
    @endphp
    
    {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatContactController@update',[$contact->id]), 'method' => 'PUT', 'id' => $form_id]) !!}

    <div class="modal-header">
      <button type="button" class="close closing_contact_modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="type" value="{{$contact->type}}">
        
        <div class="col-md-4   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']);
              !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-4   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.should_notify') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('should_notify', ['1' => __('messages.yes'), '0' => __('messages.no')], $contact->should_notify, ['placeholder'
                  => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-4   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.credit_notification') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('credit_notification', ['vat_sale' => 'VAT Sale'], $contact->credit_notification, ['placeholder'
                  => __( 'contact.none' ), 'required', 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>

        <div class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
              </span>
              {!! Form::text('contact_id', !empty($contact_id) ? $contact_id : null, ['class' => 'form-control','placeholder' =>
              __('lang_v1.contact_id'), 'readonly']); !!}
            </div>
          </div>
        </div>

        <div
          class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
              </span>
              {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control input_number', 'required', 'placeholder' =>
              __('contact.mobile')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('alternate_number', $contact->alternate_number, ['class' => 'form-control input_number', 'placeholder' =>
              __('contact.alternate_contact_number')]); !!}
            </div>
          </div>
        </div>
        
        <div
          class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('vat_no', __('contact.vat_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('vat_no', $contact->vat_no, ['class' => 'form-control input_number', 'placeholder' =>
              __('contact.vat_number')]); !!}
            </div>
          </div>
        </div>
     
        </div>

       
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default closing_contact_modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div>
<script>
    $(document).on('click', '.closing_contact_modal', function() {
        $('.contact_modal_recipient').modal('hide');
        $('.contact_modal').modal('hide');
    })
</script>