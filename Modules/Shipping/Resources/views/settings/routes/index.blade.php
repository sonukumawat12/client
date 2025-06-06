<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('route_names', __( 'shipping::lang.route_name' )) !!}
                {!! Form::select('route_names', $route_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'route_names']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('orignal_locations', __( 'shipping::lang.orignal_location' )) !!}
                {!! Form::select('orignal_locations', $orignal_locations, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'orignal_locations']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('destinations', __( 'shipping::lang.destination' )) !!}
                {!! Form::select('destinations', $destinations, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'destinations']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('users', __( 'shipping::lang.user_added' )) !!}
                {!! Form::select('users', $users, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'users']);
                !!}
            </div>
        </div>
       
      
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.all_your_routes')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\RouteController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot

  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="routes_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.date')</th>
          <th>@lang('shipping::lang.route_name')</th>
          <th>@lang('shipping::lang.orignal_location')</th>
          <th>@lang('shipping::lang.destination')</th>
          <th>@lang('shipping::lang.distance_km')</th>
          <th>@lang('shipping::lang.rate_km')</th>
          <th>@lang('shipping::lang.route_amount')</th>
          <th>@lang('shipping::lang.driver_incentive')</th>
          <th>@lang('shipping::lang.helper_incentive')</th>
          <th>@lang('shipping::lang.user_added')</th>

        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->