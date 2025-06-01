<div class="row">
    <div class="box-tools" style="margin-left: 12px" >
            <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
            @if(!isset($view_page))
            <button type="button" class="btn btn-primary btn-modal"
                data-href="{{action('ContactController@create', ['type' =>'customer'])}}" data-container=".contact_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
            @endif
     </div>
    <div class="form-group col-sm-4">
          {!! Form::label('customer_id', __( 'shipping::lang.sender_customer' ) .":") !!}
          {!! Form::select('customer_id', $customers, isset($data) ? $data[0]->customer_id : null, ['class' => 'form-control select2', 'required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('address', __( 'shipping::lang.address' ) .":") !!}
          {!! Form::text('address', isset($data) ? $data[0]->address : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.address'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('mobile', __( 'shipping::lang.mobile' ) .":") !!}
          {!! Form::text('mobile', isset($data) ? $data[0]->mobile : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.mobile'),isset($view_page) ? "disabled" : '']); !!}
    </div>

</div>