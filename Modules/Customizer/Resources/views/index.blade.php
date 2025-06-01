@extends('helpguide::dashboard.base',
[
'page' => 'customizer',
'pageTitle' => __('Customizer')
])

@section('content')
<div id="appm-customzer">
    <appm-customizer><div class="preloader"></div></appm-customizer>
</div>
@endsection

@section('script')
  @parent()
  <script>
    const MODULE_URL = "{{ route('modules.customizer') }}/";
</script>
@endsection

@section('style')
@parent()
<link href="{{ asset('assets/modules/customizer/css/customizer.css') }}" rel="stylesheet">
@endsection

@section('script_footer')
@parent()
<script src="{{ asset('assets/modules/customizer/js/customizer.js') }}" defer ></script>
@endsection