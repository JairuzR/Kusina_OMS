<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'All notifications cleared.');
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function feed()
    {
        $user = auth()->user();

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()->latest()->take(5)->get()->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->data['title'],
                'message' => $n->data['message'],
                'type'    => $n->data['type'] ?? 'info',
                'url'     => $n->data['url'] ?? null,
                'read'    => !is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ]),
        ]);
    }
}