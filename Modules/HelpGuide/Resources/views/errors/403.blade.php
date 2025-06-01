@extends('helpguide::errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __("The requested page cannot be accessed" ?: 'Forbidden'))
@section('image', asset('assets/img/error-403.svg'))