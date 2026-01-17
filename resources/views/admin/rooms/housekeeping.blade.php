@extends('admin.layout')

@section('title', 'Housekeeping - Sapphire Hotel Management')
@section('header', 'Housekeeping Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Housekeeping</h2>
        <p class="text-gray-600">Manage room cleaning and maintenance</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.rooms.maintenance') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-tools mr-2"></i>Maintenance
        </a>
        <a href="{{ route('admin.rooms.floor-plan') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-map mr-2"></i>Floor Plan
        </a>
        <a href="{{ route('admin.rooms.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
        </a>
    </div>
</div>

<!-- Housekeeping Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Needs Cleaning</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $rooms->where('status', 'cleaning')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-broom text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Occupied Rooms</p>
                <p class="text-2xl font-bold text-red-600">{{ $rooms->where('status', 'occupied')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-user text-red-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Ready for Cleaning</p>
                <p class="text-2xl font-bold text-orange-600">
                    {{ $rooms->where('status', 'occupied')->filter(function($room) {
                        return $room->currentBooking && $room->currentBooking->check_out_date && \Carbon\Carbon::parse($room->currentBooking->check_out_date)->isToday();
                    })->count() }}
                </p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-clock text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Tasks</p>
                <p class="text-2xl font-bold text-blue-600">{{ $rooms->where('status', 'cleaning')->count() + $rooms->where('status', 'occupied')->filter(function($room) { return $room->currentBooking && $room->currentBooking->check_out_date && \Carbon\Carbon::parse($room->currentBooking->check_out_date)->isToday(); })->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-tasks text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Housekeeping Tasks -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Housekeeping Tasks</h3>
    </div>
    
    <!-- Rooms Needing Cleaning -->
    @if($rooms->where('status', 'cleaning')->count() > 0)
        <div class="p-6 bg-yellow-50 border-b">
            <h4 class="font-medium text-yellow-800 mb-4">
                <i class="fas fa-broom mr-2"></i>Rooms Needing Cleaning ({{ $rooms->where('status', 'cleaning')->count() }})
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rooms->where('status', 'cleaning') as $room)
                    <div class="bg-white border border-yellow-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="font-bold text-gray-800">{{ $room->room_number }}</h5>
                                <p class="text-sm text-gray-600">{{ $room->roomType->name }} - Floor {{ $room->floor }}</p>
                            </div>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                Cleaning
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Room Type:</span>
                                <span class="font-medium">{{ $room->roomType->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Guest:</span>
                                <span class="font-medium">
                                    @if($lastBooking = $room->bookings()->where('status', 'checked_out')->latest()->first())
                                        {{ $lastBooking->user ? $lastBooking->user->name : ($lastBooking->guest_name ?? 'Guest') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="flex-1">
                                @csrf
                                <button type="submit" name="status" value="available" 
                                        class="w-full bg-green-600 text-white px-2 py-1 rounded text-sm hover:bg-green-700 transition">
                                    <i class="fas fa-check mr-1"></i>Mark Clean
                                </button>
                            </form>
                            <a href="{{ route('admin.rooms.show', $room) }}" 
                               class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition text-center">
                                <i class="fas fa-eye mr-1"></i>Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Rooms Ready for Cleaning (Check-out Today) -->
    @php
        $checkOutToday = $rooms->where('status', 'occupied')->filter(function($room) {
            return $room->currentBooking && $room->currentBooking->check_out_date && \Carbon\Carbon::parse($room->currentBooking->check_out_date)->isToday();
        });
    @endphp
    
    @if($checkOutToday->count() > 0)
        <div class="p-6 bg-orange-50 border-b">
            <h4 class="font-medium text-orange-800 mb-4">
                <i class="fas fa-clock mr-2"></i>Check-out Today ({{ $checkOutToday->count() }})
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($checkOutToday as $room)
                    <div class="bg-white border border-orange-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="font-bold text-gray-800">{{ $room->room_number }}</h5>
                                <p class="text-sm text-gray-600">{{ $room->roomType->name }} - Floor {{ $room->floor }}</p>
                            </div>
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                Check-out Today
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Guest:</span>
                                <span class="font-medium">{{ $room->currentBooking ? ($room->currentBooking->user ? $room->currentBooking->user->name : ($room->currentBooking->guest_name ?? 'Guest')) : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-out Time:</span>
                                <span class="font-medium">{{ $room->currentBooking ? \Carbon\Carbon::parse($room->currentBooking->check_out_date)->format('H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="flex-1">
                                @csrf
                                <button type="submit" name="status" value="cleaning" 
                                        class="w-full bg-yellow-600 text-white px-2 py-1 rounded text-sm hover:bg-yellow-700 transition">
                                    <i class="fas fa-broom mr-1"></i>Start Cleaning
                                </button>
                            </form>
                            @if($room->currentBooking)
                            <a href="{{ route('admin.bookings.show', $room->currentBooking) }}" 
                               class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition text-center">
                                <i class="fas fa-eye mr-1"></i>Booking
                            </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Currently Occupied Rooms -->
    @if($rooms->where('status', 'occupied')->count() > 0)
        <div class="p-6">
            <h4 class="font-medium text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Currently Occupied ({{ $rooms->where('status', 'occupied')->count() }})
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rooms->where('status', 'occupied') as $room)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="font-bold text-gray-800">{{ $room->room_number }}</h5>
                                <p class="text-sm text-gray-600">{{ $room->roomType->name }} - Floor {{ $room->floor }}</p>
                            </div>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                Occupied
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Guest:</span>
                                <span class="font-medium">{{ $room->currentBooking ? ($room->currentBooking->user ? $room->currentBooking->user->name : ($room->currentBooking->guest_name ?? 'Guest')) : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-out:</span>
                                <span class="font-medium">{{ $room->currentBooking ? \Carbon\Carbon::parse($room->currentBooking->check_out_date)->format('M d') : 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            @if($room->currentBooking && \Carbon\Carbon::parse($room->currentBooking->check_out_date)->isToday())
                                <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" name="status" value="cleaning" 
                                            class="w-full bg-yellow-600 text-white px-2 py-1 rounded text-sm hover:bg-yellow-700 transition"
                                            onclick="return confirm('Mark this room for cleaning? Guest is checking out today.')">
                                        <i class="fas fa-broom mr-1"></i>Ready for Clean
                                    </button>
                                </form>
                            @endif
                            @if($room->currentBooking)
                            <a href="{{ route('admin.bookings.show', $room->currentBooking) }}" 
                               class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition text-center">
                                <i class="fas fa-eye mr-1"></i>Booking
                            </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- No Tasks -->
    @if($rooms->where('status', 'cleaning')->count() == 0 && $checkOutToday->count() == 0)
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-broom text-4xl mb-4"></i>
            <p class="text-lg">No housekeeping tasks at the moment</p>
            <p class="text-sm mt-2">All rooms are either occupied or ready for guests</p>
        </div>
    @endif
</div>

<!-- Housekeeping Schedule -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Schedule</h3>
    <div class="space-y-4">
        <div class="flex items-center space-x-3">
            <div class="bg-yellow-100 rounded-full p-2">
                <i class="fas fa-sun text-yellow-600"></i>
            </div>
            <div>
                <p class="font-medium">Morning (9:00 AM - 12:00 PM)</p>
                <p class="text-sm text-gray-600">Focus on check-outs from yesterday and priority rooms</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-blue-100 rounded-full p-2">
                <i class="fas fa-cloud-sun text-blue-600"></i>
            </div>
            <div>
                <p class="font-medium">Afternoon (12:00 PM - 4:00 PM)</p>
                <p class="text-sm text-gray-600">Handle today's check-outs and routine cleaning</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-purple-100 rounded-full p-2">
                <i class="fas fa-moon text-purple-600"></i>
            </div>
            <div>
                <p class="font-medium">Evening (4:00 PM - 8:00 PM)</p>
                <p class="text-sm text-gray-600">Final touch-ups and prepare for tomorrow</p>
            </div>
        </div>
    </div>
</div>
@endsection
