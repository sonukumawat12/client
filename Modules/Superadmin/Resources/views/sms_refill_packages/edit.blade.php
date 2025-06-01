<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\SmsRefillPackageController@update',[$data->id]), 'method' => 'put', 'id' => 'sms_packages_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'superadmin::lang.sms_packages' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('date', __( 'lang_v1.date' ) .":*") !!}
                  {!! Form::date('date', date('Y-m-d',strtotime($data->date)), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.date' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('name', __( 'superadmin::lang.name' ) .":*") !!}
                  {!! Form::text('name', $data->name, ['class' => 'form-control', 'required','placeholder' => __( 'superadmin::lang.name' ) ]); !!}
              </div>
              
              <div class="form-group">
                  {!! Form::label('unit_cost', __( 'lang_v1.unit_cost' ) .":*") !!}
                  {!! Form::text('unit_cost', $data->unit_cost, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.unit_cost' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('amount', __( 'superadmin::lang.amount' ) .":*") !!}
                  {!! Form::text('amount', $data->amount, ['class' => 'form-control', 'required','placeholder' => __( 'superadmin::lang.amount' ) ]); !!}
              </div>
              
              <div class="form-group">
                  {!! Form::label('no_of_sms', __( 'superadmin::lang.no_of_sms' ) .":*") !!}
                  {!! Form::text('no_of_sms', $data->no_of_sms, ['class' => 'form-control', 'required','placeholder' => __( 'superadmin::lang.no_of_sms' ) ]); !!}
              </div>
  
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  
  <script>
      $(document).on('change', '#unit_cost, #amount', function () {
            var unit_cost = $("#unit_cost").val() ?? 0;
            var amount = $("#amount").val() ?? 0;
        
            var sms = 0; // Default to 0 if either value is invalid
        
            if (unit_cost > 0 && amount > 0) {
                // Truncate the decimal portion without rounding
                sms = Math.trunc(amount / unit_cost);
            }
        
            $("#no_of_sms").val(sms);
        });
  </script>