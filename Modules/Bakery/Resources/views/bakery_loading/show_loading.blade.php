<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'bakery::lang.loading' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <div class="col-sm-6">
              <b>@lang('bakery::lang.date'): </b> {{@format_date($data->date)}}<br>
              <b>@lang('bakery::lang.form_no'): </b> {{$data->form_no}}<br>
              <b>@lang('bakery::lang.vehicle'): </b> {{$data->vehicle_number}}<br>
              
          </div>
          <div class="col-sm-6">
              <b>@lang('bakery::lang.driver'): </b> {{$data->driver_name}}<br>
              <b>@lang('bakery::lang.route'): </b> {{$data->route_name}}<br>
              <b>@lang('bakery::lang.user_added'): </b> {{$data->username}}<br>
          </div>
      </div>
      
          <hr>
      <div class="col-sm-12">
          <div class="table-responsive">
          <table class="table table-bordered table-striped" id="show_loading_product_table" style="width: 100%;">
                <thead>
                <tr>
                  <th>@lang('bakery::lang.product')</th>
                  <th>@lang('bakery::lang.unit_cost')</th>
                  <th>@lang('bakery::lang.qty')</th>
                  <th>@lang('bakery::lang.total_due')</th>
                  <th>@lang('bakery::lang.returned_qty')</th>
                  <th>@lang('bakery::lang.returned_qty_amt')</th>
                  <th>@lang('bakery::lang.settled_amt')</th>
                  <th>@lang('bakery::lang.short_amt')</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
      </div>
      
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   
    
    $(document).ready(function () {
        show_loading_product_table = $('#show_loading_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@getProductsShow',[$data->id])}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'name', name: 'name' },
                { data: 'unit_cost', name: 'unit_cost' },
                
                { data: 'qty', name: 'qty' ,searchable: false},
                { data: 'total_amount', name: 'total_amount' },
                
                { data: 'returned_qty', name: 'returned_qty' ,searchable: false},
                { data: 'returned_qty_amt', name: 'returned_qty_amt' ,searchable: false},
                { data: 'settled_amt', name: 'settled_amt' ,searchable: false},
                { data: 'short_amt', name: 'short_amt' ,searchable: false},
            ],
            fnDrawCallback: function(oSettings) {
                $(".table_entered_qty").trigger('input');
            },
        });
        
        
    })
</script>