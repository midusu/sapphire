@extends('admin.layout')

@section('title', 'Order History - Kitchen')
@section('header', 'Order History')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Order History</h2>
            <a href="{{ route('admin.kitchen.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $order->food->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $order->getMenuTypeLabel() }} • {{ $order->getOrderTypeLabel() }}
                                    @if($order->user)
                                        • Guest: {{ $order->user->name }}
                                    @endif
                                    @if($order->room_number)
                                        • Room {{ $order->room_number }}
                                    @endif
                                    @if($order->table_number)
                                        • Table {{ $order->table_number }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $order->order_time->format('M j, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Qty: {{ $order->quantity }}</p>
                                <p class="text-sm text-gray-600">KOT: {{ $order->kot_number }}</p>
                                @if($order->delivery_time)
                                    <p class="text-sm text-gray-600">Delivered: {{ $order->delivery_time->format('H:i') }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_price, 2) }}</p>
                                <a href="{{ route('admin.kitchen.kot-ticket', $order) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-print mr-1"></i>Print KOT
                                </a>
                            </div>
                        </div>

                        @if($order->special_instructions)
                            <div class="bg-yellow-50 p-2 rounded text-sm text-gray-700 mt-3">
                                <strong>Special instructions:</strong> {{ $order->special_instructions }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-history text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No order history</h3>
                <p class="text-gray-600">No completed or cancelled orders found.</p>
                <a href="{{ route('admin.kitchen.dashboard') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
