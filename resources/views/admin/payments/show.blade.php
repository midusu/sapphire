@extends('admin.layout')

@section('title', 'Payment Details - Sapphire Hotel Management')
@section('header', 'Payment Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Payment Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Payment #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-gray-600">Recorded on {{ $payment->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($payment->status == 'pending')
                    <form method="POST" action="{{ route('admin.payments.complete', $payment) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Complete Payment
                        </button>
                    </form>
                    <a href="{{ route('admin.payments.edit', $payment) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
                
                @if($payment->status == 'authorized')
                    <form method="POST" action="{{ route('admin.payments.capture', $payment) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-check-double mr-2"></i>Capture Payment
                        </button>
                    </form>
                @endif
                
                @if($payment->status == 'completed')
                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="inline" onsubmit="return confirm('Are you sure you want to refund this payment?')">
                        @csrf
                        <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-undo mr-2"></i>Refund
                        </button>
                    </form>
                @endif
                
                @if(in_array($payment->status, ['pending', 'failed']))
                    <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('admin.payments.invoice', $payment) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition" target="_blank">
                    <i class="fas fa-file-invoice mr-2"></i>Invoice
                </a>
                
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payments
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
                    <p class="font-medium">{{ $payment->booking?->user->name ?? $payment->activityBooking?->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <p class="font-medium">{{ $payment->booking?->user->email ?? $payment->activityBooking?->user->email }}</p>
                </div>
                @if($payment->booking?->user->phone || $payment->activityBooking?->user->phone)
                    <div>
                        <span class="text-sm text-gray-500">Phone</span>
                        <p class="font-medium">{{ $payment->booking?->user->phone ?? $payment->activityBooking?->user->phone }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Amount</span>
                    <p class="font-medium text-2xl text-green-600">${{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Payment Method</span>
                    <p class="font-medium">
                        <i class="fas fa-{{ $payment->payment_method === 'cash' ? 'money-bill' : ($payment->payment_method === 'card' ? 'credit-card' : ($payment->payment_method === 'bank_transfer' ? 'university' : 'globe')) }} mr-2"></i>
                        {{ ucfirst($payment->payment_method) }}
                    </p>
                </div>
                @if($payment->transaction_id)
                    <div>
                        <span class="text-sm text-gray-500">Transaction ID</span>
                        <p class="font-medium">{{ $payment->transaction_id }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-sm text-gray-500">Payment Date</span>
                    <p class="font-medium">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Status</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Current Status</span>
                    <p class="font-medium">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($payment->status == 'completed') bg-green-100 text-green-800
                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status == 'authorized') bg-blue-100 text-blue-800
                            @elseif($payment->status == 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </p>
                </div>
                @if($payment->status == 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            This payment is pending completion.
                        </p>
                    </div>
                @endif
                @if($payment->status == 'authorized')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            This payment is authorized and ready to be captured.
                        </p>
                    </div>
                @endif
                @if($payment->status == 'refunded')
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                        <p class="text-sm text-orange-800">
                            <i class="fas fa-undo mr-1"></i>
                            This payment has been refunded.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Booking/Activity Details -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Details</h3>
        @if($payment->booking_id)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Room Booking Information</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Booking ID:</span>
                            <span class="font-medium">#{{ str_pad($payment->booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Number:</span>
                            <span class="font-medium">{{ $payment->booking->room->room_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Type:</span>
                            <span class="font-medium">{{ $payment->booking->room->roomType->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-in:</span>
                            <span class="font-medium">{{ $payment->booking->check_in_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-out:</span>
                            <span class="font-medium">{{ $payment->booking->check_out_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Guests:</span>
                            <span class="font-medium">{{ $payment->booking->adults }} Adults {{ $payment->booking->children ? '• ' . $payment->booking->children . ' Children' : '' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Booking Charges</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Room Rate ({{ $payment->booking->check_in_date->diffInDays($payment->booking->check_out_date) }} nights)</span>
                            <span>${{ number_format($payment->booking->room->roomType->base_price * $payment->booking->check_in_date->diffInDays($payment->booking->check_out_date), 2) }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-semibold">
                            <span>Total Amount:</span>
                            <span>${{ number_format($payment->booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Activity Booking Information</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Activity Booking ID:</span>
                            <span class="font-medium">#{{ str_pad($payment->activityBooking->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Activity:</span>
                            <span class="font-medium">{{ $payment->activityBooking->activity->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ ucfirst($payment->activityBooking->activity->type) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Scheduled Time:</span>
                            <span class="font-medium">{{ $payment->activityBooking->scheduled_time->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Participants:</span>
                            <span class="font-medium">{{ $payment->activityBooking->participants }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-medium">{{ $payment->activityBooking->activity->duration }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Activity Charges</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>{{ $payment->activityBooking->activity->name }} ({{ $payment->activityBooking->participants }} participants)</span>
                            <span>${{ number_format($payment->activityBooking->activity->price * $payment->activityBooking->participants, 2) }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-semibold">
                            <span>Total Amount:</span>
                            <span>${{ number_format($payment->activityBooking->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Additional Notes -->
    @if($payment->notes)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h3>
            <p class="text-gray-700">{{ $payment->notes }}</p>
        </div>
    @endif

    <!-- Payment Timeline -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Timeline</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="bg-purple-100 rounded-full p-2 mt-1">
                    <i class="fas fa-receipt text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Payment Created</p>
                    <p class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            
            @if($payment->status == 'completed')
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 rounded-full p-2 mt-1">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Payment Completed</p>
                        <p class="text-sm text-gray-600">{{ $payment->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($payment->status == 'refunded')
                <div class="flex items-start space-x-3">
                    <div class="bg-orange-100 rounded-full p-2 mt-1">
                        <i class="fas fa-undo text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium">Payment Refunded</p>
                        <p class="text-sm text-gray-600">{{ $payment->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
