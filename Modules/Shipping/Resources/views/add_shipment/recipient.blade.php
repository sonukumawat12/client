<div class="row">
    <div class="box-tools" style="margin-left: 12px" >
            <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
             @if(!isset($view_page))
             <button type="button" class="btn btn-primary btn-modal"
                data-href="{{action('\Modules\Shipping\Http\Controllers\RecipientController@create')}}" data-container=".contact_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
             @endif
     </div>
    <div class="form-group col-sm-4">
          {!! Form::label('recipient_id', __( 'shipping::lang.recipient' ) .":") !!}
          {!! Form::select('recipient_id', $recipients, isset($data) ? $data[0]->recipient_id : null, ['class' => 'form-control select2', 'required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_address', __( 'shipping::lang.address' ) .":") !!}
          {!! Form::text('rec_address', isset($data) ? $data[0]->rec_address : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.address')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_mobile_1', __( 'shipping::lang.mobile_1' ) .":") !!}
          {!! Form::text('rec_mobile_1', isset($data) ? $data[0]->rec_mobile_1 : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.mobile_1')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_mobile_2', __( 'shipping::lang.mobile_2' ) .":") !!}
          {!! Form::text('rec_mobile_2', isset($data) ? $data[0]->rec_mobile_2 : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.mobile_2')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_postal_code', __( 'shipping::lang.postal_code' ) .":") !!}
          {!! Form::text('rec_postal_code', isset($data) ? $data[0]->rec_postal_code : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.postal_code')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_land_no', __( 'shipping::lang.land_no' ) .":") !!}
          {!! Form::text('rec_land_no', isset($data) ? $data[0]->rec_land_no : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.land_no')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('rec_landmarks', __( 'shipping::lang.landmarks' ) .":") !!}
          {!! Form::text('rec_landmarks', isset($data) ? $data[0]->rec_landmarks : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.landmarks')]); !!}
    </div>
    
</div>