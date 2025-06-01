<div class="row">
    <div class="form-group col-sm-2">
          {!! Form::label('package_name', __( 'shipping::lang.package_name' ) .":") !!}
          {!! Form::text('package_name', isset($data) ? $data[0]->package : null, ['class' => 'form-control to_reset', 'placeholder' =>
          __('shipping::lang.package_name'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('price_type', __( 'shipping::lang.price_type' ) .":") !!}
          {!! Form::select('price_type', ['manual' => 'Manual Price','volumetric' => 'Volumetric Price','shipping' => 'Shipping Price'], isset($data) ? $data[0]->price_type : null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('length_cm', __( 'shipping::lang.length_cm' ) .":") !!}
          {!! Form::text('length_cm', isset($data) ? $data[0]->length : null, ['class' => 'form-control to_reset', 'placeholder' =>
          __('shipping::lang.length_cm'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    <div class="form-group col-sm-2">
          {!! Form::label('width_cm', __( 'shipping::lang.width_cm' ) .":") !!}
          {!! Form::text('width_cm', isset($data) ? $data[0]->width : null, ['class' => 'form-control to_reset', 'placeholder' =>
          __('shipping::lang.width_cm'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('height_cm', __( 'shipping::lang.height_cm' ) .":") !!}
          {!! Form::text('height_cm', isset($data) ? $data[0]->height : null, ['class' => 'form-control to_reset', 'placeholder' =>
          __('shipping::lang.height_cm'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    <div class="form-group col-sm-2">
          {!! Form::label('weight_cm', __( 'shipping::lang.weight_cm' ) .":") !!}
          {!! Form::text('weight_cm', isset($data) ? $data[0]->weight : null, ['class' => 'form-control to_reset', 'placeholder' =>
          __('shipping::lang.weight_cm'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    
</div>
<div class="row">
    
    <div class="form-group col-sm-2">
          {!! Form::label('per_kg', (isset($data) && $data[0]->fixed_price_value == 1) ? 'Fixed Price' : __( 'shipping::lang.per_kg' ) .":", ['id' => 'per_kg_label']) !!}
          {!! Form::text('per_kg', isset($data) ? $data[0]->rate_per_kg : null, ['class' => 'form-control', 'readonly', 'placeholder' =>
          __('shipping::lang.per_kg')]); !!}
          <input type="hidden" id="constant_value" value="0">
          <input type="hidden" id="fixed_price_value" name="fixed_price_value" value="@if(isset($data)){{$data[0]->fixed_price_value}}@endif">
          
    </div>
    
    
    <div class="form-group col-sm-2">
          {!! Form::label('volumetric_weight', __( 'shipping::lang.volumetric_weight' ) .":") !!}
          {!! Form::text('volumetric_weight', isset($data) ? $data[0]->volumetric_weight : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.volumetric_weight')]); !!}
    </div>
    
    
    
    <div class="form-group col-sm-2">
          {!! Form::label('shipping_charge', __( 'shipping::lang.shipping_charge' ) .":") !!}
          {!! Form::text('shipping_charge', isset($data) ? number_format($data[0]->shipping_charge,2) : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.shipping_charge')]); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('declared_value', __( 'shipping::lang.declared_value' ) .":") !!}
          {!! Form::text('declared_value', isset($data) ? number_format($data[0]->declared_value,2) : null, ['class' => 'form-control to_reset','placeholder' =>
          __('shipping::lang.declared_value'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('service_fee', __( 'shipping::lang.service_fee' ) .":") !!}
          {!! Form::text('service_fee', isset($data) ? number_format($data[0]->service_fee,2) : null, ['class' => 'form-control','placeholder' =>
          __('shipping::lang.service_fee'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
    <div class="form-group col-sm-2">
          {!! Form::label('total', __( 'shipping::lang.total' ) .":") !!}
          {!! Form::text('total', isset($data) ? number_format($data[0]->total,2) : null, ['class' => 'form-control','readonly', 'placeholder' =>
          __('shipping::lang.total'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
</div>
<div class="row">
    <div class="form-group col-sm-3">
          {!! Form::label('package_description', __( 'shipping::lang.package_description' ) .":") !!}
          <textarea class="form-control to_reset" rows="3" name ="package_description" id ="package_description" @if(isset($view_page)) readonly @endif></textarea>
    </div>
      {{-- <div class="form-group col-sm-2 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"  onchange="checkBoxChanged(this)">
            <label class="form-check-label" for="flexSwitchCheckDefault"  id="clickableElement">No Need to fill</label>
      </div> --}}
    @if(!isset($view_page))
    <br><button type="button" class="btn btn-success pull-right" id="addItem">{{ __('lang_v1.add')}} </button>
    @endif
</div>
@if(!isset($view_page))
<div class="row">
    <table width="100%" id="package_items_table">
        <thead>
            <tr>
                <th>{{__( 'shipping::lang.package_name' )}}</th>
                <th>{{__( 'shipping::lang.length_cm' )}}</th>
                <th>{{__( 'shipping::lang.width_cm' )}}</th>
                <th>{{__( 'shipping::lang.height_cm' )}}</th>
                <th>{{__( 'shipping::lang.weight_cm' )}}</th>
                <th>{{__( 'shipping::lang.shipping_charge' )}}</th>
                <th>{{__( 'shipping::lang.service_fee' )}}</th>
                <th>{{__( 'shipping::lang.declared_value' )}}</th>
                <th>{{__( 'shipping::lang.total' )}}</th>
                <th>*</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th>{{__( 'shipping::lang.grand_total' )}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><span id="footer_grand_total">0.00</span></th> 
                
            </tr>
        </tfoot>
    </table>
    
</div>
 @endif
 
 <style>

.form-switch {
  display: inline-block;
}
.form-check-input {
    width: 1em;
    height: 1em;
    margin-top: 0.25em;
    vertical-align: top;
    background-color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border: 1px solid rgba(0,0,0,.25);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    -webkit-print-color-adjust: exact;
    color-adjust: exact;
}
.form-switch .form-check-input {
    width: 2em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
    background-position: left center;
    border-radius: 2em;
    transition: background-position .15s ease-in-out;
    outline: 0px !important;
}
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.form-check-input:checked {
  border: 0;
}
.form-switch .form-check-input:checked {
    background-position: right center;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
}
</style>