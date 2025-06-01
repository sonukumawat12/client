@extends('helpguide::my_account.base', ['page' => 'profile', 'pageTitle', __('Profile')])

@section('content')
<div class="container page-profile">
  <form method="POST" action="">
  @csrf
  <div class="row">
      <div class="col-md-6">
          <div class="card">
              <div class="card-header card-header-divider">
                  <div class="card-sub-title">{{__('General Information')}}</div>
              </div>
              <div class="card-body">
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="email">{{__('Email')}}</label>
                      <input type="email" class="form-control" name="email" id="email" placeholder="{{__('Email')}}" value="{{ $user->email }}">
                    </div>
                    <div class="form-group col-12">
                      <label for="name">{{__('Name')}}</label>
                      <input type="name" class="form-control" name="name" id="name" placeholder="{{__('Name')}}" value="{{ $user->name }}">
                    </div>

                    {{-- <div class="form-group col-12">
                      <label for="country">{{__('Country')}}</label>
                      <input type="name" class="form-control" name="country" id="country" placeholder="{{__('country')}}" value="{{ $user->country }}">
                    </div> --}}

                    <div class="form-group col-12">
                      <label for="locale">{{__('Language')}}</label>
                      <select name="locale" class="form-control">
                        @foreach (availableLanguages() as $langK => $langV)
                            <option value="{{$langK}}" {{ ( old("locale", $user->locale) == $langK ? "selected":"") }}>{{ $langV }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-12">
                      <label for="name">{{__('Signature')}}</label>
                      <textarea class="form-control" name="signature" id="signature" placeholder="{{__('signature')}}">{{ $user->signature }}</textarea>
                      <small id="signatureHelp" class="form-text text-muted">{{ __('profile_signature_max_length') }}</small>
                    </div>

                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-6">
          <div class="card">
              <div class="card-header card-header-divider">
                  {{__('Change password')}}
              </div>
              <div class="card-body">
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="current_password">{{__('Current password')}}:</label>
                      <input type="password" class="form-control" name="current_password" id="current_password" placeholder="{{__('Current password')}}">
                    </div>
                    <div class="form-group col-12">
                      <label for="password">{{__('New password')}}:</label>
                      <input type="password" class="form-control" name="password" id="password" placeholder="{{__('New password')}}">
                    </div>
                    <div class="form-group col-12">
                      <label for="password_confirmation">{{__('Re-type new password')}}:</label>
                      <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="{{__('Re-type new password')}}">
                    </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-lg-12">
      <input class="btn btn-primary float-end mt-3" type="submit" value="{{__('Save')}}">
      </div>
  </div>
  </form>
</div>
@endsection
