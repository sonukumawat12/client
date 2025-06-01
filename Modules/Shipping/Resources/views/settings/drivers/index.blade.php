<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('driver_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('driver_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'driver_date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('employee_no', __( 'shipping::lang.employee_no' )) !!}
                {!! Form::select('employee_no', $employee_nos, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'employee_no']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('driver_name', __( 'shipping::lang.driver_name' )) !!}
                {!! Form::select('driver_name', $driver_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'driver_name']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('nic_number', __( 'shipping::lang.nic_number' )) !!}
                {!! Form::select('nic_number', $nic_numbers, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'nic_number']);
                !!}
            </div>
        </div>
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.all_your_drivers')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Shipping\Http\Controllers\DriverController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="driver_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.joined_date')</th>
          <th>@lang('shipping::lang.employee_no')</th>
          <th>@lang('shipping::lang.driver_name')</th>
          <th>@lang('shipping::lang.nic_number')</th>
          <th>@lang('shipping::lang.dl_number')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->