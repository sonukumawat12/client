@extends('helpguide::install.layouts.master', [
'step_text' => 'Database & App URL',
'action' => route('install.database'),
'step' => 4
])

@section('content')
<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="app_url">{{ __('Application URL') }}</label>
    <div class="col-sm-7">
        <input id="app_url" type="text" placeholder="Application URL" class="form-control"
            name="app_url" value="{{ old('app_url', url('/')) }}" required autocomplete="app_url">
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="database_hostname">{{__('Database Hostname')}}</label>
    <div class="col-sm-7">
        <input id="database_hostname" type="text" placeholder="Database Hostname" class="form-control"
            name="database_hostname" value="{{ old('database_hostname', '127.0.0.1') }}" required autocomplete="database_hostname">
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="database_port">Database port</label>
    <div class="col-sm-7">
        <input id="database_port" type="text" placeholder="Database port" class="form-control" name="database_port"
            value="{{ old('database_port','3306') }}" required autocomplete="database_port">
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="database_name">Database name</label>
    <div class="col-sm-7">
        <input id="database_name" type="text" placeholder="Database name" class="form-control" name="database_name"
            value="{{ old('database_name') }}" required autocomplete="database_name">
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="database_username">Database username</label>
    <div class="col-sm-7">
        <input id="database_username" type="text" placeholder="Database username" class="form-control"
            name="database_username" value="{{ old('database_username') }}" required autocomplete="database_username">
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-5 col-form-label text-md-right" for="database_password">Database password</label>
    <div class="col-sm-7">
        <input id="database_password" type="password" placeholder="Database password" class="form-control"
            name="database_password" value="{{ old('database_password') }}" autocomplete="database_password">
    </div>
</div>

@endsection