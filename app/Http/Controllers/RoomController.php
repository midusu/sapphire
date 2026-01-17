<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['roomType', 'currentBooking'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->paginate(20);
            
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'notes' => 'nullable|string|max:1000',
        ]);

        Room::create($request->all());
        
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully!');
    }

    public function show(Room $room)
    {
        $room->load(['roomType', 'currentBooking.user', 'bookings' => function($query) {
            $query->orderBy('check_in_date', 'desc')->limit(10);
        }]);
        
        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'notes' => 'nullable|string|max:1000',
        ]);

        $room->update($request->all());
        
        return redirect()->route('admin.rooms.show', $room)
            ->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        // Check if room has active bookings
        $activeBookings = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();
            
        if ($activeBookings > 0) {
            return back()->with('error', 'Cannot delete room with active bookings.');
        }

        $room->delete();
        
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully!');
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance,cleaning',
        ]);

        $oldStatus = $room->status;
        $room->update(['status' => $request->status]);
        
        // Send housekeeping alerts for cleaning and maintenance
        if (in_array($request->status, ['cleaning', 'maintenance'])) {
            $housekeepingUsers = \App\Models\User::whereHas('role', function($query) {
                $query->where('slug', 'housekeeping');
            })->get();
            
            foreach ($housekeepingUsers as $user) {
                $user->notify(new \App\Notifications\HousekeepingAlertNotification($room, $request->status));
            }
        }
        
        // Log the status change (using Laravel's built-in logging)
        \Log::info('Room status changed', [
            'room_id' => $room->id,
            'room_number' => $room->room_number,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Room status updated successfully!');
    }

    public function availability()
    {
        $startDate = request('start_date', Carbon::today());
        $endDate = request('end_date', Carbon::today()->copy()->addDays(30));
        $roomTypeId = request('room_type_id');
        
        // Simple test - if room_type_id is provided, use it, otherwise show all
        if ($roomTypeId) {
            $rooms = Room::with(['roomType'])
                ->where('status', 'available')
                ->where('room_type_id', $roomTypeId)
                ->orderBy('floor')
                ->orderBy('room_number')
                ->get();
        } else {
            $rooms = Room::with(['roomType'])
                ->where('status', 'available')
                ->orderBy('floor')
                ->orderBy('room_number')
                ->get();
        }
        
        $roomTypes = RoomType::selectRaw('MIN(id) as id, name')
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        return view('admin.rooms.availability', compact('rooms', 'roomTypes', 'startDate', 'endDate'));
    }

    public function housekeeping()
    {
        $rooms = Room::with(['roomType', 'currentBooking'])
            ->whereIn('status', ['cleaning', 'occupied'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('admin.rooms.housekeeping', compact('rooms'));
    }

    public function maintenance()
    {
        $rooms = Room::with(['roomType'])
            ->where('status', 'maintenance')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('admin.rooms.maintenance', compact('rooms'));
    }

    public function floorPlan()
    {
        $rooms = Room::with(['roomType', 'currentBooking'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        $floors = $rooms->groupBy('floor');

        return view('admin.rooms.floor-plan', compact('rooms', 'floors'));
    }

    public function getAvailableRooms(Request $request)
    {
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $roomTypeId = $request->room_type_id;

        $availableRooms = Room::where('status', 'available')
            ->where('room_type_id', $roomTypeId)
            ->whereDoesntHave('bookings', function($query) use ($checkIn, $checkOut) {
                $query->whereIn('status', ['confirmed', 'checked_in'])
                    ->where(function($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function($q) use ($checkIn, $checkOut) {
                              $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                          });
                    });
            })
            ->with('roomType')
            ->get();

        return response()->json($availableRooms);
    }

    public function statistics()
    {
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'cleaning_rooms' => Room::where('status', 'cleaning')->count(),
            'occupancy_rate' => Room::count() > 0 ? (Room::where('status', 'occupied')->count() / Room::count() * 100) : 0,
            'by_type' => Room::with('roomType')
                ->get()
                ->groupBy('roomType.name')
                ->map(function($rooms) {
                    return [
                        'total' => $rooms->count(),
                        'available' => $rooms->where('status', 'available')->count(),
                        'occupied' => $rooms->where('status', 'occupied')->count(),
                    ];
                }),
            'by_floor' => Room::all()
                ->groupBy('floor')
                ->map(function($rooms) {
                    return [
                        'total' => $rooms->count(),
                        'available' => $rooms->where('status', 'available')->count(),
                        'occupied' => $rooms->where('status', 'occupied')->count(),
                    ];
                }),
        ];

        return response()->json($stats);
    }
}
