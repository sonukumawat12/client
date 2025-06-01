{!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@store'), 'method' =>
    'post']) !!}
    
    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-3">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.date' ),'id'=>'date', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-3">
          {!! Form::label('form_no', __( 'bakery::lang.form_no' ) . ':*') !!}
          {!! Form::text('form_no', $form_no, ['class' => 'form-control', 'placeholder' => __( 'bakery::lang.form_no'), 'id'
          => 'form_no', 'readonly']); !!}
        </div>
        
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('vehicle_id', __( 'bakery::lang.vehicle' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::select('vehicle_id', $fleet, !empty($existing) ? $existing->vehicle_id : null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id' => 'vehicle_id','required']); !!}
                    <span class="input-group-btn">
                    <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\FleetController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('driver_id', __( 'bakery::lang.driver' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::select('driver_id', $drivers,  !empty($existing) ? $existing->driver_id : null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                          => 'driver_id','required']); !!}
                    <span class="input-group-btn">
                    <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\DriverController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
                </div>
            </div>
            
          
          
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_id', __( 'bakery::lang.product' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::select('product_id', $products, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                          => 'loading_product_id']); !!}
                    <span class="input-group-btn">
                    <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\BakeryProductController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
                </div>
            </div>
            
          
          
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('route_id', __( 'bakery::lang.route' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::select('route_id', $routes,  !empty($existing) ? $existing->route_id :null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                      => 'route_id','required']); !!}
                    <span class="input-group-btn">
                    <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\RouteController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
                </div>
            </div>
          
        </div>
     
      </div>

    </div>
    <div class="modal-footer">
      <div class="col-sm-3"></div>
      <div class="col-sm-3">
          <h5 class="">@lang('bakery::lang.total_qty')</h5>
          <b><span class="text-danger grand_qty">0.00</span></b>
      </div>
      <div class="col-sm-3">
          <h5 class="">@lang('bakery::lang.total_due')</h5>
          <b><span class="text-danger grand_total">0.00</span></b>
      </div>
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="loading_product_table" style="width: 100%;">
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
    
{!! Form::close() !!}

<script>
    
</script>