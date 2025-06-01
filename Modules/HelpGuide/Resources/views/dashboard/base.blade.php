@extends('layouts.app')
@section('title', 'Help Guide')
@section('helpguide_content')
{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}

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

    <link href="{{ asset('build/ui/dashboard/css/dashboard.css?v='.config('vars.asset_version')) }}" rel="stylesheet">

    @if(isRTL(app()->getLocale()))
        <link href="{{ asset('build/common/css/rtl.css?v='.config('vars.asset_version')) }}" rel="stylesheet">
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    @yield('head_assets')
    @yield('style')
    @yield('script')
    <script>
        const BASE_URL = "{{ url('/') }}/";
        const MYACCOUNT_URL = "{{ route('dashboard') }}/";
        const API_URL = "{{ url('/api/v1/') }}/";
        const ADMIN_API_URL = "{{ route('dashboard') }}/api/v1/";
        const USER = { user_permissions: ["create_ticket","edit_ticket","update_ticket","view_ticket","view_any_ticket","close_ticket","delete_ticket","delete_any_ticket","permanently_delete_ticket","manage_tickets","reassign_ticket","update_any_ticket","reply_ticket","create_ticket_reply","create_any_ticket_reply","update_ticket_reply","delete_ticket_reply","delete_any_ticket_reply","update_any_ticket_reply","manage_categories","create_category","edit_category","delete_category","view_category","view_any_category","delete_any_category","update_any_category","create_user","update_user","delete_user","view_user","viewany_user","permanently_delete_user","manage_customers","create_customer","update_customer","manage_employees","create_employee","update_employee","assign_role","create_role","edit_role","delete_role","view_role","permanently_delete_role","assign_permissions","manage_acl","manage_articles","create_article","update_article","delete_article","permanently_delete_article","unpublish_article","publish_article","view_any_article","delete_any_article","update_any_article","create_saved_reply","view_saved_reply","edit_saved_reply","delete_saved_reply","upload_module","list_modules","manage_modules","statistics_view","statistics_view_any","add_reply_signature","view_error_logs","delete_error_log","update_settings","view_settings","update_application","view_customer_purchase","update_customer_purchase","admin_only"] }
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
            font-size: 14px !important;
        }
        .mx-5 {
            margin-left: 1rem !important;
        }
        .btn-submit-ticket {
            font-size: 1.75rem !important;
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
            width: 100% !important;
        }
        .form-floating>.form-control-plaintext~label, .form-floating>.form-control:focus~label, .form-floating>.form-control:not(:placeholder-shown)~label, .form-floating>.form-select~label {
            transform: scale(.55) translateY(-.5rem) translateX(.15rem);
        }
        .form-floating>.form-control, .form-floating>.form-control-plaintext, .form-floating>.form-select {
            height: calc(3rem + 10px);
            font-size: 1.55rem;
        }
        .clear_cache_btn{
            padding: 8px;
        }
        .searchIcon {
            font-size: 1.6rem;
            height: 2.5rem;
            line-height: 2.5rem;
        }
        #header-add-new{
            font-size: 18px !important;
        }
        .main-content {
            background: white;
        }
        .page-content {
            background: white;
        }
        .notResult{
            font-size: 18px;
        }
        .btn-link{
            font-size: 18px !important;
        }
        .dropdown-menu-end{
            font-size: 18px !important;
        }
        .empty-state{
            font-size: 18px !important;
        }
        .page-title{
            font-size: 18px !important;
        }
        .btn-outline-secondary{
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
        .list-group-item{
            font-size: 18px !important;
        }
        .text-capitalize{
            font-size: 18px !important;
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
        .btn-primary {
            background-color: #2596be;
            background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
            z-index: 1000;
        }
        .btn-success {
            background-color: #2596be;
            background-image: linear-gradient(180deg, #2596be 10%, #054a63 100%);
        }
        .vs__selected {
            background-color: #2596be !important;
            border: 1px solid #2596be !important;
        }
        .badge {
            background-color: #62d5ff;
        }
        .nav-tabs .nav-link.active {
            background-color: #2596be;
        }
    </style>
</head>

<body>

    <div id="app">
        <button class="btn sidebarToggleBtn sidebarToggleBtnMobile" style="display: none;" >
            <i class="bi bi-list fs-3"></i>
        </button>
        @include('helpguide::dashboard.layouts.header')
        {{-- @include('helpguide::dashboard.layouts.sidebar') --}}

      <main class="page-content">

        <div class="main-content px-3 pb-2 container ">
            @isset($pageTitle)
            @empty($hideTitle)
            <div class="page-title">
                @isset($backTo)
                <a href="{{ $backTo }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-arrow-left-short"></i>
                </a>
                @endisset
                {{ $pageTitle }}
            </div>
            @endempty
            @endisset

            @if(isDemo())
            <div class="alert alert-info" role="alert">
                This is a working Demo version, Some features has been disabled
            </div>
            @endif

            @if( app()->isDownForMaintenance() )
            <div class="alert alert-warning" role="alert">
                Maintenance mode is <b>ON</b>, To disable the Maintenance mode go to settings -> Advanced
                settings
            </div>
            @endif

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
            @if (isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (\Session::has('success'))
            <div class="alert alert-success">
                <ul class="m-0">
                    <li>{!! \Session::get('success') !!}</li>
                </ul>
            </div>
            @endif

            @yield('content')


        </div>

        @include('helpguide::dashboard.layouts.footer')
    </main>

    </div>

    <script src="{{ asset('build/ui/dashboard/js/dashboard.js?v='.config('vars.asset_version')) }}" defer></script>
    <script src="{{ route('dashboard.lang') }}?v={{config('vars.asset_version')}}"></script>

    @yield('script_footer')

</body>

<script>
    function checkElements() {
        const elements = document.querySelectorAll('.col-sm-12.col-md-6.col-lg-4.col-xl-3.d-flex');
        if (elements.length) {
            elements.forEach(element => {
                if (element.innerHTML.trim() == "<!---->") {
                    element.style.display = 'none';
                    element.classList.remove('d-flex');
                }
            });
        }
    }
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                checkElements();
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
    document.addEventListener("DOMContentLoaded", checkElements);
</script>

{{-- </html> --}}
@endsection