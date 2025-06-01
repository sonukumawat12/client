{!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\BakeryLoadingReturnController@store'), 'method' =>
    'post']) !!}
    
    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-3">
          {!! Form::label('return_date', __( 'bakery::lang.return_date' ) . ':*') !!}
          {!! Form::text('return_date', @format_datetime(date('Y-m-d H:i')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'bakery::lang.return_date' ),'id'=>'return_date', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-3">
          {!! Form::label('return_form_no', __( 'bakery::lang.return_form_no' ) . ':*') !!}
          {!! Form::text('return_form_no', $rform_no, ['class' => 'form-control', 'placeholder' => __( 'bakery::lang.form_no'), 'id'
          => 'return_form_no', 'readonly']); !!}
        </div>
        
        <div class="form-group col-sm-3">
          {!! Form::label('loading_date', __( 'bakery::lang.loading_date' ) . ':*') !!}
          {!! Form::text('loading_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'readonly', 'placeholder' => __(
          'bakery::lang.loading_date' ),'id'=>'return_loading_date', 'readonly']); !!}
        </div>
        
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('return_vehicle_id', __( 'bakery::lang.vehicle' ) . ':*') !!}
               
                {!! Form::select('return_vehicle_id', $fleet, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id' => 'return_vehicle_id']); !!}
                    
            </div>
        </div>
        
        <div class="clearfix"></div>
        
         <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_id', __( 'bakery::lang.product' ) . ':*') !!}
                {!! Form::select('product_id', $products, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                          => 'return_product_id']); !!}
            </div>
            
          
          
        </div>
        
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('return_driver_id', __( 'bakery::lang.driver' ) . ':*') !!}
               
                {!! Form::select('return_driver_id', $drivers, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.please_select'), 'id'
                      => 'return_driver_id']); !!}
                   
            </div>
            
          
          
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('return_loading_form_no', __( 'bakery::lang.return_loading_form_no' ) . ':*') !!}
                <select class="form-control select2" name="return_loading_form_no" id="return_loading_form_no">
                    <option value="">@lang('lang_v1.please_select')</option>
                    @foreach($loading_form_nos as $lform_no)
                        <option value="{{ $lform_no->id }}" data-vehicle="{{$lform_no->vehicle_id}}" data-date="{{@format_date($lform_no->date)}}" data-driver="{{$lform_no->driver_id}}" >{{$lform_no->form_no}}</option>
                    @endforeach
                </select>
            </div>
          
        </div>
        
        
       
     
      </div>

    </div>
    <div class="modal-footer">
      <div class="col-sm-2">
          <h5 class="">@lang('bakery::lang.total_loaded')</h5>
          <b><span class="text-danger grand_total_loaded">0.00</span></b>
      </div>
      <div class="col-sm-2">
          <h5 class="">@lang('bakery::lang.total_returned')</h5>
          <b><span class="text-danger grand_total_returned">0.00</span></b>
      </div>
      
      <div class="col-sm-2">
          <h5 class="">@lang('bakery::lang.total_due')</h5>
          <b><span class="text-danger grand_total_due">0.00</span></b>
      </div>
      
      <div class="col-sm-2">
          <h5 class="">@lang('bakery::lang.total_settled')</h5>
          <b><span class="text-danger grand_total_settled">0.00</span></b>
      </div>
      
      <div class="col-sm-2">
          <h5 class="">@lang('bakery::lang.total_short')</h5>
          <b><span class="text-danger grand_total_short">0.00</span></b>
      </div>
      
      
      <button type="submit" class="btn btn-primary submit-return">@lang( 'messages.save' )</button>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="returns_product_table" style="width: 100%;">
        <thead>
        <tr>
          <th>@lang('bakery::lang.product')</th>
          <th>@lang('bakery::lang.unit_cost')</th>
          <th>@lang('bakery::lang.qty')</th>
          <th>@lang('bakery::lang.total_loaded')</th>
          
          <th>@lang('bakery::lang.qty_returned')</th>
          <th>@lang('bakery::lang.total_returned')</th>
          <th>@lang('bakery::lang.total_due')</th>
          <th>@lang('bakery::lang.total_settled')</th>
          <th>@lang('bakery::lang.total_short')</th>
          
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