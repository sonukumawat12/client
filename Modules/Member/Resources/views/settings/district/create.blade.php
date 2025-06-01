<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\DistrictController@store'), 'method' =>
    'post', 'id' => 'district_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_district' )</h4>
    </div>

    <div class="modal-body">
     
       <div class="form-group">
        {!! Form::label('province', __('member::lang.provinces') . ':*') !!}
        {!! Form::select('province',$provinces, null, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>

     <div class="form-group">
        {!! Form::label('name', __('member::lang.district') . ':*') !!}
        <div class="d-flex">
        {!! Form::select('name',$districts, null, [
            'class' => 'form-control select2',
            'id' => 'district_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
        
     <span class="">
            <button type="button" class="btn btn-primary" id="add_new_district_btn">
              <i class="fa fa-plus"></i>
            </button>
          </span>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_district_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#gramaseva_vasama_date').datepicker({
        format: 'mm/dd/yyyy'
  });
    
     $('#add_new_district_btn').on('click', function () {
        $('#new_district_name').val(''); // Clear the input field
        $('#add_new_district_modal').modal('show');
    });
    
     $('#save_new_district').on('click', function () {
        const newDistrictName = $('#new_district_name').val().trim();

        if (!newDistrictName) {
            toastr.error('Please Enter District Name.');
            return;
        }

        const newOption = new Option(newDistrictName, newDistrictName, true, true);
        $('#district_select').append(newOption).trigger('change');

        $('#add_new_district_modal').modal('hide');
    });
    
    
    $('#province_select').on('change', function () {
        const selectedProvince = $(this).val();

        $('#district_select').html('<option value="">@lang("messages.please_select")</option>');

        if (selectedProvince) {
            $.get('/member-module/get-districts/' + selectedProvince, function (data) {
                $.each(data, function (id, name) {
                    $('#district_select').append(new Option(name, id));
                });
            }).fail(function () {
                toastr.error('No District Found!');
            });
        }
    });
    

</script>