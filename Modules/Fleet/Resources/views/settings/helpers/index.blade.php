<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @component('components.filters', ['title' => __('report.filters')])
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_employee_no', __( 'fleet::lang.employee_no' )) !!}
          {!! Form::select('helper_employee_no', $helper_employee_nos, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_employee_no']);
          !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_name', __( 'fleet::lang.helper_name' )) !!}
          {!! Form::select('helper_name', $helper_names, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_name']);
          !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_nic_number', __( 'fleet::lang.nic_number' )) !!}
          {!! Form::select('helper_nic_number', $helper_nic_numbers, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_nic_number']);
          !!}
        </div>
      </div>
      @endcomponent
    </div>
  </div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.all_your_helpers')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Fleet\Http\Controllers\HelperController@create')}}" data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
 <div class="row">
  <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="helper_table" style="width: 100%;">
          <thead>
            <tr>
              <th class="notexport" style="width: 10%;">@lang('messages.action')</th>
              <th style="width: 15%;">@lang('fleet::lang.joined_date')</th>
              <th style="width: 10%;">@lang('fleet::lang.employee_no')</th>
              <th style="width: 20%;">@lang('fleet::lang.helper_name')</th> <!-- Increased width for better readability -->
              
              <th style="width: 15%;">@lang('fleet::lang.salary_expense_category')</th> <!-- Adjusted column width -->
              <th style="width: 15%;">@lang('fleet::lang.bata_expense_category')</th> <!-- Adjusted column width -->
              <th style="width: 10%;">@lang('fleet::lang.advance_expense_category')</th>
              
              <th style="width: 15%;">@lang('fleet::lang.nic_number')</th> <!-- Adjusted column width for consistency -->
            </tr>
          </thead>
        </table>
      </div>
  </div>
  </div>
  @endcomponent
</section>
<!-- /.content -->
