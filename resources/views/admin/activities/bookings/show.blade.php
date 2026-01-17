@extends('admin.layout')

@section('title', 'Activity Booking Details - Sapphire Hotel Management')
@section('header', 'Activity Booking Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Booking Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Activity Booking #{{ str_pad($activityBooking->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-gray-600">Created on {{ $activityBooking->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($activityBooking->status == 'pending')
                    <form method="POST" action="{{ route('admin.activities.bookings.confirm', $activityBooking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Confirm Booking
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.activities.bookings.cancel', $activityBooking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                    </form>
                @endif
                
                @if($activityBooking->status == 'confirmed')
                    <form method="POST" action="{{ route('admin.activities.bookings.complete', $activityBooking) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-check-double mr-2"></i>Mark Complete
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('admin.activities.bookings.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
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
                    <p class="font-medium">{{ $activityBooking->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <p class="font-medium">{{ $activityBooking->user->email }}</p>
                </div>
                @if($activityBooking->user->phone)
                    <div>
                        <span class="text-sm text-gray-500">Phone</span>
                        <p class="font-medium">{{ $activityBooking->user->phone }}</p>
                    </div>
                @endif
                @if($activityBooking->booking)
                    <div>
                        <span class="text-sm text-gray-500">Room Booking</span>
                        <p class="font-medium">Room {{ $activityBooking->booking->room->room_number }}</p>
                        <p class="text-sm text-gray-600">{{ $activityBooking->booking->check_in_date->format('M d') }} - {{ $activityBooking->booking->check_out_date->format('M d') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Activity</span>
                    <p class="font-medium">{{ $activityBooking->activity->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Type</span>
                    <p class="font-medium">{{ ucfirst($activityBooking->activity->type) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Scheduled Time</span>
                    <p class="font-medium">{{ $activityBooking->scheduled_time->format('l, M d, Y H:i') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Duration</span>
                    <p class="font-medium">{{ $activityBooking->activity->duration }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Participants</span>
                    <p class="font-medium">{{ $activityBooking->participants }} / {{ $activityBooking->activity->max_participants }}</p>
                </div>
                @if($activityBooking->activity->requirements)
                    <div>
                        <span class="text-sm text-gray-500">Requirements</span>
                        <div class="mt-1">
                            @foreach(json_decode($activityBooking->activity->requirements) as $requirement)
                                <div class="text-sm text-gray-600">• {{ $requirement }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                            @if($activityBooking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($activityBooking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($activityBooking->status == 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $activityBooking->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Booking Date</span>
                    <p class="font-medium">{{ $activityBooking->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($activityBooking->scheduled_time > now())
                    <div>
                        <span class="text-sm text-gray-500">Time Until Activity</span>
                        <p class="font-medium">{{ $activityBooking->scheduled_time->diffForHumans() }}</p>
                    </div>
                @endif
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
                        <span>{{ $activityBooking->activity->name }} ({{ $activityBooking->participants }} participants × ${{ number_format($activityBooking->activity->price, 2) }})</span>
                        <span>${{ number_format($activityBooking->activity->price * $activityBooking->participants, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>Total Amount</span>
                        <span>${{ number_format($activityBooking->total_price, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Payment Status</h4>
                @forelse($activityBooking->payments as $payment)
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
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-4">
                        No payments recorded yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Special Notes -->
    @if($activityBooking->notes)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Notes</h3>
            <p class="text-gray-700">{{ $activityBooking->notes }}</p>
        </div>
    @endif

    <!-- Activity Requirements -->
    @if($activityBooking->activity->requirements)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Requirements</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(json_decode($activityBooking->activity->requirements) as $requirement)
                    <div class="flex items-center space-x-2">
                        <div class="bg-blue-100 rounded-full p-1">
                            <i class="fas fa-info text-blue-600 text-xs"></i>
                        </div>
                        <span class="text-gray-700">{{ $requirement }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Booking Timeline</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="bg-purple-100 rounded-full p-2 mt-1">
                    <i class="fas fa-calendar-plus text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Booking Created</p>
                    <p class="text-sm text-gray-600">{{ $activityBooking->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            
            @if($activityBooking->status != 'pending')
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 rounded-full p-2 mt-1">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Booking Confirmed</p>
                        <p class="text-sm text-gray-600">{{ $activityBooking->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($activityBooking->status == 'completed')
                <div class="flex items-start space-x-3">
                    <div class="bg-blue-100 rounded-full p-2 mt-1">
                        <i class="fas fa-check-double text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Activity Completed</p>
                        <p class="text-sm text-gray-600">{{ $activityBooking->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            <div class="flex items-start space-x-3">
                <div class="bg-orange-100 rounded-full p-2 mt-1">
                    <i class="fas fa-clock text-orange-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Scheduled Activity</p>
                    <p class="text-sm text-gray-600">{{ $activityBooking->scheduled_time->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
