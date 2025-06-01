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
                    <table class="table table-vcenter card-table" id="languages-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Locale</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    languages_table = $('#languages-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@landing_languages')}}",
            data: function (d) {
                
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'language', name: 'language' },
            { data: 'active', name: 'active' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
           
        ],
        fnDrawCallback: function (oSettings) {
            
        }
    });
});
    
</script>
@endsection