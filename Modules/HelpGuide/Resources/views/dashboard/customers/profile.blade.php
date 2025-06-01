@extends('helpguide::dashboard.base', ['page' => 'customers', 'pageTitle' => __('Customer')])

@section('content')
    <view-user userid="{{$user->id}}" type="customers"><div class="preloader"></div></view-user>
@endsection