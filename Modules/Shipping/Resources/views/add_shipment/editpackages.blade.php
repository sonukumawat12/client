<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\AddShipmentController@postupdatePackages',[$id]), 'method' => 'post']) !!}
    <div class="box box-primary" style="font-size: 12px !important">
        
        <div class="modal-header">
            <button type="button" class="close closing_contact_modal" aria-label="Close" onclick="closeScanCodeModal()"><span
            aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
           
            <div class="card">
            @foreach($packages as $pac)
                <div class="col-md-12">
                    <h5>{{$pac->package_name}}</h5>
                </div>
                <div class="row">
                    
                    <div class="form-group col-sm-3">
                          {!! Form::label('length_cm', __( 'shipping::lang.length_cm' ) .":") !!}
                          {!! Form::text('package['.$pac->id.'][length]', $pac->length, ['class' => 'form-control to_reset', 'placeholder' =>
                          __('shipping::lang.length_cm')]); !!}
                    </div>
                    <div class="form-group col-sm-3">
                          {!! Form::label('width', __( 'shipping::lang.width_cm' ) .":") !!}
                          {!! Form::text('package['.$pac->id.'][width]', $pac->width, ['class' => 'form-control to_reset', 'placeholder' =>
                          __('shipping::lang.width_cm')]); !!}
                    </div>
                    
                    <div class="form-group col-sm-3">
                          {!! Form::label('height', __( 'shipping::lang.height_cm' ) .":") !!}
                          {!! Form::text('package['.$pac->id.'][height]', $pac->height, ['class' => 'form-control to_reset', 'placeholder' =>
                          __('shipping::lang.height_cm')]); !!}
                    </div>
                    <div class="form-group col-sm-3">
                          {!! Form::label('weight', __( 'shipping::lang.weight_cm' ) .":") !!}
                          {!! Form::text('package['.$pac->id.'][weight]', $pac->weight, ['class' => 'form-control to_reset', 'placeholder' =>
                          __('shipping::lang.weight_cm')]); !!}
                    </div>
                </div>
                <hr>
            @endforeach
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default closing_scancode_modal" onclick="closeScanCodeModal()">@lang( 'messages.close' )</button>
        </div>
        <!-- /.box-body -->
        </div>
        {!! Form::close() !!}
    </div>
</div>
