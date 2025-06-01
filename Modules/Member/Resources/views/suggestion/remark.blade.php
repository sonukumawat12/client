<style>
    .tox-menubar{
        display: none !important;
    }
    .tox-toolbar-overlord{
        display: none !important;
    }
    .tox-statusbar{
        display: none !important;
    }
</style>
<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
  
      <style>
        .select2 {
          width: 100% !important;
        }
      </style>
      {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\SuggestionController@store'), 'method' =>
      'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ])
      !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remark</h4>
      </div>
  
      <div class="modal-body">
      
        <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('remarks', __( 'member::lang.remarks' )) !!}
              {!! Form::textarea('remarks', $suggestion->remarks, [
                    'class' => 'form-control',
                    'placeholder' => __('member::lang.remarks'),
                    'readonly' => 'readonly' // Correctly placed outside of the placeholder
                ]); !!}
              </div>
        </div>
  
       
      
      </div>
      <div class="clearfix"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
     if ($('#details').length) {
          tinymce.init({
              selector: 'textarea#details',
          });
      }
  </script>