<div class="row">
    <div class="form-group col-sm-4">
          {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) .":") !!}
          {!! Form::select('shipping_mode', $shipping_mode, isset($data) ? $data[0]->shipping_mode : null, ['class' => 'form-control select2','required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
     
    <div class="form-group col-sm-4">
          {!! Form::label('shipping_package', __( 'shipping::lang.shipping_package' ) .":") !!}
          {!! Form::select('shipping_package', $package, isset($data) ? $data[0]->package_type_id : null, ['class' => 'form-control select2','required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('shipping_delivery', __( 'shipping::lang.shipping_delivery' ) .":") !!}
          {!! Form::select('shipping_delivery', $shipping_delivery, isset($data) ? $data[0]->schedule_id : null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
</div>
<div class="row">
    
    <div class="form-group col-sm-4">
          {!! Form::label('delivery_time', __( 'shipping::lang.delivery_date' ) .":") !!}
          <input type="date" class="form-control" name="delivery_time" value="@if(isset($data)){{$data[0]->delivery_time}}@endif" @if(isset($view_page)) readonly @endif>
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' ) .":") !!}
          {!! Form::select('shipping_partner', $shipping_partner, isset($data) ? $data[0]->shipping_partner : null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-4">
          {!! Form::label('shipping_status', __( 'shipping::lang.shipping_status' ) .":") !!}
          {!! Form::select('shipping_status', $shipping_status, isset($data) ? $data[0]->delivery_status : null, ['class' => 'form-control select2','required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
</div>
<div class="row">
    
    <div class="form-group col-sm-4">
          {!! Form::label('drivers', __( 'shipping::lang.driver' ) .":") !!}
          {!! Form::select('drivers', $drivers, isset($data) ? $data[0]->delivery_status : null, ['class' => 'form-control select2','required', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    @if(!isset($view_page))
    <div class="form-group col-sm-4">
          {!! Form::label('attachment', __( 'shipping::lang.package_image' )) !!}
          {!! Form::file('attachment', ['accept' => 'image/*']) !!}
        </div>
    @endif
    
    
</div>