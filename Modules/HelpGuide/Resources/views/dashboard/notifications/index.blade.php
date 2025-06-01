@extends('helpguide::dashboard.base', ['page' => 'notifications', 'pageTitle' => __('notifications.notifications')])

@section('content')
<div class="mb-2">
    @forelse ($notifications as $notification)
    <a class="d-flex align-items-center p-3 text-decoration-none border-bottom" href="{{ @$notification->data['link'] }}">
        <div class="me-2">
            <i class="bi bi-2x bi-{{ @$notification->data['icon'] }} text-{{@$notification->data['color']}}"></i>
        </div>
        <div>
            <span class="font-weight-bold">{{ @$notification->data['text'] }}</span>
            <div class="small text-gray-500">{{  @$notification->date }}</div>
        </div>
    </a>
    @empty
        <div class="text-center p-5">{{ __('notifications.no_notifications_yet') }}</div>
    @endforelse
    </div>

{{ $notifications->links() }}
@endsection
