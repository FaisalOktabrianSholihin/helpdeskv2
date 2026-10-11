<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $items = Notification::forUser($request->user()->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'icon' => $n->icon,
                'title' => $n->title,
                'text' => $n->text,
                'time' => $n->created_at?->diffForHumans(),
                'unread' => is_null($n->read_at),
            ]);

        return response()->json(['ok' => true, 'notifications' => $items]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        $notification->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        Notification::forUser($request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
