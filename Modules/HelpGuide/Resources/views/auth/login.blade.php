@extends('helpguide::auth.base')

@section('content')
<section class="auth-form-container">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="auth-form-card">
          <div class="row">

            <div class="col">
              <div class="auth-form ms-3">
                <div class="main-head">
                  <h1 class="h4 text-gray-900 mb-4">{{__('auth.welcome')}}</h1>
                </div>

                @if(isDemo())
                <div class="alert alert-info">
                  <div>Demo accounts</div>
                  <small class="d-inline-block">Admin : admin@demo.com / 12345678</small>
                  <small class="d-inline-block">Agent : agent@demo.com / 12345678</small>
                  <small class="d-inline-block">Customer : customer@demo.com / 12345678</small>
                </div>
                @endif

                <app-login canRegister="@if(Route::has('register')) true @else false @endif">
                  <div class="p-5">
                    <div class="preloader"></div>
                  </div>
                </app-login>

                @if(Route::has('register'))
                <div class="bg-light text-center my-3 p-3">
                  <div>{{ __("auth.dont_have_an_account") }}
                    <a href="{{ route('register') }}">{{ __('auth.register_here') }} </a>
                  </div>
                </div>
                @endif

              </div>
            </div>
            @if (isOAuthEnabled())
            <div class="col-5 auth-card-side">
              <div class="auth-form-card-a">
                <div class="auth-form-container_socialmedia">
                  <div class="mt-4 mb-1 text-capitalize fs-4 text-white mb-5">{{__('Login with')}}</div>
                  @foreach (OAuthProviders() as $OAuthProvider)
                  <a href="{{ route('auth.socialite',  ['provider' => $OAuthProvider]) }}"
                    class="btn  btn-lg w-100 btn-primary rounded-pill btn-sm mb-2 text-capitalize">
                    <i class="bi bi-{{$OAuthProvider}}"></i> {{ $OAuthProvider }}
                  </a>
                  @endforeach
                </div>
              </div>
            </div>
            @endif

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection