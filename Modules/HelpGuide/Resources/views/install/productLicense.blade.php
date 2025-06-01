@extends('helpguide::install.layouts.master', [
    'step_text' => 'Product license',
    'action' => route('install.product_license'),
    'step' => 3
])

@section('content')
<div class="form-group">
    <label for="pc">{{ __('Enter your purchase code') }}</label>
    <input id="pc" type="text" placeholder="Your Envato purchase code" class="form-control @error('pc') is-invalid @enderror" name="pc" value="{{ old('pc') }}" required autocomplete="pc">
    @error('pc')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
    <small id="pcHelp" class="form-text text-muted">
        Purchase code is available on your Envato account under the downloads page
        <a href="https://help.market.envato.com/hc/en-us/articles/202822600" target="_blank">
            Where Is My Purchase Code?
        </a>
    </small>
</div>
@endsection
