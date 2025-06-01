@extends('helpguide::dashboard.base', ['page' => 'tickets', 'pageTitle' => __('Ticket').' ID #'.$ticket->id.' | '.$ticket->title, 'hideTitle' => true])

@section('content') 
<div class="container page-ticket"> 
    <my-ticket :ticketid="{{$ticket->id}}" 
        @if( Auth::User()->can('delete_ticket_reply') || Auth::User()->can('delete_any_ticket_reply') )
        :can-delete-reply=true
        @else
        :can-delete-reply=false
        @endif
        @if( Auth::User()->can('delete_any_ticket') || Auth::User()->can('delete_ticket') )
        :can-delete-ticket=true
        @else
        :can-delete-ticket=false
        @endif
    ><div class="preloader"></div></my-ticket>
</div>
@endsection

@section('head_assets')
@parent
<link href="{{ asset('assets/libs/vue-select/vue-select.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/libs/vue-select/vue-select.min.js') }}"></script>
@endsection
