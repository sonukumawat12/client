<style>
    .card {
    transition: box-shadow 0.3s ease;
}
.card:hover {
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
}
</style>

<div class="card {{$class ?? ''}}" @if(!empty($id)) id="{{$id}}" @endif  style="font-size: 12px !important; 
            background: #fff; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);">
    @if(empty($header))
        @if(!empty($title) || !empty($tool))
            <div class="card-header" style="padding: 10px 15px; border-bottom: 1px solid #eee;">
                {!! $icon ?? '' !!}
                <h4 class="card-title" style="margin: 0; display: inline-block;">{{ $title ?? '' }}</h4>

                @if(isset($date))
                    <span id="report_date_range" style="float: right; font-weight: normal;">
                        Date Range: {{ date('m/01/Y') }} ~ {{ date('m/t/Y') }}
                    </span>
                @endif

                @if(!empty($tool))
                    <div style="float: right; margin-left: 10px;">
                        {!! $tool !!}
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="card-header">
            {!! $header !!}
        </div>
    @endif

    <div class="card-body" style="padding: 15px;">
        {{ $slot }}
    </div>
</div>
