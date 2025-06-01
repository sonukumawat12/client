<!-- Main content -->
<section class="content">
  

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.account_nos')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\FleetAccountNumberController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="account_nos_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.invoice_name')</th>
          <th>@lang('shipping::lang.account_no')</th>
          <th>@lang('shipping::lang.dealer_name')</th>
          <th>@lang('shipping::lang.dealer_account_number')</th>
          <th>@lang('shipping::lang.bank_name')</th>
          <th>@lang('shipping::lang.branch')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->