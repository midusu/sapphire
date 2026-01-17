@extends('layouts.public')

@section('title', 'Amenities - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Amenities</h1>
            <p class="text-xl text-gray-600">World-class facilities for your comfort and enjoyment</p>
        </div>

        @if($amenities->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-star text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600">Amenities information coming soon!</p>
        </div>
        @else
        @foreach($amenities as $category => $items)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 capitalize">{{ $category }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($items as $amenity)
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition text-center">
                    @if($amenity->icon)
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="{{ $amenity->icon }} text-blue-600 text-3xl"></i>
                    </div>
                    @elseif($amenity->image_path)
                    <div class="h-32 mb-4 rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $amenity->image_path) }}" alt="{{ $amenity->name }}"
                            class="w-full h-full object-cover">
                    </div>
                    @endif
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $amenity->name }}</h3>
                    @if($amenity->description)
                    <p class="text-gray-600">{{ $amenity->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection
