@extends('helpguide::dashboard.base',
[
'page' => 'translation',
'pageTitle' => __('Translation')
])

@section('content')
<div id="appm-translation">
        @include('translation::notifications')
        @yield('body')
</div>
@endsection

@section('style')
@parent()
<link href="{{ asset('assets/modules/translation/css/translation.css') }}" rel="stylesheet">
@endsection

@section('script_footer')
@parent()
<script src="{{ asset('assets/modules/translation/js/translation.js') }}" defer></script>
@endsection