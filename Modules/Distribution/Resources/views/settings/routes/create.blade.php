<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Distribution\Http\Controllers\DistributionRoutesController@store'), 'method' =>
    'post', 'id' => !empty($quick_add) ? 'quick_add_route' : 'routes_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Area</h4>
    </div>

    <div class="modal-body">
      <div class="row">
         <div class="form-group col-sm-12">
          <label>Route No</label>
          <input type="text" class="form-control" value="{{$id}}" disabled>
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('name', 'Name' . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Name', 'id'
          => 'name']); !!}
        </div>
        
        <div class="form-group col-sm-12">
         {!! Form::label('province_id', 'Province' . ':*') !!}
          {!! Form::select('province_id[]', $provinces, null, ['class' => 'form-control select2 input-sm', 
			
			'id' => 'province_id', 
			'required', 'multiple']); !!}
        </div> 
        
        <div class="form-group col-sm-12">
          {!! Form::label('district_id[]', 'District' . ':*') !!}
          {!! Form::select('district_id[]', $districts, null, ['class' => 'form-control select2 input-sm', 
			
			'id' => 'district_id', 
			'required', 'multiple']); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('area_id', 'Area' . ':*') !!}
          {!! Form::select('area_id[]', $areas, null, ['class' => 'form-control select2 input-sm', 
			
			'id' => 'area_id', 
			'required', 'multiple']); !!}
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
    $('#province_id').on('change', function() {
            var provinceId = $(this).val();
        
            if(provinceId == ""){
                provinceId = 0;
            }
            
            $.ajax({
                url: '/distribution/districtdropdown/' + provinceId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate district dropdown with returned data
                    $('#district_id').empty();
                    $('#district_id').append(data.options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    // Handle the error
                }
            });
        });
        
        
    $('#district_id').on('change', function() {
            var provinceId = $(this).val();
        
            if(provinceId == ""){
                provinceId = 0;
            }
            
            $.ajax({
                url: '/distribution/areadropdown/' + provinceId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate district dropdown with returned data
                    $('#area_id').empty();
                    $('#area_id').append(data.options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    // Handle the error
                }
            });
        });
</script>
