<div class="pos-tab-content @if(session('status.tab') == 'taxes') active @endif">
  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'property::lang.all_your_sales_officer' )])
    @can('property.settings.tax')
    @slot('tool')
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-primary btn-modal" 
      data-href="{{action('\Modules\Property\Http\Controllers\SalesOfficerController@create')}}" 
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('property.settings.sales_officer')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="sales_officer_table" style="width: 100%">
        <thead>
          <tr>
            <th>@lang( 'property::lang.date' )</th>
            <th>@lang( 'property::lang.sale_officer' )</th>
            <th>@lang( 'property::lang.username' )</th>
            <th>@lang( 'property::lang.added_user' )</th>
            <th>@lang( 'property::lang.action' )</th>
          </tr>
        </thead>
      </table>
    </div>
    @endcan
    @endcomponent

</section>
</div>