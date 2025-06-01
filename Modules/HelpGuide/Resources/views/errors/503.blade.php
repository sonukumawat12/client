@extends('helpguide::errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')

@section('message')
@php
$downMessage = (array)json_decode(file_get_contents(storage_path('framework/down')), true);

if( $downMessage ){
    if(isset( $downMessage['message'] )){ 
        echo $downMessage['message'];
    }
} else {
    echo __('Service Unavailable');
}
@endphp
@endsection 

@section('image', asset('assets/img/error-500.svg'))