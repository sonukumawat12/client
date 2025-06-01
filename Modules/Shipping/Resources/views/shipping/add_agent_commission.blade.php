
<div class="modal-dialog" role="document">
  <div class="modal-content">
      
    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\ShippingController@storeAgentCommission', $shipment->id), 'method' =>
    'post']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.agent' )</h4> 
    </div>
    
    @if(!empty($shipment->agent_id))
        <div class="modal-body">
        
          <div class="row">
            <div class="form-group col-sm-6">
              {!! Form::label('transaction_date', __( 'shipping::lang.transaction_date' ) . ':*') !!}
              {!! Form::text('transaction_date', @format_datetime(date('Y-m-h H:i')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
              'shipping::lang.transaction_date' )]); !!}
            </div>
            <div class="form-group col-sm-6">
              {!! Form::label('shipment_id', __( 'shipping::lang.shipment' ) . ':*') !!}
              <input type="hidden" name="shipment_id" id="shipment" value="{{$shipment->id}}" required>
              <input type="hidden" name="agent_id" id="shipment" value="{{$shipment->agent_id}}" required>
              <input class="form-control" disabled value="{{$shipment->tracking_no}}">
            </div>
          </div>  
          <div class="row">
            <div class="form-group col-sm-6">
              {!! Form::label('customer', __( 'shipping::lang.customer' ) . ':*') !!}
              {!! Form::text('customer', $shipment_details->customer_name, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.customer'),'readonly']); !!}
            </div> 
            
            <div class="form-group col-sm-6">
              {!! Form::label('package_type', __( 'shipping::lang.package_type' ) . ':*') !!}
              {!! Form::text('package_type', $shipment_details->package_name, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.package_type'),'readonly']); !!}
            </div> 
          </div>  
          <div class="row">
            
            <div class="form-group col-sm-6">
              {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) . ':*') !!}
              {!! Form::text('shipping_mode', $shipment_details->shipping_mode,['class' => 'form-control', 'placeholder' => __( 'shipping::lang.shipping_mode'),'readonly']); !!}
            </div> 
            
            <div class="form-group col-sm-6">
              {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' ) . ':*') !!}
              {!! Form::text('shipping_partner', $shipment_details->partner_name,['class' => 'form-control', 'placeholder' => __( 'shipping::lang.shipping_partner'),'readonly']); !!}
            </div> 
          </div>
           
          <div class="row">
                <div class="form-group col-sm-6">
                  {!! Form::label('shipping_agent', __( 'shipping::lang.shipping_agent' ) . ':*') !!}
                  {!! Form::text('shipping_agent', $shipment_details->agent_name,['class' => 'form-control', 'placeholder' => __( 'shipping::lang.shipping_agent'),'readonly']); !!}
                </div> 
            
              <div class="form-group col-sm-6">
                  {!! Form::label('amount', __( 'shipping::lang.commission' ) . ':*') !!}
                  @if(empty($commission))
                    {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.commission'),'required','step' => 'any']) !!}
                  @else
                    {!! Form::text('amount', @num_format($commission->amount), ['class' => 'form-control','readonly' ,'placeholder' => __( 'shipping::lang.commission'),'required','step' => 'any']) !!}
                  @endif
                  
              </div>
          </div> 
    
        </div>
        @if(empty($commission))
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        @endif
    @else
        <div class="alert alert-warning">@lang('shipping::lang.assign_agent_first')</div>
    @endif

    

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();
</script>