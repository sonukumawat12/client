<div class="modal-dialog modal-lg" role="document">
           <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Shared With
                </h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="shares_table"  style="width: 100%;">
                              <thead>
                                <tr>
                                  <th>Shared with</th>
                                  <th>Shared on</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach($shares as $share)
                                    <tr>
                                       <td>{{$share->shareName}}</td>
                                        <td>{{@format_datetime($share->created_at)}}</td>
                                    </tr>
                                    
                                  @endforeach
                              </tbody>
                            </table>
                          </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                
            </div>
        </div>
    {!! Form::close() !!}
  </div>
<script type="text/javascript">
$(document).ready(function() {
      $("#shares_table").DataTable();
});
</script>