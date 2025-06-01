@extends('layouts.app')
@section('title', __('petro::lang.petro_sms_notifications'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('petro::lang.petro_sms_notifications')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PetroNotificationTemplateController@store'), 'method' => 'post' ]) !!}
    
    <div class="row no-print">
        <div class="col-md-12">
            @include('petro::notification_template.partials.sms', ['templates' => $notifications])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-danger btn-big">@lang('messages.save')</button>
        </div>
    </div>
    {!! Form::close() !!}

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    // $('textarea.ckeditor').each( function(){
    //     var editor_id = $(this).attr('id');
    //     tinymce.init({
    //         selector: 'textarea#'+editor_id,
    //     });
    // });
</script>
@endsection
