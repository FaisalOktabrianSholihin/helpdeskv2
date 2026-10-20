<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function scope(Request $request)
    {
        $user = $request->user();

        return Notification::forUser($user->id, ! $user->isRequester());
    }

    public function index(Request $request)
    {
        $items = $this->scope($request)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map->toFrontendArray();

        return response()->json(['ok' => true, 'notifications' => $items]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        abort_unless($this->scope($request)->whereKey($notification->id)->exists(), 403);

        $notification->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        $this->scope($request)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
