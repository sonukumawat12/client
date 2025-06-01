<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@update', $loading->id), 'method' =>
    'put' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'bakery::lang.loading' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-3">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date($loading->date), ['class' => 'form-control date', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.date' ),'id'=>'date', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-3">
          {!! Form::label('form_no', __( 'bakery::lang.form_no' ) . ':*') !!}
          {!! Form::text('form_no', $loading->form_no, ['class' => 'form-control', 'placeholder' => __( 'bakery::lang.form_no'), 'id'
          => 'form_no', 'readonly']); !!}
        </div>
        
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('vehicle_id', __( 'bakery::lang.vehicle' ) . ':*') !!}
                <!--<div class="input-group">-->
                    {!! Form::select('vehicle_id', $fleet, $loading->vehicle_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id' => 'vehicle_id','required']); !!}
                   
                <!--</div>-->
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('driver_id', __( 'bakery::lang.driver' ) . ':*') !!}
                <!--<div class="input-group">-->
                    {!! Form::select('driver_id', $drivers, $loading->driver_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                          => 'driver_id','required']); !!}
                    
                <!--</div>-->
            </div>
            
          
          
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_id', __( 'bakery::lang.product' ) . ':*') !!}
                <!--<div class="input-group">-->
                    {!! Form::select('product_id', $products, $loading->product_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                          => 'product_id','required']); !!}
                    
                <!--</div>-->
            </div>
            
          
          
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('route_id', __( 'bakery::lang.route' ) . ':*') !!}
                <!--<div class="input-group">-->
                    {!! Form::select('route_id', $routes, $loading->route_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                      => 'route_id','required']); !!}
                    
                <!--</div>-->
            </div>
          
        </div>
     
      </div>
      <div class="col-sm-12">
          <div class="col-sm-3"></div>
              <div class="col-sm-3">
                  <h5 class="">@lang('bakery::lang.total_qty')</h5>
                  <b><span class="text-danger edit_grand_qty">0.00</span></b>
              </div>
              <div class="col-sm-3">
                  <h5 class="">@lang('bakery::lang.total_due')</h5>
                  <b><span class="text-danger edit_grand_total">0.00</span></b>
              </div>
              <div class="col-sm-3">
                  <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.save' )</button>
              </div>
          
          </div>
          
          <div class="col-sm-12">
              <div class="table-responsive">
              <table class="table table-bordered table-striped" id="edit_loading_product_table" style="width: 100%;">
                    <thead>
                    <tr>
                      <th>@lang('bakery::lang.product')</th>
                      <th>@lang('bakery::lang.unit_cost')</th>
                      <th>@lang('bakery::lang.qty')</th>
                      <th>@lang('bakery::lang.total_due')</th>
                      <th>@lang('bakery::lang.user_added')</th>
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

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();
    $('.date').datepicker('setDate', '{{@format_date($loading->date)}}');
    
    
    $(document).ready(function () {
        edit_loading_product_table = $('#edit_loading_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@getProducts')}}?loading_id={{$loading->id}}',
                data: function (d) {
                    
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'name', name: 'name' },
                { data: 'unit_cost', name: 'unit_cost' },
                
                { data: 'qty', name: 'qty' ,searchable: false},
                { data: 'total_due', name: 'total_due' ,searchable: false},
                
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
                $(".table_entered_qty").trigger('input');
            },
        });
        
        
    })
</script>