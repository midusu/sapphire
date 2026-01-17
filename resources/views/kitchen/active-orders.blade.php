@extends('admin.layout')

@section('title', 'Active Orders - Kitchen')
@section('header', 'Active Orders')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Active Orders</h2>
            <a href="{{ route('admin.kitchen.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
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
                                @if($order->isScheduled())
                                    <p class="text-xs text-orange-600 mt-1">Scheduled</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <p class="text-sm text-gray-600">Qty: {{ $order->quantity }}</p>
                                <p class="text-sm text-gray-600">Prep time: {{ $order->food->preparation_time }} min</p>
                                <p class="text-sm text-gray-600">KOT: {{ $order->kot_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_price, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $order->getDeliveryTimeLabel() }}</p>
                            </div>
                        </div>

                        @if($order->special_instructions)
                            <div class="bg-yellow-50 p-2 rounded text-sm text-gray-700 mb-3">
                                <strong>Special instructions:</strong> {{ $order->special_instructions }}
                            </div>
                        @endif

                        <div class="flex space-x-2">
                            @if($order->status === 'pending')
                                <form action="{{ route('admin.kitchen.update-status', $order) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-utensils mr-1"></i>Start Preparing
                                    </button>
                                </form>
                            @elseif($order->status === 'preparing')
                                <form action="{{ route('admin.kitchen.update-status', $order) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i>Mark Ready
                                    </button>
                                </form>
                            @elseif($order->status === 'ready' && $order->order_type === 'room_service')
                                <form action="{{ route('admin.kitchen.update-status', $order) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-truck mr-1"></i>Mark as Delivered
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.kitchen.kot-ticket', $order) }}" target="_blank" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">
                                <i class="fas fa-print mr-1"></i>KOT
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No active orders</h3>
                <p class="text-gray-600">There are currently no active orders in the kitchen.</p>
                <a href="{{ route('admin.kitchen.dashboard') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
