@extends('admin.layout')

@section('title', 'Dashboard - Sapphire Hotel Management')
@section('header', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Bookings</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_bookings'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-calendar text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Active Guests</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['active_guests'] ?? 0 }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-users text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Available Rooms</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['available_rooms'] ?? 0 }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-bed text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Activity Bookings</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['activity_bookings'] ?? 0 }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-hiking text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Today's Revenue</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['today_revenue'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.bookings.create') }}" class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600 transition">
            <i class="fas fa-plus-circle text-2xl mb-2"></i>
            <p class="text-sm">New Booking</p>
        </a>
        <a href="{{ route('admin.bookings.index') }}?status=confirmed" class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600 transition">
            <i class="fas fa-check-circle text-2xl mb-2"></i>
            <p class="text-sm">Check-in</p>
        </a>
        <a href="{{ route('admin.activities.bookings.create') }}" class="bg-orange-500 text-white p-4 rounded-lg text-center hover:bg-orange-600 transition">
            <i class="fas fa-calendar-plus text-2xl mb-2"></i>
            <p class="text-sm">Activity Booking</p>
        </a>
        <a href="{{ route('admin.payments.create') }}" class="bg-purple-500 text-white p-4 rounded-lg text-center hover:bg-purple-600 transition">
                <i class="fas fa-file-invoice text-2xl mb-2"></i>
                <p class="text-sm">Record Payment</p>
            </a>
        <a href="{{ route('admin.rooms.create') }}" class="bg-indigo-500 text-white p-4 rounded-lg text-center hover:bg-indigo-600 transition">
            <i class="fas fa-bed text-2xl mb-2"></i>
            <p class="text-sm">Add Room</p>
        </a>
        <a href="{{ route('admin.guests.create') }}" class="bg-teal-500 text-white p-4 rounded-lg text-center hover:bg-teal-600 transition">
            <i class="fas fa-user-plus text-2xl mb-2"></i>
            <p class="text-sm">Add Guest</p>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Room Availability -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Availability by Type</h3>
        <div class="space-y-3">
            @foreach($availableRoomsByType as $roomType)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-sm font-medium text-gray-700">{{ $roomType->name }}</span>
                    <span class="text-sm text-gray-600">{{ $roomType->rooms_count }} available</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Activity Bookings Today -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Bookings Today</h3>
        <div class="space-y-3">
            @foreach($todayActivityBookings as $type => $bookings)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                    <span class="text-sm text-gray-600">{{ $bookings->count() }} bookings</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Room Statistics -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Overview</h3>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Room::count() }}</div>
            <div class="text-sm text-gray-600">Total Rooms</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Room::where('status', 'available')->count() }}</div>
            <div class="text-sm text-gray-600">Available</div>
        </div>
    </div>
    <div class="space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Occupied:</span>
            <span class="font-medium">{{ \App\Models\Room::where('status', 'occupied')->count() }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Cleaning:</span>
            <span class="font-medium">{{ \App\Models\Room::where('status', 'cleaning')->count() }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Maintenance:</span>
            <span class="font-medium">{{ \App\Models\Room::where('status', 'maintenance')->count() }}</span>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t">
        <div class="text-center">
            <div class="text-lg font-bold text-blue-600">
                {{ \App\Models\Room::count() > 0 ? round((\App\Models\Room::where('status', 'occupied')->count() / \App\Models\Room::count()) * 100) : 0 }}%
            </div>
            <div class="text-sm text-gray-600">Occupancy Rate</div>
        </div>
    </div>
</div>

<!-- Guest Statistics -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Overview</h3>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-gray-800">{{ \App\Models\User::where('role_id', 7)->count() }}</div>
            <div class="text-sm text-gray-600">Total Guests</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\User::where('role_id', 7)->whereMonth('created_at', now()->month)->count() }}</div>
            <div class="text-sm text-gray-600">New This Month</div>
        </div>
    </div>
    <div class="space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Active Guests:</span>
            <span class="font-medium">{{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function($query) { $query->whereIn('status', ['confirmed', 'checked_in']); })->count() }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Repeat Guests:</span>
            <span class="font-medium">{{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function($query) { $query->where('status', 'checked_out'); }, '>', 1)->count() }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">VIP Guests:</span>
            <span class="font-medium">{{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function($query) { $query->where('status', 'checked_out'); }, '>=', 10)->count() }}</span>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t">
        <div class="text-center">
            <a href="{{ route('admin.guests.check-in-today') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-sign-in-alt mr-1"></i>Today's Check-ins →
            </a>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Bookings</h3>
    <div class="space-y-3">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentBookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->user ? $booking->user->name : ($booking->guest_name ?? 'Guest') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->room->room_number }} ({{ $booking->room->roomType->name }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->check_in_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->check_out_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                                @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Activity Bookings -->
<div class="bg-white rounded-lg shadow p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity Bookings</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentActivityBookings as $activityBooking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activityBooking->user ? $activityBooking->user->name : 'Guest' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activityBooking->activity->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activityBooking->scheduled_time->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activityBooking->participants }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($activityBooking->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($activityBooking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($activityBooking->status == 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($activityBooking->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Notifications Section -->
@if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden mt-6">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Recent Notifications</h3>
        <a href="{{ route('admin.notifications.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            View All →
        </a>
    </div>
    <div class="divide-y divide-gray-200">
        @foreach($unreadNotifications as $notification)
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($notification->type == 'booking_confirmation') bg-green-100 text-green-800
                                @elseif($notification->type == 'payment_alert') bg-blue-100 text-blue-800
                                @elseif($notification->type == 'activity_reminder') bg-purple-100 text-purple-800
                                @elseif($notification->type == 'housekeeping_alert') bg-yellow-100 text-yellow-800
                                @elseif($notification->type == 'low_inventory') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                            </span>
                            <h4 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h4>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
