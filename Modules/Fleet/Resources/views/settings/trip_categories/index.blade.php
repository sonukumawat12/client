<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.all_trip_categories')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\TripCategoryController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" style="width: 100%; table-layout: auto;" id="trip_categories_table">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('fleet::lang.date_time')</th>
          <th>@lang('fleet::lang.trip_category_name')</th>
          {{-- <th>@lang('fleet::lang.vehicle_type')</th> --}}
          <th>@lang('fleet::lang.amount_calculated_method')</th>
          <th>@lang('fleet::lang.user_added')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->