<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\LabelController@store'), 'method' => 'post', 'id' => 'label_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.add_label' )</h4>
    </div>

    <div class="modal-body">

      <div class="form-group">
        {!! Form::label('label_1', __( 'leads::lang.label_1' )) !!}
        {!! Form::text('label_1', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.label_1' )]);
        !!}
      </div>
      
       <div class="form-group">
        {!! Form::label('label_2', __( 'leads::lang.label_2' )) !!}
        {!! Form::text('label_2', null, ['class' => 'form-control', 'placeholder' => __( 'leads::lang.label_2' )]);
        !!}
      </div>
      
       <div class="form-group">
        {!! Form::label('label_3', __( 'leads::lang.label_3' )) !!}
        {!! Form::text('label_3', null, ['class' => 'form-control', 'placeholder' => __( 'leads::lang.label_3' )]);
        !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_category_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

