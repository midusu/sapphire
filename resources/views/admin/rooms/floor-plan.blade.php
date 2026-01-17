@extends('admin.layout')

@section('title', 'Floor Plan - Sapphire Hotel Management')
@section('header', 'Floor Plan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Floor Plan</h2>
        <p class="text-gray-600">Visual overview of all rooms by floor</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.rooms.availability') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-calendar-check mr-2"></i>Check Availability
        </a>
        <a href="{{ route('admin.rooms.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-list mr-2"></i>Room List
        </a>
    </div>
</div>

<!-- Floor Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Floors</p>
                <p class="text-2xl font-bold text-gray-800">{{ $floors->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-building text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Rooms</p>
                <p class="text-2xl font-bold text-gray-800">{{ $rooms->count() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-door-open text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Available</p>
                <p class="text-2xl font-bold text-green-600">{{ $rooms->where('status', 'available')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Occupied</p>
                <p class="text-2xl font-bold text-red-600">{{ $rooms->where('status', 'occupied')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-user text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Floor Plan -->
<div class="space-y-8">
    @foreach($floors as $floorNumber => $floorRooms)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Floor {{ $floorNumber }}</h3>
                    <div class="flex space-x-4 text-sm">
                        <span class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                            Available ({{ $floorRooms->where('status', 'available')->count() }})
                        </span>
                        <span class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                            Occupied ({{ $floorRooms->where('status', 'occupied')->count() }})
                        </span>
                        <span class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></div>
                            Cleaning ({{ $floorRooms->where('status', 'cleaning')->count() }})
                        </span>
                        <span class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-1"></div>
                            Maintenance ({{ $floorRooms->where('status', 'maintenance')->count() }})
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Floor Layout Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($floorRooms->sortBy('room_number') as $room)
                        <div class="relative">
                            <a href="{{ route('admin.rooms.show', $room) }}" 
                               class="block p-4 border-2 rounded-lg transition-all hover:shadow-lg
                               @if($room->status == 'available') border-green-500 bg-green-50 hover:bg-green-100
                               @elseif($room->status == 'occupied') border-red-500 bg-red-50 hover:bg-red-100
                               @elseif($room->status == 'cleaning') border-yellow-500 bg-yellow-50 hover:bg-yellow-100
                               @else border-orange-500 bg-orange-50 hover:bg-orange-100
                               @endif">
                                <!-- Room Number -->
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-800">{{ $room->room_number }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ $room->roomType?->name ?? 'Unknown Type' }}</div>
                                    
                                    <!-- Status Indicator -->
                                    <div class="mt-2">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($room->status == 'available') bg-green-200 text-green-800
                                            @elseif($room->status == 'occupied') bg-red-200 text-red-800
                                            @elseif($room->status == 'cleaning') bg-yellow-200 text-yellow-800
                                            @else bg-orange-200 text-orange-800
                                            @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Current Guest Info (if occupied) -->
                                    @if($room->currentBooking)
                                        <div class="mt-2 text-xs text-gray-700">
                                            <div class="font-medium">{{ $room->currentBooking ? ($room->currentBooking->user ? $room->currentBooking->user->name : ($room->currentBooking->guest_name ?? 'Guest')) : 'N/A' }}</div>
                                            <div>Until {{ $room->currentBooking->check_out_date->format('M d') }}</div>
                                        </div>
                                    @endif
                                    
                                    <!-- Room Rate -->
                                    <div class="mt-2 text-xs font-medium text-green-600">
                                        ${{ number_format($room->roomType->base_price, 2) }}
                                    </div>
                                </div>
                            </a>
                            
                            <!-- Quick Actions Overlay -->
                            <div class="absolute top-2 right-2 opacity-0 hover:opacity-100 transition-opacity">
                                <div class="flex space-x-1">
                                    @if($room->status == 'available')
                                        <a href="{{ route('admin.bookings.create') }}?room_id={{ $room->id }}" 
                                           class="bg-green-600 text-white p-1 rounded text-xs hover:bg-green-700"
                                           title="Quick Booking">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="inline">
                                        @csrf
                                        <button type="submit" name="status" value="cleaning" 
                                                class="bg-yellow-600 text-white p-1 rounded text-xs hover:bg-yellow-700"
                                                title="Mark for Cleaning"
                                                @if($room->status == 'occupied') onclick="return confirm('Mark room for cleaning?')" @endif>
                                            <i class="fas fa-broom"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Floor Statistics -->
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800">{{ $floorRooms->count() }}</div>
                            <div class="text-gray-600">Total Rooms</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $floorRooms->where('status', 'available')->count() }}</div>
                            <div class="text-gray-600">Available</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $floorRooms->where('status', 'occupied')->count() }}</div>
                            <div class="text-gray-600">Occupied</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $floorRooms->count() > 0 ? round(($floorRooms->where('status', 'occupied')->count() / $floorRooms->count()) * 100) : 0 }}%
                            </div>
                            <div class="text-gray-600">Occupancy Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Legend -->
<div class="bg-white rounded-lg shadow p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Status Legend</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-6 h-6 bg-green-500 rounded"></div>
            <div>
                <div class="font-medium">Available</div>
                <div class="text-sm text-gray-600">Ready for booking</div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-6 h-6 bg-red-500 rounded"></div>
            <div>
                <div class="font-medium">Occupied</div>
                <div class="text-sm text-gray-600">Currently occupied</div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-6 h-6 bg-yellow-500 rounded"></div>
            <div>
                <div class="font-medium">Cleaning</div>
                <div class="text-sm text-gray-600">Being cleaned</div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-6 h-6 bg-orange-500 rounded"></div>
            <div>
                <div class="font-medium">Maintenance</div>
                <div class="text-sm text-gray-600">Under maintenance</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <a href="{{ route('admin.rooms.housekeeping') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
        <i class="fas fa-broom mr-2"></i>Housekeeping View
    </a>
    <a href="{{ route('admin.rooms.maintenance') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
        <i class="fas fa-tools mr-2"></i>Maintenance View
    </a>
</div>

<style>
/* Custom styles for hover effects */
.room-card {
    transition: all 0.3s ease;
}

.room-card:hover {
    transform: translateY(-2px);
}

.quick-actions {
    transition: opacity 0.3s ease;
}
</style>
@endsection
