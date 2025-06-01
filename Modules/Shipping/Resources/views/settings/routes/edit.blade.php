<style>
  .justify-content-between{
    justify-content:space-between!important;
  }
  .main-modal {
  height:675px;
  overflow:auto;
}
  </style>
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteController@update', $route->id), 'method' =>
    'put', 'id' => 'route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.route' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-6">
          {!! Form::label('date', __( 'shipping::lang.date' ) . ':*') !!}
          {!! Form::text('date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'shipping::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('route_name', __( 'shipping::lang.route_name' ) . ':*') !!}
          {!! Form::text('route_name', $route->route_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'shipping::lang.route_name'), 'id'
          => 'route_name']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('orignal_location', __( 'shipping::lang.orignal_location' ) . ':*') !!}
          {!! Form::text('orignal_location', $route->orignal_location, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.orignal_location'), 'id'
          => 'orignal_location']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('destination', __( 'shipping::lang.destination' ) . ':*') !!}
          {!! Form::text('destination', $route->destination, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.destination'), 'id'
          => 'destination']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('distance', __( 'shipping::lang.distance_km' ) . ':*') !!}
          {!! Form::text('distance', @num_format($route->distance), ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.distance'), 'id'
          => 'distance']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('rate', __( 'shipping::lang.rate_km' ) . ':*') !!}
          {!! Form::text('rate', @num_format($route->rate), ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.rate'), 'id'
          => 'rate']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('route_amount', __( 'shipping::lang.route_amount' ) . ':*') !!}
          {!! Form::text('route_amount', @num_format($route->route_amount), ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.route_amount'), 'id'
          => 'route_amount']); !!}
        </div>       
        <div class="form-group col-sm-6">
          {!! Form::label('driver_incentive', __( 'shipping::lang.driver_incentive' ) . ':*') !!}
          {!! Form::text('driver_incentive', @num_format($route->driver_incentive), ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.driver_incentive'), 'id'
          => 'driver_incentive']); !!}
        </div>       
        <div class="form-group col-sm-6">
          {!! Form::label('helper_incentive', __( 'shipping::lang.helper_incentive' ) . ':*') !!}
          {!! Form::text('helper_incentive', @num_format($route->helper_incentive), ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.helper_incentive'), 'id'
          => 'helper_incentive']); !!}
        </div>    
        
        
        <div class="form-group col-sm-12">
          <div class="d-flex justify-content-between py-2">
            <h4>Incentives</h4>
          <button type="button" class="btn  btn-primary btn-modal pull-right" data-href="{{action('\Modules\Fleet\Http\Controllers\SettingController@createIncentives')}}"
      data-container=".view_modal_2">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

          </div>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="add_incentive_table" style="width: 100%;">
            <thead>
              <tr>
                <th>@lang('shipping::lang.incentive_name')</th>
                <th>@lang('shipping::lang.incentive_type_th')</th>
                <th>@lang('shipping::lang.applicable_to')</th>
                <th>@lang('shipping::lang.fixed_amount')</th>
                <th>@lang('shipping::lang.percentage_amount')</th>
                <th>@lang('shipping::lang.based_on')</th>
                <th>@lang('shipping::lang.amount')</th>
                <th class="notexport">@lang('messages.action')</th>
                
              </tr>
            </thead>
            <tbody>
              @foreach($incentives as $incentive)
              <tr>
              <td>{{$incentive->incentive_name}}<input type="hidden" value="{{$incentive->incentive_name}}" name="incentive_name[]"></td>
              <td>{{$incentive->incentive_type}}<input type="hidden" value="{{$incentive->incentive_type}}" name="incentive_type[]"></td>
              <td>{{ucfirst($incentive->applicable_to)}}<input type="hidden" name="applicable_to[]" value="{{$incentive->applicable_to}}"></td>
              @if($incentive->incentive_type=='fixed')
              <td>{{$incentive->amount}}<input type="hidden" value="{{$incentive->amount}}" name="fixed_amount[]"></td>
              @else
              <td>--<input type="hidden" value="--" name="fixed_amount[]"></td>
              @endif
  <td>{{($incentive->percentage)?$incentive->percentage:'--'}}<input type="hidden" value="{{$incentive->percentage}}" name="percentage[]"></td>
  <td>{{($incentive->based_on=='company_decision')?'Company Decision':'Trip Amount'}}<input type="hidden" value="{{$incentive->based_on}}" name="based_on[]"></td>
  @if($incentive->incentive_type=='percentage')
              <td>{{$incentive->amount}}<input type="hidden" name="applicable_to[]" value="{{$incentive->applicable_to}}"></td><input type="hidden" value="{{$incentive->amount}}" name="company_decision[]"></td>
              @else
              <td>--</td><input type="hidden" value="--" name="company_decision[]"></td>
              @endif
  
  
  <td><button type="button" onclick="removeIncentive(this)" class="btn btn-danger" aria-label="Left Align">
                    <span aria-hidden="true">×</span>
                    </button></td>
              </tr>
              @endforeach
          </tbody>
          </table>
        </div>
          </div>
        

      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#date').datepicker('setDate', '{{@format_date($route->date)}}');
 $('#distance, #rate').change(function () {
    let distance = parseFloat($('#distance').val());
    let rate = parseFloat($('#rate').val());

    let route_amount = distance * rate;
    __write_number($('#route_amount'), route_amount);

 })

 function removeIncentive(e){
  $(e).parent().parent().remove();
 }
</script>