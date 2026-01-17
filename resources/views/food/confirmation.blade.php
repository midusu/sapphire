<x-guest-layout>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Your food order has been placed successfully.</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order ID</p>
                    <p class="font-semibold">#{{ $order->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Time</p>
                    <p class="font-semibold">{{ $order->order_time->format('M j, Y - g:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer Name</p>
                    <p class="font-semibold">{{ $order->guest_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Room Number</p>
                    <p class="font-semibold">{{ $order->room_number ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Food Item</h2>
            
            <div class="flex items-center space-x-4">
                @if ($order->food->image_url)
                    <img src="{{ $order->food->image_url }}" alt="{{ $order->food->name }}" class="w-20 h-20 object-cover rounded-lg">
                @else
                    <div class="w-20 h-20 bg-gray-200 flex items-center justify-center rounded-lg">
                        <i class="fas fa-utensils text-gray-400 text-xl"></i>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $order->food->name }}</h3>
                    <p class="text-gray-600">{{ $order->quantity }} × ${{ number_format($order->food->price, 2) }}</p>
                </div>
                
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($order->total_price, 2) }}</p>
                </div>
            </div>

            @if ($order->special_instructions)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Special Instructions:</p>
                    <p class="text-gray-800">{{ $order->special_instructions }}</p>
                </div>
            @endif
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Estimated Delivery Time</p>
                    <p class="text-gray-600">{{ $order->order_time->addMinutes($order->food->preparation_time)->format('g:i A') }} ({{ $order->food->preparation_time }} minutes)</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('food.menu') }}" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition-colors text-center">
                <i class="fas fa-utensils mr-2"></i>Order More Food
            </a>
            <a href="{{ route('food.my-orders', ['email' => $order->guest_email]) }}" class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-md font-medium hover:bg-gray-700 transition-colors text-center">
                <i class="fas fa-list-alt mr-2"></i>View My Orders
            </a>
        </div>
    </div>
</x-guest-layout>
