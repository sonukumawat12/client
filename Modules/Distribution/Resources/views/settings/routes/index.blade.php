<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
            
            <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('province_id', 'Province' . ':*') !!}
                  {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2 input-sm', 
        			'placeholder' => 'All',
        			'id' => 'route_province_id', 
        			'required', 'autofocus']); !!}
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('district_id', 'District' . ':*') !!}
                  {!! Form::select('district_id', [], null, ['class' => 'form-control select2 input-sm', 
        			'placeholder' => 'All',
        			'id' => 'route_district_id', 
        			'required', 'autofocus']); !!}
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('area_id', 'Area' . ':*') !!}
                  {!! Form::select('area_id', [], null, ['class' => 'form-control select2 input-sm', 
        			'placeholder' => 'All',
        			'id' => 'route_area_id', 
        			'required', 'autofocus']); !!}
                </div>
              </div>
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => 'Areas'])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Distribution\Http\Controllers\DistributionRoutesController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="routes_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>Route No</th>
          <th>Name</th>
          <th>District</th>
          <th>Province</th>
          <th>Area</th>
          <th>Added By</th>
          <th>Added on</th>

        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->
<script>
    $(".select2").select2();
    
    $(document).ready(function() {
        // Add event listener to route_province_id dropdown
        $('#route_province_id').on('change', function() {
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
                    $('#route_district_id').empty();
                    $('#route_district_id').append(data.options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    // Handle the error
                }
            });
        });
        
        
        $('#route_district_id').on('change', function() {
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
                    $('#route_area_id').empty();
                    $('#route_area_id').append(data.options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    // Handle the error
                }
            });
        });
        
        
    });

    
</script>