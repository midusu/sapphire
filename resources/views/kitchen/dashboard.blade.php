@extends('admin.layout')

@section('title', 'Kitchen Dashboard - Sapphire Hotel')
@section('header', 'Kitchen Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pending Orders ({{ $pendingOrders->count() }})</h3>
        @if ($pendingOrders->isEmpty())
            <p class="text-gray-500 text-center py-8">No pending orders</p>
        @else
            <div class="space-y-4">
                @foreach ($pendingOrders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $order->food->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $order->getOrderTypeLabel() }}
                                    @if ($order->table_number)
                                        • Table {{ $order->table_number }}
                                    @endif
                                    @if ($order->room_number)
                                        • Room {{ $order->room_number }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <p class="text-sm text-gray-500 mt-1">{{ $order->order_time->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <p class="text-sm text-gray-600">Qty: {{ $order->quantity }}</p>
                                <p class="text-sm text-gray-600">Prep time: {{ $order->food->preparation_time }} min</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_price, 2) }}</p>
                                <p class="text-xs text-gray-500">KOT: {{ $order->generateKotNumber() }}</p>
                            </div>
                        </div>

                        @if ($order->special_instructions)
                            <div class="bg-yellow-50 p-2 rounded text-sm text-gray-700 mb-3">
                                <strong>Special instructions:</strong> {{ $order->special_instructions }}
                            </div>
                        @endif

                        <div class="flex space-x-2">
                            <form action="{{ route('admin.kitchen.update-status', $order) }}" method="POST" class="flex-1">
                                @csrf
                                @if ($order->status === 'pending')
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-utensils mr-1"></i>Start Preparing
                                    </button>
                                @elseif ($order->status === 'preparing')
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i>Mark Ready
                                    </button>
                                @endif
                            </form>
                            <a href="{{ route('admin.kitchen.kot-ticket', $order) }}" target="_blank" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">
                                <i class="fas fa-print mr-1"></i>KOT
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Ready for Pickup/Delivery -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ready for Pickup/Delivery ({{ $completedOrders->count() }})</h3>
        @if ($completedOrders->isEmpty())
            <p class="text-gray-500 text-center py-8">No orders ready</p>
        @else
            <div class="space-y-4">
                @foreach ($completedOrders as $order)
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $order->food->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $order->getOrderTypeLabel() }}
                                    @if ($order->table_number)
                                        • Table {{ $order->table_number }}
                                    @endif
                                    @if ($order->room_number)
                                        • Room {{ $order->room_number }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Ready
                                </span>
                                <p class="text-sm text-gray-500 mt-1">Ready at {{ $order->kitchen_completed_time->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Qty: {{ $order->quantity }}</p>
                                <p class="text-xs text-gray-500">KOT: {{ $order->kot_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_price, 2) }}</p>
                            </div>
                        </div>

                        @if ($order->order_type === 'room_service')
                            <form action="{{ route('admin.kitchen.update-status', $order) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                    <i class="fas fa-truck mr-1"></i>Mark as Delivered
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="mt-8 flex space-x-4">
    <a href="{{ route('admin.kitchen.active-orders') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        <i class="fas fa-list mr-2"></i>View All Active Orders
    </a>
    <a href="{{ route('admin.kitchen.order-history') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
        <i class="fas fa-history mr-2"></i>Order History
    </a>
</div>
@endsection
