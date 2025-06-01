<div class="pos-tab-content @if(session('status.tab') == 'taxes') active @endif">
  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Purchase Land Account'])
    @can('property.settings.tax')
    @slot('tool')
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-primary btn-modal"
      data-href="{{action('\Modules\Property\Http\Controllers\PurchaseLandAccountController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('property.settings.tax')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="purchase_land_account_table" style="width: 100%">
        <thead>
          <tr>
            <th>@lang( 'property::lang.date' )</th>
            <th>@lang( 'property::lang.business_location' )</th>
            <th>@lang( 'property::lang.payment_option' )</th>
            <th>@lang( 'property::lang.payment_only' )</th>
            <th>@lang( 'property::lang.credit_account' )</th>
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
