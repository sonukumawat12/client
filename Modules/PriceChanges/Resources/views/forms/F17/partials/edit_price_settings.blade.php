<div class="modal-dialog modal-xl" role="document" style="height: 95vh; overflow-y: auto">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\PriceChanges\Http\Controllers\F17FormController@editPriceChangeSettings', ['id' => $setting->id]), 'method' =>
    'post', 'id' => 'fleet_form', 'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
        id = "settings_edit_btn"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'pricechanges::lang.price_change_settings' )</h4>
    </div>

    <div class="modal-body">
        
      <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('income_account_id_edit', __( 'pricechanges::lang.price_gain_account' )) !!} {!!
          Form::select('income_account_id_edit', $incomeAccounts, $setting->gain_account_id, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'modal_account_id']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('expense_account_id_edit', __( 'pricechanges::lang.price_loss_account' )) !!} {!!
          Form::select('expense_account_id_edit', $expenseAccounts, $setting->loss_account_id, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'modal_location_id']); !!}
        </div>
      </div>
        
      </div>
    </div>
    <input type="hidden" name="setting_id" id="setting_id" value = "{{$setting->id}}">
    <div class="clearfix"></div>
    <div class="modal-footer">
        
      <button type="button" class="btn btn-primary" id="save_leads_btn">
        @lang( 'messages.save' )
      </button>
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
    {!! Form::close() !!}
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->


<script>
    $(document).ready(function() {
        $('#save_leads_btn').click(function() {
        // Serialize form data
            var formData = $('#fleet_form').serialize();
            var id = $('setting_id').val();
    
            // Make AJAX request
            $.ajax({
                url: '/pricechanges/edit-prices_change_settings/'+id,
                method: 'POST',
                data: formData,
                success: function(result) {
                    if(result.success == 1){
                        toastr.success(result.msg);
                        $('.dataTable').DataTable().destroy();
                        $('#price_change_settings_table').DataTable({
                                processing: true,
                                serverSide: false,
                                ajax: {
                                        url: '/pricechanges/list-prices_change_settings/'
                                    },
                                    columns: [
                                        { data: 'date', name: 'date' },
                                        { data: 'gain_account_name', name: 'gain_account_name' },
                                        { data: 'loss_account_name', name: 'loss_account_name' },
                                        { data: 'user', name: 'user' },
                                        { data: 'edit', name: 'edit'}
                                      
                                    ],
                                    
                                }); 
                    }else{
                                toastr.error(result.msg);
                    }           
                                
                    document.getElementById("settings_edit_btn").click();
               },
            });
        });
    });
</script>
