<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\GramasevaVasamaController@store'), 'method' =>
    'post', 'id' => 'gramaseva_vasama_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_gramaseva_vasama' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('province_id', __('member::lang.province') . ':*') !!}
        {!! Form::select('province_id',$provinces, null, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>

      <div class="form-group">
        {!! Form::label('district_gram', __('member::lang.district') . ':*') !!}
        {!! Form::select('district_gram',$districts, null, [
            'class' => 'form-control select2',
            'id' => 'district_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>

      <div class="form-group">
        {!! Form::label('electrorate_id', __('member::lang.electrorate') . ':*') !!}
        {!! Form::select('electrorate_id',$electrorates, null, [
            'class' => 'form-control select2',
            'id' => 'electrorate_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
      </div>

      <div class="form-group">
        {!! Form::label('gramaseva_vasama', __( 'member::lang.gramaseva_vasama' )) !!}

        <div class="d-flex">
        {!! Form::select('gramaseva_vasama',$gramasevavasamas, null, [
            'class' => 'form-control select2',
            'id' => 'gramasevavasamas_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}

        <span class="">
            <button type="button" class="btn btn-primary" id="add_new_gsvasama_btn">
              <i class="fa fa-plus"></i>
            </button>
        </span>
        </div>

      </div>

      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'gramaseva_vasama_date']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_gramaseva_vasama_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#gramaseva_vasama_date').datepicker({
        format: 'mm/dd/yyyy'
    });
    $('#electrorate_select').select2({
        width: '100%'
    });
    $('#province_select').select2({
        width: '100%'
    });
   
    // $('#electrorate_select').change(function(){
        
    //   $.ajax({
    //     method: 'post',
    //     url: '{{action('\Modules\Member\Http\Controllers\ElectrorateController@get')}}',
    //     data: { 
    //       id : $(this).val(),
    //      },
    //     success: function(result) {
    //       $('#province_select').val(result.province_id).trigger('change');
    //       $('#district_gram').val(result.district);
          
    //     },
    // });
    // });
    
    // Enable the disabled dropdown before form submission
  $('#gramaseva_vasama_form').submit(function(event) {
      $('#province_select').prop('disabled', false); // Temporarily enable the province dropdown
  });

  // Re-disable the dropdown after form submission
  $('#save_gramaseva_vasama_btn').click(function() {
      //$('#province_select').prop('disabled', true); // Disable again after the form is submitted
  });
  
    $('#add_new_gsvasama_btn').on('click', function () {
        $('#new_gsvasama_name').val(''); // Clear the input field
        $('#add_new_gsvasama_modal').modal('show');
    });
    
        $('#save_new_gsvasama').on('click', function () {
        const newProvinceName = $('#new_gsvasama_name').val().trim();

        if (!newProvinceName) {
            toastr.error('Please Enter Gramasevavasama Name.');
            return;
        }

        const newOption = new Option(newProvinceName, newProvinceName, true, true);
        $('#gramasevavasamas_select').append(newOption).trigger('change');

        $('#add_new_gsvasama_modal').modal('hide');
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

    $('#electrorate_select').on('change', function () {
        const selectedGsvasama = $(this).val();

        $('#gramasevavasamas_select').html('<option value="">@lang("messages.please_select")</option>');

        if (selectedGsvasama) {
            $.get('/member-module/get-gsvasamas/' + selectedGsvasama, function (data) {
                $.each(data, function (id, name) {
                    $('#gramasevavasamas_select').append(new Option(name, id));
                });
            }).fail(function () {
                toastr.error('No Gramaseva vasama Found!');
            });
        }
    }); 

</script>