
@extends('layouts.app')

@section('title', __('subscription::lang.sms_templates'))

@section('content')
<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'subscription::lang.sms_templates')])
            {!! Form::open(['url' =>
                action('\Modules\Subscription\Http\Controllers\SubscriptionSmsTemplateController@store'), 'method' =>
                'post', 'id' => 'subscription_form', 'enctype' => 'multipart/form-data' ]) !!}
             <div class="row">
                  <div class="col-md-6">
                      <div class="alert alert-warning">
                          <b>{business_name} {product_name} {amount} {expiry_date}</b>
                      </div>
                    <div class="form-group">
                      {!! Form::label('sms_body', __( 'subscription::lang.sms_body' )) !!} 
                      {!! Form::textarea('sms_body', !empty($templates) ? $templates->sms_body : null, ['class' => 'form-control sms_body',
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
                              'subscription::lang.days' )]); !!}
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
                              'subscription::lang.days' )]); !!}
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
                              'subscription::lang.days' )]); !!}
                            </div>
                          </div>
                      </div>
                      
                      <div class="row">
                          
                          <div class="col-sm-6">
                                <div class="checkbox">
                                    <h5>
                                        {!! Form::checkbox('days_4_status', 1, !empty($templates) ? $templates->days_4_status : null, [ 'class' => 'input-icheck-red ch_select','id' =>	'days_4_status']); !!} {{__('subscription::lang.reminder_4')}}
                                    </h5>
                                </div>
                            </div>
                          
                          <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::text('days_4',!empty($templates) ? $templates->days_4 : null, ['class' => 'form-control days_4',  'placeholder' => __(
                              'subscription::lang.days' )]); !!}
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
        </div>
    </div>
    <div class="modal fade subscription_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
   $(document).ready(function(){
       $('#days_1_status').trigger('change');
       $('#days_2_status').trigger('change');
       $('#days_3_status').trigger('change');
       $('#days_4_status').trigger('change');
       
       $('#days_1_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_1").prop('readonly', false);
                $(".days_1").prop('required', true);
            } else {
                $(".days_1").prop('readonly', true);
                $(".days_1").prop('required', false);
            }
        });
        
       $('#days_2_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_2").prop('readonly', false);
                $(".days_2").prop('required', true);
            } else {
                $(".days_2").prop('readonly', true);
                $(".days_2").prop('required', false);
            }
        });
        
       $('#days_3_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_3").prop('readonly', false);
                $(".days_3").prop('required', true);
            } else {
                $(".days_3").prop('readonly', true);
                $(".days_3").prop('required', false);
            }
        });
        
       $('#days_4_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_4").prop('readonly', false);
                $(".days_4").prop('required', true);
            } else {
                $(".days_4").prop('readonly', true);
                $(".days_4").prop('required', false);
            }
        });
        
       $('#days_1_status').trigger('change');
       $('#days_2_status').trigger('change');
       $('#days_3_status').trigger('change');
       $('#days_4_status').trigger('change');
        
   })
    
</script>
@endsection