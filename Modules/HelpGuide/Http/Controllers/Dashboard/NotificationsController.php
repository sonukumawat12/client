<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function unread()
    {
        $user = auth()->user();
        return $user->unreadNotifications()->select('id','created_at','data')->get()->map(function($item) {
            $t = Carbon::parse($item['created_at'])->format(setting('date_format'));
            $t .= " - ";
            $t .= Carbon::parse($item['created_at'])->diffForHumans();
            $item['date'] = $t;
            return $item;
        });
    }

    public function all()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(50);
        
         $notifications->map(function($item) {
            $t = Carbon::parse($item['created_at'])->format(setting('date_format'))." - ".Carbon::parse($item['created_at'])->diffForHumans();
            $item['date'] = $t;
            return $item;
        });

        return view('helpguide::dashboard.notifications.index', ['notifications' => $notifications]);
    }

    public function markAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();
    }
}
