<!-- Main content -->
<section class="content">
 
  @component('components.widget', ['class' => 'box-primary', 'title' =>'Provinces'])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal  pull-right"
      data-href="{{action('\Modules\Distribution\Http\Controllers\DistributionProvincesController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="provinces_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>Name</th>
          <th>Added By</th>
          <th>Added On</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->