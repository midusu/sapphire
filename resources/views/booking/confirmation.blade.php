@extends('layouts.public')

@section('title', 'Booking Confirmation - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden text-center p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Booking Confirmed!</h1>
            <p class="text-gray-600 mb-8 text-lg">
                Thank you for choosing Sapphire Hotel. Your booking request has been received successfully.
            </p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Booking Summary</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking Reference:</span>
                        <span class="font-medium">#{{ $booking instanceof \App\Models\ActivityBooking ? 'A-' . $booking->id : 'R-' . $booking->id }}</span>
                    </div>

                    @if($booking instanceof \App\Models\ActivityBooking)
                        {{-- Activity Booking Details --}}
                        <div class="flex justify-between">
                            <span class="text-gray-600">Activity:</span>
                            <span class="font-medium">{{ $booking->activity->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Scheduled Time:</span>
                            <span class="font-medium">{{ $booking->scheduled_time->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Participants:</span>
                            <span class="font-medium">{{ $booking->participants }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-3 mt-3">
                            <span class="text-gray-800 font-semibold">Total Amount:</span>
                            <span class="text-blue-600 font-bold text-lg">${{ number_format($booking->total_price ?? $booking->total_amount, 2) }}</span>
                        </div>
                    @else
                        {{-- Room Booking Details --}}
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Type:</span>
                            <span class="font-medium">{{ $booking->room->roomType->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-in:</span>
                            <span class="font-medium">{{ $booking->check_in_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-out:</span>
                            <span class="font-medium">{{ $booking->check_out_date->format('M d, Y') }}</span>
                        </div>

                        @if($booking->coupon)
                            <div class="flex justify-between border-t pt-3 mt-3 text-green-600">
                                <span class="font-medium">Discount ({{ $booking->coupon->code }}):</span>
                                @php
                                    $subTotal = $booking->room->roomType->base_price * $booking->check_in_date->diffInDays($booking->check_out_date);
                                    $discount = $subTotal - $booking->total_amount;
                                @endphp
                                <span class="font-bold">-${{ number_format($discount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between border-t pt-3 mt-3">
                            <span class="text-gray-800 font-semibold">Total Amount:</span>
                            <span class="text-blue-600 font-bold text-lg">${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="space-y-4">
                <p class="text-sm text-gray-500">
                    A confirmation email has been sent to 
                    <span class="font-medium text-gray-900">
                        @if($booking instanceof \App\Models\ActivityBooking)
                            {{-- Check special_requirements JSON for guest info, or fallback to user email --}}
                            @php
                                $guestInfo = is_string($booking->special_requirements) 
                                    ? json_decode($booking->special_requirements, true) 
                                    : $booking->special_requirements;
                            @endphp
                            {{ $guestInfo['guest_email'] ?? $booking->user->email ?? 'N/A' }}
                        @else
                            {{ $booking->guest_email ?? $booking->user->email }}
                        @endif
                    </span>.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                    <a href="{{ route('home') }}" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                        Return Home
                    </a>
                    @auth
                        <a href="{{ route('guest.dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                            View My Bookings
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
