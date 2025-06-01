@php
    $footersettings = DB::table('system')->where('key', 'app_footer')->select('value')->first();
@endphp

<!-- Main Footer -->
<footer class="main-footer no-print" style="display: flex; justify-content: start; align-items: center; width : 100%; padding-left : 10px;">
    <small>
        {{ $footersettings->value ?? "" }}
    </small>
</footer>