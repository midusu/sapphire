<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\ActivityBooking;
use App\Models\Payment;
use Carbon\Carbon;

class TestDashboardController extends Controller
{
    public function index()
    {
        // Prepare stats array for the view
        $stats = [
            'total_bookings' => Booking::count(),
            'active_guests' => \App\Models\User::where('role_id', 7)->whereHas('bookings', function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            })->count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'activity_bookings' => ActivityBooking::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

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

        return view('admin.dashboard-test', compact('stats', 'recentBookings', 'recentActivityBookings'));
    }
}
