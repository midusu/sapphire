<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicWebsiteController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::with(['rooms' => function($query) {
            $query->where('status', 'available');
        }])->get();
        
        $activities = Activity::where('is_active', true)->get();
        $featuredGallery = Gallery::active()->featured()->orderBy('display_order')->take(6)->get();
        $featuredAmenities = Amenity::active()->featured()->orderBy('display_order')->take(6)->get();
        
        return view('public.home', compact('roomTypes', 'activities', 'featuredGallery', 'featuredAmenities'));
    }

    public function gallery()
    {
        $galleries = Gallery::active()->orderBy('display_order')->get()->groupBy('category');
        
        return view('public.gallery', compact('galleries'));
    }

    public function amenities()
    {
        $amenities = Amenity::active()->orderBy('display_order')->get()->groupBy('category');
        
        return view('public.amenities', compact('amenities'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Get all rooms
        $allRooms = Room::with('roomType')->get();
        
        // Get conflicting bookings
        $conflictingBookings = Booking::where(function($query) use ($checkIn, $checkOut) {
            $query->where(function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<=', $checkIn)
                  ->where('check_out_date', '>', $checkIn);
            })->orWhere(function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>=', $checkOut);
            })->orWhere(function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '>=', $checkIn)
                  ->where('check_out_date', '<=', $checkOut);
            });
        })
        ->whereIn('status', ['confirmed', 'checked_in'])
        ->pluck('room_id');

        // Filter available rooms
        $availableRooms = $allRooms->reject(function($room) use ($conflictingBookings) {
            return $conflictingBookings->contains($room->id) || $room->status !== 'available';
        });

        // Group by room type
        $availableByType = $availableRooms->groupBy('room_type_id')->map(function($rooms) {
            return [
                'count' => $rooms->count(),
                'room_type' => $rooms->first()->roomType,
                'rooms' => $rooms,
            ];
        });

        return response()->json([
            'available' => $availableByType->map(function($group) {
                return [
                    'room_type_id' => $group['room_type']->id,
                    'room_type_name' => $group['room_type']->name,
                    'available_count' => $group['count'],
                    'base_price' => $group['room_type']->base_price,
                ];
            })->values(),
        ]);
    }
}
