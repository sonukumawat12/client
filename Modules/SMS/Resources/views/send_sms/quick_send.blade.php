@extends('layouts.app')

@section('title', __('sms::lang.sms_campaign'))

@section('content')
<!-- Main content -->
<style>
    
    .bootstrap-tagsinput {
       word-wrap: break-word;
        width: 100%;
        font-size: 16px !important;
        height: 50vh;
    }
</style>
<section class="content">
    <div class="row">
        
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_quick_send' )])
        
        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SmsSendController@submitQuickSend'), 'method' =>
            'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ])
            !!}
        <div class="row">
           
            
            <div class="col-md-6">
                 <div class="form-group">
                    {!! Form::label('contacts', __('lang_v1.sender_names')) !!}
                    <select name="contacts" class="form-control select2" required id="contacts">
                        @if ($smsSettings["default_gateway"] == "hutch_sms")
                        <option value="{{ $smsSettings['hutch_mask'] }}">
                            {{ $smsSettings["hutch_mask"] }} <!-- Only show the group name in the dropdown -->
                        </option>
                        @endif
                        @if ($smsSettings["default_gateway"] == "utlimate_sms")
                        <option value="{{ $smsSettings['ultimate_sender_id'] }}">
                            {{ $smsSettings["ultimate_sender_id"] }} <!-- Only show the group name in the dropdown -->
                        </option>
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    {!! Form::label('phone_nos', __( 'sms::lang.phone_nos_separate_comma' )) !!}
                    {!! Form::text('phone_nos', null, ['class' => 'form-control tagsinput','required', 'placeholder' => __(
                    'sms::lang.phone_nos' ),
                    'id' => 'phone_nos']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                  <label for="note"> @lang( 'sms::lang.message' )</label>
                  {!! Form::textarea('message', null, ['class' => 'form-control','required', 'placeholder' => __(
                  'sms::lang.message' ), 'style' => 'width: 100%', 'rows' => 15]); !!}
                </div>
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right">@lang( 'sms::lang.send' )</button>
            </div>
      
        </div>
        
        {!! Form::close() !!}
        
        @endcomponent
    </div>
@endsection

@section('javascript')
<script>
$(document).ready(function(){
    $('#phone_nos').tagsinput({
      allowDuplicates: false
    });
})
</script>
@endsection