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
            <div class="card">
                <div class="table-responsive px-2 py-2">
                    <table class="table table-vcenter card-table" id="table">
                        <thead>
                            <tr>
                                <th>{{ __('Page') }}</th>
                                <th class="w-1">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allPages as $page)
                            <tr>
                                <td class="text-capitalize">{{ $page }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@editPage', $page)}}">{{ __('Edit') }}</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('javascript')

@endpush