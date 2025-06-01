<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\LabelController@update', $category->id), 'method' => 'PUT', 'id' => 'label_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.edit_label' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('label_1', __( 'leads::lang.label_1' )) !!}
        {!! Form::text('label_1', $category->label_1, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.label_1' )]);
        !!}
      </div>
      
       <div class="form-group">
        {!! Form::label('label_2', __( 'leads::lang.label_2' )) !!}
        {!! Form::text('label_2', $category->label_2, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.label_2' )]);
        !!}
      </div>
      
       <div class="form-group">
        {!! Form::label('label_3', __( 'leads::lang.label_3' )) !!}
        {!! Form::text('label_3', $category->label_3, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.label_3' )]);
        !!}
      </div>

     
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_category_btn">@lang( 'leads::lang.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

