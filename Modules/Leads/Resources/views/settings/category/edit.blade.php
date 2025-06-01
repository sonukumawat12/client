<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\CategoryController@update', $category->id), 'method' => 'PUT', 'id' => 'category_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.edit_category' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'leads::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y', strtotime($category->date)), ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.date' ), 'id' => 'date']);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('name', __( 'leads::lang.name' )) !!}
        {!! Form::text('name', $category->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.name' )]);
        !!}
      </div>
      
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('add_as_sub_cat', 1, !empty($category->parent_id) ? true : false,[ 'class' => 'toggler', 'data-toggle_id' => 'parent_cat_div',
            'data-toggle_class' => 'parent_cat_div'
            ]); !!} @lang( 'category.add_as_sub_category' ) 
          </label>
        </div>
      </div>
      
      
      <div class="form-group @if(empty($category->parent_id)) hide @endif" id="parent_cat_div">
        {!! Form::label('parent_id', __( 'category.select_parent_category' ) . ':') !!}
        {!! Form::select('parent_id', $categories, $category->parent_id, ['class' => 'form-control select2']); !!}
      </div>

     
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_category_btn">@lang( 'leads::lang.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   $('#date').datepicker({
      format: 'mm/dd/yyyy'
  });
  $(".select2").select2()
</script>