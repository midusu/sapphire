@extends('admin.layout')

@section('title', 'Today\'s Check-outs - Sapphire Hotel Management')
@section('header', 'Today\'s Check-outs')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Today's Check-outs</h2>
        <p class="text-gray-600">Guests checking out today - {{ now()->format('F j, Y') }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.guests.check-in-today') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-sign-in-alt mr-2"></i>Check-ins
        </a>
        <a href="{{ route('admin.guests.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Guests
        </a>
    </div>
</div>

<!-- Check-out Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Check-outs</p>
                <p class="text-2xl font-bold text-orange-600">{{ $guests->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-sign-out-alt text-orange-600"></i>
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
                <p class="text-sm text-gray-600">Already Checked Out</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ $guests->filter(function($guest) {
                        return $guest->bookings->where('status', 'checked_out')->count() > 0;
                    })->count() }}
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pending Check-outs</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ $guests->filter(function($guest) {
                        return $guest->bookings->where('status', 'confirmed')->count() > 0;
                    })->count() }}
                </p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-clock text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Check-out Timeline -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-out Schedule</h3>
    <div class="space-y-4">
        @forelse($guests as $guest)
            @foreach($guest->bookings->whereIn('status', ['confirmed', 'checked_in', 'checked_out']) as $booking)
                <div class="flex items-start space-x-4 p-4 
                    @if($booking->status == 'checked_out') bg-green-50 hover:bg-green-100
                    @else bg-orange-50 hover:bg-orange-100
                    @endif rounded-lg transition">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full 
                            @if($booking->status == 'checked_out') bg-green-600
                            @else bg-orange-600
                            @endif flex items-center justify-center text-white font-bold">
                            {{ $booking->check_out_date->format('H') }}
                        </div>
                        <div class="text-xs text-center text-gray-600 mt-1">
                            {{ $booking->check_out_date->format('H:i') }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                                <p class="text-sm text-gray-600">Room {{ $booking->room->room_number }} • {{ $booking->room->roomType->name }}</p>
                                <p class="text-sm text-gray-600">
                                    Stayed {{ $booking->check_in_date->diffInDays($booking->check_out_date) }} nights
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
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($booking->status == 'checked_out') bg-green-100 text-green-800
                                    @else bg-orange-100 text-orange-800
                                    @endif font-semibold">
                                    @if($booking->status == 'checked_out')
                                        <i class="fas fa-check mr-1"></i>Checked Out
                                    @else
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <!-- Guest Information -->
                        <div class="mt-3 flex space-x-4 text-sm text-gray-600">
                            <span><i class="fas fa-envelope mr-1"></i>{{ $guest->email }}</span>
                            @if($guest->phone)
                                <span><i class="fas fa-phone mr-1"></i>{{ $guest->phone }}</span>
                            @endif
                            <span><i class="fas fa-calendar mr-1"></i>{{ $booking->check_in_date->format('M d') }} check-in</span>
                            <span><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                        
                        <!-- Payment Status -->
                        <div class="mt-3">
                            @php
                                $paidAmount = $booking->payments->where('status', 'completed')->sum('amount');
                                $isFullyPaid = $paidAmount >= $booking->total_amount;
                            @endphp
                            @if($isFullyPaid)
                                <div class="text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>Fully paid (${{ number_format($paidAmount, 2) }})
                                </div>
                            @else
                                <div class="text-sm text-orange-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Outstanding balance: ${{ number_format($booking->total_amount - $paidAmount, 2) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Check-out Details -->
                        @if($booking->status == 'checked_out')
                            <div class="mt-3 p-2 bg-green-100 rounded text-sm text-green-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Checked out at {{ $booking->updated_at->format('H:i') }}
                            </div>
                        @else
                            <!-- Late Check-out Requests -->
                            <div class="mt-3 p-2 bg-yellow-100 rounded text-sm text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Standard check-out time: 11:00 AM
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
                            @if($booking->status != 'checked_out')
                                @if(!$isFullyPaid)
                                    <a href="{{ route('admin.payments.create') }}?booking_id={{ $booking->id }}" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                        <i class="fas fa-credit-card mr-1"></i>Collect Payment
                                    </a>
                                @endif
                                <button class="bg-orange-600 text-white px-3 py-1 rounded text-sm hover:bg-orange-700 transition">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Check-out Now
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-sign-out-alt text-4xl mb-4"></i>
                <p class="text-lg">No check-outs scheduled for today</p>
                <p class="text-sm mt-2">All guests are either staying longer or no departures are scheduled</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Check-out Process Checklist -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-out Process</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Financial Settlement</h4>
            <div class="space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Verify final bill amount</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Check room service charges</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Process payment</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-green-600">
                    <span class="text-sm">Provide receipt</span>
                </label>
            </div>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Room Inspection</h4>
            <div class="space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Check room condition</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Verify minibar usage</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Check for damages</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded text-blue-600">
                    <span class="text-sm">Return room keys/cards</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Housekeeping Coordination -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Housekeeping Coordination</h3>
    <div class="space-y-3">
        @php
            $roomsToClean = $guests->map(function($guest) {
                return $guest->bookings->whereIn('status', ['confirmed', 'checked_in'])->map(function($booking) {
                    return $booking->room;
                });
            })->flatten()->unique('id');
        @endphp
        @forelse($roomsToClean as $room)
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="bg-yellow-100 rounded-full p-2">
                        <i class="fas fa-broom text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Room {{ $room->room_number }}</p>
                        <p class="text-sm text-gray-600">{{ $room->roomType->name }} • Floor {{ $room->floor }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 transition">
                        <i class="fas fa-broom mr-1"></i>Assign Cleaning
                    </button>
                    <button class="bg-orange-600 text-white px-3 py-1 rounded text-sm hover:bg-orange-700 transition">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Priority
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-broom text-2xl mb-2"></i>
                <p>No rooms need immediate cleaning</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <button class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
        <i class="fas fa-bell mr-2"></i>Notify Housekeeping
    </button>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-print mr-2"></i>Print Check-out List
    </button>
    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-envelope mr-2"></i>Send Thank You Emails
    </button>
</div>
@endsection
