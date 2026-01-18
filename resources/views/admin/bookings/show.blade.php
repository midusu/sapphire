@extends('admin.layout')

@section('title', 'Booking Details - Sapphire Hotel Management')
@section('header', 'Booking Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Booking Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-gray-600">Created on {{ $booking->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($booking->status == 'pending')
                    <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Confirm Booking
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                    </form>
                @endif
                
                @if($booking->status == 'confirmed')
                    <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Check In
                        </button>
                    </form>
                @endif
                
                @if($booking->status == 'checked_in')
                    <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Guest Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Name</span>
                    <p class="font-medium">{{ $booking->user ? $booking->user->name : ($booking->guest_name ?? 'Guest') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <p class="font-medium">{{ $booking->user ? $booking->user->email : ($booking->guest_email ?? 'N/A') }}</p>
                </div>
                @if($booking->user ? $booking->user->phone : $booking->guest_phone)
                    <div>
                        <span class="text-sm text-gray-500">Phone</span>
                        <p class="font-medium">{{ $booking->user ? $booking->user->phone : $booking->guest_phone }}</p>
                    </div>
                @endif
                @if($booking->user->address)
                    <div>
                        <span class="text-sm text-gray-500">Address</span>
                        <p class="font-medium">{{ $booking->user->address }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-sm text-gray-500">Guests</span>
                    <p class="font-medium">{{ $booking->adults }} Adults {{ $booking->children ? '• ' . $booking->children . ' Children' : '' }}</p>
                </div>
            </div>
        </div>

        <!-- Room Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Room Number</span>
                    <p class="font-medium">{{ $booking->room->room_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Room Type</span>
                    <p class="font-medium">{{ $booking->room->roomType->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Floor</span>
                    <p class="font-medium">Floor {{ $booking->room->floor ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Rate per Night</span>
                    <p class="font-medium">${{ number_format($booking->room->roomType->base_price ?? 0, 2) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Room Status</span>
                    <p class="font-medium">
                        @if($booking->room)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($booking->room->status == 'available') bg-green-100 text-green-800
                                @elseif($booking->room->status == 'occupied') bg-red-100 text-red-800
                                @elseif($booking->room->status == 'cleaning') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($booking->room->status) }}
                            </span>
                        @else
                            <span class="text-gray-500">N/A</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Booking Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Booking Status</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Current Status</span>
                    <p class="font-medium">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                            @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Check-in Date</span>
                    <p class="font-medium">{{ $booking->check_in_date->format('l, M d, Y') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Check-out Date</span>
                    <p class="font-medium">{{ $booking->check_out_date->format('l, M d, Y') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Nights</span>
                    <p class="font-medium">{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} nights</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Charges</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Room Rate ({{ $booking->check_in_date->diffInDays($booking->check_out_date) }} nights × ${{ number_format($booking->room->roomType->base_price ?? 0, 2) }})</span>
                        <span>${{ number_format(($booking->room->roomType->base_price ?? 0) * $booking->check_in_date->diffInDays($booking->check_out_date), 2) }}</span>
                    </div>
                    @if($booking->special_requests)
                        <div class="flex justify-between text-gray-500">
                            <span>Special Requests</span>
                            <span>No additional charge</span>
                        </div>
                    @endif
                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>Total Amount</span>
                        <span>${{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Payment Status</h4>
                @forelse($booking->payments as $payment)
                    <div class="border rounded-lg p-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium">${{ number_format($payment->amount, 2) }}</span>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        @if($payment->payment_method != 'pending')
                            <div class="text-sm text-gray-600">
                                Method: {{ ucfirst($payment->payment_method) }}
                            </div>
                        @endif
                        @if($payment->transaction_id)
                            <div class="text-sm text-gray-600">
                                Transaction ID: {{ $payment->transaction_id }}
                            </div>
                        @endif
                        
                        @if($payment->status == 'pending')
                            <div class="mt-2 flex space-x-2">
                                <form method="POST" action="{{ route('admin.payments.complete', $payment) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">
                                        Mark as Paid
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-4">
                        No payments recorded yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Special Requests -->
    @if($booking->special_requests)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Requests</h3>
            <p class="text-gray-700">{{ $booking->special_requests }}</p>
        </div>
    @endif

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Booking Timeline</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="bg-blue-100 rounded-full p-2 mt-1">
                    <i class="fas fa-calendar-plus text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Booking Created</p>
                    <p class="text-sm text-gray-600">{{ $booking->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            
            @if($booking->status != 'pending')
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 rounded-full p-2 mt-1">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Booking Confirmed</p>
                        <p class="text-sm text-gray-600">{{ $booking->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($booking->status == 'checked_in')
                <div class="flex items-start space-x-3">
                    <div class="bg-blue-100 rounded-full p-2 mt-1">
                        <i class="fas fa-sign-in-alt text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Guest Checked In</p>
                        <p class="text-sm text-gray-600">{{ $booking->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($booking->status == 'checked_out')
                <div class="flex items-start space-x-3">
                    <div class="bg-orange-100 rounded-full p-2 mt-1">
                        <i class="fas fa-sign-out-alt text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Guest Checked Out</p>
                        <p class="text-sm text-gray-600">{{ $booking->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
