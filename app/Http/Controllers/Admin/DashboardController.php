<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Activity;
use App\Models\ActivityBooking;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's check-ins and check-outs
        $today = Carbon::today();
        $checkIns = Booking::whereDate('check_in_date', $today)->count();
        $checkOuts = Booking::whereDate('check_out_date', $today)->count();

        // Room occupancy
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Available rooms by type
        $availableRoomsByType = RoomType::withCount(['rooms' => function ($query) {
            $query->where('status', 'available');
        }])->get();

        // Activity bookings overview
        $todayActivityBookings = ActivityBooking::whereDate('scheduled_time', $today)
            ->with('activity')
            ->get()
            ->groupBy('activity.type');

        // Revenue summary
        $todayRevenue = Payment::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('amount');

        $monthlyRevenue = Payment::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'completed')
            ->sum('amount');

        // Recent bookings
        $recentBookings = Booking::with(['user', 'room.roomType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent activity bookings
        $recentActivityBookings = ActivityBooking::with(['user', 'activity'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get unread notifications for current user
        $user = Auth::user();
        // Use direct query to ensure we get notifications
        $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Prepare stats array for the view
        $stats = [
            'total_bookings' => Booking::count(),
            'active_guests' => \App\Models\User::where('role_id', 7)->whereHas('bookings', function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            })->count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'activity_bookings' => ActivityBooking::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'occupancy_rate' => $occupancyRate,
            'check_ins_today' => $checkIns,
            'check_outs_today' => $checkOuts,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'checkIns',
            'checkOuts',
            'totalRooms',
            'occupiedRooms',
            'occupancyRate',
            'availableRoomsByType',
            'todayActivityBookings',
            'todayRevenue',
            'monthlyRevenue',
            'recentBookings',
            'recentActivityBookings',
            'unreadNotifications'
        ));
    }
}
