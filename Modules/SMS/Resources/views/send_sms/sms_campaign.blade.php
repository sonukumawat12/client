@extends('layouts.app')

@section('title', __('lang_v1.sms_campaign'))

@section('content')
<!-- Main content -->



<section class="content">
    <div class="row">

        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_campaign' )])

        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SmsSendController@submitsmsCampaign'), 'method' => 'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ]) !!}

        <div class="row">
    <!-- LEFT COLUMN -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', __('sms::lang.campaign_name')) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('sms::lang.name'), 'id' => 'name']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('contacts', __('lang_v1.sender_names')) !!}
            <select name="contacts" class="form-control select2" required id="contacts">
                @if ($smsSettings["default_gateway"] == "hutch_sms")
                    <option value="{{ $smsSettings['hutch_mask'] }}">{{ $smsSettings["hutch_mask"] }}</option>
                @endif
                @if ($smsSettings["default_gateway"] == "utlimate_sms")
                    <option value="{{ $smsSettings['ultimate_sender_id'] }}">{{ $smsSettings["ultimate_sender_id"] }}</option>
                @endif
            </select>
        </div>

        <div class="form-group">
            {!! Form::label('sms_group', __('lang_v1.sms_groups')) !!}
            <select name="sms_group[]" class="form-control select2" required id="sms_group" multiple>
                @foreach($sms_group as $group)
                    <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                @endforeach
            </select>
        </div>

        @if($isCustomerGroupEnabled)
            <div class="form-group">
                {!! Form::label('customer_group', __('sms::lang.customer_group')) !!}
                {!! Form::select('customer_group[]', $allContacts, null, [
                    'class' => 'form-control select2',
                    'multiple' => true,
                    'required',
                    'placeholder' => __('lang_v1.please_select'),
                    'id' => 'customer_group'
                ]) !!}
            </div>

            <div class="form-group" id="contact-dropdown-container" style="display:none;">
                {!! Form::label('contact_id', __('contact.contact')) !!}
                <select name="contact_id[]" id="contact_id" class="form-control select2" multiple>
                    <option value="">@lang('lang_v1.please_select')</option>
                </select>
            </div>
        @endif

        <div class="form-group">
            <div id="selected_sms_groups" class="form-control" style="height: auto; min-height: 38px; display: none; color: #a73737">
                <!-- Selected SMS groups will be displayed here -->
            </div>
        </div>

        <input type="text" name="sms_group" id="res" style="display: none;">
        <div id="recipients" style="display: none;" name="recipients"></div>

        <div class="form-group">
            @if($smsNonDelivery)
                <label>{!! Form::label('dSpeed', __( 'sms::lang.dspeed' )) !!}</label><br>
                <input type="checkbox" class="i-check" value="1" name="dSpeed" id="dSpeed">
            @endif
        </div>

        <div class="form-group">
            {!! Form::label('schedule_campaign', __( 'sms::lang.schedule_campaign' )) !!}
            <input type="checkbox" class="i-check" value="1" name="schedule_campaign" id="schedule_campaign">
        </div>

        <div class="form-group schedule_div hide">
            {!! Form::label('send_time', __( 'sms::lang.send_time' )) !!}
            <input type="datetime-local" class="form-control schedule_field" name="send_time">
        </div>

        <div class="form-group schedule_div hide">
            {!! Form::label('frequency', __( 'sms::lang.frequency' )) !!}
            {!! Form::select('frequency', ['One Time' => 'One Time', 'Daily' => 'Daily', 'Monthly' => 'Monthly', 'Yearly' => 'Yearly'], null, [
                'class' => 'form-control schedule_field select2',
                'id' => 'frequency'
            ]) !!}
        </div>

        <div class="form-group schedule_div frequency_div hide">
            {!! Form::label('end_time', __( 'sms::lang.end_time' )) !!}
            <input type="datetime-local" class="form-control frequency_field" name="end_time">
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('message', __('sms::lang.message')) !!}
            <textarea placeholder="{{ __('sms::lang.message') }}" class="form-control" name="message" rows="15" id="message" maxlength="5000"></textarea>
        </div>

        <div class="form-group">
            <small class="text-primary text-uppercase">
                {{ __('sms::lang.remaining') }} : <span id="remaining">5000</span>
                ( <span class="text-success" id="charCount"> 0 </span> {{ __('sms::lang.characters') }} )
            </small>
            <br>
            <small class="text-primary text-uppercase">
                {{ __('sms::lang.message') }}(s) : <span id="messages">1</span>
                ({{ __('sms::lang.encoding') }} : <span class="text-success" id="encoding">GSM_7BIT</span>)
            </small>
        </div>
    </div>
</div>


        <div class="row">
            <div class="col-md-6">
                <button type="button" id="phoneMessagePreview" class="btn btn-info mr-1 mb-1"><i
                        data-feather="smartphone"></i> {{ __('sms::lang.preview') }}
                </button>
            </div>
            <!--<div class="col-md-6">-->
            <!--    <button type="submit" class="btn btn-primary pull-right">@lang( 'sms::lang.send' )</button>-->
            <!--</div> -->

            <div class="">
                <input type="hidden" value="plain" name="sms_type" id="sms_type">

                <button type="button" id="sendMessagePreview" class="btn btn-primary mt-1 mb-1">
                    <i data-feather="send"></i>
                    {{ __('sms::lang.send') }}
                </button>
            </div>
        </div>

        {!! Form::close() !!}

        @endcomponent
    </div>
</section>

<!-- Mobile Preview Modal -->
@include('sms::send_sms._mobilePreviewModal')

<!-- message preview Modal -->
@include('sms::send_sms._messagePreviewModal')
<!-- // Basic Vertical form layout section end -->

@endsection


@section('javascript')
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<script src="{{ asset('js/scripts/sms-counter.js') }}"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2();
    function getMemberCount(group) {
        console.log(group)
        var members = group.members ? group.members : '';
        members = members.replace(/[^a-zA-Z0-9\s,]/g, '');
        var memberList = members.split(',').filter(Boolean);
        return group.member_count;
    }


        // Function to update the display of selected groups
    function updateSelectedGroupsDisplay() {
        var totalMembersCount = 0;
        var totalMembersCount = 0;
        var tableRows = $('#sms_group').find('option:selected').map(function() {
        var groupId = $(this).val();
        var group = @json($sms_group).find(g => g.id == groupId);
        var mobileNumbersCount = getMemberCount(group);
        totalMembersCount += mobileNumbersCount;
        // var formattedMobileNumbersCount = mobileNumbersCount.toLocaleString();
        var formattedTotalMembersCount = totalMembersCount.toLocaleString();
        
         $("#msgRecepients").html(formattedTotalMembersCount);
    
        // Return the row data for the table
        return `<tr>
                    <td>${group.group_name}</td>
                    <td>${mobileNumbersCount}</td>
                    <td>${formattedTotalMembersCount}</td>
                </tr>`;
    }).get().join(''); // Join rows to form table content
    
        // Create the table structure with headers
        var tableHTML = `
            <table border="1" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="color: #a73737;">
                        <th>Selected Groups</th>
                        <th>Mobile Numbers</th>
                        <th>Total Numbers</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        `;
    
        // Display the table if there are selected groups
        if (tableRows) {
            $('#selected_sms_groups').html(tableHTML).show();
            
            // Store the comma-separated members and selected IDs
            var recipientsText = $('#sms_group').find('option:selected').map(function() {
                var groupId = $(this).val();
                var group = @json($sms_group).find(g => g.id == groupId);
                return group.members ? group.members.replace(/[^a-zA-Z0-9\s,]/g, '') : '';
            }).get().join(',');
    
            var recipientsId = $('#sms_group').find('option:selected').map(function() {
                return $(this).val();
            }).get().join(',');
    
            $('#recipients').html(recipientsText);
            $('#res').val(recipientsId);
        } else {
            $('#selected_sms_groups').hide();
            $('#recipients').html('');
        }
    }
    

        // Initialize the selected groups display on load
        // updateSelectedGroupsDisplay();

        // Update display when SMS group selection changes
        $('#sms_group').change(function() {
            updateSelectedGroupsDisplay();
        });
        
      $('#closeMessagePreview').click(function() {
            $('#messagePreview').modal('hide');
        });


        // Show/hide schedule fields based on schedule checkbox
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

        // Show/hide frequency fields based on frequency selection
        $('#frequency').change(function() {
            if ($(this).val() != 'One Time') {
                $('.frequency_div').removeClass('hide');
                $(".frequency_field").attr('required', 'required');
            } else {
                $('.frequency_div').addClass('hide');
                $(".frequency_field").removeAttr('required');
            }
        });

        $('#message').on('change keyup paste', function() {
            get_character();
        });

        $("#phoneMessagePreview").on("click", function() {
            const msg = $("#message").val();
            $("#messageto").html(msg);
            $('#phonePreview').attr("aria-hidden", "false").modal("show");
        });

        $("#sendMessagePreview").on("click", function() {
            get_recipients_count();
            let msgData = SmsCounter.count($get_msg.val(), true),
                senderId = $("#sender_id"),
                recipients = $('#recipients').html();
                message = $get_msg,
                msgCount = msgData.messages,
                msgLength = msgData.length,
                msg = $get_msg.val();
            $("#msgLength").html(msgLength);
            $("#msgCost").html(msgCount);
           
            $("#msg").html(msg);

            // validate fields
            if (get_recipients_count < 1 || message.val().length < 1) {
                toastr['warning']("{{ __('locale.auth.insert_required_fields') }}",
                    "{{ __('locale.labels.attention') }}", {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                        // rtl: isRtl
                    });
                return
            }
            $('#messagePreview').attr("aria-hidden", "false").modal("show");
        });

        $("#finalSend").on("click", function(e) {
            e.preventDefault();
            let form = $("form#sms_list_interest_form");
            
            // Get recipients from the hidden div
            let recipients = $('#recipients').html().trim();
            
            
            $(this).html($(this).data('loading-text'));
            // feather.replace();
            form.submit();
        });



        let $remaining = $('#remaining'),
            $char_count = $('#charCount'),
            $encoding = $('#encoding'),
            $get_msg = $("#message"),
            $messages = $('#messages'),
            firstInvalid = $('form').find('.is-invalid').eq(0),
            $get_recipients = $('#recipients').html(),
            number_of_recipients_ajax = 0,
            number_of_recipients_manual = 0;

        // Calculate number of recipients
        // get_recipients_count()

        //Calculate the message length
        // get_character()

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

    function isUnicode(text) {
        // Checks for any non-ASCII characters (Unicode)
        let pattern = /[^\x00-\x7F]/;
        return pattern.test(text);
    }

    function isArabic(text) {
        let pattern = /[\u0600-\u06FF\u0750-\u077F]/;
        return pattern.test(text);
    }

    function get_character() {
        if ($get_msg[0].value !== null) {
    
            let data = SmsCounter.count($get_msg[0].value, true);
            let messageContent = $get_msg[0].value;
    
            if (data.encoding === 'UTF16' || isUnicode(messageContent)) {
                $('#sms_type').val('unicode').trigger('change');
    
                // Set text direction based on whether it's Arabic or not
                if (isArabic(messageContent)) {
                    $get_msg.css('direction', 'rtl');
                } else {
                    $get_msg.css('direction', 'ltr');
                }
            } else {
                $('#sms_type').val('plain').trigger('change');
                $get_msg.css('direction', 'ltr');
            }
    
            $char_count.text(data.length);
            $remaining.text(data.remaining + ' / ' + data.per_message);
            $messages.text(data.messages);
            $encoding.text(data.encoding);
        }
    }

    function get_delimiter() { 
        return $('input[name=delimiter]:checked').val();
    }

    function get_recipients_count() {
        // Get the recipients text content
        let recipients_value = $('#recipients').html().trim();
        if (recipients_value) {
            let delimiter = ',';
    
            // Split based on the selected delimiter
            if (delimiter === ';') {
                number_of_recipients_manual = recipients_value.split(';').length;
            } else if (delimiter === ',') {
                number_of_recipients_manual = recipients_value.split(',').length;
            } else if (delimiter === '|') {
                number_of_recipients_manual = recipients_value.split('|').length;
            } else if (delimiter === 'tab') {
                number_of_recipients_manual = recipients_value.split('\t').length;
            } else if (delimiter === 'new_line') {
                number_of_recipients_manual = recipients_value.split('\n').length;
            } else {
                number_of_recipients_manual = 0;
            }
        } else {
            number_of_recipients_manual = 0;
        }
    
        let total = number_of_recipients_manual + Number(number_of_recipients_ajax);
        var formattedTotalMembersCount = total.toLocaleString();
        $('.number_of_recipients').text(formattedTotalMembersCount);
        return formattedTotalMembersCount;
    }
    $('#recipients').on('change keyup paste', get_recipients_count);
    
    $("input[name='delimiter']").change(function() {
        get_recipients_count();
    });






        //Make mobile preview time lively
        setInterval(function() {
            let date = new Date();
            let hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours()
            let minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()
            let seconds = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds()
            $('.top-section-time').html(
                hours + ":" + minutes + ":" + seconds
            );
        }, 500);


    });
</script>

<script>

$(document).ready(function () {
    $('#customer_group').on('change', function () {
        let selectedGroups = $(this).val(); // array of selected global_ids

        if (!selectedGroups || selectedGroups.length === 0) {
            $('#contact_id').empty().append('<option value="">@lang("lang_v1.please_select")</option>');
            $('#contact-dropdown-container').hide();
            return;
        }

        $.ajax({
            url: '/smsmodule/sms-campaign/get-contacts-by-group',
            method: 'GET',
            data: { global_ids: selectedGroups },
            success: function (data) {
                let options = '<option value="">@lang("lang_v1.please_select")</option>';
                $.each(data.contacts, function (index, contact) {
                    options += `<option value="${contact.id}">${contact.name}</option>`;
                });
                $('#contact_id').html(options);
                $('#contact-dropdown-container').show();
            }
        });
    });
});

</script>
@endsection