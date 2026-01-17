@extends('admin.layout')

@section('title', 'Room Availability - Sapphire Hotel Management')
@section('header', 'Room Availability')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Room Availability</h2>
    <p class="text-gray-600">Check room availability for specific dates</p>
    <div class="mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded">
    <strong>DEBUG INFO:</strong><br>
    Request room_type_id: "{{ request('room_type_id') }}"<br>
    Total rooms found: {{ $rooms->count() }}<br>
    @if(request('room_type_id'))
        <strong>Filter Applied:</strong> {{ $roomTypes->where('id', request('room_type_id'))->first()?->name ?? 'Unknown' }} - Showing {{ $rooms->count() }} rooms
    @endif
</div>
</div>

<!-- Room Type Filter -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter by Room Type</h3>
    <div class="flex flex-wrap gap-2">
        <a href="/admin/rooms/availability" class="px-4 py-2 rounded-lg border {{ !request('room_type_id') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            All Rooms (62)
        </a>
        <a href="/admin/rooms/availability?room_type_id=1" class="px-4 py-2 rounded-lg border {{ request('room_type_id') == '1' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            Standard Room (28)
        </a>
        <a href="/admin/rooms/availability?room_type_id=2" class="px-4 py-2 rounded-lg border {{ request('room_type_id') == '2' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            Deluxe Room (25)
        </a>
        <a href="/admin/rooms/availability?room_type_id=3" class="px-4 py-2 rounded-lg border {{ request('room_type_id') == '3' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            Suite (5)
        </a>
        <a href="/admin/rooms/availability?room_type_id=4" class="px-4 py-2 rounded-lg border {{ request('room_type_id') == '4' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            Presidential Suite (4)
        </a>
    </div>
</div>

<!-- Availability Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Rooms</p>
                <p class="text-2xl font-bold text-gray-800">{{ $rooms->count() }}</p>
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
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Unavailable</p>
                <p class="text-2xl font-bold text-orange-600">{{ $rooms->whereIn('status', ['maintenance', 'cleaning'])->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-tools text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Room Availability Grid -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">
            Availability for {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
        </h3>
    </div>
    
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
                            <p class="text-sm opacity-90">{{ $room->roomType?->name ?? 'Unknown Type' }}</p>
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
                            <span class="text-gray-600">Rate:</span>
                            <span class="font-medium">${{ number_format($room->roomType->base_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Capacity:</span>
                            <span class="font-medium">{{ $room->roomType->max_occupancy }} guests</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 flex space-x-2">
                        <a href="{{ route('admin.bookings.create') }}?room_id={{ $room->id }}&check_in={{ $startDate->format('Y-m-d') }}&check_out={{ $endDate->format('Y-m-d') }}" 
                           class="flex-1 bg-green-600 text-white px-2 py-1 rounded text-sm hover:bg-green-700 transition text-center">
                            <i class="fas fa-plus mr-1"></i>Book
                        </a>
                        
                        <a href="{{ route('admin.rooms.show', $room) }}" class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition text-center">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-search text-4xl mb-4"></i>
                <p>No rooms found for the selected criteria</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <a href="{{ route('admin.rooms.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
    </a>
    <a href="{{ route('admin.rooms.floor-plan') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-map mr-2"></i>View Floor Plan
    </a>
</div>

<script>
// Set minimum date to today
document.querySelector('input[name="start_date"]').min = new Date().toISOString().split('T')[0];
document.querySelector('input[name="end_date"]').min = new Date().toISOString().split('T')[0];

// Auto-update end date when start date changes
document.querySelector('input[name="start_date"]').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateField = document.querySelector('input[name="end_date"]');
    
    // Set end date to start date + 1 day minimum
    const minEndDate = new Date(startDate);
    minEndDate.setDate(minEndDate.getDate() + 1);
    
    endDateField.min = minEndDate.toISOString().split('T')[0];
    
    // If current end date is before new minimum, update it
    if (new Date(endDateField.value) < minEndDate) {
        endDateField.value = minEndDate.toISOString().split('T')[0];
    }
});
</script>
@endsection
