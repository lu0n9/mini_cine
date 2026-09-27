<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        $unreadNotificationCount = $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->count();

        return view('client.pages.notifications.index', compact(
            'notifications',
            'unreadNotificationCount'
        ));
    }

    public function show(Request $request, int $notification)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($notification);

        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return view('client.pages.notifications.show', compact('notification'));
    }
}
