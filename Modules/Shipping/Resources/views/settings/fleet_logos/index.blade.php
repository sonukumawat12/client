<!-- Main content -->
<section class="content">
  

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.fleet_logos')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\FleetLogoController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>
  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="fleet_logos_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.date')</th>
          <th>@lang('shipping::lang.logo')</th>
          <th>@lang('shipping::lang.image_name')</th>
          <th>@lang('shipping::lang.alignment')</th>
          <th>@lang('shipping::lang.user')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->