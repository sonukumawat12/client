<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\ProvinceController@store'), 'method' =>
    'post', 'id' => 'provinces_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_province' )</h4>
    </div>

    <div class="modal-body">
       <div class="form-group">
        {!! Form::label('country', __('member::lang.country') . ':*') !!}
        {!! Form::select('country',$countries, null, [
            'class' => 'form-control select2',
            'id' => 'country_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
       </div>
      
      <div class="form-group">
        {!! Form::label('name', __('member::lang.province') . ':*') !!}
        <div class="d-flex">
        {!! Form::select('name',$provinces, null, [
            'class' => 'form-control select2',
            'id' => 'province_select',
            'required',
            'placeholder' => __('messages.please_select'),
        ]) !!}
         <span class="">
            <button type="button" class="btn btn-primary" id="add_new_province_btn">
              <i class="fa fa-plus"></i>
            </button>
          </span>
          </div>
       </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_provinces_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   $('#country_select').select2({
        width: '100%'
    });
  $('#province_select').select2({
       width: '85%',
       minimumResultsForSearch: 0 
  });
          
    $('#add_new_province_btn').on('click', function () {
        $('#new_province_name').val(''); // Clear the input field
        $('#add_new_province_modal').modal('show');
    });

    $('#save_new_province').on('click', function () {
        const newProvinceName = $('#new_province_name').val().trim();

        if (!newProvinceName) {
            toastr.error('Please Enter Province Name.');
            return;
        }

        const newOption = new Option(newProvinceName, newProvinceName, true, true);
        $('#province_select').append(newOption).trigger('change');

        $('#add_new_province_modal').modal('hide');
    });
    
     $('#country_select').on('change', function () {
        const selectedCountry = $(this).val();

        $('#province_select').html('<option value="">@lang("messages.please_select")</option>');

        if (selectedCountry) {
            $.get('/member-module/get-provinces/' + selectedCountry, function (data) {
                $.each(data, function (id, name) {
                    $('#province_select').append(new Option(name, id));
                });
            }).fail(function () {
                toastr.error('No Province Found!');
            });
        }
    });
 
</script>  