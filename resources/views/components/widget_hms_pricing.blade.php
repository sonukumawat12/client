<style>
    .custom-card {
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 0;
    }

    .custom-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eaeaea;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9f9f9;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .custom-card-body {
        padding: 20px;
        font-size: 14px;
    }
</style>

<div class="custom-card {{ $class ?? '' }}" @if(!empty($id)) id="{{ $id }}" @endif>
    @if(empty($header))
        @if(!empty($title) || !empty($tool))
            <div class="custom-card-header">
                <div>
                    {!! $icon ?? '' !!}
                    <span>{{ $title ?? '' }}</span>
                </div>
                <div class="d-flex align-items-center">
                    @if(isset($date))
                        <span id="report_date_range" class="mr-2 text-muted" style="font-weight: normal;">
                            {{ __('Date Range:') }} {{ date('m/01/Y') }} ~ {{ date('m/t/Y') }}
                        </span>
                    @endif
                    {!! $tool ?? '' !!}
                </div>
            </div>
        @endif
    @else
        <div class="custom-card-header">
            {!! $header !!}
        </div>
    @endif

    <div class="custom-card-body">
        {{ $slot }}
    </div>
</div>
