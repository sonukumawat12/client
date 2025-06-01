@extends('layouts.app')
@section('title', __('Add Shipment SW'))

<style>
    .select2 {
        width: 100% !important;
    }
</style>
@section('content')

{!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\AddShipmentSWController@store'), 'method' => 'post', 'id' => 'add_purchase_form',
	'files' => true ]) !!}
    <section class="content-header">
        
    <div class="form-row">
        <div class="clearfix mt-10"></div>
        <div class="row m-0">
           <div class="col-md-3 col-3">
                <div class="form-group">
                  {!! Form::label('date', __( 'shipping::lang.date' ) . ':*') !!}
                  {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'shipping::lang.date' )]); !!}
                </div>
            </div> 
           {{-- Hide On Aug 07-2024 By Sakhawat --}}
           <div class="col-md-3 col-3 hide">
                <div class="form-group">
                  {!! Form::label('tracking_no', __( 'shipping::lang.tracking_no' ) . ':*') !!}
                  {!! Form::text('tracking_no', $tracking_no, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'shipping::lang.tracking_no' )]); !!}
                </div>
            </div> 
            {{-- Hide On Aug 07-2024 By Sakhawat --}}
            <div class="col-md-3 col-3 hide">
                <div class="form-group">
                  {!! Form::label('total_payable', __( 'shipping::lang.total_payable' ) . ':*') !!}
                  {!! Form::text('total_payable_formatted', 0, ['class' => 'form-control', 'required', 'readonly', 'id' => 'total_payable_formatted', 'placeholder' => __(
                  'shipping::lang.total_payable' )]); !!}
                  <input type="hidden" name="total_payable" id="total_payable"> 
                </div>
            </div>
            <div class="col-md-3 col-3">
                <div class="form-group" style="margin-left:12px;">
                    {!! Form::hidden('currency', $currency->symbol,['id'=>'currency']) !!}
                    {!! Form::label('location_id', __('shipping::lang.select_location') . ':') !!}
                    {!! Form::select('location_id', $businessLocations, null, [
                        'id' => 'location',
                        'class' => 'form-control select2'
                    ]); !!}
                </div>
            </div>
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('agent_no', __( 'shipping::lang.shipping_agents' ) .":") !!}
                    {!! Form::select('agent_no', $shipping_agents, isset($data) ? $data[0]->agent_id : null, ['class' => 'form-control select2', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
                </div>
            </div>
            <div class="col-md-3 col-3">
                {{-- <div class="form-group" >
                    {!! Form::label('customer_id', __( 'shipping::lang.sender_customer' ) .":") !!}
                    <div class="box-tools-btn">
                            <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
                            @if(!isset($view_page))
                                <button type="button" class="btn btn-primary btn-modal btn-sm btn-login"
                                    data-href="{{action('ContactController@create', ['type' =>'customer'])}}" data-container=".contact_modal">
                                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
                            @endif
                        </div>
                    {!! Form::select('customer_id', $customers, isset($data) ? $data[0]->customer_id : null, ['class' => 'form-control select2', 'required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
                </div> --}}
                <div class="form-group" >
                    {!! Form::label('customer_id', __( 'shipping::lang.sender_customer' ) .":") !!}
                    <div class="input-group">
                        {!! Form::select('customer_id', $customers, isset($data) ? $data[0]->customer_id : null, ['class' => 'form-control js-states  select2', 'required', 'placeholder' =>
                        __('messages.please_select'),isset($view_page) ? "disabled" : '',"style"=>'70px',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
                        
                        @if(!isset($view_page))
                        <span class="input-group-btn">
                            <button type="button"  class="btn btn-primary add-on-btn btn-modal" data-href="{{action('ContactController@create', ['type' =>'customer'])}}" data-container=".contact_modal"><i class="fa fa-plus fa-lg"></i></button>
                        </span>
                        @endif
                    </div> 
                </div>
            </div> 
               
        </div>
        <div class="clearfix mt-10"></div>
        <div class="row m-0">
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('address', __( 'shipping::lang.address' ) .":") !!}
                    {!! Form::text('address', isset($data) ? $data[0]->address : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.address'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">
                    
                <div class="form-group">
                    {!! Form::label('mobile', __( 'shipping::lang.mobile' ) .":") !!}
                    {!! Form::text('mobile', isset($data) ? $data[0]->mobile : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.mobile'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
                
            </div>  
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('recipient_id', __( 'shipping::lang.recipient' ) .":") !!}
                    {{--<div class="box-tools-btn">
                        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
                        @if(!isset($view_page))
                        <button type="button" class="btn btn-primary btn-modal btn-sm btn-login"
                            data-href="{{action('\Modules\Shipping\Http\Controllers\RecipientController@create')}}" data-container=".contact_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')</button>
                        @endif
                    </div>  --}}
                    <div class="input-group">
                    {!! Form::select('recipient_id', $recipients, isset($data) ? $data[0]->recipient_id : null, ['class' => 'form-control select2', 'required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                    <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
                        @if(!isset($view_page))
                        <span class="input-group-btn">
                            <button type="button"  class="btn btn-primary add-on-btn btn-modal" data-href="{{action('\Modules\Shipping\Http\Controllers\RecipientController@create')}}" data-container=".contact_modal"><i class="fa fa-plus fa-lg"></i></button>
                        </span>
                        
                        @endif
                    </div>
                </div>        
                    
            </div> 
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_address', __( 'shipping::lang.address' ) .":") !!}
                    {!! Form::text('rec_address', isset($data) ? $data[0]->rec_address : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.address'),"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div> 
        </div>
        <div class="clearfix mt-10"></div>
        <div class="row m-0">
                
                   
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_mobile_1', __( 'shipping::lang.mobile_1' ) .":") !!}
                    {!! Form::text('rec_mobile_1', isset($data) ? $data[0]->rec_mobile_1 : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.mobile_1'),"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_mobile_2', __( 'shipping::lang.mobile_2' ) .":") !!}
                    {!! Form::text('rec_mobile_2', isset($data) ? $data[0]->rec_mobile_2 : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.mobile_2'),'autocomplete'=>'true',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div> 
             <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_postal_code', __( 'shipping::lang.postal_code' ) .":") !!}
                    {!! Form::text('rec_postal_code', isset($data) ? $data[0]->rec_postal_code : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.postal_code'),"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_land_no', __( 'shipping::lang.land_no' ) .":") !!}
                    {!! Form::text('rec_land_no', isset($data) ? $data[0]->rec_land_no : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.land_no'),"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>   
            
        </div> 
        <div class="clearfix mt-10"></div>
        <div class="row m-0">  
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('rec_landmarks', __( 'shipping::lang.landmarks' ) .":") !!}
                    {!! Form::text('rec_landmarks', isset($data) ? $data[0]->rec_landmarks : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.landmarks'),"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div> 
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) .":") !!}
                    {!! Form::select('shipping_mode', $shipping_mode, isset($data) ? $data[0]->shipping_mode : null, ['class' => 'form-control select2','required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>
            <div class="col-md-3 col-3">        
            
                <div class="form-group">
                    {!! Form::label('shipping_package', __( 'shipping::lang.shipping_package' ) .":") !!}
                    {!! Form::select('shipping_package', $package, isset($data) ? $data[0]->package_type_id : null, ['class' => 'form-control select2','required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">        
                    
                <div class="form-group">
                    {!! Form::label('shipping_delivery', __( 'shipping::lang.shipping_delivery' ) .":") !!}
                    {!! Form::select('shipping_delivery', $shipping_delivery, isset($data) ? $data[0]->schedule_id : null, ['class' => 'form-control select2', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>        
               
            
        </div>   
        <div class="clearfix mt-10"></div>
        <div class="row m-0"> 
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('delivery_time', __( 'shipping::lang.delivery_date' ) .":") !!}
                    <input type="date" class="form-control" name="delivery_time" value="@if(isset($data)){{$data[0]->delivery_time}}@endif" @if(isset($view_page)) readonly @endif>
                </div>
            </div>        
            <div class="col-md-3 col-3">        
                <div class="form-group">
                    {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' ) .":") !!}
                    {!! Form::select('shipping_partner', $shipping_partner, isset($data) ? $data[0]->shipping_partner : null, ['class' => 'form-control select2', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autofocus"=>"1","autocomplete"=>"on"]); !!}
                </div>
            </div>    
            <div class="col-md-3 col-3">              
                <div class="form-group">
                    {!! Form::label('shipping_status', __( 'shipping::lang.shipping_status' ) .":") !!}
                    {!! Form::select('shipping_status', $shipping_status, isset($data) ? $data[0]->delivery_status : null, ['class' => 'form-control select2','required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">     
                <div class="form-group">
                    {!! Form::label('drivers', __( 'shipping::lang.driver' ) .":") !!}
                    {!! Form::select('drivers', $drivers, isset($data) ? $data[0]->delivery_status : null, ['class' => 'form-control select2','required', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
           
        </div>
        <div class="clearfix mt-15"></div>
        <!--Small Div code-->
        <div class="row m-0">  
            <div class="col-md-3 col-3">     
                @if(!isset($view_page))
                <div class="form-group">
                    {!! Form::label('attachment', __( 'shipping::lang.package_image' )) !!}
                    {!! Form::file('attachment', ['accept' => 'image/*']) !!}
                    </div>
                @endif
            </div> 
            <div class="col-md-3 col-3">     
                <div class="form-group">
                    {!! Form::label('package_name', __( 'shipping::lang.package_name' ) .":") !!}
                    {!! Form::text('package_name', isset($data) ? $data[0]->package : null, ['class' => 'form-control to_reset', 'placeholder' =>
                    __('shipping::lang.package_name'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">          
                <div class="form-group">
                    {!! Form::label('price_type', __( 'shipping::lang.price_type' ) .":") !!}
                    {!! Form::select('price_type', ['manual' => 'Manual Price','volumetric' => 'Volumetric Price','shipping' => 'Shipping Price'], isset($data) ? $data[0]->price_type : null, ['class' => 'form-control select2', 'placeholder' =>
                    __('messages.please_select'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">          
                <div class="form-group">
                    {!! Form::label('length_cm', __( 'shipping::lang.length_cm' ) .":") !!}
                    {!! Form::text('length_cm', isset($data) ? $data[0]->length : null, ['class' => 'form-control to_reset', 'placeholder' =>
                    __('shipping::lang.length_cm'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>  
    </div>  
    <div class="clearfix mt-10"></div>
        <div class="row m-0">         
            <div class="col-md-3 col-3">          
                <div class="form-group">
                    {!! Form::label('width_cm', __( 'shipping::lang.width_cm' ) .":") !!}
                    {!! Form::text('width_cm', isset($data) ? $data[0]->width : null, ['class' => 'form-control to_reset', 'placeholder' =>
                    __('shipping::lang.width_cm'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">          
                <div class="form-group">
                    {!! Form::label('height_cm', __( 'shipping::lang.height_cm' ) .":") !!}
                    {!! Form::text('height_cm', isset($data) ? $data[0]->height : null, ['class' => 'form-control to_reset', 'placeholder' =>
                    __('shipping::lang.height_cm'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>        
            <div class="col-md-3 col-3">          
                <div class="form-group">
                    {!! Form::label('weight_cm', __( 'shipping::lang.weight_cm' ) .":") !!}
                    {!! Form::text('weight_cm', isset($data) ? $data[0]->weight : null, ['class' => 'form-control to_reset', 'placeholder' =>
                    __('shipping::lang.weight_cm'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>       
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('per_kg', (isset($data) && $data[0]->fixed_price_value == 1) ? 'Fixed Price' : __( 'shipping::lang.per_kg' ) .":", ['id' => 'per_kg_label']) !!}
                    {!! Form::text('per_kg', isset($data) ? $data[0]->rate_per_kg : null, ['class' => 'form-control', 'readonly', 'placeholder' =>
                    __('shipping::lang.per_kg'),"autocomplete"=>"on"]); !!}
                    <input type="hidden" id="constant_value" value="0">
                    <input type="hidden" id="fixed_price_value" name="fixed_price_value" value="@if(isset($data)){{$data[0]->fixed_price_value}}@endif">
                    
                </div>
                    
            </div>    
            </div>  
    <div class="clearfix mt-10"></div>
        <div class="row m-0">         
    
            <div class="col-md-3 col-3">
                    
                <div class="form-group">
                    {!! Form::label('volumetric_weight', __( 'shipping::lang.volumetric_weight' ) .":") !!}
                    {!! Form::text('volumetric_weight', isset($data) ? $data[0]->volumetric_weight : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.volumetric_weight'),"autocomplete"=>"on"]); !!}
                </div>
                    
            </div>       
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('shipping_charge', __( 'shipping::lang.shipping_charge' ) .":") !!}
                    {!! Form::text('shipping_charge', isset($data) ? number_format($data[0]->shipping_charge,2) : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.shipping_charge'),"autocomplete"=>"on"]); !!}
                </div>
            </div>       
            <div class="col-md-3 col-3">
            
                <div class="form-group">
                    {!! Form::label('declared_value', __( 'shipping::lang.declared_value' ) .":") !!}
                    {!! Form::text('declared_value', isset($data) ? number_format($data[0]->declared_value,2) : null, ['class' => 'form-control to_reset','placeholder' =>
                    __('shipping::lang.declared_value'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div>       
            <div class="col-md-3 col-3">
                    
                    <div class="form-group">
                        {!! Form::label('service_fee', __( 'shipping::lang.service_fee' ) .":") !!}
                        {!! Form::text('service_fee', isset($data) ? number_format($data[0]->service_fee,2) : null, ['class' => 'form-control','placeholder' =>
                        __('shipping::lang.service_fee'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                    </div>
            </div>   
            </div>  
    <div class="clearfix mt-10"></div>
        <div class="row m-0">         
    
            <div class="col-md-3 col-3">
                    
                <div class="form-group">
                    {!! Form::label('total', __( 'shipping::lang.total' ) .":") !!}
                    {!! Form::text('total', isset($data) ? number_format($data[0]->total,2) : null, ['class' => 'form-control','readonly', 'placeholder' =>
                    __('shipping::lang.total'),isset($view_page) ? "disabled" : '',"autocomplete"=>"on"]); !!}
                </div>
            </div> 
            <div class="col-md-3 col-3">
                <div class="form-group">
                    {!! Form::label('package_description', __( 'shipping::lang.package_description' ) .":") !!}
                    <textarea class="form-control to_reset" rows="3" name ="package_description" id ="package_description" @if(isset($view_page)) readonly @endif autocomplete = "on"></textarea>
                </div>
            </div>
              {{-- <div class="form-group col-sm-2 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"  onchange="checkBoxChanged(this)">
                            <label class="form-check-label" for="flexSwitchCheckDefault"  id="clickableElement">No Need to fill</label>
                    </div> --}}
                    @if(!isset($view_page))
                    <br><button type="button" class="btn btn-success pull-right" id="addItem" style="margin-right:15px">{{ __('lang_v1.add')}} </button>
                    @endif
        </div>
        <div class="clearfix mt-10"></div>
      
                @if(!isset($view_page))
                <div class="row m-0">
                    <table width="100%" id="package_items_table" class="table table-bordered table-hover table-no-side-cell-border table-responsive">
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
            </div>
            <div class="clearfix mt-15"></div>
            @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
                <div class="box-body payment_row" data-row_id="0">
                    <div id="payment_rows_div">
                        
                        @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                        <hr>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row m-0 mt-15">
                        <div class="pull-right">
                            <strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span>
                        </div>
                    </div>  
                     <div class="clearfix"></div>
                    <div class="row m-0 mt-15">
                        <div class="col-md-10">
                            <button type="button" class="btn btn-primary  mt-15" id="add-payment-row">@lang('sale.add_payment_row')</button>
                        </div>
                        <div class="col-md-2">
                           @if(!isset($view_page))
                            {!! Form::hidden('make_print',0, ['class'=>'take_print']) !!}
                            <button type="submit" id="save_print_formBtn" class="btn btn-primary  btn-flat  mt-15">@lang('messages.save_and_print')</button>
                           
                            <button type="submit" id="save_formBtn" class="btn btn-success pull-right btn-flat  mt-15">@lang('messages.save')</button>
                            @endif

                        </div>
                    </div>
                    
                </div>
                @endcomponent
        </div>
        </div>
    </section>

{!! Form::close() !!}

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection


@section('javascript') 
    <script src="{{asset('Modules/Shipping/Resources/assets/js/app.js')}}"></script>
    <script>
    $(document).ready(function() {
        $('#save_print_formBtn').click(function(){
            $('body').find('.take_print').val(1);
        })
        $('#save_formBtn').click(function(){
            $('body').find('.take_print').val(0);
        })
        $('#customer_id').change(function() {
            var customer_id = $(this).val();
            
            if(customer_id != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@customer_details') }}',
                        dataType: 'json',
                        data: {'id' : customer_id},
                        success: function(result) {
                            var customer = result.customer;
                            
                            $("#address").val(customer.address);
                            $("#mobile").val(customer.mobile);
                        },
                });
            }
        });
        $('#location').change(function () {
            var location = $(this).val();
            $.ajax({
                method: 'POST',
                url: '{{ route("location.currency") }}',
                dataType: 'json',
                data: {'id' : location},
                success: function(result) {
                $('body').find('#currency').val(result.currency);
                }
            })        
            
        })
        $('#recipient_id').change(function() {
            var rec_id = $(this).val();
            
            
            if(rec_id != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@recipient_details') }}',
                        dataType: 'json',
                        data: {'id' : rec_id},
                        success: function(result) {
                            var recipient = result.recipient;
                           
                            $("#rec_address").val(recipient.address);
                            $("#rec_mobile_1").val(recipient.mobile_1);
                            $("#rec_mobile_2").val(recipient.mobile_2);
                            $("#rec_postal_code").val(recipient.postal_code);
                            $("#rec_land_no").val(recipient.land_no);
                            $("#rec_landmarks").val(recipient.landmarks);
                            
                        },
                });
            }
        });
        
        $('#shipping_mode,#shipping_partner,#shipping_package').change(function() {
            var shipping_mode = $("#shipping_mode").val();
            var shipping_partner = $("#shipping_partner").val();
            var shipping_package = $("#shipping_package").val();
            
            
            if(shipping_mode != "" && shipping_partner != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@getRatePerKg') }}',
                        dataType: 'json',
                        data: {shipping_mode,shipping_partner,shipping_package},
                        success: function(result) {
                            //2b task done updated by dushyant
                            const label = document.getElementById('per_kg_label');
                            if(result.fixed_price == 0){
                                label.innerText = 'Price Per Kg:';
                                document.getElementById('fixed_price_value').value=0;
                            }else{                                
                                document.getElementById('fixed_price_value').value=1;
                                label.innerText = 'Fixed Price:';
                            }
                            $("#per_kg").val(result.cost);
                            $("#constant_value").val(result.constant);
                        },
                });
            }else{
                $("#per_kg").val(0);
                $("#constant_value").val(0);
            }
            $("#per_kg").trigger('change');
            $("#constant_value").trigger('change');
        });
        
        
        $('#constant_value,#length_cm,#width_cm,#height_cm,#shipping_charge,#service_fee').change(function() {
            var constant_value = __read_number($("#constant_value"));
            var length_cm =  __read_number($("#length_cm"));
            var width_cm =  __read_number($("#width_cm"));
            var height_cm =  __read_number($("#height_cm"));
            var fixed_price_value   =  __read_number($("#fixed_price_value"));
            var per_kg              =  __read_number($("#per_kg"));
            var shipping_charge     =  __read_number($("#shipping_charge"));
            var service_fee         =  __read_number($("#service_fee"));
            
             if($("#price_type").val() == 'manual'){
                var amount =    parseFloat(shipping_charge)+ parseFloat(service_fee);
                $("#total").val(__number_uf(__number_f(amount)));
                $(".payment-amount").val(__number_uf(__number_f(amount)));   
            }else{
                 
                var volumetric_weight = ((length_cm * width_cm * height_cm) / (constant_value * 1000)) ?? 0;
                
                if (isNaN(volumetric_weight)) {
                    volumetric_weight = 0;
                }
                
                $("#volumetric_weight").val(volumetric_weight);
                
                if(fixed_price_value == 0){
                    var amount =  per_kg * volumetric_weight + shipping_charge + service_fee;
                }else{
                    var amount = per_kg + shipping_charge+ service_fee;

                }
                
                $("#total").val(__number_uf(__number_f(amount)));
                $(".payment-amount").val(__number_uf(__number_f(amount))); 
            }

            
        });
                
        
        $('#constant_value,#per_kg,#length_cm,#width_cm,#height_cm,#price_type,#weight_cm').change(function() {
            var constant_value = __read_number($("#constant_value"));
            var length_cm = __read_number($("#length_cm"));
            var width_cm = __read_number($("#width_cm"));
            var height_cm = __read_number($("#height_cm"));
            var price_type = $("#price_type").val();
            
            if(price_type == 'manual'){
                $("#shipping_charge").attr("readonly", false); 
                $("#per_kg").val(0);
                $("#volumetric_weight").val(0);
                $("#shipping_charge").trigger('change');
            }else{
                var volumetric_weight  = "";
                volumetric_weight = (length_cm * width_cm * height_cm) / (constant_value * 1000);
                
                var shipping_charge1 = volumetric_weight * __read_number($("#per_kg"));
                var shipping_charge2 = __read_number($("#weight_cm")) * __read_number($("#per_kg"));
                
                if(shipping_charge1 > shipping_charge2 || shipping_charge2 == "" || shipping_charge2 == "NaN"){
                    $("#shipping_charge").val(__number_uf(__number_f(shipping_charge1)));
                }else if(shipping_charge2 > shipping_charge1 || shipping_charge1 == "" || shipping_charge1 == "NaN"){
                    $("#shipping_charge").val(__number_uf(__number_f(shipping_charge2)));
                }else{
                    $("#shipping_charge").val(__number_uf(__number_f(shipping_charge1)));
                }
                
                if($("#shipping_charge").val() == "NaN" || $("#shipping_charge").val() == ""){
                    $("#shipping_charge").val(0);
                }
                
                $("#shipping_charge").trigger('change');
                
                $("#shipping_charge").attr("readonly", true); 
                
            }
            
            
        });
        
        
    });
    
    
    $(document).ready(function() {
        // Event handler for the "Add Item" button
        $('#addItem').click(function() {
            // Get values from the input fields
            var currency = $('#currency').val();
            var packageName = $('#package_name').val();
            var lengthCm = __read_number($('#length_cm'));
            var widthCm = __read_number($('#width_cm'));
            var heightCm = __read_number($('#height_cm'));
            var weightCm = __read_number($('#weight_cm'));
            var shippingCharge = __read_number($('#shipping_charge'));
            
            
            var serviceFee = __read_number($('#service_fee'));
            var declaredValue = __read_number($('#declared_value'));
            
            var total = __read_number($('#total'));
            
            var isValid = true;
            var inputIds = [
                    'package_name',
                    'length_cm',
                    'width_cm',
                    'height_cm',
                    'weight_cm',
                    'per_kg',
                    'volumetric_weight',
                    'price_type',
                    'shipping_charge',
                    'declared_value',
                    'service_fee',
                    'total'
                ];
                
                var packageArray = [];
            
                // Loop through the specified input IDs
                for (var i = 0; i < inputIds.length; i++) {
                    var inputId = inputIds[i];
                    var inputValue = $('#' + inputId).val();

                    if($("#price_type").val() == 'manual'){
                        
                        if (inputValue === '' && (inputId == 'total')) {
                            isValid = false;
                            $('#' + inputId).addClass('error');
                        } else {
                            $('#' + inputId).removeClass('error');
                            
                            // hidden items html
                            packageArray.push('<input type="hidden" name="new_' + inputId + '[]" value="' + inputValue + '">');
                        }
                    }else{
                        // Check if the field is empty
                        if (inputValue === '') {
                            isValid = false;
                            $('#' + inputId).addClass('error');
                        } else {
                            $('#' + inputId).removeClass('error');
                            
                            // hidden items html
                            packageArray.push('<input type="hidden" name="new_' + inputId + '[]" value="' + inputValue + '">');
                        }
                    }

                    
                    
                }
                
                if (!isValid) {
                    toastr.error("Please fill all the fields!");
                    return false;
                }
                
                 packageArray.push('<input type="hidden" name="new_package_description[]" value="' + $("#package_description").val() + '">');
    
    
            // Create a new row with the values
            var newRow = '<tr>' +
                '<td>' + packageName + '</td>' +
                '<td>' + lengthCm + '</td>' +
                '<td>' + widthCm + '</td>' +
                '<td>' + heightCm + '</td>' +
                '<td>' + weightCm + '</td>' +
                '<td>' + currency + __number_f(shippingCharge) + '</td>' +
                '<td>' + currency + __number_f(serviceFee) + '</td>' +
                '<td>' + currency + __number_f(declaredValue) + '</td>' +
                '<td class="total_cell">' + currency + __number_f(total) + '</td>' +
                '<td><button type="button" class="btn btn-danger removeItem"> - </button>'+
                packageArray.join('') + // Join all hidden inputs into the row
                '</td>' +
                '</tr>';
    
            // Append the new row to the table
            $('#package_items_table').append(newRow);
    
            // Reset input values after adding the row
            $('.to_reset').val('');
            calculateTotal();
        });
        
        $('.contact_modal').on('shown.bs.modal', function() {
            $('.contact_modal')
            .find('.select2')
            .each(function() {
                var $p = $(this).parent();               
                $(this).select2({ 
                    dropdownParent: $p
                });
            });

        });

        
    
        // Event handler for removing a row
        $('#package_items_table').on('click', '.removeItem', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });
        
        
        
        
        $('.payment-amount').change(function() {
            calculateTotal();
        });
    
        
    });
    
    
    function calculateTotal() {
            var total = 0;
            var paid = 0;
            var currency = $('#currency').val();
           
            $('.total_cell').each(function() {
                var value = __number_uf(($(this).text()));
                if (!isNaN(value)) {
                    total += value;
                }
            });
            
            $('.payment-amount').each(function() {
                var value = __number_uf(($(this).val()));
                if (!isNaN(value)) {
                    paid += value;
                }
            });
            
            var balance = total- paid;
            
            
            $("#payment_due").html(currency+__number_f(balance));
            
            if(balance == 0){
                $("#save_formBtn").show();
            }else{
                $("#save_formBtn").hide();
            }
            
            $("#total_payable").val(total);
            __write_number($("#total_payable_formatted"),total);
            $("#footer_grand_total").html(currency+__number_f(total));
            
        }

    
    
    </script>
@endsection
