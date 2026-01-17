@extends('layouts.public')

@section('title', 'Room Booking - Sapphire Hotel')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Book Your Room</h1>
                <p class="text-xl text-gray-600">Choose from our selection of luxurious accommodations</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($roomTypes as $roomType)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="h-48 bg-gray-200 relative">
                            @if($roomType->image_url)
                                <img src="{{ asset('storage/' . $roomType->image_url) }}" alt="{{ $roomType->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                    <i class="fas fa-bed text-white text-6xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $roomType->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $roomType->description }}</p>

                            <div class="mb-4">
                                <span
                                    class="text-3xl font-bold text-blue-600">${{ number_format($roomType->base_price, 2) }}</span>
                                <span class="text-gray-500">per night</span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Available Rooms: {{ $roomType->rooms->count() }}</p>
                                <p class="text-sm text-gray-600">Max Occupancy: {{ $roomType->max_occupancy ?? 4 }} guests</p>
                            </div>

                            @if($roomType->rooms->count() > 0)
                                <a href="{{ route('booking.rooms.create') }}?room_type={{ $roomType->id }}"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition">
                                    Book Now
                                </a>
                            @else
                                <button disabled
                                    class="w-full bg-gray-400 text-white px-4 py-2 rounded-lg text-center cursor-not-allowed">
                                    Sold Out
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection