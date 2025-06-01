@extends('layouts.app')
@section('title', 'Help Guide')
@section('helpguide_content')
{{-- <!DOCTYPE html> --}}
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}

<head>
    {{-- <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <title>{{ isset($pageTitle) ? $pageTitle : setting('app_name') }}</title> --}}

    {{-- <link href="{{ asset(setting('favicon')) }}" rel="icon">
    <link href="{{ asset(setting('favicon')) }}" rel="apple-touch-icon"> --}}

    <link href="{{ asset('build/common/css/libs.css?v='.config('vars.asset_version')) }}" rel="stylesheet">
    <link href="{{ asset('build/common/css/main.css?v='.config('vars.asset_version')) }}" rel="stylesheet">
    <link href="{{ asset('build/ui/customer/css/customer.css?v='.config('vars.asset_version')) }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    @if(isRTL(app()->getLocale()))
        <link href="{{ asset('build/common/css/rtl.css?v='.config('vars.asset_version')) }}" rel="stylesheet">
    @endif

    @yield('head_assets')
    @yield('style')
    @yield('script')

    {!! customStyle('customer_area') !!}

    <script>
        const BASE_URL = "{{ url('') }}/";
        const MYACCOUNT_URL = "{{ route('my_account') }}/";
        const API_URL = "{{ url('/api/v1') }}/";
    </script>
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
            padding-left: 10px !important;
            padding-right: 10px !important;
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
        }
        #sidebarFilter {
            font-size: 14px !important;
        }
        .main-content {
        padding-top: 0rem !important;
        }
        .page-content {
        /* left: var(--sidebar-width); */
        left: 10px !important;
        }
        .h6, h6 {
            font-size: 1.85rem !important;
        }
        .py-0 {
            font-size: 2.15rem !important;
        }
        .page-header {
            margin-top: 0px !important;
            height: 60px !important;
        }
        .searchBar {
            position: relative !important;
        }
        .is-open {
            position: relative !important;
            margin-top: 24.74rem;
        }
        .w-100 {
            width: 200% !important;
        }
        #app-header {
            margin-top: -80px;
        }
        .navbar-nav {
            float: none;
        }
        .justify-content-lg-start {
            margin-top: 60px;
        }
        .btn-primary {
            background-color: #2596be;
            background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
            z-index: 1000;
        }
        .btn-success {
            background-color: #2596be;
            background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
        }
        .main-content {
            background: white;
        }
        .dropdown-menu{
            font-size: 12px;
        }
        .mx-3 {
            font-size: 18px;
        }
        .btn-outline-primary {
            --bs-btn-color: #2596be;
            --bs-btn-border-color: #2596be;
            --bs-btn-hover-bg: #2596be;
            --bs-btn-hover-border-color: #2596be;
            --bs-btn-active-bg: #2596be;
            --bs-btn-active-border-color: #2596be;
            --bs-btn-disabled-color: #2596be;
            --bs-btn-disabled-border-color: #2596be;
            font-size: 18px !important;
        }
        .vs__selected {
            background-color: #2596be !important;
            border: 1px solid #2596be !important;
        }
        .btn-sm{
            height: calc(25.5px* 1.25) !important;
            padding: 5px;
        }
        .btn-outline-secondary{
            height: calc(25.5px* 1.30) !important;
            width: calc(25.5px* 1.30) !important;
            padding: 6px !important;
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
    <div id="app" class="flex-shrink-0">
        @include('helpguide::my_account.layouts.header')

        <div class="container py-2">

            @section('page-heading')
                @isset($pageTitle)
                <div class="page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            {{ $pageTitle }}
                        </div>
                    </div>
                </div>
            @endisset

            @if(isDemo())
            <div class="alert alert-info" role="alert">
                This is a working Demo version, Some features has been disabled
            </div>
            @endif

            @show
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            @if (session('danger'))
            <div class="alert alert-danger" role="alert">
                {{ session('danger') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @yield('content')
        </div>

    </div>

    {{-- @include('helpguide::my_account.layouts.footer') --}}

    <script src="{{ asset('build/ui/customer/js/customer.js?v='.config('vars.asset_version')) }}" defer></script>
    <script src="{{ route('my_account.lang') }}?v={{config('vars.asset_version')}}" ></script>

    @yield('script_footer')

</body>

{{-- </html> --}}
@endsection