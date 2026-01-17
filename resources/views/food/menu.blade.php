<x-guest-layout>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Food Menu</h1>
            <p class="text-gray-600">Order delicious food anytime, anywhere in the hotel</p>
        </div>

        @if ($foods->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-utensils text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg">No food items available at the moment.</p>
                <p class="text-gray-500 mt-2">Please check back later.</p>
            </div>
        @else
            @foreach ($foods as $category => $items)
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4 capitalize">{{ $category }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($items as $food)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                @if ($food->image_url)
                                    <img src="{{ $food->image_url }}" alt="{{ $food->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $food->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($food->description, 100) }}</p>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xl font-bold text-blue-600">${{ number_format($food->price, 2) }}</span>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $food->preparation_time }} min
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $food->available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $food->available ? 'Available' : 'Unavailable' }}
                                        </span>
                                        
                                        @if ($food->available)
                                            <a href="{{ route('food.order', $food) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                                Order Now
                                            </a>
                                        @else
                                            <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed">
                                                Unavailable
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('food.my-orders') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-list-alt mr-2"></i>View My Orders
            </a>
        </div>
    </div>
</x-guest-layout>
