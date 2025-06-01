@php

    $trip_rates = ['Vehicle Page' => 'Vehicle Page','Add Trip Page' => 'Add Trip Page' ];
    $driver_helpers_rates = ['Vehicle Page' => 'Vehicle Page','Add Trip Page' => 'Add Trip Page' ];

@endphp


<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
      <!-- Filters Component -->
      @component('components.filters', ['title' => __('report.filters')])
          <div class="col-md-3 mb-3">
            <div class="form-group">
            {!! Form::label('trip_rates', __( 'fleet::lang.trip_rates' )) !!}
            {!! Form::select('trip_rates', $trip_rates, null, ['class' => 'form-control select2',
            'required',
            'placeholder' => __(
            'fleet::lang.please_select' ), 'id' => 'trip_rates']);
            !!}
          </div>
        </div>
        
         <div class="col-md-3 mb-3">
            <div class="form-group">
            {!! Form::label('driver_helpers_rates', __( 'fleet::lang.driver_helpers_rates' )) !!}
            {!! Form::select('driver_helpers_rates', $driver_helpers_rates, null, ['class' => 'form-control select2',
            'required',
            'placeholder' => __(
            'fleet::lang.please_select' ), 'id' => 'driver_helpers_rates']);
            !!}
          </div>
        </div>
      @endcomponent
    </div>
  </div>
  <!-- Rates Table -->
  <div class="row mt-8">
    <div class="col-md-12">
      @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.rates')])
        @slot('tool')
        <div class="box-tools">
          
        </div>
        @endslot
        <div class="table-responsive">
          <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
              <tr>
                <th>@lang('fleet::lang.sources')</th>
                <th>@lang('fleet::lang.types')</th>
                <th>@lang('fleet::lang.rates')</th>
                <th>@lang('fleet::lang.details')</th>
              </tr>
            </thead>
          </table>
        </div>
      @endcomponent
    </div>
  </div>
</section>
<!-- /.content -->