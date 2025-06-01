
@php

@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('\Modules\Airline\Http\Controllers\AirlineAgentController@store'), 'method' => 'post', 'id' => 'driver_add_form']) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}"> <!-- Ensure CSRF token is included -->

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('airline::lang.agent')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <!-- Joined Date -->
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __('airline::lang.added_date') . ':*') !!}
          {!! Form::text('joined_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __('airline::lang.joined_date')]) !!}
        </div>

        <!-- Agent Name -->
        <div class="form-group col-sm-12">
          {!! Form::label('agent', __('airline::lang.name') . ':*') !!}
          {!! Form::text('agent', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.name'), 'id' => 'add_name', 'required']) !!}
        </div>

        <!-- Address -->
        <div class="form-group col-sm-12">
          {!! Form::label('address', __('airline::lang.address') . ':*') !!}
          {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.address'), 'id' => 'add_address', 'required']) !!}
        </div>

        <!-- Mobile 1 -->
        <div class="form-group col-sm-12">
          {!! Form::label('mobile_1', __('airline::lang.mobile_1') . ':*') !!}
          {!! Form::text('mobile_1', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.mobile_1'), 'id' => 'add_mobile_1', 'required']) !!}
        </div>

        <!-- Mobile 2 -->
        <div class="form-group col-sm-12">
          {!! Form::label('mobile_2', __('airline::lang.mobile_2') . ':*') !!}
          {!! Form::text('mobile_2', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.mobile_2'), 'id' => 'add_mobile_2']) !!}
        </div>

        <!-- Landline Number -->
        <div class="form-group col-sm-12">
          {!! Form::label('land_no', __('airline::lang.land_no') . ':*') !!}
          {!! Form::text('land_no', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.land_no'), 'id' => 'add_land_no']) !!}
        </div>

        <!-- Opening Balance -->
        <div class="form-group col-sm-12">
          {!! Form::label('opening_balance', __('airline::lang.opening_balance') . ':*') !!}
          {!! Form::number('opening_balance', null, ['class' => 'form-control', 'placeholder' => __('airline::lang.opening_balance'), 'id' => 'add_opening_balance', 'required']) !!}
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#joined_date').datepicker('setDate', new Date());
 $(".select2").select2();
</script>