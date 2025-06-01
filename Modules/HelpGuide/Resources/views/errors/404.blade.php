@extends('helpguide::errors::minimal')

@section('title', __('Page not found'))
@section('code', '404')
@section('message', __("The requested page cannot be found" ?: 'Page not found'))
@section('image', asset('assets/img/error-404.svg'))