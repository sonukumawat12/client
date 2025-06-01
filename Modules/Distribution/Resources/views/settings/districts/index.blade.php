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
			'id' => 'districts_province_id', 
			'required', 'autofocus']); !!}
        </div>
      </div>
      @endcomponent
    </div>
  </div>

  @component('components.widget', ['class' => 'box-primary', 'title' => 'Districts'])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal  pull-right"
      data-href="{{action('\Modules\Distribution\Http\Controllers\DistributionDistrictsController@create')}}" data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="districts_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>Name</th>
          <th>Province Name</th>
          <th>Created By</th>
          <th>Created On</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->

<script>
    $(".select2").select2();
</script>