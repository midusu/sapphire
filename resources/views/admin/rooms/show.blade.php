@extends('admin.layout')

@section('title', 'Room Details - Sapphire Hotel Management')
@section('header', 'Room Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Room Header with Image -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Room {{ $room->room_number }}</h2>
                <p class="text-gray-600">{{ $room->roomType->name }} - Floor {{ $room->floor }}</p>
            </div>
            <div class="flex justify-end">
                <img src="{{ asset('images/rooms/' . strtolower(str_replace(' ', '-', $room->roomType->name)) . '.jpg') }}" 
                     alt="{{ $room->roomType->name }}" 
                     class="w-full h-48 object-cover rounded-lg shadow-md"
                     onerror="this.src='{{ asset('images/rooms/standard-room.jpg') }}'">
            </div>
        </div>
            <div class="flex space-x-2">
                @if($room->status != 'occupied')
                    <a href="{{ route('admin.rooms.edit', $room) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit Room
                    </a>
                @endif
                
                <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="inline">
                    @csrf
                    <select name="status" class="border rounded px-3 py-2 mr-2" onchange="this.form.submit()">
                        <option value="{{ $room->status }}" disabled>Change Status</option>
                        <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="cleaning" {{ $room->status == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                        <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </form>
                
                <a href="{{ route('admin.rooms.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Room Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Room Number</span>
                    <p class="font-medium">{{ $room->room_number }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Floor</span>
                    <p class="font-medium">{{ $room->floor }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Room Type</span>
                    <p class="font-medium">{{ $room->roomType->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Base Rate</span>
                    <p class="font-medium text-green-600">${{ number_format($room->roomType->base_price, 2) }}/night</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Max Occupancy</span>
                    <p class="font-medium">{{ $room->roomType->max_occupancy }} guests</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Current Status</span>
                    <p class="font-medium">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($room->status == 'available') bg-green-100 text-green-800
                            @elseif($room->status == 'occupied') bg-red-100 text-red-800
                            @elseif($room->status == 'cleaning') bg-yellow-100 text-yellow-800
                            @else bg-orange-100 text-orange-800
                            @endif">
                            {{ ucfirst($room->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Guest (if occupied) -->
        @if($room->currentBooking)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Guest</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Guest Name</span>
                        <p class="font-medium">{{ $room->currentBooking->user->name ?? 'Unknown Guest' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Email</span>
                        <p class="font-medium">{{ $room->currentBooking->user->email ?? 'N/A' }}</p>
                    </div>
                    @if(optional($room->currentBooking->user)->phone)
                        <div>
                            <span class="text-sm text-gray-500">Phone</span>
                            <p class="font-medium">{{ $room->currentBooking->user->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-500">Check-in Date</span>
                        <p class="font-medium">{{ $room->currentBooking->check_in_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Check-out Date</span>
                        <p class="font-medium">{{ $room->currentBooking->check_out_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Guests</span>
                        <p class="font-medium">{{ $room->currentBooking->adults }} Adults {{ $room->currentBooking->children ? '• ' . $room->currentBooking->children . ' Children' : '' }}</p>
                    </div>
                    <div class="pt-3 border-t">
                        <a href="{{ route('admin.bookings.show', $room->currentBooking) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                            <i class="fas fa-eye mr-1"></i>View Booking
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Room Features -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Features</h3>
            <div class="space-y-3">
                @if($room->roomType->description)
                    <div>
                        <span class="text-sm text-gray-500">Description</span>
                        <p class="text-gray-700">{{ $room->roomType->description }}</p>
                    </div>
                @endif
                
                @if($room->roomType->amenities)
                    <div>
                        <span class="text-sm text-gray-500">Amenities</span>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach(json_decode($room->roomType->amenities) as $amenity)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                    {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($room->notes)
                    <div>
                        <span class="text-sm text-gray-500">Room Notes</span>
                        <p class="text-gray-700">{{ $room->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Bookings</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($room->bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->user->name ?? 'Unknown Guest' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->check_in_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->check_out_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                                    @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No bookings found for this room
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Room Status History -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Status History</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="bg-blue-100 rounded-full p-2 mt-1">
                    <i class="fas fa-door-open text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Room Created</p>
                    <p class="text-sm text-gray-600">{{ $room->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            
            @if($room->updated_at != $room->created_at)
                <div class="flex items-start space-x-3">
                    <div class="bg-gray-100 rounded-full p-2 mt-1">
                        <i class="fas fa-edit text-gray-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Last Updated</p>
                        <p class="text-sm text-gray-600">{{ $room->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($room->currentBooking)
                <div class="flex items-start space-x-3">
                    <div class="bg-red-100 rounded-full p-2 mt-1">
                        <i class="fas fa-user text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Currently Occupied</p>
                        <p class="text-sm text-gray-600">Since {{ $room->currentBooking->check_in_date->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
