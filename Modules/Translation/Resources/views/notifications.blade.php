@if(Session::has('success'))
    <div class="alert alert-success my-2" role="alert">
        {{ Session::get('success') }}
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger my-2" role="alert">
        {!! Session::get('error') !!}
    </div>
@endif