
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\AgentController@storeCommission', $agent->id), 'method' =>
    'post']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.agent' )</h4> 
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-6">
          {!! Form::label('transaction_date', __( 'shipping::lang.transaction_date' ) . ':*') !!}
          {!! Form::text('transaction_date', @format_datetime(date('Y-m-h H:i')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'shipping::lang.transaction_date' )]); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('shipment_id', __( 'shipping::lang.shipment' ) . ':*') !!}
          {!! Form::select('shipment_id', $shipments,null, ['class' => 'form-control select2', 'placeholder' => __( 'shipping::lang.please_select'), 'id'
          => 'shipment','style'=> 'width: 100% !important;','required']); !!}
        </div>
      </div>  
      <div class="row">
        <div class="form-group col-sm-6">
          {!! Form::label('customer', __( 'shipping::lang.customer' ) . ':*') !!}
          {!! Form::text('customer', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.customer'),'readonly']); !!}
        </div> 
        
        <div class="form-group col-sm-6">
          {!! Form::label('package_type', __( 'shipping::lang.package_type' ) . ':*') !!}
          {!! Form::text('package_type', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.package_type'),'readonly']); !!}
        </div> 
      </div>  
      <div class="row">
        
        <div class="form-group col-sm-6">
          {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' ) . ':*') !!}
          {!! Form::text('shipping_mode', null,['class' => 'form-control', 'placeholder' => __( 'shipping::lang.shipping_mode'),'readonly']); !!}
        </div> 
        
        <div class="form-group col-sm-6">
          {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' ) . ':*') !!}
          {!! Form::text('shipping_partner', null,['class' => 'form-control', 'placeholder' => __( 'shipping::lang.shipping_partner'),'readonly']); !!}
        </div> 
      </div>
       
      <div class="row">
          <div class="form-group col-sm-6">
              {!! Form::label('amount', __( 'shipping::lang.commission' ) . ':*') !!}
              {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.commission'),'required','step' => 'any']); !!}
          </div>
      </div> 

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();
    
    $(document).ready(function () {
        $('#shipment').on('change', function () {
            var selectedValue = $(this).val();
            
            $("#customer").val("");
            $("#package_type").val("");
            $("#shipping_mode").val("");
            $("#shipping_partner").val("");
    
            if (selectedValue !== null) {
                $.ajax({
                    url: '{{url("shipping/agents/one-shipment-details/")}}/'+selectedValue, 
                    method: 'GET',
                    data: {  }, 
                    success: function (response) {
                        $("#customer").val(response.customer_name);
                        $("#package_type").val(response.package_name);
                        $("#shipping_mode").val(response.shipping_mode);
                        $("#shipping_partner").val(response.partner_name);
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }
        });
    });

</script>