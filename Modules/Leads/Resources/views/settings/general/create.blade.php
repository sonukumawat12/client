<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\SettingController@store'), 'method' => 'post', 'id' => 'category_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.add_setting' )</h4>
    </div>

    <div class="modal-body">
       <div class="form-group">
            <label for="general-date">Date</label>
            <input type="text" class="form-control" id="general-date" placeholder="Date">
        </div>
        
        <div class="form-group">
            <label for="quotations">Quotations done by the user</label>
            <select class="form-control" id="quotations" placeholder="Select yes or no">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        
         <div class="form-group">
            <label for="sales-inv">Sales Invoice done by the user </label>
            <select class="form-control" id="sales-inv" name="sale_invoice" placeholder="Select yes or no">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        <div class="form-group">
            <label for="resp">Client Response</label>
            <input type="text" class="form-control" id="resp">
        </div>
        <div class="form-group">
            <label for="action">Action</label>
            <input type="text" class="form-control" id="action">
        </div>
        <div class="form-group">
            <label for="user">User</label>
            <input type="text" class="form-control" id="user">
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

	$('#general-date').datepicker({
		format: 'mm/dd/yyyy',
		beforeShowDay: function() {
      		return false;
		}
	});

  	$('#general-date').datepicker('setDate', today);
</script>