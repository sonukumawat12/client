<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ExpenseCategoryCodeController@update', [$expense_category_code->id]), 'method' => 'PUT' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'expense.expense_category_code' )</h4>
    </div>

    <div class="modal-body">
         
          
          <div class="form-group">
            {!! Form::label('starting_no', __( 'expense.starting_no' ) . ':*') !!}
            {!! Form::text('starting_no', $expense_category_code->starting_no, ['class' => 'form-control', 'required', 'placeholder' => __(
            'expense.starting_no' )]); !!}
          </div>
    
          <div class="form-group">
            {!! Form::label('prefix', __( 'expense.prefix' ) . ':') !!}
            {!! Form::text('prefix', $expense_category_code->prefix, ['class' => 'form-control', 'placeholder' => __( 'expense.prefix' )]); !!}
          </div>
    </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    
    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
