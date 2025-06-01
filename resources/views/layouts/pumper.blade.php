@inject('request', 'Illuminate\Http\Request')

@if($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit'))
@php
$pos_layout = true;
@endphp
@else
@php
$pos_layout = false;
@endphp
@endif
@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- google adsense -->
    <script data-ad-client="ca-pub-1123727429633739" async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{url($settings->uploadFileFicon)}}" />
    <script src="{{ asset('AdminLTE/plugins/jQuery/jquery-2.2.3.min.js?v=' . $asset_v) }}"></script>
    @include('layouts.partials.css')
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js?v=' . $asset_v) }}"></script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
    
    <script src="{{ asset('AdminLTE/plugins/select2/js/select2.full.min.js?v=' . $asset_v) }}"></script>
    <!-- CSS file -->
    
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js" integrity="sha512-SXJkO2QQrKk2amHckjns/RYjUIBCI34edl9yh0dzgw3scKu0q4Bo/dUr+sGHMUha0j9Q1Y7fJXJMaBi4xtyfDw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.css" integrity="sha512-X6069m1NoT+wlVHgkxeWv/W7YzlrJeUhobSzk4J09CWxlplhUzJbiJVvS9mX1GGVYf5LA3N9yQW5Tgnu9P4C7Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://kit.fontawesome.com/bb1c887317.js" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    @yield('css')

    <style>
        .sidebar-mini.sidebar-collapse .content-wrapper,
        .sidebar-mini.sidebar-collapse .main-footer,
        .sidebar-mini.sidebar-collapse .right-side {
            margin-left: 0px !important;
        }
    </style>

</head>

<body
    class="@if($pos_layout) hold-transition @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'blue'}}@endif sidebar-mini @endif">
  
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="@if(!$pos_layout) content-wrapper @endif" style="margin-left: 0px;">
            @php
            $business_id = session()->get('user.business_id');
            $business_details = App\Business::find($business_id);
            @endphp
            <!-- Add currency related field-->
            <input type="hidden" id="__code" value="{{session('currency')['code']}}">
            <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
            <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
            <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
            <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
            <input type="hidden" id="__precision" value="{{$business_details->currency_precision}}">
            <input type="hidden" id="__quantity_precision" value="{{$business_details->quantity_precision}}">
            <!-- End of currency related field-->

            @if (session('status'))
            <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
                data-msg="{{ session('status.msg') }}">
            @endif
            @yield('content')
            @if(config('constants.iraqi_selling_price_adjustment'))
            <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- This will be printed -->
            <section class="invoice print_section" id="receipt_section">
            </section>

        </div>
        @include('home.todays_profit_modal')
        <!-- /.content-wrapper -->


        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>

    </div>

    @include('layouts.partials.javascripts')
   
   
    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="stock_tranfer_notification_model">
    </div>
    
   <div class="modal fade" id="fullScreenModal" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Body -->
            <div class="modal-body">
                <p id="noteContent" class="text-center text-bold">@lang('petro::lang.can_fullscreen_text')</p>
                <div class="text-right">
                    <a href="#" class="btn btn-primary request-fullscreen">@lang('petro::lang.ok_proceed')</a>
                </div>
            </div>
        </div>
    </div>
</div>

    
    <script>
    
        function requestFullscreen(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { /* Firefox */
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { /* IE/Edge */
                element.msRequestFullscreen();
            }
        }
        
        function toggleFullscreen() {
                var doc = document.documentElement;
                var isFullscreen = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
        
                if (!isFullscreen) {
                    // Enter fullscreen mode
                    if (doc.requestFullscreen) {
                        doc.requestFullscreen();
                    } else if (doc.mozRequestFullScreen) { /* Firefox */
                        doc.mozRequestFullScreen();
                    } else if (doc.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                        doc.webkitRequestFullscreen();
                    } else if (doc.msRequestFullscreen) { /* IE/Edge */
                        doc.msRequestFullscreen();
                    }
                } else {
                    // Exit fullscreen mode
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) { /* Firefox */
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) { /* Chrome, Safari & Opera */
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) { /* IE/Edge */
                        document.msExitFullscreen();
                    }
                }
            }
        
        $(document).ready(function() {
            
            $('.toggle-fullscreen').on('click', function() {
                toggleFullscreen();
            });
            
            $('.request-fullscreen').on('click', function() {
                requestFullscreen(document.documentElement);
                $('#fullScreenModal').modal('hide');
            });
            
          });
          
          @if ((session('status.success') == false || session('status.success') == 0) && !empty(session('status.msg')))
                toastr.error('{{ session('status.msg') }}', 'Error');
            @endif
    
    </script>
    
    {{-- @if(auth()->user()->is_pump_operator == 1)
        @php
            $can_fullscreen = \Modules\Petro\Entities\PumpOperator::find(auth()->user()->pump_operator_id)->can_fullscreen ?? 0;
        @endphp
        
        @if($can_fullscreen == 0)
            <script>
                var doc = document.documentElement;
                var isFullscreen = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
        
                if (!isFullscreen) {
                    $('#fullScreenModal').modal({
                        backdrop: 'static', 
                        keyboard: false 
                    });
                }
            </script>
        @endif
    
    @endif --}}
  
</body>

</html>