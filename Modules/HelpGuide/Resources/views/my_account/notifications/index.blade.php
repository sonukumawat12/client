@extends('helpguide::my_account.base', ['page' => 'notifications', 'pageTitle', __('notifications.notifications')])

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{  __('notifications.notifications') }}</h1>
</div>

<div class="mb-2">
@forelse ($notifications as $notification)
<a class="d-flex align-items-center p-3 text-decoration-none border-bottom" href="{{ @$notification->data['link'] }}">
    <div class="me-2">
        <div class="icon-circle p-2 rounded-circle bg-{{ @$notification->data['color'] }}">
            <i class="bi bi-{{ @$notification->data['icon'] }} text-white"></i>
        </div>
    </div>
    <div>
        <span class="font-weight-bold">{{ @$notification->data['text'] }}</span>
        <div class="small text-gray-500">{{  @$notification->date }}</div>
    </div>
</a>
@empty
    <div class="text-center p-5">{{__('notifications.no_notifications_yet')}}</div>
@endforelse
</div>

{{ $notifications->links() }}
@endsection
