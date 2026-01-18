<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityBooking;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityBooking::with(['user', 'activity']);

        // Filter by Activity
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_time', '<=', $request->date_to);
        }

        $activityBookings = $query->orderBy('scheduled_time', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        return view('admin.activities.bookings.index', compact('activityBookings'));
    }

    public function create()
    {
        $activities = Activity::where('is_active', true)->get();
        $bookings = Booking::where('status', 'confirmed')
            ->orWhere('status', 'checked_in')
            ->with(['user', 'room.roomType'])
            ->get();
        
        return view('admin.activities.bookings.create', compact('activities', 'bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'activity_id' => 'required|exists:activities,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'scheduled_time' => 'required|date|after:now',
            'participants' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        
        // Check if participants exceed max capacity
        if ($request->participants > $activity->max_participants) {
            return back()->with('error', "Maximum participants for this activity is {$activity->max_participants}");
        }

        // Check for scheduling conflicts
        $conflictingBookings = ActivityBooking::where('activity_id', $request->activity_id)
            ->where('scheduled_time', $request->scheduled_time)
            ->where('status', '!=', 'cancelled')
            ->sum('participants');

        if ($conflictingBookings + $request->participants > $activity->max_participants) {
            return back()->with('error', 'Activity is fully booked at this time. Please choose a different time.');
        }

        $totalPrice = $activity->price * $request->participants;

        DB::beginTransaction();
        try {
            $activityBooking = ActivityBooking::create([
                'user_id' => $request->user_id,
                'activity_id' => $request->activity_id,
                'booking_id' => $request->booking_id,
                'scheduled_time' => $request->scheduled_time,
                'participants' => $request->participants,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create payment record
            Payment::create([
                'activity_booking_id' => $activityBooking->id,
                'amount' => $totalPrice,
                'payment_method' => 'pending',
                'status' => 'pending',
            ]);

            DB::commit();
            
            return redirect()->route('admin.activities.bookings.index')
                ->with('success', 'Activity booking created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating activity booking: ' . $e->getMessage());
        }
    }

    public function show(ActivityBooking $activityBooking)
    {
        $activityBooking->load(['user', 'activity', 'booking.room.roomType', 'payments']);
        return view('admin.activities.bookings.show', compact('activityBooking'));
    }

    public function confirm(ActivityBooking $activityBooking)
    {
        if ($activityBooking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $activityBooking->update(['status' => 'confirmed']);
        
        return back()->with('success', 'Activity booking confirmed successfully!');
    }

    public function complete(ActivityBooking $activityBooking)
    {
        if ($activityBooking->status !== 'confirmed') {
            return back()->with('error', 'Booking must be confirmed before marking as complete.');
        }

        $activityBooking->update(['status' => 'completed']);
        
        return back()->with('success', 'Activity marked as completed!');
    }

    public function cancel(ActivityBooking $activityBooking)
    {
        if (in_array($activityBooking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this booking.');
        }

        $activityBooking->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Activity booking cancelled successfully!');
    }

    public function calendar()
    {
        $activities = Activity::where('is_active', true)->get();
        $startDate = request('start', Carbon::now()->startOfMonth());
        $endDate = request('end', Carbon::now()->endOfMonth());
        
        $bookings = ActivityBooking::with(['activity', 'user'])
            ->whereBetween('scheduled_time', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        return view('admin.activities.calendar', compact('activities', 'bookings', 'startDate', 'endDate'));
    }

    public function getAvailableSlots(Request $request)
    {
        $activityId = $request->activity_id;
        $date = Carbon::parse($request->date);
        
        $activity = Activity::findOrFail($activityId);
        $bookings = ActivityBooking::where('activity_id', $activityId)
            ->whereDate('scheduled_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        // Generate time slots (every hour from 9 AM to 6 PM)
        $slots = [];
        for ($hour = 9; $hour <= 18; $hour++) {
            $slotTime = $date->copy()->setHour($hour)->setMinutes(0)->setSeconds(0);
            $endTime = $slotTime->copy()->addMinutes($activity->duration->hour * 60 + $activity->duration->minute);
            
            $bookedParticipants = $bookings
                ->where('scheduled_time', $slotTime)
                ->sum('participants');
            
            $available = $activity->max_participants - $bookedParticipants;
            
            $slots[] = [
                'time' => $slotTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'available' => $available,
                'is_available' => $available > 0
            ];
        }

        return response()->json($slots);
    }

    // Public booking methods
    public function publicIndex()
    {
        $activities = Activity::where('is_active', true)->get();
        
        return view('booking.activities.index', compact('activities'));
    }

    public function publicCreate()
    {
        $activities = Activity::where('is_active', true)->get();
        
        return view('booking.activities.create', compact('activities'));
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'scheduled_time' => 'required|date|after:now',
            'participants' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:card,cash,online,bank_transfer',
            // Guest information if not logged in
            'guest_name' => 'required_if:user_id,null|string|max:255',
            'guest_email' => 'required_if:user_id,null|email|max:255',
            'guest_phone' => 'required_if:user_id,null|string|max:20',
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        
        // Check if participants exceed max capacity
        if ($request->participants > $activity->max_participants) {
            return back()->with('error', "Maximum participants for this activity is {$activity->max_participants}")->withInput();
        }

        // Check if the time slot is available
        $bookedParticipants = ActivityBooking::where('activity_id', $request->activity_id)
            ->where('scheduled_time', $request->scheduled_time)
            ->where('status', '!=', 'cancelled')
            ->sum('participants');

        if ($bookedParticipants + $request->participants > $activity->max_participants) {
            return back()->with('error', 'This time slot is fully booked. Please choose a different time.')->withInput();
        }

        // Calculate total price
        $totalPrice = $request->participants * $activity->price;

        DB::beginTransaction();
        try {
            $activityBooking = ActivityBooking::create([
                'user_id' => auth()->check() ? auth()->id() : null, // null if guest
                'activity_id' => $request->activity_id,
                'scheduled_time' => $request->scheduled_time,
                'participants' => $request->participants,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'notes' => $request->notes,
                // Store guest info if not logged in
                'special_requirements' => !auth()->check() ? json_encode([
                    'guest_name' => $request->guest_name,
                    'guest_email' => $request->guest_email,
                    'guest_phone' => $request->guest_phone,
                ]) : null,
            ]);

            // Create payment record
            Payment::create([
                'activity_booking_id' => $activityBooking->id,
                'amount' => $totalPrice,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            DB::commit();
            
            return redirect()->route('booking.confirmation', $activityBooking)
                ->with('success', 'Activity booking request submitted successfully! We will confirm your booking shortly.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create activity booking. Please try again.')->withInput();
        }
    }
}
