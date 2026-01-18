@extends('layouts.public')

@section('title', 'Food Menu - Sapphire Hotel')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Food Menu</h1>
                    <p class="text-gray-600 mt-1">Order from our delicious selection</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guest.food-menu', ['menu_type' => 'room_service']) }}"
                        class="px-4 py-2 rounded-lg {{ $menuType === 'room_service' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        <i class="fas fa-bed mr-2"></i>Room Service
                    </a>
                    <a href="{{ route('guest.food-menu', ['menu_type' => 'restaurant']) }}"
                        class="px-4 py-2 rounded-lg {{ $menuType === 'restaurant' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        <i class="fas fa-utensils mr-2"></i>Restaurant
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu Categories -->
        <div class="space-y-8">
            @php
                $categories = $foods->groupBy('category');
            @endphp

            @foreach($categories as $category => $categoryFoods)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 capitalize">{{ $category }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categoryFoods as $food)
                            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                @if($food->image_url)
                                    <div class="h-48 bg-gray-200">
                                        <img src="{{ asset('storage/' . $food->image_url) }}" alt="{{ $food->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400 text-3xl"></i>
                                    </div>
                                @endif

                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-900">{{ $food->name }}</h3>
                                        <span class="text-lg font-bold text-green-600">${{ number_format($food->price, 2) }}</span>
                                    </div>

                                    <p class="text-gray-600 text-sm mb-3">{{ $food->description }}</p>

                                    <div class="flex justify-between items-center text-sm text-gray-500 mb-3">
                                        <span><i class="fas fa-clock mr-1"></i>{{ $food->preparation_time }} min</span>
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $food->getMenuTypeLabel() }}</span>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('guest.order-food', $food) }}"
                                            class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-center">
                                            <i class="fas fa-plus mr-1"></i>Order Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @if($foods->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-utensils text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No items available</h3>
                <p class="text-gray-600">No food items are currently available for this menu type.</p>
            </div>
        @endif
    </div>
@endsection