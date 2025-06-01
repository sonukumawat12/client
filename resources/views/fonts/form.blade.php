<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\App\Http\Controllers\Chequer\DefaultFontsController@store'), 'method' => 'POST', 'id' =>
      'fonts_form']) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('lang_v1.add_new_font')</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group">
                {!! Form::label('font', __('lang_v1.font') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-font"></i>
                    </span>
                    {!! Form::text('font',null, ['class' => 'form-control','placeholder' => __('lang_v1.font'),
                    'required']); !!}
                </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
  
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.add' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
    $(document).on('submit','#fonts_form',function(){
        event.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        var submitButton = form.find('.submit-btn');
        submitButton.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if(response.success){
                    toastr.success(response.msg, 'Success');
                    fontSelet.trigger('setFont',response.data.font).trigger('change');
                    $("#def_font").val(response.data.font);
                    $('.modal').modal('hide');
                }else{
                    toastr.error(response.msg, 'Error');
                }
                
            },
            error: function(xhr, status, error) {
                // Handle error response
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = errors;

                    toastr.error(errorMessage, 'Validation Errors');
                } else {
                    var error = xhr.responseJSON.message ?? "";
                    if (error == "") {
                        var error = 'Something Went Wrong!, Try again!';
                    }
                    toastr.error(error, 'Error');
                }
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    })
  </script>