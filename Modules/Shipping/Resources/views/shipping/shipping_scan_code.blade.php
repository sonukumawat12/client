<div class="" role="document">
  <div class="modal-content">

@if($type == 'showbar' || $type == 'showqr')

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Scan Code</h4>
    </div>
@endif
    <div class="modal-body">
      @if($type == 'qr' || $type == 'bar')
        <div class="row">
          @if($type == 'qr')

            <div class="form-group col-sm-12">
              <img src="data:image/png;base64, {{ $qrCode }} " alt="qrCode"   />
            </div>
          @endif
          @if($type == 'bar')
            <div class="form-group col-sm-12">
              {!! $barCode !!}
            </div>
          @endif
        </div>
      @else
        <div class="row">
          @if($barCode != '')
            <div class="form-group col-sm-12">
              {!! Form::label('joined_date',"Bar Code". ':*') !!}<Br><Br>
              {!! $barCode !!}
            </div>
            <div class="form-group col-sm-12">
              {!! Form::label('size',"Bar code size in mm (width & height)") !!}<Br><Br>
              <div class="col-sm-6">
                {!! Form::number('size', '', ['class' => 'form-control width_size','placeholder' => 'Width']); !!}
              </div>
              <div class="col-sm-6">
                {!! Form::number('size', '', ['class' => 'form-control height_size','placeholder' => 'Height']); !!}
              </div>
            </div>
            <div class="form-group col-sm-12">
              {!! Form::label('print',"No. of prints at a time") !!}<Br><Br>
              <div class="col-sm-6">
                {!! Form::number('print', '1', ['class' => 'form-control print_time']); !!}
              </div>
            </div>
            <div class="form-group col-sm-12">
              <button type="button" class="btn btn-primary btn-modal" onclick="printBarcode({{ $shipment_id }},'{{ $detail_id }}')">
                    <i class="fa fa-print" aria-hidden="true"></i> Print</button>
            </div>
          @endif
          @if($qrCode != '')        
          <div class="form-group col-sm-12">
            {!! Form::label('joined_date',"QR Code". ':*') !!}<Br><Br>
            <img src="data:image/png;base64, {{ $qrCode }} " alt="barcode"   />
          </div>
          <div class="form-group col-sm-12">
              {!! Form::label('size',"Bar code size in mm (width & height)") !!}<Br><Br>
              <div class="col-sm-6">
                {!! Form::number('size', '', ['class' => 'form-control qr_width_size','placeholder' => 'Width']); !!}
              </div>
              <div class="col-sm-6">
                {!! Form::number('size', '', ['class' => 'form-control qr_height_size','placeholder' => 'Height']); !!}
              </div>
            </div>
            <div class="form-group col-sm-12">
              {!! Form::label('print',"No. of prints at a time") !!}<Br><Br>
              <div class="col-sm-6">
                {!! Form::number('print', '1', ['class' => 'form-control qr_print_time']); !!}
              </div>
            </div>
            <div class="form-group col-sm-12">
              <button type="button" class="btn btn-primary btn-modal" onclick="printQrcode({{ $shipment_id }},'{{ $detail_id }}')">
                    <i class="fa fa-print" aria-hidden="true"></i> Print</button>
            </div>
          @endif
        </div>
      @endif
      

    </div>
@if($type == 'showbar' || $type == 'showqr')
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

@endif
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
</script>