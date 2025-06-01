
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('list_loading_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('list_loading_date_range', @format_date('first day of this month') . ' ~ ' .
                @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'list_loading_date_range', 'readonly']); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('list_loading_vehicle_id', __( 'bakery::lang.vehicle' ) . ':*') !!}
                {!! Form::select('list_loading_vehicle_id', $fleet, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.all'), 'id' => 'list_loading_vehicle_id','required']); !!}
            </div>
        </div>
        
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('list_loading_driver_id', __( 'bakery::lang.driver' ) . ':*') !!}
                {!! Form::select('list_loading_driver_id', $drivers, null, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.all'), 'id'
                          => 'list_loading_driver_id','required']); !!}
            </div>
        </div>
        
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('bakery::lang.list_loading')])
  
  <div class="table-responsive">
      <table class="table table-bordered table-striped" id="list_loading_table" style="width: 100%;">
        <thead>
        <tr>
          <th>@lang('bakery::lang.action')</th>
          <th>@lang('bakery::lang.date')</th>
          <th>@lang('bakery::lang.form_no')</th>
          <th>@lang('bakery::lang.vehicle')</th>
          <th>@lang('bakery::lang.driver')</th>
          <th>@lang('bakery::lang.route')</th>
          <th>@lang('bakery::lang.total_due')</th>
          <th>@lang('bakery::lang.total_sold')</th>
          <th>@lang('bakery::lang.total_returned')</th>
          <th>@lang('bakery::lang.total_short')</th>
          <th>@lang('bakery::lang.user_added')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
</div>

  @endcomponent
</section>
<!-- /.content -->