@extends('helpguide::my_account.base')
@section('content')
<div class="my-3">
    <router-view>
        <div class="preloader" style="opacity: 1%;"></div>
    </router-view>
</div>
@endsection
