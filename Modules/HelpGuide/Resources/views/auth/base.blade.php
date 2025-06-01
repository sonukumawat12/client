<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ setting('app_name') }}</title>
  
  <link href="{{asset('build/common/css/main.css?v='.config('vars.asset_version'))}}" rel="stylesheet">
  <link href="{{ asset('build/frontend/css/auth.css?v='.config('vars.asset_version')) }}" rel="stylesheet">

  {!! customStyle('frontend') !!}

  <script>
    const BASE_URL = "{{ url('/') }}/";
    const MYACCOUNT_URL = "{{ route('my_account') }}/";
  </script>

  @yield('style')

</head>

<body>
  <div id="app">
      @yield('content')
  </div>
  <script src="{{ route('frontend.lang') }}?v={{config('vars.asset_version')}}"></script>
  <script src="{{ asset('build/frontend/js/frontend.js?v='.config('vars.asset_version')) }}"></script>
</body>

</html>