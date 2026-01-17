<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view notifications.');
        }
        
        // Get notifications directly to ensure we get them
        $notificationsQuery = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
        
        $notifications = $notificationsQuery->paginate(20);

        // Debug: Log to help troubleshoot
        \Log::info('NotificationController - User ID: ' . $user->id);
        \Log::info('NotificationController - Total count: ' . $notificationsQuery->count());
        \Log::info('NotificationController - Paginated count: ' . $notifications->total());
        \Log::info('NotificationController - Items on page: ' . $notifications->count());

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return back()->with('success', 'All notifications marked as read.');
    }
}
