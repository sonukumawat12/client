@php

@endphp

<style>
    
    .bootstrap-tagsinput {
        transition: transform 0.3s ease, z-index 0s ease;
        transform-origin: center top; 
        overflow: hidden;
        white-space: normal;
        word-wrap: break-word;
        width: 100%;
    }
</style>


<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\SmsApiClientController@update',[$data->id]), 'method' => 'put', 'id' => 'edit_sms_api_clients_forms' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'superadmin::lang.sms_api_clients' )</h4>
      </div>
  
      <div class="modal-body">
          <div class="row">
              <div class="form-group col-md-4">
                  {!! Form::label('date', __( 'lang_v1.date' ) .":*") !!}
                  {!! Form::date('date', date('Y-m-d',strtotime($data->date)), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.date' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('name', __( 'superadmin::lang.name' ) .":*") !!}
                  {!! Form::text('name', $data->name, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.name' ),'required' ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('address', __( 'superadmin::lang.address' ) .":*") !!}
                  {!! Form::text('address', $data->address, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.address' ),'required' ]); !!}
              </div>
              
              <div class="clearfix"></div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('contact_mobile', __( 'superadmin::lang.contact_mobile' ) .":*") !!}
                  {!! Form::text('contact_mobile', $data->contact_mobile, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.contact_mobile' ),'required' ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('land_no', __( 'superadmin::lang.land_no' )) !!}
                  {!! Form::text('land_no', $data->land_no, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.land_no' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('contact_name', __( 'superadmin::lang.contact_name' )) !!}
                  {!! Form::text('contact_name', $data->contact_name, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.contact_name' ) ]); !!}
              </div>
              
              <div class="clearfix"></div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('api_key', __( 'superadmin::lang.api_key' ) .":*") !!}
                  {!! Form::text('api_key', $data->api_key, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.api_key' ),'readonly','required' ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('username', __( 'superadmin::lang.username' ) .":*") !!}
                  {!! Form::text('username', $data->username, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.username' ),'required' ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('password', __( 'superadmin::lang.password' ) .":*") !!}
                  {!! Form::text('password', $data->password, ['class' => 'form-control','placeholder' => __( 'superadmin::lang.password' ), 'readonly','required' ]); !!}
              </div>
              
              <div class="clearfix"></div>
              
              <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('default_gateway', __('lang_v1.default_gateway') . ':') !!}
                    {!! Form::select('default_gateway', ['ultimate_sms' => 'Ultimate SMS', 'hutch_sms' => 'Hutch SMS'],  $data->default_gateway, ['class' => 'form-control', 'id' => 'default_gateway']); !!}
                </div>
              </div>
              
              <div class="col-md-4 ultimate_sms @if($data->default_gateway != 'ultimate_sms') hide @endif">
                <div class="form-group">
                    {!! Form::label('ultimate_token', __('lang_v1.ultimate_token') . ':') !!}
                    {!! Form::text('ultimate_token',  $data->ultimate_token, ['class' => 'form-control', 'id' => 'ultimate_token']); !!}
                </div>
              </div>
              
                <div class="col-xs-4 hutch_sms @if($data->default_gateway != 'hutch_sms') hide @endif">
                    <div class="form-group">
                        {!! Form::label('hutch_username', __('lang_v1.hutch_username') . ':') !!}
                        {!! Form::text('hutch_username', $data->hutch_username, ['class' => 'form-control', 'id' => 'hutch_username']); !!}
                    </div>
                </div>
                <div class="col-xs-4 hutch_sms @if($data->default_gateway != 'hutch_sms') hide @endif">
                    <div class="form-group">
                        {!! Form::label('hutch_password', __('lang_v1.hutch_password') . ':') !!}
                        {!! Form::text('hutch_password', $data->hutch_password, ['class' => 'form-control', 'id' => 'hutch_password']); !!}
                    </div>
                </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('sender_names', __( 'superadmin::lang.sender_names_separated' ) .":*") !!}
                  {!! Form::text('sender_names', $data->sender_names, ['class' => 'form-control tagsinput','placeholder' => __( 'superadmin::lang.sender_names' ),'required' ]); !!}
              </div>
              
              
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
      $(".select2").select2();
      
      $(document).ready(function() {
        $('.tagsinput').tagsinput({
          allowDuplicates: true
        });
    });
    

  </script>
  