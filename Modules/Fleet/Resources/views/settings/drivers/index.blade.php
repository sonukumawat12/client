<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('employee_no', __( 'fleet::lang.employee_no' )) !!}
                {!! Form::select('employee_no', $employee_nos, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'employee_no']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('driver_name', __( 'fleet::lang.driver_name' )) !!}
                {!! Form::select('driver_name', $driver_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'driver_name']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('nic_number', __( 'fleet::lang.nic_number' )) !!}
                {!! Form::select('nic_number', $nic_numbers, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'fleet::lang.please_select' ), 'id' => 'nic_number']);
                !!}
            </div>
        </div>
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.all_your_drivers')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\DriverController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="driver_table" style="width: 100%;  table-layout: auto;">
          <thead>
            <tr>
              <th class="notexport">@lang('messages.action')</th>
              <th>@lang('fleet::lang.joined_date')</th>
              <th>@lang('fleet::lang.employee_no')</th>
              <th>@lang('fleet::lang.driver_name')</th>
              <th>@lang('fleet::lang.salary_expense_category')</th>
              <th>@lang('fleet::lang.bata_expense_category')</th>
              <th>@lang('fleet::lang.advance_expense_category')</th>
              <th>@lang('fleet::lang.nic_number')</th>
              <th>@lang('fleet::lang.dl_number')</th>
              <th>@lang('fleet::lang.dl_type')</th>
              <th>@lang('fleet::lang.expiry_date')</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data will be rendered dynamically by DataTables -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endcomponent
</section>
<!-- /.content -->