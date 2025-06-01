<div class="modal fade" id="add_new_district_modal" tabindex="-1" role="dialog" aria-labelledby="addDistrictModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="addDistrictModalLabel">@lang('member::lang.add_district')</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('new_district_name', __('member::lang.district') . ':*') !!}
          <input type="text" class="form-control" id="new_province_name" placeholder="Enter District Name." required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save_new_province">@lang('messages.add')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      </div>
    </div>
  </div>
</div>