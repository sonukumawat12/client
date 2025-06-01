@php
    use App\NotificationTemplate;
    $general_notifications = NotificationTemplate::generalNotifications();
    $customer_notifications = NotificationTemplate::customerNotifications();
    $supplier_notifications = NotificationTemplate::supplierNotifications();
    
    $notification_numbers = !empty($sms_settings['notification_parameters']) ? json_decode($sms_settings['notification_parameters'],true) : [];;
    
@endphp


<link href="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<style>
    .label-info {
        background-color: #8F3A84;
    }
</style>
<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('default_gateway', __('lang_v1.default_gateway') . ':') !!}
                {!! Form::select('sms_settings[default_gateway]', ['direct' => 'Direct', 'utlimate_sms' => 'Ultimate SMS', 'hutch_sms' => 'Hutch SMS'], !empty($sms_settings['default_gateway']) ? $sms_settings['default_gateway'] : null, ['class' => 'form-control', 'id' => 'default_gateway']); !!}
            </div>
        </div>
        <hr>
        
        
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('msg_phone_nos', __('lang_v1.msg_phone_nos') . ':') !!} <small>(@lang('lang_v1.separate_comma') )</small><br>
                {!! Form::text('sms_settings[msg_phone_nos]', !empty($sms_settings['msg_phone_nos']) ? $sms_settings['msg_phone_nos']: null, ['style' => 'width: 100% !important;','class' => 'form-control', 'id' => 'msg_phone_nos','data-role' => "tagsinput"]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
    <hr>
        
    </div>
    
    <div class="row ultimate_sms @if(!empty($sms_settings['default_gateway']) && $sms_settings['default_gateway'] != 'utlimate') hide @endif ">
        <div class="col-xs-12"> <strong><h5>{{ __('lang_v1.ultimate_sms_details') }}</h5></strong></div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('ultimate_token', __('lang_v1.ultimate_token') . ':') !!}
                {!! Form::text('sms_settings[ultimate_token]', !empty($sms_settings['ultimate_token']) ? $sms_settings['ultimate_token'] : null, ['class' => 'form-control', 'id' => 'ultimate_token']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('ultimate_sender_id', __('lang_v1.ultimate_sender_id') . ':') !!}
                {!! Form::text('sms_settings[ultimate_sender_id]', !empty($sms_settings['ultimate_sender_id']) ? $sms_settings['ultimate_sender_id'] : null, ['class' => 'form-control', 'id' => 'ultimate_sender_id']); !!}
            </div>
        </div>
        <hr>
    </div>
    
    
    
    <div class="row hutch_sms @if(!empty($sms_settings['default_gateway']) && $sms_settings['default_gateway'] != 'hutch_sms') hide @endif">
        <div class="col-xs-12"> <strong><h5>{{ __('lang_v1.hutch_sms_details') }}</h5></strong></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('hutch_username', __('lang_v1.hutch_username') . ':') !!}
                {!! Form::text('sms_settings[hutch_username]', !empty($sms_settings['hutch_username']) ? $sms_settings['hutch_username'] : null, ['class' => 'form-control', 'id' => 'hutch_username']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('hutch_password', __('lang_v1.hutch_password') . ':') !!}
                {!! Form::text('sms_settings[hutch_password]', !empty($sms_settings['hutch_password']) ? $sms_settings['hutch_password'] : null, ['class' => 'form-control', 'id' => 'hutch_password']); !!}
            </div>
        </div>
        
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('hutch_mask', __('lang_v1.hutch_mask') . ':') !!}
                {!! Form::text('sms_settings[hutch_mask]', !empty($sms_settings['hutch_mask']) ? $sms_settings['hutch_mask'] : null, ['class' => 'form-control', 'id' => 'hutch_mask']); !!}
            </div>
        </div>
        <hr>
        
    </div>
    
    
    
    <div class="row direct  @if(!empty($sms_settings['default_gateway']) && $sms_settings['default_gateway'] != 'direct') hide @endif">
        <div class="col-xs-3">
            <div class="form-group">
            	{!! Form::label('sms_settings_url', 'URL:') !!}
            	{!! Form::text('sms_settings[url]', $sms_settings['url'], ['class' => 'form-control','placeholder' => 'URL', 'id' => 'sms_settings_url']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('send_to_param_name', __('lang_v1.send_to_param_name') . ':') !!}
                {!! Form::text('sms_settings[send_to_param_name]', $sms_settings['send_to_param_name'], ['class' => 'form-control','placeholder' => __('lang_v1.send_to_param_name'), 'id' => 'send_to_param_name']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('msg_param_name', __('lang_v1.msg_param_name') . ':') !!}
                {!! Form::text('sms_settings[msg_param_name]', $sms_settings['msg_param_name'], ['class' => 'form-control','placeholder' => __('lang_v1.msg_param_name'), 'id' => 'msg_param_name']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('request_method', __('lang_v1.request_method') . ':') !!}
                {!! Form::select('sms_settings[request_method]', ['get' => 'GET', 'post' => 'POST'], $sms_settings['request_method'], ['class' => 'form-control', 'id' => 'request_method']); !!}
            </div>
        </div>
        
        
        <div class="col-md-12">
        <table class="table table-bordered table-striped" style="width: 100%" id="numbers_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.name')</th>
                    <th>@lang('lang_v1.phone')</th>
                    <th>@lang('lang_v1.general_notifications')</th>
                    <th>@lang('lang_v1.customer_notifications')</th>
                    <th>@lang('lang_v1.supplier_notifications')</th>
                    <th>*</th>
                </tr>
            </thead>
            
            <tbody>
                @if(empty($notification_numbers))
                <tr>
                    <td>
                        {!! Form::text('phone_name[]', null, ['class' => 'form-control notification_fields']); !!}
                    </td>
                    
                    <td>
                        {!! Form::text('phone_number[]', null, ['class' => 'form-control', 'required']); !!}
                    </td>
                    <td class="text-left">
                        @foreach($general_notifications as $key => $notification)
                            {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                        @endforeach
                    </td>
                    
                    <td class="text-left">
                        @foreach($customer_notifications as $key => $notification)
                            {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                        @endforeach
                    </td>
                    
                    <td class="text-left">
                        @foreach($supplier_notifications as $key => $notification)
                            {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                        @endforeach
                    </td>

                    <td>
                        <button type="button" id="add_number_row" class="btn btn-success">+</button>
                    </td>
                </tr>
                @else
                
                    @php $count = 0; @endphp
                
                    @foreach($notification_numbers as $no)
                        <tr>
                            <td>
                            {!! Form::text('phone_name[]', !empty($no['phone_name']) ? $no['phone_name'] : null, ['class' => 'form-control notification_fields']); !!}
                            </td>
                            
                            <td>
                                {!! Form::text('phone_number[]', $no['phone_number'], ['class' => 'form-control', 'required']); !!}
                            </td>
                            
                            <td class="text-left">
                                @foreach($general_notifications as $key => $notification)
                                    {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, !empty($no['notifications'][$key]) ? $no['notifications'][$key] : false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                                @endforeach
                            </td>
                            
                            <td class="text-left">
                                @foreach($customer_notifications as $key => $notification)
                                    {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, !empty($no['notifications'][$key]) ? $no['notifications'][$key] : false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                                @endforeach
                            </td>
                            
                            <td class="text-left">
                                @foreach($supplier_notifications as $key => $notification)
                                    {{$notification['name']}} {!! Form::checkbox($key . '[]', 1, !empty($no['notifications'][$key]) ? $no['notifications'][$key] : false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}<br>
                                @endforeach
                            </td>
        
                            <td>
                                @if($count == 0)
                                    <button type="button" id="add_number_row" class="btn btn-success">+</button>
                                @else
                                    <button type="button" class="btn btn-danger remove-number-row">-</button>
                                @endif
                                
                            </td>
                        </tr>
                        @php $count++; @endphp
                    @endforeach
                
                @endif
                
            </tbody>
            
        </table>
        
        <input type="hidden" id="notification_parameters" value="{{!empty($sms_settings['notification_parameters']) ? $sms_settings['notification_parameters'] : null}}" name="sms_settings[notification_parameters]">
        
    </div>
    
    
        
        <div class="clearfix"></div>
        <hr>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key1', __('lang_v1.sms_settings_param_key', ['number' => 1]) . ':') !!}
                {!! Form::text('sms_settings[param_1]', $sms_settings['param_1'], ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 1]), 'id' => 'sms_settings_param_key1']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val1', __('lang_v1.sms_settings_param_val', ['number' => 1]) . ':') !!}
                {!! Form::text('sms_settings[param_val_1]', $sms_settings['param_val_1'], ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 1]), 'id' => 'sms_settings_param_val1' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key2', __('lang_v1.sms_settings_param_key', ['number' => 2]) . ':') !!}
                {!! Form::text('sms_settings[param_2]', $sms_settings['param_2'], ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 2]), 'id' => 'sms_settings_param_key2']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val2', __('lang_v1.sms_settings_param_val', ['number' => 2]) . ':') !!}
                {!! Form::text('sms_settings[param_val_2]', $sms_settings['param_val_2'], ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 2]), 'id' => 'sms_settings_param_val2' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key3', __('lang_v1.sms_settings_param_key', ['number' => 3]) . ':') !!}
                {!! Form::text('sms_settings[param_3]', $sms_settings['param_3'], ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 3]), 'id' => 'sms_settings_param_key3']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val3', __('lang_v1.sms_settings_param_val', ['number' => 3]) . ':') !!}
                {!! Form::text('sms_settings[param_val_3]', $sms_settings['param_val_3'], ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 3]), 'id' => 'sms_settings_param_val3' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key4', __('lang_v1.sms_settings_param_key', ['number' => 4]) . ':') !!}
                {!! Form::text('sms_settings[param_4]', $sms_settings['param_4'], ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 4]), 'id' => 'sms_settings_param_key4']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val4', __('lang_v1.sms_settings_param_val', ['number' => 4]) . ':') !!}
                {!! Form::text('sms_settings[param_val_4]', $sms_settings['param_val_4'], ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 4]), 'id' => 'sms_settings_param_val4' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key5', __('lang_v1.sms_settings_param_key', ['number' => 5]) . ':') !!}
                {!! Form::text('sms_settings[param_5]', $sms_settings['param_5'], ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 5]), 'id' => 'sms_settings_param_key5']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val5', __('lang_v1.sms_settings_param_val', ['number' => 5]) . ':') !!}
                {!! Form::text('sms_settings[param_val_5]', $sms_settings['param_val_5'], ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 5]), 'id' => 'sms_settings_param_val5' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key6', __('lang_v1.sms_settings_param_key', ['number' => 6]) . ':') !!}
                {!! Form::text('sms_settings[param_6]', !empty($sms_settings['param_6']) ? $sms_settings['param_6'] : null, ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 6]), 'id' => 'sms_settings_param_key6']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val6', __('lang_v1.sms_settings_param_val', ['number' => 6]) . ':') !!}
                {!! Form::text('sms_settings[param_val_6]', !empty($sms_settings['param_val_6']) ? $sms_settings['param_val_6'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 6]), 'id' => 'sms_settings_param_val6' ]); !!}
            </div>
        </div>
         <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key7', __('lang_v1.sms_settings_param_key', ['number' => 7]) . ':') !!}
                {!! Form::text('sms_settings[param_7]', !empty($sms_settings['param_7']) ? $sms_settings['param_7'] : null, ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 7]), 'id' => 'sms_settings_param_key7']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val7', __('lang_v1.sms_settings_param_val', ['number' => 7]) . ':') !!}
                {!! Form::text('sms_settings[param_val_7]', !empty($sms_settings['param_val_7']) ? $sms_settings['param_val_7'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 7]), 'id' => 'sms_settings_param_val7' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key8', __('lang_v1.sms_settings_param_key', ['number' => 8]) . ':') !!}
                {!! Form::text('sms_settings[param_8]', !empty($sms_settings['param_8']) ? $sms_settings['param_8'] : null, ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 8]), 'id' => 'sms_settings_param_key8']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val8', __('lang_v1.sms_settings_param_val', ['number' => 8]) . ':') !!}
                {!! Form::text('sms_settings[param_val_8]', !empty($sms_settings['param_val_8']) ? $sms_settings['param_val_8'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 8]), 'id' => 'sms_settings_param_val8' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key9', __('lang_v1.sms_settings_param_key', ['number' => 9]) . ':') !!}
                {!! Form::text('sms_settings[param_9]', !empty($sms_settings['param_9']) ? $sms_settings['param_9'] : null, ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 9]), 'id' => 'sms_settings_param_key9']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val9', __('lang_v1.sms_settings_param_val', ['number' => 9]) . ':') !!}
                {!! Form::text('sms_settings[param_val_9]', !empty($sms_settings['param_val_9']) ? $sms_settings['param_val_9'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 9]), 'id' => 'sms_settings_param_val9' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_key10', __('lang_v1.sms_settings_param_key', ['number' => 10]) . ':') !!}
                {!! Form::text('sms_settings[param_10]', !empty($sms_settings['param_10']) ? $sms_settings['param_10'] : null, ['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 10]), 'id' => 'sms_settings_param_key10']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_param_val10', __('lang_v1.sms_settings_param_val', ['number' => 10]) . ':') !!}
                {!! Form::text('sms_settings[param_val_10]', !empty($sms_settings['param_val_10']) ? $sms_settings['param_val_10'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 10]), 'id' => 'sms_settings_param_val10' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_header_1', __('lang_v1.sms_settings_header_val', ['number' => 1]) . ':') !!}
                {!! Form::text('sms_settings[header_1]', !empty($sms_settings['header_1']) ? $sms_settings['header_1'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 1]), 'id' => 'sms_settings_header_1' ]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_header_2', __('lang_v1.sms_settings_header_val', ['number' => 2]) . ':') !!}
                {!! Form::text('sms_settings[header_1]', !empty($sms_settings['header_2']) ? $sms_settings['header_2'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 2]), 'id' => 'sms_settings_header_2' ]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sms_settings_header_3', __('lang_v1.sms_settings_header_val', ['number' => 3]) . ':') !!}
                {!! Form::text('sms_settings[header_3]', !empty($sms_settings['header_3']) ? $sms_settings['header_3'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 3]), 'id' => 'sms_settings_header_3' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                    {!! Form::text('test_number', null, ['class' => 'form-control','placeholder' => __('lang_v1.test_number'), 'id' => 'test_number']); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-success pull-right" id="test_sms_btn">@lang('lang_v1.test_sms_configuration')</button>
                    </span>
                </div>
            </div>
        </div>

    </div>
    
</div>
<script>
    // Add row when add_number_row button is clicked
    $('#numbers_table').on('click', '#add_number_row', function() {
        var lastRow = $('#numbers_table tbody tr:last');
        var newRow = lastRow.clone(); // Clone the last row
        newRow.find('input[type="text"]').val(''); // Clear the input value
        lastRow.after(newRow); // Append the new row after the last row

        // Add remove button to the new row
        var removeButton = $('<button>', {
            'type': 'button',
            'class': 'btn btn-danger remove-number-row',
            'text': '-'
        });
        newRow.find('td:last').html(removeButton); // Add the remove button to the last cell
    });

    // Remove row when remove-number-row button is clicked
    $('#numbers_table').on('click', '.remove-number-row', function() {
        $(this).closest('tr').remove(); // Remove the row
    });
    
    $(document).on('change','#default_gateway', function(){
        
        if($(this).val() == 'utlimate_sms'){
            $(".direct").addClass('hide');
            $(".hutch_sms").addClass('hide');
            $(".ultimate_sms").removeClass('hide');
        }else if($(this).val() == 'hutch_sms'){
            $(".direct").addClass('hide');
            $(".hutch_sms").removeClass('hide');
            $(".ultimate_sms").addClass('hide');
        }else{
            $(".direct").removeClass('hide');
            $(".hutch_sms").addClass('hide');
            $(".ultimate_sms").addClass('hide');
        }
    })
    
     $(document).on('change', '.toggler', function() {
        var formData = [];
        
        $('#numbers_table tbody tr').each(function() {
            var phoneNumber = $(this).find('input[name="phone_number[]"]').val(); // Get phone number value
            var phoneName = $(this).find('input[name="phone_name[]"]').val(); // Get phone number value
            var checkboxes = $(this).find('.toggler'); // Get checkboxes
            var rowValues = {
              'phone_number': phoneNumber,
              'notifications': {} ,
              'phone_name' : phoneName
            };
            
            checkboxes.each(function() {
              var checkboxName = $(this).attr('name').replace('[]', ''); // Get the checkbox name
              var checkboxValue = $(this).is(':checked') ? 1 : 0; // Determine checkbox value (1 if checked, 0 if not)
              rowValues['notifications'][checkboxName] = checkboxValue; // Add checkbox name and value to rowValues object
            });
            
            formData.push(rowValues); // Add rowValues to formData array
          });
          
        $("#notification_parameters").val(JSON.stringify(formData));
    });
</script>