<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'storeEarning']), 'method' => 'post', 'id' => 'add_earning_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_earning' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
          {!! Form::label('form_number', __( 'lang_v1.form_number' ) . ':') !!}
          {!! Form::text('form_number', $formNumber, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.form_number' ),'readonly']) !!}
      </div>
      <div class="form-group">
        {!! Form::label('date', __( 'lang_v1.date' ) . ':*') !!}
        {!! Form::date('date', \Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.date' )])!!}
      </div>
      <div class="form-group">
        {!! Form::label('amount', __( 'lang_v1.amount' ) . ':*') !!}
        {!! Form::text('amount', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.amount' )])!!}
      </div>
      <div class="form-group">
        {!! Form::label('note', __( 'lang_v1.note' ) . ':*') !!}
        {!! Form::text('note', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.note' )])!!}
      </div>
      <div class="form-group">
        {!! Form::label('add_by', __( 'lang_v1.add_by' ) . ':*') !!}
        {!! Form::text('add_by', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.add_by' )])!!}
      </div>
    </div>
    <input type="hidden" name="employee_id" id="employee_id" value="{{$employeeId}}">
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
    <div class="modal-body">

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->