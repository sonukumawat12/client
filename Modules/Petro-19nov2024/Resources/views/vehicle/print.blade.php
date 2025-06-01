@extends('layouts.blankPrint')
@php
	$vehicleNumber = $vehicleData->number;
@endphp

@section('title', __( 'vehicle.print_vehicle_detail' ))

@section('content')

<section class="content-header no-print">
    <h1>@lang( 'vehicle.print_vehicle_detail' )</h1>
</section>
<section class="content">
    <div class="row">
        <div class="">
            <div class="text-center">
                <div  id="canvas_div" class="hide print" style="   width: 300px; margin: auto;">
                    <img src="" id="qr_img" class="hide">
                    <br>
                    <span
                        style="margin-top: 0px; padding-top: 0px;"></span>
                </div>
				<br>
				<?php
				echo '<img src="data:image/png;base64,' . \DNS1D::getBarcodePNG($vehicleNumber, 'C39+') . '" alt="barcode"   />';
				?>
                <br><h4 id="vehicle_number"><b>@lang('vehicle.vehicle_no' ):</b> {{$vehicleNumber}}</h4>
                <br>
                <button class="btn btn-success  no-print"  onClick="window.print()" >@lang('vehicle.print_detail')</button>
					<a  class="btn btn-primary btn-flat btn-login no-print" href="{{route('login')}}">@lang('vehicle.back_login')</a>
            </div>
        </div>
    </div>
</section>
@stop
@section('javascript')

<script src="{{ asset('js/html2canvas.js') }}"></script>
<script src="{{ asset('modules/productcatalogue/plugins/qrcode/qrcode.js') }}"></script>
<script type="text/javascript">
    $(document).ready( function(){
		v_number="<?php echo $vehicleNumber; ?>";
        if (v_number) {
            var color = '#000000';
            var opts = {
                errorCorrectionLevel: 'H',
                margin: 4,
                width: 250,
                color: {
                    dark: color,
                    light: "#ffffffff"
                }
            }
            QRCode.toDataURL(v_number,opts,function (err, url){
                $('#qr_img').attr('src', url);
                $('#qr_img').removeClass('hide');
                $('#canvas_div').removeClass('hide');
            });      
        } 
	});
</script>
@endsection

