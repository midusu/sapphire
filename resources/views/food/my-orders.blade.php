<x-guest-layout>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Orders</h1>
            <p class="text-gray-600">Track your food orders</p>
        </div>

        <!-- Email Search Form -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form action="{{ route('food.my-orders') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Enter your email to view orders</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? '' }}" required
                        placeholder="your.email@example.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search Orders
                    </button>
                </div>
            </form>
        </div>

        @if ($email)
            @if ($orders->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 text-lg">No orders found for this email.</p>
                    <p class="text-gray-500 mt-2">Place your first order from our menu!</p>
                    <a href="{{ route('food.menu') }}" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-md font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-utensils mr-2"></i>View Menu
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start space-x-4">
                                        @if ($order->food->image_url)
                                            <img src="{{ $order->food->image_url }}" alt="{{ $order->food->name }}" class="w-16 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded-lg">
                                                <i class="fas fa-utensils text-gray-400 text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-800">{{ $order->food->name }}</h3>
                                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                                                <div>
                                                    <span class="text-gray-600">Order #:</span>
                                                    <span class="font-medium ml-1">{{ $order->id }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600">Quantity:</span>
                                                    <span class="font-medium ml-1">{{ $order->quantity }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600">Total:</span>
                                                    <span class="font-medium ml-1">${{ number_format($order->total_price, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600">Time:</span>
                                                    <span class="font-medium ml-1">{{ $order->order_time->format('M j, g:i A') }}</span>
                                                </div>
                                            </div>

                                            @if ($order->room_number)
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-600">Room:</span>
                                                    <span class="font-medium ml-1">{{ $order->room_number }}</span>
                                                </div>
                                            @endif

                                            @if ($order->special_instructions)
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-600">Special instructions:</span>
                                                    <p class="text-gray-800 mt-1">{{ $order->special_instructions }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 lg:mt-0 lg:ml-4">
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-blue-600">${{ number_format($order->total_price, 2) }}</p>
                                        
                                        @if ($order->status === 'pending')
                                            <div class="mt-2 text-sm text-yellow-600">
                                                <i class="fas fa-clock mr-1"></i>
                                                Est. {{ $order->order_time->addMinutes($order->food->preparation_time)->diffForHumans() }}
                                            </div>
                                        @elseif ($order->status === 'delivered')
                                            <div class="mt-2 text-sm text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Delivered {{ $order->updated_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('food.menu') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Place New Order
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg">Enter your email address to view your orders.</p>
                <p class="text-gray-500 mt-2">You'll be able to see all your past and current food orders.</p>
            </div>
        @endif
    </div>
</x-guest-layout>
