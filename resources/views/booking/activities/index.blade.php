@extends('layouts.app')

@section('title', 'Activity Booking - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Book Your Activities</h1>
            <p class="text-xl text-gray-600">Choose from our exciting adventure activities</p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($activities as $activity)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="h-48 bg-gradient-to-r from-green-400 to-blue-600 flex items-center justify-center">
                        @if($activity->type === 'zipline')
                            <i class="fas fa-parachute-box text-white text-6xl"></i>
                        @elseif($activity->type === 'swimming')
                            <i class="fas fa-swimmer text-white text-6xl"></i>
                        @else
                            <i class="fas fa-hiking text-white text-6xl"></i>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $activity->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $activity->description }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-3xl font-bold text-blue-600">${{ number_format($activity->price, 2) }}</span>
                                <span class="text-gray-500">per person</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Max Participants:</span>
                                <span class="font-semibold">{{ $activity->max_participants }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="text-sm text-gray-600">Duration: {{ $activity->duration ?? '1 hour' }}</span>
                        </div>

                        <a href="{{ route('booking.activities.create') }}?activity={{ $activity->id }}" 
                           class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-center hover:bg-green-700 transition">
                            Book Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
