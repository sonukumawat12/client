@extends('layouts.app')

@section('title', __('lang_v1.sms_campaign'))

@section('content')
<!-- Main content -->

<section class="content">
    <div class="row">
        
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_campaign' )])
        
        
        
        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SmsSendController@submitSmsFile'), 'method' =>
            'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ])
            !!}
            
        
        
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/send_sms_template.csv') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                    
                    <div class="alert alert-danger col-sm-12" style="margin: 15px;">@lang('sms::lang.sms_from_file_helper')</div>
                </div>
        
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', __( 'sms::lang.name' )) !!}
                            {!! Form::text('name', null, ['class' => 'form-control','required', 'placeholder' => __(
                            'sms::lang.name' ),
                            'id' => 'name']);
                            !!}
                        </div>
                    </div>
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
                    </div>
                    
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                            {!! Form::file('file', ['accept'=> '.csv', 'required' => 'required']); !!}
                          </div>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('schedule_campaign', __( 'sms::lang.schedule_campaign' )) !!}
                            <input type="checkbox" class="i-check" value="1" name="schedule_campaign" id="schedule_campaign">
                        </div>
                    </div>
                    
                    <div class="col-md-6 schedule_div hide">
                        <div class="form-group">
                            {!! Form::label('send_time', __( 'sms::lang.send_time' )) !!}
                            <input type="datetime-local" class="form-control schedule_field" name="send_time">
                        </div>
                    </div>
                </div>
            </div>
           
            
            
        </div>
        <div class="row">
            
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
    $(".select2").select2();
    
    $('#schedule_campaign').change(function() {
        if ($(this).is(':checked')) {
            $('.schedule_div').removeClass('hide');
            $(".schedule_field").attr('required', 'required');
        } else {
            $('.schedule_div').addClass('hide');
            $(".schedule_field").removeAttr('required');
        }
        
        $('#frequency').trigger('change');
    });

    // Additional logic to show/hide the frequency_div based on frequency selection
    $('#frequency').change(function() {
        if ($(this).val() != 'One Time') {
            $('.frequency_div').removeClass('hide');
            $(".frequency_field").attr('required', 'required');
        } else {
            $('.frequency_div').addClass('hide');
            $(".frequency_field").removeAttr('required');
        }
    });
    
    $('#message_tags').change(function() {
        var tag = $(this).val();
        if (tag) {
            var messageField = $('textarea[name="message"]');
            var cursorPos = messageField.prop('selectionStart');
            var text = messageField.val();
            var textBefore = text.substring(0, cursorPos);
            var textAfter = text.substring(cursorPos, text.length);
            messageField.val(textBefore + tag + textAfter);
            
            // Clear the selected value in the dropdown
            $(this).val('').trigger('change');
        }
    });
})
</script>
@endsection