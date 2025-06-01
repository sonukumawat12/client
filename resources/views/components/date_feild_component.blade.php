<fieldset>
  <div class="row">
    <div class="col-md-6 p-0">
      <label class="text-center w-100 l-date">Year</label>
      <div class="field-inline-block w-100 text-center">
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control"  placeholder="Y" name="{{$date_feild_name}}[y][]" @isset($data_feild['y'])value="{{$data_feild['y'][0]}}" @endisset>
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y" name="{{$date_feild_name}}[y][]" @isset($data_feild['y'])value="{{$data_feild['y'][1]}}" @endisset/>
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y" name="{{$date_feild_name}}[y][]" @isset($data_feild['y'])value="{{$data_feild['y'][2]}}" @endisset/>
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y" name="{{$date_feild_name}}[y][]" @isset($data_feild['y'])value="{{$data_feild['y'][3]}}" @endisset/>
      </div>
    </div>
       
    <div class="col-md-3 p-0">
      <label class="text-center w-100 l-date">Month</label>
      <div class="field-inline-block w-100 text-center">
        <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="M" name="{{$date_feild_name}}[m][]" @isset($data_feild['y'])value="{{$data_feild['m'][0]}}" @endisset/>
        <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="M" name="{{$date_feild_name}}[m][]" @isset($data_feild['y'])value="{{$data_feild['m'][1]}}" @endisset/>
      </div>
    </div>
    <div class="col-md-3 p-0">
        <label class="text-center w-100 l-date">Date</label>
        <div class="field-inline-block w-100 text-center">
      
       <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="D" name="{{$date_feild_name}}[d][]" @isset($data_feild['y'])value="{{$data_feild['d'][0]}}" @endisset/>
       <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="D" name="{{$date_feild_name}}[d][]" @isset($data_feild['y'])value="{{$data_feild['d'][1]}}" @endisset/>
      </div>
    </div>
  </div>
  </fieldset>
  