@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Superadmin Settings')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.super_admin_settings')<small>@lang('superadmin::lang.edit_super_admin_settings')</small>
    </h1>
</section>
<style>
    .wrapper {
        overflow: hidden;
    }
</style>
<!-- Main content -->
<section class="content">
    <div class="row row-deck row-cards">
        <div class="col-sm-12 col-lg-12">
            <form action="{{ action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@savePage', Request::segment(3)) }}" method="post"
                enctype="multipart/form-data" class="card">
                @csrf
                <div class="card-body">
                    <div class="row">
                        @for ($i = 0; $i < count($sections); $i++)
                        <div class="col-md-4">
                            <div id="section{{ $i }}" class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Content') }} {{ $i + 1 }}</label>
                                        <textarea rows="3" cols="10" class="form-control" name="section{{ $i }}"
                                            data-bs-toggle="autosize" placeholder="{{ __('Page Content') }} {{ $i }}..."
                                            required>{{ $sections[$i]->section_content }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor

                        <div class="col-md-4 col-xl-4 my-3">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
@section('javascript')
    @if(session('toast_error'))
        <script>
            toastr.error('{{ session('toast_error') }}');
        </script>
    @endif
    
    @if(session('toast_success'))
        <script>
            toastr.success('{{ session('toast_success') }}');
        </script>
    @endif

    <script type="text/javascript">
        
    </script>
@endsection