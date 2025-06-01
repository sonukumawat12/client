<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\CategoryController@store'), 'method' => 'post', 'id' => 'category_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.add_category' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'leads::lang.date' )) !!}
        {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.date' ), 'id' => 'category-date']);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('name', __( 'leads::lang.name' )) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'leads::lang.name' )]);
        !!}
      </div>
      
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('add_as_sub_cat', 1, false,[ 'class' => 'toggler', 'data-toggle_id' => 'parent_cat_div',
            'data-toggle_class' => 'parent_cat_div'
            ]); !!} @lang( 'category.add_as_sub_category' ) 
          </label>
        </div>
      </div>
      
      
      <div class="form-group hide" id="parent_cat_div">
        {!! Form::label('parent_id', __( 'category.select_parent_category' ) . ':') !!}
        {!! Form::select('parent_id', $categories, null, ['class' => 'form-control select2']); !!}
      </div>
      
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_category_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
	var date = new Date();
  	var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
  	$(".select2").select2();

	$('#category-date').datepicker({
		format: 'mm/dd/yyyy',
		beforeShowDay: function() {
      		return false;
		}
	});

  	$('#category-date').datepicker('setDate', today);
</script>