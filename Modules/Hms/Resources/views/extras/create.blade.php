<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action([\Modules\Hms\Http\Controllers\ExtraController::class, 'store']), 'method' => 'post', 'id' => 'add_extra' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'hms::lang.add_extra' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', __('hms::lang.name') . '*') !!}
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'required',
                'placeholder' => __('hms::lang.name'),
            ]) !!}
        </div>
        @php
            $business_id = request()->session()->get('user.business_id');
            $currency_precision = \App\Business::where('id', $business_id)->value('currency_precision') ?? 2;
            $step_value = '0.' . str_repeat('0', $currency_precision - 1) . '1';
            $default_price = number_format(0, $currency_precision, '.', '');
        @endphp
        
        <div class="form-group">
            {!! Form::label('price', __('hms::lang.price') . '*') !!}
            {!! Form::number('price', $default_price, [
                'class' => 'form-control',
                'required',
                'step' => $step_value,
                'placeholder' => __('hms::lang.price'),
            ]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('price_per', __('hms::lang.per'). '*') !!}
            {!! Form::select('price_per', $price_per, '', [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
      </div>
  
      <div class="modal-footer">
       	<button id="submitBtn" type="submit" class="btn btn-success pull-right m-8">@lang('messages.save')</button>
		<button id="cancelBtn" type="button" class="btn btn-primary pull-right m-8 " data-dismiss="modal">@lang('messages.cancel')</button> <!-- @eng 15/2 -->
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->