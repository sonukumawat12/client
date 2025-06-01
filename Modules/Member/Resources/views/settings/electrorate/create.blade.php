<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\ElectrorateController@store'), 'method' =>
    'post', 'id' => 'elec_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_electrorate' )</h4>
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
        {!! Form::label('district', __('member::lang.district') . ':*') !!}
        {!! Form::select('district',$districts, null, [
            'class' => 'form-control select2',
            'id' => 'district_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>
      
       <div class="form-group">
        {!! Form::label('name', __( 'member::lang.electrorate' )) !!}
      <div class="d-flex">
        {!! Form::select('name',$electrorates, null, [
            'class' => 'form-control select2',
            'id' => 'electrorate_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
        <span class="">
            <button type="button" class="btn btn-primary" id="add_new_electrorate_btn">
              <i class="fa fa-plus"></i>
            </button>
         </span>
        </div>
      </div>


    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_electrorate_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#province_select').select2({
        width: '100%'
    });
   $('#district_electrorate').select2({
        width: '100%'
    });
    
  $('#add_new_electrorate_btn').on('click', function () {
      $('#new_electrorate_name').val(''); // Clear the input field
      $('#add_new_electrorate_modal').modal('show');
  }); 
    
    $('#save_new_electrorate').on('click', function () {
        const newElectrorateName = $('#new_electrorate_name').val().trim();

        if (!newElectrorateName) {
            toastr.error('Please Enter Electrorate Name.');
            return;
        }

        const newOption = new Option(newElectrorateName, newElectrorateName, true, true);
        $('#electrorate_select').append(newOption).trigger('change');

        $('#add_new_electrorate_modal').modal('hide');
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
    
    $('#district_select').on('change', function () {
        const selectedDistrict = $(this).val();

        $('#electrorate_select').html('<option value="">@lang("messages.please_select")</option>');

        if (selectedDistrict) {
            $.get('/member-module/get-electrorates/' + selectedDistrict, function (data) {
                $.each(data, function (id, name) {
                    $('#electrorate_select').append(new Option(name, id));
                });
            }).fail(function () {
                toastr.error('No Electrorate Found!');
            });
        }
    });    
    
</script>
