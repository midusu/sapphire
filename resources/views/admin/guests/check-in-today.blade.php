@extends('admin.layout')

@section('title', 'Today\'s Check-ins - Sapphire Hotel Management')
@section('header', 'Today\'s Check-ins')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Today's Check-ins</h2>
        <p class="text-gray-600">Guests checking in today - {{ now()->format('F j, Y') }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.guests.check-out-today') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-sign-out-alt mr-2"></i>Check-outs
        </a>
        <a href="{{ route('admin.guests.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Guests
        </a>
    </div>
</div>

<!-- Check-in Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Check-ins</p>
                <p class="text-2xl font-bold text-green-600">{{ $guests->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-sign-in-alt text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">VIP Guests</p>
                <p class="text-2xl font-bold text-yellow-600">
                    {{ $guests->filter(function($guest) {
                        $totalBookings = $guest->bookings->count();
                        $totalSpent = $guest->payments->where('status', 'completed')->sum('amount');
                        return $totalBookings >= 10 || $totalSpent >= 5000;
                    })->count() }}
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-star text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">New Guests</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ $guests->filter(function($guest) {
                        return $guest->bookings->count() == 1;
                    })->count() }}
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-user-plus text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Expected Revenue</p>
                <p class="text-2xl font-bold text-purple-600">
                    ${{ number_format($guests->sum(function($guest) {
                        return $guest->bookings->where('status', 'confirmed')->sum('total_amount');
                    }), 0) }}
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Timeline -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-in Timeline</h3>
    <div class="space-y-4">
        @forelse($guests as $guest)
            @foreach($guest->bookings->where('status', 'confirmed') as $booking)
                <div class="flex items-start space-x-4 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                            {{ $booking->check_in_date->format('H') }}
                        </div>
                        <div class="text-xs text-center text-gray-600 mt-1">
                            {{ $booking->check_in_date->format('H:i') }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                                <p class="text-sm text-gray-600">Room {{ $booking->room->room_number }} • {{ $booking->room->roomType->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $booking->adults }} Adults {{ $booking->children ? '• ' . $booking->children . ' Children' : '' }}
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                @php
                                    $totalBookings = $guest->bookings->count();
                                    $totalSpent = $guest->payments->where('status', 'completed')->sum('amount');
                                    $loyaltyStatus = 'bronze';
                                    if ($totalBookings >= 20 || $totalSpent >= 10000) {
                                        $loyaltyStatus = 'platinum';
                                    } elseif ($totalBookings >= 10 || $totalSpent >= 5000) {
                                        $loyaltyStatus = 'gold';
                                    } elseif ($totalBookings >= 5 || $totalSpent >= 2000) {
                                        $loyaltyStatus = 'silver';
                                    }
                                @endphp
                                @if($loyaltyStatus == 'gold' || $loyaltyStatus == 'platinum')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">
                                        <i class="fas fa-star mr-1"></i>VIP
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                    Confirmed
                                </span>
                            </div>
                        </div>
                        
                        <!-- Guest Information -->
                        <div class="mt-3 flex space-x-4 text-sm text-gray-600">
                            <span><i class="fas fa-envelope mr-1"></i>{{ $guest->email }}</span>
                            @if($guest->phone)
                                <span><i class="fas fa-phone mr-1"></i>{{ $guest->phone }}</span>
                            @endif
                            <span><i class="fas fa-calendar mr-1"></i>{{ $booking->check_out_date->format('M d') }} checkout</span>
                            <span><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                        
                        <!-- Special Requests -->
                        @if($booking->special_requests)
                            <div class="mt-3 p-2 bg-yellow-100 rounded text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Special Requests:</strong> {{ $booking->special_requests }}
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                                <i class="fas fa-eye mr-1"></i>View Booking
                            </a>
                            <a href="{{ route('admin.guests.show', $guest) }}" class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition">
                                <i class="fas fa-user mr-1"></i>Guest Profile
                            </a>
                            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">
                                <i class="fas fa-check-circle mr-1"></i>Check-in Now
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-sign-in-alt text-4xl mb-4"></i>
                <p class="text-lg">No check-ins scheduled for today</p>
                <p class="text-sm mt-2">All guests are either already checked in or no arrivals are scheduled</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Check-in Preparation Checklist -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-in Preparation</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Room Preparation</h4>
            <div class="space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Verify room cleaning status</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Check room amenities</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Prepare welcome kit</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Set room keys/cards</span>
                </label>
            </div>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Guest Services</h4>
            <div class="space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Prepare guest registration</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Verify payment method</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Review special requests</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Notify housekeeping</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-bell mr-2"></i>Notify Front Desk
    </button>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-print mr-2"></i>Print Check-in List
    </button>
    <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-envelope mr-2"></i>Send Welcome Emails
    </button>
</div>
@endsection
