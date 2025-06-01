@extends('layouts.app')

@section('title', __('lang_v1.sms_campaign'))

@section('content')
<!-- Main content -->

<section class="content">
    <div class="row">
        
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_campaign' )])
        
        
        
        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SmsSendController@submitSmsFileFinal'), 'method' =>
            'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ])
            !!}
            
        
        
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <strong>@lang('sms::lang.phone_nos'):</strong> {{sizeof($data['imported_data'])}}
                        </div>
                    </div>
                     <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('message_tags', __( 'sms::lang.message_tags' )) !!}
                            {!! Form::select('message_tags',$data['tags'], null, ['class' => 'form-control select2',
                            'id' => 'message_tags', 'placeholder' => __(
                            'lang_v1.please_select' )]);
                            !!}
                        </div>
                    </div>
                    <input type="hidden" name="data" value='{{json_encode($data)}}'>
                    <div class="col-md-12">
                        <div class="form-group">
                          <label for="note"> @lang( 'sms::lang.message' )</label>
                          {!! Form::textarea('message', null, ['class' => 'form-control','required', 'placeholder' => __(
                          'sms::lang.message' ), 'style' => 'width: 100%', 'rows' => 10]); !!}
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