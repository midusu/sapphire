@extends('layouts.public')

@section('title', 'Photo Gallery - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Photo Gallery</h1>
            <p class="text-xl text-gray-600">Take a visual tour of our beautiful property</p>
        </div>

        @if($galleries->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-images text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600">Gallery coming soon!</p>
        </div>
        @else
        @foreach($galleries as $category => $items)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 capitalize">{{ $category }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($items as $gallery)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition">
                            <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition">
                                <h3 class="font-semibold text-lg">{{ $gallery->title }}</h3>
                                @if($gallery->description)
                                <p class="text-sm mt-1">{{ Str::limit($gallery->description, 80) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection
