@extends('helpguide::dashboard.base', ['page' => 'settings', 'pageTitle' => __('Settings'), 'hideTitle' => true])

@section('content')
<div class="container page-settings mb-3">
  <app-settings><div class="card card-body"><div class="preloader"></div></div></app-settings>
</div>
@endsection