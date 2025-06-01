

<style>
  .vehicle-register-wrap label {
    color: #000 !important;
  }

  .sa-innate-form input[type=text], input[type=password], input[type=file], textarea, select, email {
    font-size: 13px;
    padding: 10px;
    border: 1px solid#ccc;
    outline: none;
    width: 100%;
    margin: 0px;
  }    
</style>


<div class="row vehicle-register-wrap">    
    <div class="col-md-12">
        
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title pull-left">{{isset($vehicle_data) && $vehicle_data->number?'UPDATE VEHICLE':'REGISTER YOUR VEHICLE'}}</h3>
            </div>

            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('number','Vehicle No:') !!}
                        <div>
                            {!! Form::text('number', $vehicle_data->number ?? null, ['class' => 'form-control','placeholder' => 'Vehicle No','required']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('vehicle_category_id','Vehicle Category:') !!}
                    <div>
                        {!! Form::select('vehicle_category_id', $categories ?? [], $vehicle_data->vehicle_category_id ?? '', [ 'id' => 'vehicle_category_id', 'class' => 'form-control','placeholder' => 'Please Select','required']); !!}
                    </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('fuel_type_id','Fuel Type:') !!}
                    <div>
                        {!! Form::select('fuel_type_id', $fuel_types ?? [], $vehicle_data->fuel_type_id ?? '', ['class' => 'form-control','placeholder' => 'Please Select','required']); !!}
                    </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('r_name','Vehicle Name:') !!}
                        <div>
                            {!! Form::text('name', $vehicle_data->name ?? null, ['class' => 'form-control','placeholder' => 'Vehicle Name','required', 'id'=>"r_name", 'autocomplete' => 'on']); !!}
                        </div>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('r_district_id','District:') !!}
                    <div>
                        {!! Form::select('district_id', $districts ?? [], $vehicle_data->district_id ?? '', ['class' => 'form-control','placeholder' => 'Please Select','required', 'id'=>"r_district_id"]); !!}
                    </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('r_town','Town:') !!}
                        <div>
                            {!! Form::text('town', $vehicle_data->town ?? null, ['class' => 'form-control','placeholder' => 'Town','required', 'id'=>"r_town"]); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('r_mobile','Mobile No:') !!}
                        <div>
                            {!! Form::text('mobile', $vehicle_data->mobile ?? null, ['class' => 'form-control','placeholder' => 'Mobile No','required', 'id' => "r_mobile"]); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('v_landline','Land Line No:') !!}
                        <div>
                            {!! Form::text('landline', $vehicle_data->landline ?? null, ['class' => 'form-control','placeholder' => 'Land Line No', 'id' => "v_landline"]); !!}
                        </div>
                    </div>
                </div>
              </div>
              <div class="col-md-12">
                  <hr/>
              </div>

              <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('image','Image:') !!}
                    <div>
                      {!! Form::file('image', $vehicle_data->image ?? null, ['class' => 'form-control','placeholder' => 'Image','required']); !!}
                    </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('passcode','Passcode:') !!}
                    <div>
                        <?php $passcode = $vehicle_data->passcode ?? sprintf("%05d", mt_rand(1, 99999));?>
                        {!! Form::text('passcode',$vehicle_data->passcode ?? sprintf("%05d", mt_rand(1, 99999)) , ['class' => 'form-control','placeholder' => 'Passcode','readonly','required']); !!}
                    </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('r_password','Password:') !!}
                    <div>
                        {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password','required', 'id'=>"r_password"]); !!}
                    </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('created_at','Date:') !!}
                    <div>
                        {!! Form::text('created_at', $vehicle_data->created_at ?? \Carbon\Carbon::now()->toDateString(), ['class' => 'form-control ','placeholder' => 'Date', 'readonly','required']); !!}
                    </div>
                    </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
            
            <div class="box-footer">
                <button type="submit" class="btn btn-success pull-right">{{isset($vehicle_data) && $vehicle_data->passcode ?'Update':'Register'}}</button>
            </div>
        </div>
          <!-- /.box -->
    </div>

</div>