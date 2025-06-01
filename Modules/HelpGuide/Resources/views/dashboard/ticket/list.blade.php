@extends('helpguide::dashboard.base', ['page' => 'tickets', 'pageTitle' => __('Tickets')])

@section('content') 
<div class="container page-ticket"> 
<div class="row">

    <div class="col-md-12">
        <ticket-list type="{{Request::get('type')}}" category="{{ Request::get('category') }}" title="Tickets" class="ticket-list-holder"><div class="preloader"></div></ticket-list>
    </div>
    
</div>
</div>
@endsection

@section('head_assets')
@parent
<link href="{{ asset('assets/libs/vue-select/vue-select.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/libs/vue-select/vue-select.min.js') }}"></script>
@endsection