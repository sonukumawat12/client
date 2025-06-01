@extends('layouts.app')
@section('title', __('essentials::lang.essentials_n_hrm_settings'))

@section('content')
@include('essentials::layouts.nav_essentials')

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}
    <div class="row">
        <div class="col-xs-12">
           @include('essentials::settings.partials.essentials_settings')
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function () {
        tinymce.init({
            selector: 'textarea#leave_instructions',
        });

        $('#essentials_settings_form').validate({ 
            ignore: [],
        });
    });
</script>
@endsection