<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.commissions_table' )</h4>
    </div>

    <div class="modal-body">
      
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="commissions_table" style="width: 100%;">
          <thead>
            <tr>
              <th>@lang('shipping::lang.date')</th>
              <th>@lang('shipping::lang.tracking_no')</th>
              <th>@lang('shipping::lang.customer')</th>
              <th>@lang('shipping::lang.shipping_agent')</th>
              <th>@lang('shipping::lang.shipping_mode')</th>
              <th>@lang('shipping::lang.shipping_package')</th>
              <th>@lang('shipping::lang.shipping_partner')</th>
              <th>@lang('shipping::lang.commission')</th>
              <th>@lang('shipping::lang.payment_status')</th>
              <th>@lang('shipping::lang.added_by')</th>
            </tr>
          </thead>
        </table>
      </div>

    </div>

    <div class="modal-footer">
     
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 commissions_table = $('#commissions_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\AgentController@viewCommissionsTable',[$id]) }}',
                    data: function(d) {
                        
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    
                    {
                        data: 'tracking_no',
                        name: 'tracking_no'
                    },
                    
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'agent_name',
                        name: 'agent_name'
                    },
                    {
                        data: 'shipping_mode',
                        name: 'shipping_mode'
                    },
                    {
                        data: 'package_name',
                        name: 'package_name'
                    },
                    {
                        data: 'partner_name',
                        name: 'partner_name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'createdBy',
                        name: 'createdBy'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
</script>