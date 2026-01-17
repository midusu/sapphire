<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\ActivityBooking;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'active_guests' => Booking::where('status', 'checked_in')->count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'activity_bookings' => ActivityBooking::where('status', '!=', 'cancelled')->count(),
            'today_revenue' => Payment::whereDate('created_at', Carbon::today())
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
