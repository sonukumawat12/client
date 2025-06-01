@extends('helpguide::errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('image', asset('assets/img/error-429.svg'))