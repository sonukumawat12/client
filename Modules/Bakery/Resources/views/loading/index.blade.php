<!-- Main content -->
<section class="content">
  <div class="row">
</div>

    {!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\LoadingController@store'), 'method' =>
    'post', 'id' => 'driver_add_form',  'files' => true ]) !!}

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
                    {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                    'fleet::lang.date' )]); !!}
                </div>
            </div>

            @php

                $loading = \Modules\Bakery\Entities\BakeryLoading::latest()->first();
                $form_no = 1;
                if ($loading){
                    $form_no = $loading->form_no;
                    $business_id = request()->session()->get('user.business_id');
                     $business = \App\Business::where('id', $business_id)->first();
                    $remove = !empty($business->ref_no_prefixes['form_no']) ? $business->ref_no_prefixes['form_no'] : '';
                     $form_no = (int) str_replace($remove, '', $form_no) + 1;
                }
            @endphp
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('form_no', 'Loading Form No'. ':*') !!}
                    {!! Form::text('form_no', $form_no, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                    'form_no' )]); !!}
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('vehicle_id', 'Vehicle' . ':*') !!}
                    <div class="input-group">
                        {!! Form::select('vehicle_id', $vehicles, !empty($duplicate_product->vehicle_id) ? $duplicate_product->vehicle_id : '', ['class' => 'form-control select2', 'required']); !!}
                        <span class="input-group-btn">
                        <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\FleetController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('driver_id', 'Driver' . ':*') !!}
                    <div class="input-group">
                        {!! Form::select('driver_id', $drivers, !empty($duplicate_product->driver_id) ? $duplicate_product->driver_id : '', ['class' => 'form-control select2', 'required']); !!}
                        <span class="input-group-btn">
                        <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\DriverController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('product_id', 'Products' . ':*') !!}
                    <div class="input-group">
                        {!! Form::select('product_id', $products, !empty($duplicate_product->product_id) ? $duplicate_product->product_id : '', ['class' => 'form-control select2', 'required']); !!}
                        <span class="input-group-btn">
                        <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\BakeryProductController@create')}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                    </div>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('route_id', 'Routes' . ':*') !!}
                    <div class="input-group">
                        {!! Form::select('route_id', $routes, !empty($duplicate_product->route_id) ? $duplicate_product->route_id : '', ['class' => 'form-control select2', 'required']); !!}
                        <span class="input-group-btn">
                        <button type="button"  class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Bakery\Http\Controllers\RouteController@create')}}"  data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                    </div>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('qty', 'QTY'. ':*') !!}
                    {!! Form::number('qty', !empty($duplicate_product->product_id) ? $duplicate_product->product_id : '', ['class' => 'form-control', 'required', 'placeholder' => __(
                    'qty' )]); !!}
                </div>
            </div>

        </div>
    @endcomponent

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
    </div>

    {!! Form::close() !!}


</section>
<!-- /.content -->

<script>
    $('#date').datepicker('setDate', new Date());
</script>