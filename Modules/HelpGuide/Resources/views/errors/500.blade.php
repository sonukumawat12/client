@extends('helpguide::errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))
@section('image', asset('assets/img/error-500.svg'))