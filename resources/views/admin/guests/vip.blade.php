@extends('admin.layout')

@section('title', 'VIP Guests - Sapphire Hotel Management')
@section('header', 'VIP Guest Management')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">VIP Guest Management</h2>
    <p class="text-gray-600">Manage and monitor VIP guests and special services</p>
</div>

<!-- VIP Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total VIP Guests</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $guests->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-star text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Gold Members</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $guests->where('loyalty_status', 'gold')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-trophy text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Platinum Members</p>
                <p class="text-2xl font-bold text-purple-600">{{ $guests->where('loyalty_status', 'platinum')->count() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-crown text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total VIP Revenue</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($guests->sum('total_spent'), 0) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- VIP Alert Banner -->
<div class="bg-gradient-to-r from-yellow-500 to-purple-600 rounded-lg shadow p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold mb-2">VIP Guest Services</h3>
            <p class="text-sm opacity-90">VIP guests receive premium services including priority check-in, room upgrades, dedicated concierge, and exclusive amenities.</p>
        </div>
        <div class="flex space-x-2">
            <button class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition">
                <i class="fas fa-bell mr-2"></i>Alert Staff
            </button>
            <button class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition">
                <i class="fas fa-phone mr-2"></i>Contact VIP
            </button>
        </div>
    </div>
</div>

<!-- Current VIP Guests (Active) -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b bg-yellow-50">
        <h3 class="text-lg font-semibold text-gray-800">Currently Staying VIP Guests</h3>
    </div>
    <div class="p-6">
        @php
            $activeVipGuests = $guests->filter(function($guest) {
                return $guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists();
            });
        @endphp
        @forelse($activeVipGuests as $guest)
            <div class="border rounded-lg p-4 mb-4 hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="font-bold text-gray-800">{{ $guest->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    @if($guest->loyalty_status == 'gold') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst($guest->loyalty_status) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                    Currently Staying
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $guest->email }} • {{ $guest->phone }}</p>
                            @if($guest->currentBooking = $guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->first())
                                <p class="text-sm text-gray-600">
                                    Room {{ $guest->currentBooking->room->room_number }} • 
                                    {{ $guest->currentBooking->room->roomType->name }} • 
                                    Check-out: {{ $guest->currentBooking->check_out_date->format('M d') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.guests.show', $guest) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <button class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition">
                            <i class="fas fa-concierge-bell mr-1"></i>Services
                        </button>
                    </div>
                </div>
                
                <!-- VIP Services -->
                <div class="mt-4 pt-4 border-t">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-4 text-sm">
                            <span class="text-gray-600">
                                <i class="fas fa-calendar-check mr-1"></i>
                                {{ $guest->total_bookings }} bookings
                            </span>
                            <span class="text-gray-600">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                ${{ number_format($guest->total_spent, 2) }} spent
                            </span>
                            <span class="text-gray-600">
                                <i class="fas fa-clock mr-1"></i>
                                Member since {{ $guest->created_at->format('Y') }}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-green-600 hover:text-green-800" title="VIP Check-in">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-800" title="VIP Lounge">
                                <i class="fas fa-couch"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-800" title="Concierge">
                                <i class="fas fa-concierge-bell"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-star text-4xl mb-4"></i>
                <p>No VIP guests currently staying</p>
            </div>
        @endforelse
    </div>
</div>

<!-- All VIP Guests -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">All VIP Guests</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loyalty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($guests as $guest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="fas fa-star text-yellow-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $guest->name }}</div>
                                    @if($guest->nationality)
                                        <div class="text-sm text-gray-500">{{ $guest->nationality }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $guest->email }}</div>
                            @if($guest->phone)
                                <div class="text-sm text-gray-500">{{ $guest->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $guest->total_bookings }}
                            <div class="text-xs text-gray-500">
                                {{ $guest->bookings->where('status', 'checked_out')->count() }} completed
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${{ number_format($guest->total_spent, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($guest->loyalty_status == 'gold') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                <i class="fas fa-crown mr-1"></i>{{ ucfirst($guest->loyalty_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.guests.show', $guest) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.create') }}?guest_id={{ $guest->id }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <button class="text-purple-600 hover:text-purple-900" title="VIP Services">
                                    <i class="fas fa-concierge-bell"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900" title="Special Request">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No VIP guests found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- VIP Services Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">VIP Services</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-yellow-600"></i>
                    <span class="font-medium">Priority Check-in</span>
                </div>
                <span class="text-sm text-gray-600">Dedicated counter</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-crown text-purple-600"></i>
                    <span class="font-medium">Room Upgrade</span>
                </div>
                <span class="text-sm text-gray-600">When available</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-concierge-bell text-blue-600"></i>
                    <span class="font-medium">24/7 Concierge</span>
                </div>
                <span class="text-sm text-gray-600">Personal service</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-utensils text-green-600"></i>
                    <span class="font-medium">Complimentary Dining</span>
                </div>
                <span class="text-sm text-gray-600">Breakfast & more</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">VIP Preferences</h3>
        <div class="space-y-3">
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Most Preferred Room Type</span>
                    <span class="text-sm text-gray-600">Suite</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 65%"></div>
                </div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Average Stay Duration</span>
                    <span class="text-sm text-gray-600">4.2 nights</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: 42%"></div>
                </div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Peak Booking Season</span>
                    <span class="text-sm text-gray-600">Summer</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 78%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <a href="{{ route('admin.guests.loyalty') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-crown mr-2"></i>Loyalty Program
    </a>
    <a href="{{ route('admin.guests.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2"></i>Back to Guests
    </a>
</div>
@endsection
