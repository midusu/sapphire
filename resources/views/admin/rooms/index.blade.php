@extends('admin.layout')

@section('title', 'Rooms - Sapphire Hotel Management')
@section('header', 'Room Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Room Management</h2>
    <div class="space-x-2">
        <a href="{{ route('admin.rooms.floor-plan') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-map mr-2"></i>Floor Plan
        </a>
        <a href="{{ route('admin.rooms.availability') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-calendar-check mr-2"></i>Availability
        </a>
        <a href="{{ route('admin.rooms.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Room
        </a>
    </div>
</div>

<!-- Room Statistics -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Rooms</p>
                <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Room::count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-door-open text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Available</p>
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Room::where('status', 'available')->count() }}</p>
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
                <p class="text-2xl font-bold text-red-600">{{ \App\Models\Room::where('status', 'occupied')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-user text-red-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Cleaning</p>
                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Room::where('status', 'cleaning')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-broom text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Maintenance</p>
                <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Room::where('status', 'maintenance')->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-tools text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
            <option value="cleaning" {{ request('status') == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
        </select>
        
        <select name="room_type" class="border rounded px-3 py-2">
            <option value="">All Room Types</option>
            @foreach(\App\Models\RoomType::all() as $roomType)
                <option value="{{ $roomType->id }}" {{ request('room_type') == $roomType->id ? 'selected' : '' }}>
                    {{ $roomType->name }}
                </option>
            @endforeach
        </select>
        
        <select name="floor" class="border rounded px-3 py-2">
            <option value="">All Floors</option>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" {{ request('floor') == $i ? 'selected' : '' }}>Floor {{ $i }}</option>
            @endfor
        </select>
        
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        
        <a href="{{ route('admin.rooms.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-times"></i> Clear
        </a>
    </form>
</div>

<!-- Rooms Grid -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-6">
        @forelse($rooms as $room)
            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Room Header -->
                <div class="p-4 bg-gradient-to-r 
                    @if($room->status == 'available') from-green-500 to-green-600
                    @elseif($room->status == 'occupied') from-red-500 to-red-600
                    @elseif($room->status == 'cleaning') from-yellow-500 to-yellow-600
                    @else from-orange-500 to-orange-600
                    @endif text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold">{{ $room->room_number }}</h3>
                            <p class="text-sm opacity-90">{{ $room->roomType->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-white bg-opacity-20">
                            {{ ucfirst($room->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Room Body -->
                <div class="p-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Floor:</span>
                            <span class="font-medium">{{ $room->floor }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ $room->roomType->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rate:</span>
                            <span class="font-medium">${{ number_format($room->roomType->base_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Capacity:</span>
                            <span class="font-medium">{{ $room->roomType->max_occupancy }} guests</span>
                        </div>
                    </div>
                    
                    @if($room->currentBooking)
                        <div class="mt-3 p-2 bg-blue-50 rounded text-xs">
                            <div class="font-medium text-blue-800">Occupied by:</div>
                            <div class="text-blue-600">{{ $room->currentBooking->user->name }}</div>
                            <div class="text-blue-600">Until: {{ $room->currentBooking->check_out_date->format('M d') }}</div>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 flex space-x-2">
                        <a href="{{ route('admin.rooms.show', $room) }}" class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition text-center">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        
                        @if($room->status != 'occupied')
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="flex-1 bg-gray-600 text-white px-2 py-1 rounded text-sm hover:bg-gray-700 transition text-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-door-open text-4xl mb-4"></i>
                <p>No rooms found</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
        <div class="flex-1 flex justify-between sm:hidden">
            {{ $rooms->links() }}
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing 
                    <span class="font-medium">{{ $rooms->firstItem() }}</span>
                    to 
                    <span class="font-medium">{{ $rooms->lastItem() }}</span>
                    of 
                    <span class="font-medium">{{ $rooms->total() }}</span>
                    rooms
                </p>
            </div>
            <div>
                {{ $rooms->links() }}
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
@endsection
