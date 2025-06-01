@extends('layouts.app')
@section('title', __('hms::lang.hms'))
@section('content')
    @include('hms::layouts.nav')
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                

                 
  
            </div>
            
 
        </div>
        
    @endsection

    @section('javascript')
        {!! $booking_chart->script() !!}
        {!! $past_booking_chart->script() !!}
    @endsection
