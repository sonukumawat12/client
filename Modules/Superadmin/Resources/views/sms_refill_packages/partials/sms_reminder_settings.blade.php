@php

    $body = "Dear {business_client_name},".PHP_EOL."Your current SMS Balance is {sms_balance}".PHP_EOL.".Please refill the account for the smooth functionality of the SMS System.";
@endphp


@component('components.widget', ['class' => 'box-primary', 'title' => __(
'subscription::lang.sms_templates')])
{!! Form::open(['url' =>
    action('\Modules\Superadmin\Http\Controllers\SmsReminderSettingController@store'), 'method' =>
    'post', 'id' => 'subscription_form', 'enctype' => 'multipart/form-data' ]) !!}
 <div class="row">
      <div class="col-md-6">
          <div class="alert alert-warning">
              <b>{business_client_name} {sms_balance}</b>
          </div>
        <div class="form-group">
          {!! Form::label('sms_body', __( 'subscription::lang.sms_body' )) !!} 
          {!! Form::textarea('sms_body', !empty($templates) ? $templates->sms_body : $body, ['class' => 'form-control sms_body',
          'required', 'placeholder' => __( 'subscription::lang.sms_body' )]); !!}
        </div>
      </div>
      
      <div class="col-md-6">
          <div class="row">
              
              <div class="col-sm-6">
                    <div class="checkbox">
                        <h5>
                            {!! Form::checkbox('days_1_status', 1, !empty($templates) ? $templates->days_1_status : null, [ 'class' => 'input-icheck-red ch_select','id' =>	'days_1_status']); !!} {{__('subscription::lang.reminder_1')}}
                        </h5>
                    </div>
                </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::text('days_1',!empty($templates) ? $templates->days_1 : null, ['class' => 'form-control days_1',  'placeholder' => __(
                  'superadmin::lang.sms_balance' )]); !!}
                </div>
              </div>
          </div>
          
          <div class="row">
              
              <div class="col-sm-6">
                    <div class="checkbox">
                        <h5>
                            {!! Form::checkbox('days_2_status', 1, !empty($templates) ? $templates->days_2_status : null, [ 'class' => 'input-icheck-red ch_select','id' =>	'days_2_status']); !!} {{__('subscription::lang.reminder_2')}}
                        </h5>
                    </div>
                </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::text('days_2',!empty($templates) ? $templates->days_2 : null, ['class' => 'form-control days_2', 'placeholder' => __(
                  'superadmin::lang.sms_balance' )]); !!}
                </div>
              </div>
          </div>
          
          
          <div class="row">
              
              <div class="col-sm-6">
                    <div class="checkbox">
                        <h5>
                            {!! Form::checkbox('days_3_status', 1, !empty($templates) ? $templates->days_3_status : null, [ 'class' => 'input-icheck-red ch_select','id' =>	'days_3_status']); !!} {{__('subscription::lang.reminder_3')}}
                        </h5>
                    </div>
                </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::text('days_3',!empty($templates) ? $templates->days_3 : null, ['class' => 'form-control days_3',  'placeholder' => __(
                  'superadmin::lang.sms_balance' )]); !!}
                </div>
              </div>
          </div>
          
          <div class="row">
              
              <div class="col-sm-6">
                    <div class="checkbox">
                        <h5>
                            <input type="hidden" name="days_4_status" value="1">
                            {!! Form::checkbox('days_default_status', 1,  1, [ 'class' => 'input-icheck-red ch_select','id' =>	'days_defaul_status', 'disabled']); !!} {{__('subscription::lang.reminder_4')}}
                        </h5>
                    </div>
                </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::text('days_4', 0, ['class' => 'form-control days_default',  'placeholder' => __(
                  'superadmin::lang.sms_balance' ),'readonly']); !!}
                </div>
              </div>
          </div>
          
          <button type="submit" class="btn btn-primary" id="save_leads_btn">
            @lang( 'messages.save' )
          </button>

      </div>
      
  </div>
  
{!! Form::close() !!}

@endcomponent
        