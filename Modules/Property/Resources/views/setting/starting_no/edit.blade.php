<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyStartingNoController@update',
    $installment_cycle->id), 'method' =>
    'post', 'id' => 'starting_no_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.edit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="form-group col-sm-12">
          {!! Form::label('prefix', 'Prefix:*') !!}
          {!! Form::text('prefix', $installment_cycle->prefix, ['class' => 'form-control', 'placeholder' => 'Prefix', 'id'
          => 'prefix']); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('starting_no', 'Starting File:*') !!}
          {!! Form::text('starting_no', $installment_cycle->starting_no, ['class' => 'form-control', 'placeholder' => 'Starting File', 'id'
          => 'starting_no']); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::checkbox('is_active', 1, $installment_cycle->is_active, ['id'
          => 'is_active']) !!}
          {!! Form::label('is_active', 'Is Active ?') !!}
        </div>
        
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

