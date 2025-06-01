@extends('helpguide::auth.base')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <a href="{{route('login')}}" class="float-end my-4 btn btn-light text-dark text-capitalize">{{__('login')}}</a>
        </div>
        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block login-aside">
                            <div class="bg-login-image"></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">{{ __('auth.register') }}</h1>
                                </div>

                                @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="form-floating mb-3">
                                      <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="f-name" value="{{ old('name') }}" placeholder="{{__('Name')}}">
                                      <label for="f-name">{{ __('Full name') }}</label>
                                      @error('name')
                                          <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                      @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                      <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="f-email" value="{{ old('email') }}" placeholder="{{__('email')}}">
                                      <label for="f-email">{{ __('E-mail') }}</label>
                                      @error('email')
                                          <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                      @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                      <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="f-password" value="{{ old('password') }}" placeholder="{{__('password')}}">
                                      <label for="f-password">{{ __('Password') }}</label>
                                      @error('password')
                                          <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                      @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                      <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="f-password-confirm" value="{{ old('password-confirm') }}" placeholder="{{__('password-confirm')}}">
                                      <label for="f-password-confirm">{{ __('Password confirm') }}</label>
                                      @error('password_confirmation')
                                          <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                      @enderror
                                    </div>

                                    @foreach (customFields('user', 'auth_register') as $item)
                                    <div class="form-floating row mb-3">
                                        <label for="custom_{{ $item['key'] }}" class="col-md-4 col-form-label text-md-right">
                                            {{ __($item['label']) }}
                                        </label>
                                        <div class="col-md-6">

                                            @switch($item['type'])
                                                @case('select')
                                                    <select id="custom_{{ $item['key'] }}" class="form-control @error("custom_".$item['key']) is-invalid @enderror" name="custom_{{ $item['key'] }}">
                                                        @if(is_array($item['value']))
                                                        @foreach ($item['value'] as $lk => $lv)
                                                            <option value="{{$lk}}" @if(old('locale') == $lk) selected @endif >
                                                            {{ $lv }}
                                                            </option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    @break
                                                @default
                                                <input id="custom_{{ $item['key'] }}" type="{{ $item['type'] }}" class="form-control @error("custom_".$item['key']) is-invalid @enderror" name="custom_{{ $item['key'] }}" value="{{ old("custom_".$item['key']) }}" required autocomplete="custom_{{$item['key']}}">
                                            @endswitch

                                            @error("custom_".$item['key'])
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @endforeach

                                    @if(defaultSetting('user_register_locale', false) && count(availableLanguages()) > 1 )
                                    <div class="form-floating row mb-3">
                                        <label for="locale" class="col-md-4 col-form-label text-md-right">
                                            {{ __('Language') }}
                                        </label>
                                        <div class="col-md-6">

                                            <select class="form-control @error('locale') is-invalid @enderror" name="locale">
                                            @foreach (availableLanguages() as $lk => $lv)
                                            <option value="{{$lk}}" @if(old('locale') == $lk)
                                                selected
                                            @elseif( $lk == App()->getLocale() )
                                                selected
                                            @endif >
                                              {{ $lv }}
                                            </option>
                                            @endforeach
                                            </select>

                                            @error("locale")
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-floating row mb-0">
                                        <div class="col-md-12 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('auth.register') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
