@extends('helpguide::dashboard.base', ['page' => 'employees', 'pageTitle' => __('Employee')])

@section('content')
    <view-user userid="{{$user->id}}" type="employees"><div class="preloader"></div></view-user>
@endsection