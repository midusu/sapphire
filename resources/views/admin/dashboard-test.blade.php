<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-blue-600 mb-8">Sapphire Hotel Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-hiking text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.bookings.create') }}" class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600 transition">
                    <i class="fas fa-plus text-2xl mb-2"></i>
                    <p class="text-sm">New Booking</p>
                </a>
                <a href="{{ route('admin.guests.create') }}" class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600 transition">
                    <i class="fas fa-user-plus text-2xl mb-2"></i>
                    <p class="text-sm">Add Guest</p>
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="bg-yellow-500 text-white p-4 rounded-lg text-center hover:bg-yellow-600 transition">
                    <i class="fas fa-bed text-2xl mb-2"></i>
                    <p class="text-sm">Manage Rooms</p>
                </a>
                <a href="{{ route('admin.activities.calendar') }}" class="bg-purple-500 text-white p-4 rounded-lg text-center hover:bg-purple-600 transition">
                    <i class="fas fa-calendar text-2xl mb-2"></i>
                    <p class="text-sm">Activities</p>
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Bookings</h2>
                @if($recentBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBookings as $booking)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">{{ $booking->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">${{ number_format($booking->total_amount, 2) }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->status }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No recent bookings</p>
                @endif
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity Bookings</h2>
                @if($recentActivityBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentActivityBookings as $activityBooking)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">{{ $activityBooking->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $activityBooking->activity->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">${{ number_format($activityBooking->total_price, 2) }}</p>
                                    <p class="text-sm text-gray-600">{{ $activityBooking->participants }} people</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No recent activity bookings</p>
                @endif
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('logout') }}" class="text-red-600 hover:text-red-800">Logout</a>
        </div>
    </div>
</body>
</html>
