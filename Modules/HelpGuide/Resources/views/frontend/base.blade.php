@extends('layouts.app')
@section('title', 'Help Guide')
@section('content')
{{-- <!DOCTYPE html>
<html lang="en"> --}}

<head>
  {{-- <meta charset="utf-8"> --}}

  {{-- <title>Help Guide {{ isset($title) ? $title : ( setting('site_title') ? setting('site_title') : 'Knowledge base' ) }}</title> --}}

  {{-- <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"> --}}
  {{-- <meta content="{{ setting('site_keywords') }}" name="keywords">
  <meta content="{{ setting('site_description') }}" name="description"> --}}

  @if(isset($canonical))
  <link rel="canonical" href="{{ $canonical }}" />
  @endif

  {{-- <link href="{{ asset(setting('favicon')) }}" rel="icon">
  <link href="{{ asset(setting('favicon')) }}" rel="apple-touch-icon"> --}}

  {{-- <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,700" rel="stylesheet"> --}}

  <link href="{{asset('build/common/css/main.css?v='.config('vars.asset_version'))}}" rel="stylesheet">
  <link href="{{asset('build/common/css/icons.css?v='.config('vars.asset_version'))}}" rel="stylesheet">
  <link href="{{asset('build/frontend/css/frontend.css?v='.config('vars.asset_version'))}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <script>
    const BASE_URL = "{{ url('/') }}/helpguide/";
    const MYACCOUNT_URL = "{{ route('my_account') }}/";
  </script>

  @yield('styles')

  {!! customStyle('frontend') !!}

  {!! setting('custom_css') !!}

  <style>
    .btn {
      font-size: 1.15rem !important;
    }
    .h4, h4 {
      font-size: 1.85rem !important;
    }
    .h2, h2 {
      font-size: 2.4rem !important;
    }
    .border-0 {
      font-size: 2rem !important;
      height: calc(1.8em + .75rem + 10px) !important;
    }
    body {
      font-size: 1.8rem !important;
    }
    .h1, h1 {
      font-size: 2.6rem !important;
    }
    .form-control {
      font-size: 18px !important;
    }
    .mx-5 {
      margin-left: 1rem !important;
    }
    .btn-submit-ticket {
      font-size: 1.75rem !important;
      background-color: #2596be;
      background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
    }
    .packages_btn {
      padding: 8px;
    }
    .clock_in_btn {
      padding: 5px;
      background: white;
      padding-left: 10px;
      padding-right: 10px;
      font-weight: bold;
    }
    .clock_out_btn {
      padding: 5px;
      background: white;
      padding-left: 10px;
      padding-right: 10px;
      font-weight: bold;
    }
    .section-heading {
      background-color: #2596be !important;
        /* background-color: #4e73df;
        background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%); */
    }
    #sidebarFilter {
      font-size: 14px !important;
    }
    .main-content {
      background: white;
    }
    .dropdown-menu{
        font-size: 12px;
    }
    .btn-primary {
        background-color: #2596be;
        font-size: 15px !important;
        color: white;
        background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
        z-index: 1000;
    }
    .btn-success {
        background-color: #2596be;
        font-size: 15px !important;
        color: white;
        background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
    }
    .btn-outline-success {
        font-size: 15px !important;
    }
    .btn-outline-danger {
        font-size: 15px !important;
    }
    #lang-list {
      font-size: 18px !important;
      z-index: 1000;
    }
  </style>
</head>

<body>
  <div id="app">
    
    @if ( ! setting('frontend_enabled', true))
    <div class="alert bg-warning position-fixed top-0 w-100 p-1 text-capitalize text-center" style="z-index: 999">{{ __('Frontend is disabled !') }}</div>
    @endif

    @section('header')
    <header id="header" class="" style="z-index: 900;">
      <div class="container p-1">
        {{-- <div class="logo float-start">
          <a href="{{ route('frontend') }}" class="scrollto">
            <img src="{{ asset(setting('app_logo')) }}" class="img-fluid">
          </a>
        </div> --}}
        <nav class="main-nav float-end">
          <ul>
            @if(isDemo())
            <li>
              <a class="btn btn-success text-white btn-sm me-2" href="#">
                {{__('Buy NOW')}}
              </a>
            </li>
            @endif

            {{-- @if(defaultSetting('ticket_allowed', true))
            <li><a class="btn btn-submit-ticket btn-sm me-2 p-1 px-2" href="{{ route("my_account") }}">
            {{__('Submit a ticket')}}</a></li>
            @endif --}}

            @if( count(availableLanguages()) > 1 )
            <li>
              <div class="dropdown">
                <button class="dropdown-toggle btn btn-light dropdown-toggle px-2 p-1" type="button" id="lang-list" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ getLocaleName(App()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="lang-list">
                  @foreach (availableLanguages() as $lk => $lv)
                  <li><a href="{{ url('helpguide/' . $lk) }}" class="dropdown-item">
                    {{ $lv }}
                  </a></li>
                  @endforeach
                </ul>
              </div>
            </li>
            @endif

            {{-- @if (auth()->guest())
            <li><a href="{{ route('login') }}">{{__('Sign in') }}</a></li>
            @else
            <li class="nav-item">
              @can('superadmin')
                <a class="nav-link nav-link p-1" href="{{ route('dashboard') }}">
              @endcan
              @cannot('superadmin')
                <a class="nav-link nav-link p-1" href="{{ route('my_account') }}">
              @endcannot
                <img class="img-profile rounded-circle me-2" width="24" src="{{ Auth::user()->avatar() }}">
                <span class="me-2 text-gray-600 small d-none d-lg-inline-block">Dashboard</span>
              </a>
            </li>
            @endif --}}
          </ul>
        </nav>

      </div>
    </header>
    @show


    @section('sub-header')
    <div class="section-heading">
      <div class="container">
        <h1 class="mb-3 text-white text-center">{{__('How can we help you today?')}}</h1>
        <search-bar style="max-width: 500px; margin: auto">
          <div class="preloader"></div>
        </search-bar>
      </div>
    </div>
    @show


    <main id="main">
      @yield('main_content')
    </main>

    <footer id="footer" class="bg-white p-3 text-center">
      <div class="container">
        <div class="row">
          <div class="col-12">

            <div class="copyright d-inline">
              &copy; {{__('Copyright')}} <strong>{{ setting('app_name') }}</strong>. {{__('All Rights Reserved')}}
            </div>

            @if( count(availableLanguages()) > 1 )
            
              <div class="dropdown dropup d-inline">
                <button class="dropdown-toggle btn btn-light dropdown-toggle px-2 p-1" type="button" id="lang-list" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ getLocaleName(App()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="lang-list">
                  @foreach (availableLanguages() as $lk => $lv)
                  <li><a href="{{ url($lk) }}" class="dropdown-item">
                    {{ $lv }}
                  </a></li>
                  @endforeach
                </ul>
              </div>
            
            @endif

          </div>
        </div>

      </div>
    </footer>

    <a href="#" class="back-to-top"><i class="bi bi-arrow-up-short"></i></a>
  </div>

  @section('footer_js')
  <script src="{{ route('frontend.lang') }}?v={{config('vars.asset_version')}}"></script>
  <script src="{{ asset('build/frontend/js/frontend.js?v='.config('vars.asset_version')) }}"></script>
  {!! setting('custom_js') !!}
  @show

</body>

{{-- </html> --}}
@endsection