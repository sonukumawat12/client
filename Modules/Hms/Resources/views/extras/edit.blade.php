<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action([\Modules\Hms\Http\Controllers\ExtraController::class, 'update'], ['extra'=> $extra->id]), 'method' => 'put', 'id' => 'add_extra' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'hms::lang.edit_extra' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', __('hms::lang.name') . '*') !!}
            {!! Form::text('name', $extra->name, [
                'class' => 'form-control',
                'required',
                'placeholder' => __('hms::lang.name'),
            ]) !!}
        </div>
        @php
            $business_id = request()->session()->get('user.business_id');
            $currency_precision = \App\Business::where('id', $business_id)->value('currency_precision') ?? 2;
            $step_value = '0.' . str_repeat('0', $currency_precision - 1) . '1';
            $formatted_price = number_format((float) $extra->price, $currency_precision, '.', '');
        @endphp
        
        <div class="form-group">
            <label for="price">{{ __('hms::lang.price') }} *</label>
            <input
                type="number"
                class="form-control"
                name="price"
                id="price"
                value="{{ $formatted_price }}"
                step="{{ $step_value }}"
                required
                placeholder="{{ __('hms::lang.price') }}"
            >
        </div>
        <div class="form-group">
            {!! Form::label('price_per', __('hms::lang.per') . '*') !!}
            {!! Form::select('price_per', $price_per,$extra->price_per , [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
      </div>
  
      <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="btn btn-primary">
                @lang('cheque.save')
            </button>
        </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->