@extends('helpguide::install.layouts.master', [
    'step' => 1,
    'step_text' => 'Application requirements',
    'action' => route('install.requirements'),
])

@section('content')
    <ul class="list-group mb-3">
        @foreach ($requirements as $k => $v)
        <li class="list-group-item d-flex p-1 justify-content-between align-items-center">
            {{$k}}
            @if ($v)
                <span class="badge bg-success bg-pill">OK</span>
            @else
                <span class="badge bg-danger bg-pill">FAIL</span>
            @endif
        </li>
        @endforeach
    </ul>
    <p class="d-block p-1 m-0">Recommended settings</p>
    <table class="table table-striped table-sm">
        <tr>
            <td>Name</td>
            <td>Server config</td>
            <td>Status</td>
        </tr>
        <tr>
            <td>Max execution time</td>
            <td><span class="badge bg-secondary">{{ini_get("max_execution_time")}} seconds</span></td>
            <td>
                @if ( ini_get("max_execution_time") > 120 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+120 Seconds Recommended</span>
                @endif
            </td>
        </tr>

        <tr>
            <td>Max input time</td>
            <td><span class="badge bg-secondary">{{ini_get("max_input_time")}} Seconds</span></td>
            <td>
                @if ( ini_get("max_input_time") > 120 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+120 Seconds Recommended</span>
                @endif
            </td>
        </tr>

        <tr>
            <td>Max input vars</td>
            <td><span class="badge bg-secondary">{{ini_get("max_input_vars")}}</span></td>
            <td>
                @if ( ini_get("max_input_vars") > 2000 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+2000 Recommended</span>
                @endif
            </td>
        </tr>

        <tr>
            <td>Memory limit</td>
            <td><span class="badge bg-secondary">{{ini_get("memory_limit")}}</span></td>
            <td>
                @if ( ini_get("memory_limit") > 512 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+512M Recommended</span>
                @endif
            </td>
        </tr>

        <tr>
            <td>Post max size</td>
            <td><span class="badge bg-secondary">{{ini_get("post_max_size")}}</span></td>
            <td>
                @if ( ini_get("post_max_size") > 64 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+64M Recommended</span>
                @endif
            </td>
        </tr>

        <tr>
            <td>Upload max filesize</td>
            <td><span class="badge bg-secondary">{{ini_get("upload_max_filesize")}}</span></td>
            <td>
                @if ( ini_get("upload_max_filesize") > 64 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+64M Recommended</span>
                @endif
            </td>
        </tr>
        
    </table>
@endsection
