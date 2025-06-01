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
			'id' => 'area_province_id', 
			'required', 'autofocus']); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('district_id', 'District' . ':*') !!}
          {!! Form::select('district_id', $districts, null, ['class' => 'form-control select2 input-sm', 
			'placeholder' => 'All',
			'id' => 'area_district_id', 
			'required', 'autofocus']); !!}
        </div>
      </div>
      
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => 'Areas'])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal  pull-right"
      data-href="{{action('\Modules\Distribution\Http\Controllers\DistributionAreasController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="areas_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>Name</th>
          <th>District</th>
          <th>Province</th>
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
    $('#area_province_id').on('change', function() {
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
                $('#area_district_id').empty();
                $('#area_district_id').append(data.options);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                // Handle the error
            }
        });
    });
</script>