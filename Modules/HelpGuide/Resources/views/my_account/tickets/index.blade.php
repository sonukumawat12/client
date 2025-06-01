@extends('helpguide::my_account.base', ['page' => 'tickets', 'pageTitle', __('Tickets')])

@section('content')   

<div class="row">
    <div class="col-md-12">
        <mya-ticket-list title="My Tickets" class="ticket-list-holder open">
            <div class="preloader"></div>
        </mya-ticket-list>
    </div>
</div>

@endsection