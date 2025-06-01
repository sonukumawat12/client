@extends('helpguide::dashboard.base', ['page' => 'settings', 'pageTitle' => __('Advanced settings')])

@section('content')
<div class="container page-settings mb-3">
  <advanced-settings><div class="preloader"></div></advanced-settings>
  <div class="card">
    <div class="card-header">{{ __('Server info') }}</div>
    <div class="card-body p-0 m-0">
      <table class="table table-striped table-sm">

        {{-- <tr>
            <td>Max execution time</td>
            <td><span class="badge bg-secondary">{{ini_get("max_execution_time")}} seconds</span></td>
            <td>
                @if ( ini_get("max_execution_time") > 120 )
                    <span class="bg-success text-white px-1">Ok</span>
                @else 
                    <span class="bg-warning small px-1">+120 Seconds Recommended</span>
                @endif
            </td>
        </tr> --}}

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

        <tr>
          <td>Zip extension</td>
          <td><span class="badge bg-secondary">{{ extension_loaded('zip') ? __('Enabled') : __("Disabled / not installed") }}</span></td>
          <td>
              @if (  extension_loaded('zip') )
                  <span class="bg-success text-white px-1">Ok</span>
              @else 
                  <span class="bg-warning small px-1">Fail</span>
              @endif
          </td>
        </tr>
        
    </table>

    </div>
  </div>
</div>
@endsection