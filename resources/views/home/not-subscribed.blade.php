@extends('layouts.app')
@section('title', __('home.home'))

@section('content')

<style>
    .notice_card{
        color: {{$font_color}} !important;
        font-family:  {!! $font_family !!} !important;
        background-color: {{$background_color}} !important;
        font-size:  {{$font_size}}px !important;
    }
</style>

<section class="content main-content-inner no-print">
    <div class="row">
        <div class="notice_card card text-center"> {{ $message }} </div>
  </div>

</section>
<!-- /.content -->



@stop
@section('javascript')
<script>
</script>
@endsection
