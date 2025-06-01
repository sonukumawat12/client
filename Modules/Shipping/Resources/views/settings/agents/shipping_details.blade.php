<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.shipping_details' )</h4>
    </div>

    <div class="modal-body">
      
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="shipping_details_table" style="width: 100%;">
          <thead>
            <tr>
              <th>@lang('shipping::lang.added_date')</th>
              <th>@lang('shipping::lang.tracking_no')</th>
              <th>@lang('shipping::lang.sender')</th>
              <th>@lang('shipping::lang.delivery_time')</th>
              <th>@lang('shipping::lang.courier')</th>
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
 shipping_details_table = $('#shipping_details_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\AgentController@shippingDetails',[$id]) }}',
                    data: function(d) {
                        
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [
                    {
                        data: 'operation_date',
                        name: 'operation_date'
                    },
                    
                    {
                        data: 'tracking_no',
                        name: 'tracking_no'
                    },
                    
                    {
                        data: 'sender',
                        name: 'sender'
                    },
                    {
                        data: 'delivery_time',
                        name: 'delivery_time'
                    },
                    {
                        data: 'courier',
                        name: 'courier'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });
</script>