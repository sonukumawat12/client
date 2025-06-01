@inject('request', 'Illuminate\Http\Request')
@php
    $asset_vapps = filemtime(public_path('js/app.js'));
     $asset_vpre = filemtime(public_path('js/prepayment.js'));
@endphp
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('plugins/jquery-ui/jquery-ui.min.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="{{ asset('v2/js/popper.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('v2/js/bootstrap.min.js') }} "></script>
<!-- Bootstrap 3.3.6 -->
<script type="text/javascript" src="{{ asset('bootstrap/js/bootstrap.min.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js" integrity="sha512-SXJkO2QQrKk2amHckjns/RYjUIBCI34edl9yh0dzgw3scKu0q4Bo/dUr+sGHMUha0j9Q1Y7fJXJMaBi4xtyfDw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/select2/js/select2.full.min.js?v=' . $asset_v) }}"></script>
@if ($request->segment(1) != "helpguide" && $request->segment(2) != "helpguide")
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
@endif

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
<script type="text/javascript" src="https://kit.fontawesome.com/bb1c887317.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/decimal.js/10.3.1/decimal.min.js"></script>
<script type="text/javascript" src="{{ asset('v2/js/vendor/modernizr-2.8.3.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('v2/js/owl.carousel.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('v2/js/metisMenu.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('v2/js/jquery.slimscroll.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('v2/js/jquery.slicknav.min.js') }} "></script>

<!-- iCheck -->
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/iCheck/icheck.min.js?v=' . $asset_v) }}"></script>
<!-- jQuery Step -->
<script type="text/javascript" src="{{ asset('plugins/jquery.steps/jquery.steps.min.js?v=' . $asset_v) }}"></script>
<!-- Select2 -->

<style>
    .feild-box {
        border: 1px solid #8080803b;
        margin-top: 10px;
        padding: 10px;
    }
    .custom_date_p-0 {
        padding: 0px 5px !important;
    }
    .field-inline-block {
        display: inline-flex;
    }
    .l-date {
        padding: 0px;
        margin: 0px;
        font-size: 10px;
        font-weight: 500;
    }
    .custom_date_date-field {
        margin-right: 2px;
        padding: 0px 3px;
        text-align: center !important;
        height: 54px;  /* Doubled the height */
        width: 80px;   /* Doubled the width */
        border-color: #aaa !important;
    }
    .custom_date_date-field:focus {
        border-color: #2596be !important;
        outline: none;
    }
    .line-separator {
        border-top: 1px solid #ddd; /* Creates a line */
        margin: 20px 0; /* Adds space around the line */
    }
    @media (min-width: 768px) {
        .modal-content1{
            width: 470px;
        }
    }
</style>
<input type="hidden" id="target_custom_date_input">
<div class="modal fade custom_date_typing_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content1">
            <style>
                .select2 {
                    width: 100% !important;
                }
            </style>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Select Custom Date Range: Date / Month / Year</h4>
            </div>

            <div class="modal-body">
                <div class="col-md-12">
                    @php
                        $today = \Carbon\Carbon::now();
                        // Get today's year, month, and day
                        $year = $today->year;
                        $month = $today->month;
                        $day = $today->day;
                        // Split year into individual digits
                        $yearDigits = str_split($year);
                        // Split month and day into two digits
                        $monthDigits = str_split(str_pad($month, 2, '0', STR_PAD_LEFT));
                        $dayDigits = str_split(str_pad($day, 2, '0', STR_PAD_LEFT));
                    @endphp

                    <fieldset>
                        <div class="row">
                            <div class="col-sm-12 custom_date_p-0" style="color:#2596be; font-weight: bold;">From</div>
                            <div class="col-md-12 p-0 custom_date_p-0">
                                <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                    <label class="text-center">Date</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_from_date1" value="{{ $dayDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_from_date2" value="{{ $dayDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>
                                <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                    <label class="text-center">Month</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_from_month1" value="{{ $monthDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_from_month2" value="{{ $monthDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>
                                <div class="col-md-4 p-0 custom_date_p-0">
                                    <label class="text-center">Year</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year1" value="{{ $yearDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year2" value="{{ $yearDigits[1] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year3" value="{{ $yearDigits[2] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year4" value="{{ $yearDigits[3] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>
                            </div>
                            <!-- Line Separator -->
                            <div class="col-sm-12 line-separator"></div>
                            <div class="col-sm-12 custom_date_p-0" style="color:#2596be; font-weight: bold;">To</div>
                            <div class="col-md-12 p-0 custom_date_p-0">
                                <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                    <label class="text-center">Date</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_to_date1" value="{{ $dayDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_to_date2" value="{{ $dayDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>

                                <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                    <label class="text-center">Month</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_to_month1" value="{{ $monthDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_to_month2" value="{{ $monthDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>

                                <div class="col-md-4 p-0 custom_date_p-0">
                                    <label class="text-center">Year</label>
                                    <div class="field-inline-block w-100 text-center">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_to_year1" value="{{ $yearDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_to_year2" value="{{ $yearDigits[1] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_to_year3" value="{{ $yearDigits[2] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                        <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_to_year4" value="{{ $yearDigits[3] ?? '' }}" style="width: 40px; display: inline-block;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin: 20px 0;"></div>
                        </div>
                    </fieldset>

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="custom_date_apply_button">Apply</button>
            </div>
        </div> 
    </div>
</div>

<script>
    document.querySelectorAll('.custom_date_date-field').forEach((input, index) => {
        input.addEventListener('input', function() {
            if (this.value.length >= this.maxLength) {
                // Move focus to the next input
                const nextInput = document.querySelectorAll('.custom_date_date-field')[index + 1];
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();  // Highlight the next input's value
                }
            }
        });
    });
    $('.custom_date_typing_modal').on('shown.bs.modal', function() {
        $('#custom_date_from_date1').focus();
        $('#custom_date_from_date1').select();
    });
</script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".custom_start_end_date_range", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "Y-m-d",
        allowInput: true,
    });
</script>

<script type="text/javascript">
    base_path = "{{url('/')}}";
    
    $(document).ready( function(){
        $('.page-container').addClass('sbar_collapsed');
    });
    
    $('#sidebar_collapser').click(function() {
        $('.page-container').toggleClass('sbar_collapsed');
      });
     $(document).ready(function() {
      $('ul.nav li').click(function() {
        $('ul.nav li').removeClass('active'); // remove the active class from all li elements
        $(this).addClass('active'); // add the active class to the clicked li element
      });
    });
</script>


<!-- Add language file for select2 -->
@if(file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) .
'.js')))
<script
    src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}">
</script>
@endif

<!-- bootstrap toggle -->
<!-- <script type="text/javascript" src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> -->
 <script type="text/javascript" src="{{ asset('v2/js/bootstrap-toggle.min.js') }} "></script>
<!-- bootstrap datepicker -->
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.js?v=' . $asset_v) }}"></script>
<!-- DataTables -->
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/DataTables/datatables.min.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/pdfmake.min.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/vfs_fonts.js?v=' . $asset_v) }}"></script>

<!-- jQuery Validator -->
<script type="text/javascript" src="{{ asset('js/jquery-validation-1.16.0/dist/jquery.validate.min.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery-validation-1.16.0/dist/additional-methods.min.js?v=' . $asset_v) }}"></script>
@php
$validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale') ) . '.js';
@endphp
@if(file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
<script type="text/javascript" src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}">
</script>
@endif

<!-- Toastr -->
<script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js?v=' . $asset_v) }}"></script>
<!-- Bootstrap file input -->
<script type="text/javascript" src="{{ asset('plugins/bootstrap-fileinput/fileinput.min.js?v=' . $asset_v) }}"></script>
<!--accounting js-->
<script type="text/javascript" src="{{ asset('plugins/accounting.min.js?v=' . $asset_v) }}"></script>

<!--<script type="text/javascript" src="{{ asset('AdminLTE/plugins/daterangepicker/moment.min.js?v=' . $asset_v) }}"></script>-->

<script type="text/javascript" src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js?v=' . $asset_v) }}"></script>

<!--<script type="text/javascript" src="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.js?v=' . $asset_v) }}"></script>-->

<script type="text/javascript" src="{{ asset('AdminLTE/plugins/ckeditor/ckeditor.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="{{ asset('plugins/sweetalert/sweetalert.min.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="{{ asset('plugins/bootstrap-tour/bootstrap-tour.min.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="{{ asset('plugins/printThis.js?v=' . $asset_v) }}"></script>

<script type="text/javascript" src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script type="text/javascript" src="{{ asset('plugins/screenfull.min.js?v=' . $asset_v) }}"></script>



<script type="text/javascript" src=" {{ asset('plugins/moment-timezone-with-data.min.js?v=' . $asset_v) }}"></script>
@if (($request->segment(1) == 'petro' && $request->segment(2) == 'pump-operator-payments' && $request->segment(3) == 'othersale') || ($request->segment(1) == 'petro' && $request->segment(2) == 'pump-operator-payments' && $request->segment(3) == 'create'))
    {{-- removed since it causes .(). on prints --}}
@else
    <script type="text/javascript" src="{{ asset('js/offline.js') }}"></script>
@endif
{{-- <script type="module"  src="{{ asset('js/colorpicker/Colorpicker.js') }}"></script> --}}
<script type="text/javascript" src="{{asset('js/pickr.min.js') }}"></script>
@php
$business_date_format = session('business.date_format', config('constants.default_date_format'));
$datepicker_date_format = str_replace('d', 'dd', $business_date_format);
$datepicker_date_format = str_replace('m', 'mm', $datepicker_date_format);
$datepicker_date_format = str_replace('Y', 'yyyy', $datepicker_date_format);

$moment_date_format = str_replace('d', 'DD', $business_date_format);
$moment_date_format = str_replace('m', 'MM', $moment_date_format);
$moment_date_format = str_replace('Y', 'YYYY', $moment_date_format);

$business_time_format = session('business.time_format');
$moment_time_format = 'HH:mm';
if($business_time_format == 12){
$moment_time_format = 'hh:mm A';
}

$common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

$default_datatable_page_entries = !empty($common_settings['default_datatable_page_entries']) ?
$common_settings['default_datatable_page_entries'] : 25;

if(\Auth::user()){
    $user_id = \Auth::user()->id;
}

@endphp
<script>
    moment.tz.setDefault('{{ Session::get("business.time_zone") }}');
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        @if(config('app.debug') == false)
            $.fn.dataTable.ext.errMode = 'throw';
        @endif
    });
    
    var financial_year = {
    	start: moment('{{ Session::get("financial_year.start") }}'),
    	end: moment('{{ Session::get("financial_year.end") }}'),
    }
    @if(file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    //Default setting for select2
    $.fn.select2.defaults.set("language", "{{session()->get('user.language', config('app.locale'))}}");
    @endif

    var datepicker_date_format = @if(!empty($datepicker_date_format))  "{{$datepicker_date_format}}" @else "mm/dd/yyyy" @endif;
    var moment_date_format = @if(!empty($moment_date_format))  "{{$moment_date_format}}" @else "YYYY-MM-DD" @endif;
    var moment_time_format = @if(!empty($moment_time_format))  "{{$moment_time_format}}" @else "HH:mm" @endif;

    var app_locale = "{{session()->get('user.language', config('app.locale'))}}";
    var non_utf8_languages = [
        @foreach(config('constants.non_utf8_languages') as $const)
        "{{$const}}",
        @endforeach
    ];

    var __default_datatable_page_entries = "{{$default_datatable_page_entries}}";
</script>

<!-- Scripts -->
@if ($request->segment(1) != 'login')
<script type="text/javascript" src="{{ asset('js/AdminLTE-app.js?v=' . $asset_v) }}"></script>
@endif


@if(file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
<script type="text/javascript" src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}">
</script>
@else
<script type="text/javascript" src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif
{{-- @if(request()->segment(count(request()->segments())) != 'login') --}}
<script type="text/javascript" src="{{ asset('AdminLTE/plugins/pace/pace.min.js?v=' . $asset_v) }}"></script>
{{-- @endif --}}


<script type="text/javascript" src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/functions.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('js/app.js?v=' . $asset_vapps) }}"></script>
<script type="text/javascript" src="{{ asset('js/help-tour.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('plugins/calculator/calculator.js?v=' . $asset_v) }}"></script>
<script type="text/javascript" src="{{ asset('js/documents_and_note.js?v=' . $asset_v) }}"></script>


<script type="text/javascript" src="https://js.pusher.com/6.0/pusher.min.js">
</script>
@auth
<script>
    Pusher.logToConsole = true;
    
    var pusher = new Pusher('60edfb46c1105e962a07', {
        cluster: 'eu'
    });
    
    
    function saveCache(){
        let urls_to_cache = [], 
            dom_scripts = document.getElementsByTagName('script'), 
            cache_slugs = ['jquery', 'bootstrap', 'fontawesome', 'daterangepicker'];
        
        for( var x = 0; x < dom_scripts.length; x++ ){
            var src = dom_scripts[x].src;
            if( src != undefined && src.trim().length ){
                src = src.trim();
                
                for( var k = 0; k < cache_slugs.length; k++ ){
                    if( src.indexOf(cache_slugs[k]) !== -1 ){
                        if( cache_slugs[k].match(src) ){
                            urls_to_cache.push( src );
                        }
                        break;
                    }
                }
            }
        }
        
        self.addEventListener("install", event => {
            event.waitUntil(
                caches.open("pwa-assets")
                    .then(cache => {
                        return cache.addAll( urls_to_cache );
                    })
            )
        });
    }
    saveCache();
    
    
    function serveCache(){
        self.addEventListener("fetch", event => {
            event.respondWith(
                caches.match(event.request)
                    .then(cachedResponse => {
                        // It can update the cache to serve updated content on the next request
                        return cachedResponse || fetch(event.request);
                    })
            )
        });
    }
    serveCache();
    
    
    var channel = pusher.subscribe('customer-limit-approval-channel.{{auth()->user()->id}}');
    channel.bind('App\\Events\\CustomerLimitApproval', function(data) {
        $('ul#notifications_list').prepend(`
        <li class="">
        <a class="request-approval-link" href="/customer-limit-approval/get-approval-details/${data.customer_id}/${data.requested_user}">
            <i class=""></i> Request for over sell limit approval <br> Customer: ${data.customer_name}   <br>
            <small>${data.created_at}</small>
        </a>
        </li>
        `);

        let notification_count = $('.notifications_count').text();

        if(notification_count === ''){
            console.log('asdf');
            notification_count = 1;
        }else{
            notification_count = parseInt(notification_count) + 1;
        }
        $('.notifications_count').text(notification_count);
        toastr.info('New request received');
        pusher.disconnect();
    });

    $(document).on('click', 'a.request-approval-link', function(e){
        e.preventDefault();
        $.ajax({
            method: 'get',
            url: $(this).attr('href'),
            data: {  },
            success: function(result) {
                $('.limit_modal').empty().append(result);
                $('.limit_modal').modal('show');
            },
        });
    });

    $(document).on('click', '#limit_form_btn',function(e){
        e.preventDefault();

        $.ajax({
            method: 'post',
            url: $('#limit_form').attr('action'),
            data: { over_limit_percentage : $('#over_limit_percentage').val() , requested_user : $('#requested_user').val() },
            success: function(result) {
                if(result.success === 1){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
                $('.limit_modal').modal('hide');
            },
        });
    });


    var channel_apprved = pusher.subscribe('customer-limit-approved.{{auth()->user()->id}}');
    channel_apprved.bind('App\\Events\\CustomerLimitApproved', function(data) {
        toastr.success(`Sell over limit approved upto ${data.limit}% for customer ${data.customer_name}`);
        pusher.disconnect();
    });

    var stock_transfer_channel = pusher.subscribe('stock-transfer-request-complete.{{auth()->user()->id}}');
    stock_transfer_channel.bind('App\\Events\\StockTransferRequestComplete', function(data) {
        $('ul#notifications_list').prepend(`
        <li class="">
        <a class="stock-transfer-request-link" href="/stock-transfers-request/get-notification-poup/${data.transfer_request.id}">
            <i class=""></i> Request for  <br> Product: ${data.product.name}    <br>
            <i>Status: ${data.transfer_request.status} </i>
            <small>${data.transfer_request.updated_at}</small>
        </a>
        </li>
        `);

        let notification_count = $('.notifications_count').text();

        if(notification_count === ''){
            console.log('asdf');
            notification_count = 1;
        }else{
            notification_count = parseInt(notification_count) + 1;
        }
        
        $('.notifications_count').text(notification_count);
        toastr.info('New notification received');
        pusher.disconnect();
    });

    $(document).on('click', 'a.stock-transfer-request-link', function(e){
        e.preventDefault();
        $.ajax({
            method: 'get',
            url: $(this).attr('href'),
            data: {  },
            success: function(result) {
                $('.stock_tranfer_notification_model').empty().append(result);
            },
        });
    });

    
    $('.clear_cache_btn').click(function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        $.ajax({
            method: 'get',
            url: url,
            data: {  },
            success: function(result) {
                if(result.success){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
            },
        });  
    });
    
    $(document).on('click', 'a#login_payroll', function(e){
        $('.loading_gif').show();
        var user_id = <?php echo $user_id; ?>;
        e.preventDefault();
        $.ajax({
            method: 'post',
            url: '/home/login_payroll',
            data: { user_id },
            success: function(result) {
                $('.loading_gif').hide();
                window.open(result.login_url, "_blank")
            },
        });
    });
</script>


<div class="modal fade limit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endauth

<!--modified by iftekhar-->
@yield('javascript')
@stack('javascript')

@if(Module::has('Essentials'))
    @includeIf('essentials::layouts.partials.footer_part')
@endif