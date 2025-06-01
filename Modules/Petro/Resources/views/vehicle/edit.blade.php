<div class="modal-dialog" role="document" id="vehical_registration_dialog" style="width: 55%;">
  <div class="modal-content">
    <div class="modal-body">
          {!! Form::open(['url' => route('vehicle.updateVehicle', $vehicle_data->id) , 'id' => 'update_vehicle_form']) !!}
            {!! Form::token(); !!}   
            {{-- this route define in web.php --}}
            @include('vehicle.register')
          {!! Form::close() !!}
      </div>
  </div>
</div>