@extends('layouts.app')
@section('title', __('messages.settings'))

@section('css')
<style>
    .sms-tag {
        display: inline-block;
        background-color: #f8d7da; /* light red */
        color: #721c24; /* darker red text */
        padding: 2px 8px;
        margin: 3px 4px 3px 0;
        border-radius: 12px;
        font-size: 0.875rem;
        font-family: monospace;
    }
     .sms-tabs {
        display: flex;
        margin-bottom: 1em;
        cursor: pointer;
    }
    .sms-tab {
        padding: 10px 20px;
        background: #ddd;
        margin-right: 5px;
        border-radius: 4px 4px 0 0;
    }
    .sms-tab.active {
        background: #17a2b8;
        color: white;
        font-weight: bold;
    }
    .sms-tab-content {
        display: none;
        border: 1px solid #ccc;
        padding: 1em;
        border-radius: 0 4px 4px 4px;
    }
    .sms-tab-content.active {
        display: block;
    }
</style>
@endsection

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Custom Tabs -->
        @component('components.widget', ['class' => 'box-primary', 'title' => __('messages.settings') . ':'])
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="{{ request()->has('page') ? '' : 'active' }}">
                        <a href="#cn_1" data-toggle="tab" aria-expanded="true">
                            @lang('hms::lang.booking_prefix')
                        </a>
                    </li>
                    <li class="">
                        <a href="#cn_2" data-toggle="tab" aria-expanded="true">
                            @lang('lang_v1.customer_notifications')
                        </a>
                    </li>
                    <li class="">
                        <a href="#cn_3" data-toggle="tab" aria-expanded="true">
                            @lang('hms::lang.print_pdf')
                        </a>
                    </li>
                    <li class="">
                        <a href="#cn_4" data-toggle="tab" aria-expanded="true">
                            Import Rooms
                        </a>
                    </li>
                    <li class="{{ request()->has('page') ? 'active' : '' }}">
                        <a href="#cn_5" data-toggle="tab" aria-expanded="true">
                            SMS Settings
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane {{ request()->has('page') ? '' : 'active' }}" id="cn_1">
                        <div class="row">
                            <div class="box-body">
                                {!! Form::open([
                                    'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'store']),
                                    'method' => 'post',
                                    'id' => 'hms_setting',
                                    'files' => true,
                                ]) !!}
                                @php
                                    $settings = json_decode($busines->hms_settings);
                                @endphp
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('booking_prefix', __('hms::lang.booking_prefix') . '*') !!}
                                        {!! Form::text('booking_prefix', $settings->prefix ?? null, [
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => __('hms::lang.booking_prefix'),
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    {!! Form::submit(__('messages.submit'), ['class' => 'tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg']) !!}
                                </div>
                
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="cn_2">
                        <h3>@lang('hms::lang.new_booking')</h3>
                        <div class="row">
                            {!! Form::open([
                                'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'store_email_template']),
                                'method' => 'post',
                            ]) !!}
                            <div class="col-md-12">
                                <strong>@lang('lang_v1.available_tags'):</strong>
                                <p class="help-block">
                                    {{ implode(', ', $tags) }}
                                </p>
                            </div>
                            <div class="col-md-12 mt-10">
                                <div class="form-group">
                                    {!! Form::label('subject', __('lang_v1.email_subject') . ':') !!}
                                    {!! Form::text('subject', empty($template->subject) ? null : $template->subject, [
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.email_subject'),
                                        'id' => 'subject',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('cc', 'CC:') !!}
                                    {!! Form::email('cc', empty($template->cc) ? null : $template->cc, [
                                        'class' => 'form-control',
                                        'placeholder' => 'CC',
                                        'id' => 'cc',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bcc', 'BCC:') !!}
                                    {!! Form::email('bcc', empty($template->bcc) ? null : $template->bcc, [
                                        'class' => 'form-control',
                                        'placeholder' => 'BCC',
                                        'id' => 'bcc',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('email_body', __('lang_v1.email_body') . ':') !!}
                                    {!! Form::textarea('email_body', empty($template->email_body) ? null : $template->email_body, [
                                        'class' => 'form-control ckeditor',
                                        'placeholder' => __('lang_v1.email_body'),
                                        'id' => 'email_body',
                                        'rows' => 6,
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12 mt-15">
                                <label class="checkbox-inline">
                                    {!! Form::checkbox('auto_send', 1, empty($template->auto_send) ? null : $template->auto_send, [
                                        'class' => 'input-icheck',
                                    ]) !!} @lang('lang_v1.autosend_email')
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="tw-dw-btn tw-dw-btn-error tw-text-white tw-dw-btn-lg">@lang('messages.save')</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="tab-pane" id="cn_3">
                            <div class="row">
                                <div class="box-body">
                                    {!! Form::open([
                                        'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'post_pdf']),
                                        'method' => 'post',
                                        'id' => 'post_pdf',
                                        'files' => true,
                                    ]) !!}
                                    @php
                                        
                                        $settings = json_decode($busines->hms_settings);
                                    @endphp
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('footer_text', __('hms::lang.footer_text')) !!}
                                            {!! Form::textarea('footer_text', $settings->booking_pdf->footer_text ?? null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('hms::lang.footer_text'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        {!! Form::submit(__('messages.submit'), ['class' => 'tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg']) !!}
                                    </div>
                    
                                    {!! Form::close() !!}
                                </div>
                            </div>
                    </div>
                    <div class="tab-pane" id="cn_4">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::open([
                                    'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'importRooms']),
                                    'method' => 'post',
                                    'files' => true,
                                ]) !!}
                                    <div class="form-group">
                                        <label for="csv_file">Upload Excel</label>
                                        <input type="file" name="csv_file" class="form-control" required accept=".xlsx, .xls, .csv">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Import</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane {{ request()->has('page') ? 'active' : '' }}" id="cn_5">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::open([
                                    'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'saveHmsSmsSettings']),
                                    'method' => 'post',
                                    'id' => 'sms_settings_form'
                                ]) !!}
                                
                                <h4>SMS Settings</h4>
                                <hr>

                                {{-- Country Code Settings --}}
                                @php
                                    $allCountries = $settings->all_countries ?? false;
                                    $countryCodes = $settings->applicable_country_codes ?? '';
                                @endphp

                                <div class="form-group">
                                    <label for="all_countries">
                                        <input type="checkbox" id="all_countries" name="all_countries" value="1" {{ $allCountries ? 'checked' : '' }}>
                                        All Countries
                                    </label>
                                </div>

                                <div class="form-group" id="country_codes_group">
                                    <label for="country_codes">Applicable Country Codes</label>
                                    <input type="text" id="country_codes" name="country_codes" class="form-control" placeholder="Enter country codes separated by comma (e.g., +91,+1,+44)" value="{{ old('country_codes', $countryCodes) }}">
                                </div>


                                <hr>

                                {{-- SMS Templates Configuration --}}
                                <h5>SMS Templates</h5>
                                

                                @php
                                    $events = [
                                        'room_booked' => 'When the room is booked',
                                        'booking_confirmed' => 'When the room booking is confirmed',
                                        'booking_extended' => 'When the room booking period is extended',
                                        'booking_cancelled' => 'When the room booking is cancelled',
                                        'payment_done' => 'When the payment is done',
                                        'departure_reminder' => 'Departure date reminder SMS',
                                        'coupon_code' => 'Coupon Code Number for next visit',
                                    ];
                                
                                    $tags = [
                                        'booking_no', 'room_no', 'client_name', 'status', 'arrival_time',
                                        'departure_date', 'duration', 'amount', 'amount_paid', 'amount_due',
                                        'extended_till', 'extra_amount', 'coupon_code'
                                    ];
                                
                                    $saved_templates = $settings->sms_templates ?? [];
                                    $saved_thresholds = $settings->visit_threshold_coupon_code ?? null;
                                    $enabled_sms = (array) ($settings->enable_sms ?? []);
                                    $eventChunks = array_chunk($events, 3, true);
                                @endphp

                                <div class="sms-tabs" id="sms-tab-buttons">
                                    @foreach($eventChunks as $index => $chunk)
                                        <div class="sms-tab {{ $index === 0 ? 'active' : '' }}" data-tab="sms-tab-{{ $index }}">Tab {{ $index + 1 }}</div>
                                    @endforeach
                                </div>
                                
                                @if(!empty($eventChunks) && count($eventChunks) > 0)
                                    @foreach($eventChunks as $index => $chunk)
                                        <div class="sms-tab-content {{ $index === 0 ? 'active' : '' }}" id="sms-tab-{{ $index }}">
                                            <div class="row">
                                                @foreach($chunk as $key => $label)
                                                    <div style="width: 30%; margin-right: 3%; display: inline-block; vertical-align: top;">
                                                        <div style="padding: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1); border-radius: 4px;">
                                                            <div style="background-color: #17a2b8; color: white; text-align: center; padding: 5px; border-radius: 4px; margin-bottom: 10px;">
                                                                {{ $label }}
                                                            </div>
                                
                                                            <label>
                                                                <input type="checkbox" name="enable_sms[{{ $key }}]" value="1"
                                                                    {{ old("enable_sms.$key", $enabled_sms[$key] ?? false) ? 'checked' : '' }}>
                                                                Send SMS
                                                            </label>
                                
                                                            <div style="margin-top: 10px;">
                                                                <small>Available tags:</small><br>
                                                                @if(!empty($tags))
                                                                    @foreach($tags as $tag)
                                                                        <span class="sms-tag">{{ '{' . $tag . '}' }}</span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted">No tags available</span>
                                                                @endif
                                                            </div>
                                
                                                            <textarea 
                                                                name="sms_{{ $key }}" 
                                                                rows="6"
                                                                style="width: 100%; max-width: 400px; height: 150px; max-height: 200px; margin-top: 10px;" 
                                                                placeholder="Enter SMS template">{{ old('sms_' . $key, $saved_templates->$key ?? '') }}</textarea>
                                
                                                            @if($key === 'coupon_code')
                                                                <label for="visit_threshold_{{ $key }}" style="display: block; margin-top: 10px;">Visit Count Threshold</label>
                                                                <input type="number" name="visit_threshold_{{ $key }}" min="1"
                                                                       value="{{ old('visit_threshold_' . $key, $saved_thresholds ?? 1) }}"
                                                                       style="width: 100%;" placeholder="Enter number of visits before sending SMS">
                                                            @endif
                                
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        No SMS Events found.
                                    </div>
                                @endif

                                
                                

                                {{-- Business ID as hidden input (pass this dynamically) --}}
                                <input type="hidden" name="business_id" value="{{ $business_id ?? 1 }}">

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Save SMS Settings</button>
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        @endcomponent
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea#email_body',
        });
        tinymce.init({
            selector: 'textarea#footer_text',
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#all_countries').change(function() {
                if ($(this).is(':checked')) {
                    $('#country_codes').prop('disabled', true);
                } else {
                    $('#country_codes').prop('disabled', false);
                }
            }).change(); // trigger on load
        });
    </script>
    <script>
        document.querySelectorAll('.sms-tab').forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
    
                document.querySelectorAll('.sms-tab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.sms-tab-content').forEach(content => content.classList.remove('active'));
    
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
@endsection
