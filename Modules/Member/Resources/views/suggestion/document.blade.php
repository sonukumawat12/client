<style>
    .tox-menubar {
        display: none !important;
    }
    .tox-toolbar-overlord {
        display: none !important;
    }
    .tox-statusbar {
        display: none !important;
    }
</style>

<div class="modal-dialog" role="document" style="width: 40%">
    <div class="modal-content">
  
      <style>
        .select2 {
          width: 100% !important;
        }
      </style>
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Document</h4>
      </div>
  
      <div class="modal-body">
        <div class="col-md-8">
            <div class="form-group">
                @if(isset($suggestion->upload_document))
                    <div class="mt-3">
                        <strong>Current File:</strong> 
                        <span>{{ basename($suggestion->upload_document) }}</span>
                    </div>
                    <!-- add document type-->
                    <div class="mt-3">
                        <strong>Document Type:</strong>
                        <span>{{ pathinfo($suggestion->upload_document, PATHINFO_EXTENSION) }}</span>
                    </div>
                    <!-- end of document type-->
                    <div class="mt-3">
                        <a href="{{ asset($suggestion->upload_document) }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fa fa-eye"></i> {{ __('View') }}
                        </a>
                    </div>
                    <div class="mt-3">
                        <a href="{{ asset($suggestion->upload_document) }}" class="btn btn-success btn-sm" download>
                            <i class="fa fa-download"></i> {{ __('Download') }}
                        </a>
                    </div>
                   <div class="mt-3">
                        <strong>Document Preview:</strong>
                        <div class="document-preview">
                            @if (pathinfo($suggestion->upload_document, PATHINFO_EXTENSION) === 'pdf')
                                <iframe src="{{ asset($suggestion->upload_document) }}" style="width: 100%; height: 400px;" frameborder="0"></iframe>
                                <p>If the PDF does not display, <a href="{{ asset($suggestion->upload_document) }}" target="_blank">click here to view.</a></p>
                            @else
                                <p>Document preview is not available for this file type. 
                                <a href="{{ asset($suggestion->upload_document) }}" target="_blank">Click here to view.</a></p>
                            @endif
                        </div>
                    </div>
                @else
                    <p>No document uploaded.</p>
                @endif
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
