<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room.roomType']);

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('check_in_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        return view('admin.bookings.create', compact('roomTypes'));
    }

    public function getAvailableRooms(Request $request)
    {
        try {
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);
            $roomTypeId = $request->room_type_id;

            $availableRooms = Room::with('roomType')
                ->where('room_type_id', $roomTypeId)
                ->where('status', '!=', 'maintenance')
                ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                    $query->whereIn('status', ['confirmed', 'checked_in'])
                        ->where(function ($q) use ($checkIn, $checkOut) {
                            $q->whereBetween('check_in_date', [$checkIn, $checkOut->copy()->subDay()])
                              ->orWhereBetween('check_out_date', [$checkIn->copy()->addDay(), $checkOut])
                              ->orWhere(function ($q) use ($checkIn, $checkOut) {
                                  $q->where('check_in_date', '<=', $checkIn)
                                    ->where('check_out_date', '>=', $checkOut);
                              });
                        });
                })
                ->get();

            return response()->json($availableRooms);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching available rooms: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
        ];

        if ($request->has('guest_type') && $request->guest_type === 'new') {
            $rules['new_guest.name'] = 'required|string|max:255';
            $rules['new_guest.email'] = 'required|email|unique:users,email';
            $rules['new_guest.phone'] = 'required|string|max:20';
            $rules['new_guest.address'] = 'nullable|string|max:255';
        } else {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $userId = $request->user_id;

        DB::beginTransaction();
        try {
            // Create new user if needed
            if ($request->guest_type === 'new') {
                $password = \Illuminate\Support\Str::random(10);
                $user = \App\Models\User::create([
                    'name' => $request->new_guest['name'],
                    'email' => $request->new_guest['email'],
                    'phone' => $request->new_guest['phone'],
                    'address' => $request->new_guest['address'] ?? null,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                ]);
                
                // Assign guest role
                $guestRole = \App\Models\Role::where('slug', 'guest')->first();
                if ($guestRole) {
                    $user->roles()->attach($guestRole->id);
                }

                $userId = $user->id;
                
                // Send welcome email with password (todo)
            }

            $room = Room::findOrFail($request->room_id);
            $nights = Carbon::parse($request->check_in_date)
                ->diffInDays(Carbon::parse($request->check_out_date));
            
            $totalAmount = $room->roomType->base_price * $nights;

            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'adults' => $request->adults,
                'children' => $request->children ?? 0,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'special_requests' => $request->special_requests,
            ]);

            // Update room status
            $room->update(['status' => 'occupied']);

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'payment_method' => 'pending',
                'status' => 'pending',
            ]);

            DB::commit();
            
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'room.roomType', 'payments']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Booking must be confirmed before check-in.');
        }

        $booking->update(['status' => 'checked_in']);
        
        return back()->with('success', 'Guest checked in successfully!');
    }

    public function checkOut(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Guest must be checked in before check-out.');
        }

        $booking->update(['status' => 'checked_out']);
        
        // Update room status to available for cleaning
        $booking->room->update(['status' => 'cleaning']);
        
        // Send housekeeping alert for room cleaning
        $housekeepingUsers = \App\Models\User::whereHas('role', function($query) {
            $query->where('slug', 'housekeeping');
        })->get();
        
        foreach ($housekeepingUsers as $user) {
            $user->notify(new \App\Notifications\HousekeepingAlertNotification($booking->room, 'check_out'));
        }
        
        return back()->with('success', 'Guest checked out successfully!');
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => 'confirmed']);
        
        // Log audit
        \App\Helpers\AuditHelper::log('update', "Booking #{$booking->id} confirmed", $booking, 
            ['status' => $oldStatus], ['status' => 'confirmed']);
        
        // Send booking confirmation notification
        $user = $booking->user;
        if ($user) {
            $user->notify(new \App\Notifications\BookingConfirmationNotification($booking));
        } elseif ($booking->guest_email) {
            // For guest bookings without user account, create a temporary user or send directly
            // For now, we'll create a notification record if possible
        }
        
        return back()->with('success', 'Booking confirmed successfully!');
    }

    public function cancel(Booking $booking)
    {
        if (in_array($booking->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this booking.');
        }

        $booking->update(['status' => 'cancelled']);
        
        // Update room status back to available
        $booking->room->update(['status' => 'available']);
        
        return back()->with('success', 'Booking cancelled successfully!');
    }

    // Public booking methods
    public function publicIndex(Request $request)
    {
        $roomTypes = RoomType::with(['rooms' => function($query) {
            $query->where('status', 'available');
        }])->get();
        
        // If dates are provided, filter by availability
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            // Get conflicting bookings
            $conflictingBookings = Booking::where(function($query) use ($checkIn, $checkOut) {
                $query->where(function($q) use ($checkIn, $checkOut) {
                    $q->where('check_in_date', '<=', $checkIn)
                      ->where('check_out_date', '>', $checkIn);
                })->orWhere(function($q) use ($checkIn, $checkOut) {
                    $q->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>=', $checkOut);
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->pluck('room_id');
            
            // Filter room types to show only those with available rooms
            $roomTypes = $roomTypes->map(function($roomType) use ($conflictingBookings) {
                $availableRooms = $roomType->rooms->reject(function($room) use ($conflictingBookings) {
                    return $conflictingBookings->contains($room->id);
                });
                $roomType->available_count = $availableRooms->count();
                return $roomType;
            })->filter(function($roomType) {
                return $roomType->available_count > 0;
            });
        }
        
        return view('booking.rooms.index', compact('roomTypes'));
    }

    public function publicCreate()
    {
        $roomTypes = RoomType::with(['rooms' => function($query) {
            $query->where('status', 'available');
        }])->get();
        
        return view('booking.rooms.create', compact('roomTypes'));
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:1000',
            // Guest information fields - required only if not logged in
            'guest_name' => 'required_if:user_id,null|string|max:255',
            'guest_email' => 'required_if:user_id,null|email|max:255',
            'guest_phone' => 'required_if:user_id,null|string|max:20',
            'payment_method' => 'required|in:card,cash,online,bank_transfer',
            'payment_type' => 'required|in:full,deposit',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // Check if room is available for the selected dates
        $conflictingBooking = Booking::where('room_id', $request->room_id)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('check_in_date', '<=', $request->check_in_date)
                      ->where('check_out_date', '>', $request->check_in_date);
                })->orWhere(function($q) use ($request) {
                    $q->where('check_in_date', '<', $request->check_out_date)
                      ->where('check_out_date', '>=', $request->check_out_date);
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();

        if ($conflictingBooking) {
            return back()->with('error', 'Room is not available for the selected dates.')->withInput();
        }

        // Calculate total amount
        $nights = Carbon::parse($request->check_in_date)->diffInDays(Carbon::parse($request->check_out_date));
        $subTotal = $nights * $room->roomType->base_price;
        $discountAmount = 0;
        $couponId = null;

        // Apply Coupon
        if ($request->coupon_code) {
            $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                if (!$coupon->min_spend || $subTotal >= $coupon->min_spend) {
                    if ($coupon->type === 'fixed') {
                        $discountAmount = $coupon->value;
                    } else {
                        $discountAmount = $subTotal * ($coupon->value / 100);
                    }
                    // Cap discount at subtotal
                    $discountAmount = min($discountAmount, $subTotal);
                    $couponId = $coupon->id;
                    
                    // Increment usage
                    $coupon->increment('used');
                }
            }
        }

        $totalAmount = $subTotal - $discountAmount;

        // Determine user_id (null for guests)
        $userId = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null;

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'adults' => $request->adults,
                'children' => $request->children ?? 0,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'special_requests' => $request->special_requests,
                // Store guest information for non-logged in users
                'guest_name' => $userId ? null : $request->guest_name,
                'guest_email' => $userId ? null : $request->guest_email,
                'guest_phone' => $userId ? null : $request->guest_phone,
                'coupon_id' => $couponId,
            ]);

            // Calculate payment amount based on selection
            $paymentAmount = $totalAmount;
            if ($request->payment_type === 'deposit' && config('hotel.booking.require_deposit')) {
                $depositPercentage = config('hotel.booking.deposit_percentage', 20);
                $paymentAmount = $totalAmount * ($depositPercentage / 100);
            }

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $paymentAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'notes' => $request->payment_type === 'deposit' ? 'Deposit Payment (' . $depositPercentage . '%)' : 'Full Payment',
            ]);

            DB::commit();
            
            // If booking is auto-confirmed or user is logged in, send confirmation
            if ($booking->status === 'confirmed' && $booking->user) {
                $booking->user->notify(new \App\Notifications\BookingConfirmationNotification($booking));
            }
            
            $successMessage = \Illuminate\Support\Facades\Auth::check() 
                ? 'Booking request submitted successfully! We will confirm your booking shortly.'
                : 'Booking request submitted successfully! We will contact you at ' . $request->guest_email . ' to confirm your booking.';
            
            // Redirect to payment checkout if online payment is selected
            if (in_array($request->payment_method, ['card', 'online'])) {
                $payment = Payment::where('booking_id', $booking->id)->first();
                // Check if we can proceed with online payment
                if (!env('STRIPE_SECRET')) {
                     // Fallback for dev environment or misconfiguration
                     // Mark as pending but show confirmation
                     return redirect()->route('booking.confirmation', $booking)
                        ->with('success', $successMessage . ' (Payment system in test mode)');
                }
                return redirect()->route('payment.checkout', $payment);
            }

            return redirect()->route('booking.confirmation', $booking)->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create booking. Please try again.')->withInput();
        }
    }

    public function confirmation(Booking $booking)
    {
        // Simple security check: if guest, maybe rely on session or just show it (public enough?)
        // Ideally we check if auth user owns it, or if it was just created in session.
        // For now, we'll just show it.
        $booking->load(['room.roomType']);
        return view('booking.confirmation', compact('booking'));
    }
}
