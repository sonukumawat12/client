@extends('helpguide::dashboard.base', ['page' => 'error_logs', 'pageTitle' => __('Error logs')])

@section('content')

<div>
  <div class="row">
    <div class="col">
      <select class="form-control files-select my-1">
        <option value="" disabled selected>Select file</option>
        @foreach($folders as $folder)
          <option value="?f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}">📁{{$folder}}</option>
          @foreach($folder_files as $file)
          <option value="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}&f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}"
            @if ($current_file==($folder.'-'.$file) ) selected @endif>
            &nbsp;&boxur;📰 {{$file}}
          </option>
          @endforeach

        @endforeach
        <optgroup label="📁Main folder">
          @foreach($files as $file)
          <option value="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}" @if ($current_file==$file)
            selected @endif>
            📰 {{$file}}
          </option>
          @endforeach
      </select>

    </div>

    <div class="col">
      @if($current_file)
      <a class="btn btn-primary my-1"
        href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="bi bi-cloud-arrow-down"></span> Download file
      </a>
      <a class="btn btn-danger my-1" id="clean-log"
        href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="bi bi-arrow-repeat"></span> Clean file
      </a>
      <a class="btn btn-danger my-1" id="delete-log"
        href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="bi bi-trash"></span> Delete file
      </a>
      @if(count($files) > 1)
      <a class="btn btn-danger my-1" id="delete-all-log"
        href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="bi bi-trash"></span> Delete all files
      </a>
      @endif
      @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 table-container">
    @if ($logs === null)
    <div>
      Log file >50M, please download it.
    </div>
    @else
    <div class="table-responsive">
      <table id="table-log" class="table table-striped" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
        <thead>
          <tr>
            @if ($standardFormat)
            <th>Level</th>
            <th>Context</th>
            <th>Date</th>
            @else
            <th>Line number</th>
            @endif
            <th>Content</th>
          </tr>
        </thead>
        <tbody>

          @foreach($logs as $key => $log)
          <tr data-display="stack{{{$key}}}">
            @if ($standardFormat)
            <td class="text-{{{$log['level_class']}}}">
              <i class="bi bi-{{{$log['level_img']}}}" aria-hidden="true"></i>{{$log['level']}}
            </td>
            <td class="text">{{$log['context']}}</td>
            @endif
            <td class="date">{{{$log['date']}}}</td>
            <td class="text">
              @if ($log['stack'])
              <button type="button" class="float-end expand btn btn-outline-dark btn-sm mb-2 ml-2"
                data-display="stack{{{$key}}}">
                <span class="bi bi-search"></span>
              </button>
              @endif
              {{{$log['text']}}}
              @if (isset($log['in_file']))
              <br />{{{$log['in_file']}}}
              @endif
              @if ($log['stack'])
              <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">
                {{{ trim($log['stack']) }}}
              </div>
              @endif
            </td>
          </tr>
          @endforeach

        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection

@section('script_footer')
@parent()
<script>
document.addEventListener("DOMContentLoaded", function(event) {
  $('.files-select').on('change',  function () {
      location.replace(MYACCOUNT_URL+'error_logs'+this.value);
    });

    $('.table-container tr').on('click', function () {
      $('#' + $(this).data('display')).toggle();
    });
    $('#delete-log, #clean-log, #delete-all-log').click(function () {
      return confirm('Are you sure?');
    });
});
</script>
@endsection
