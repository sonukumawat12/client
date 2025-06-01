<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyStartingNoController@store'), 'method' =>
    'post', 'id' => $quick_add ? 'quick_add_starting_no_form' : 'starting_no_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Starting File</h4>
    </div>

    <div class="modal-body">
      <div class="row">
     
        <div class="form-group col-sm-12">
          {!! Form::label('prefix', 'Prefix:*') !!}
          {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => 'Prefix', 'id'
          => 'prefix']); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('starting_no', 'Starting File:*') !!}
          {!! Form::text('starting_no', null, ['class' => 'form-control', 'placeholder' => 'Starting File', 'id'
          => 'starting_no']); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::checkbox('is_active', 1, 0, ['id'
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
