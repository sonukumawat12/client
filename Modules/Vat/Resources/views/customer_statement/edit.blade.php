<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\CustomerStatementController@update', [$statement->id]), 'method' => 'put', 'id' => 'transaction_edit_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'vat::lang.edit' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('date_from', __( 'contact.date_from' ) . ':*') !!}
                      {!! Form::date('date_from', $statement->date_from, ['class' => 'form-control',  'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('date_to', __( 'contact.date_to' ) . ':*') !!}
                      {!! Form::date('date_to', $statement->date_to, ['class' => 'form-control', 'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
              </div>
              
          </div>
          
  
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" id="transaction_edit_btn" class="btn btn-primary">@lang( 'messages.update' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

  