@extends('admin.layout')

@section('title', 'Order Details - Sapphire Hotel Management')
@section('header', 'Order Details #' . ($foodOrder->kot_number ?? $foodOrder->id))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.food.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        <!-- Status Banner -->
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <div>
                    <span class="text-sm text-gray-500 uppercase tracking-wider">Status</span>
                    <h3 class="text-2xl font-bold 
                        @if($foodOrder->status == 'pending') text-yellow-600
                        @elseif($foodOrder->status == 'preparing') text-blue-600
                        @elseif($foodOrder->status == 'ready') text-purple-600
                        @elseif($foodOrder->status == 'delivered') text-green-600
                        @elseif($foodOrder->status == 'cancelled') text-red-600
                        @endif">
                        {{ ucfirst($foodOrder->status) }}
                    </h3>
                </div>

                <div class="flex space-x-2">
                    @if($foodOrder->status != 'delivered' && $foodOrder->status != 'cancelled')
                        <form action="{{ route('admin.food.orders.complete', $foodOrder) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check mr-2"></i>Mark Delivered
                            </button>
                        </form>

                        <form action="{{ route('admin.food.orders.cancel', $foodOrder) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times mr-2"></i>Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Item</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->food->name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Quantity</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->quantity }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Total Price</span>
                        <span class="font-bold text-green-600">${{ number_format($foodOrder->total_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Order Type</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->getOrderTypeLabel() }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Date & Time</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->order_time->format('M d, Y H:i') }}</span>
                    </div>
                    @if($foodOrder->special_instructions)
                        <div class="pt-2">
                            <span class="block text-gray-600 mb-1">Special Instructions</span>
                            <p class="bg-gray-50 p-3 rounded text-gray-800 text-sm">{{ $foodOrder->special_instructions }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Guest Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Name</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->guest_name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Room / Table</span>
                        <span class="font-medium text-gray-900">
                            @if($foodOrder->room_number)
                                Room {{ $foodOrder->room_number }}
                            @elseif($foodOrder->table_number)
                                Table {{ $foodOrder->table_number }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    @if($foodOrder->booking)
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Booking ID</span>
                            <a href="{{ route('admin.bookings.show', $foodOrder->booking) }}"
                                class="text-blue-600 hover:text-blue-800">
                                #{{ str_pad($foodOrder->booking->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        </div>
                    @endif
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Contact Email</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->guest_email }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Contact Phone</span>
                        <span class="font-medium text-gray-900">{{ $foodOrder->guest_phone }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection