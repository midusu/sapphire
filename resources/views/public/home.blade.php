@extends('layouts.public')

@section('title', 'Sapphire Hotel - Luxury Accommodation & Adventures')

@section('content')
<!-- Hero Section -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('{{ asset('images/hero-bg.jpg') }}')">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    </div>
    <div class="relative z-10 text-center text-white px-4">
        <h1 class="text-5xl md:text-6xl font-bold mb-4">Welcome to Sapphire Hotel</h1>
        <p class="text-xl md:text-2xl mb-8">Experience Luxury, Adventure, and Unforgettable Memories</p>
        <div class="space-x-4">
            <a href="{{ route('booking.rooms.index') }}"
                class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition inline-block">Book
                a Room</a>
            <a href="{{ route('booking.activities.index') }}"
                class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white hover:text-gray-900 transition inline-block">Book
                Activities</a>
        </div>
    </div>
</section>

<!-- Quick Booking Search -->
<section class="py-12 bg-white shadow-lg -mt-20 relative z-20 mx-4 md:mx-auto max-w-6xl rounded-lg">
    <div class="px-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Check Room Availability</h2>
        <form id="availabilityForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check In</label>
                <input type="date" name="check_in" id="check_in" required
                    class="w-full border-gray-300 rounded-lg" min="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check Out</label>
                <input type="date" name="check_out" id="check_out" required
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                <input type="number" name="adults" id="adults" value="2" min="1" required
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Check Availability
                </button>
            </div>
        </form>
        <div id="availabilityResults" class="mt-4 hidden"></div>
    </div>
</section>

<!-- Featured Amenities -->
@if($featuredAmenities->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Amenities</h2>
            <p class="text-xl text-gray-600">World-class facilities for your comfort</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredAmenities as $amenity)
            <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                @if($amenity->icon)
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="{{ $amenity->icon }} text-blue-600 text-3xl"></i>
                </div>
                @endif
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $amenity->name }}</h3>
                @if($amenity->description)
                <p class="text-gray-600">{{ Str::limit($amenity->description, 100) }}</p>
                @endif
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('amenities') }}"
                class="text-blue-600 hover:text-blue-800 font-semibold">View All Amenities →</a>
        </div>
    </div>
</section>
@endif

<!-- Rooms Section -->
<section id="rooms" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Rooms</h2>
            <p class="text-xl text-gray-600">Choose from our selection of luxurious accommodations</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($roomTypes as $roomType)
            @php
                $availableCount = $roomType->rooms->where('status', 'available')->count();
            @endphp
            @if($availableCount > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-bed text-gray-400 text-4xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $roomType->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($roomType->description ?? 'Luxurious accommodation', 80) }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-blue-600">${{ number_format($roomType->base_price, 2) }}</span>
                        <span class="text-gray-500">per night</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm text-green-600 font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>{{ $availableCount }} Available
                        </span>
                    </div>
                    <a href="{{ route('booking.rooms.create') }}?room_type={{ $roomType->id }}"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition block">Book
                        Now</a>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>

<!-- Activities Section -->
<section id="activities" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Adventure Activities</h2>
            <p class="text-xl text-gray-600">Exciting experiences for thrill-seekers</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            @foreach($activities as $activity)
            <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-{{ $activity->type === 'zipline' ? 'parachute-box' : 'swimmer' }} text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $activity->name }}</h3>
                        <p class="text-gray-600">{{ $activity->description ?? 'Experience the thrill' }}</p>
                    </div>
                </div>
                <ul class="space-y-2 text-gray-600 mb-6">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Max {{ $activity->max_participants }} participants</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Duration: {{ $activity->duration }}</li>
                    @if($activity->safety_requirements)
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Safety equipment included</li>
                    @endif
                </ul>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-blue-600">${{ number_format($activity->price, 2) }}</span>
                    <a href="{{ route('booking.activities.create') }}?activity={{ $activity->id }}"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Book
                        Now</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Gallery Preview -->
@if($featuredGallery->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Photo Gallery</h2>
            <p class="text-xl text-gray-600">Take a glimpse of our beautiful property</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($featuredGallery->take(6) as $gallery)
            <div class="relative h-64 overflow-hidden rounded-lg group cursor-pointer">
                <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition">
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition">
                        <h4 class="font-semibold">{{ $gallery->title }}</h4>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('gallery') }}"
                class="text-blue-600 hover:text-blue-800 font-semibold">View Full Gallery →</a>
        </div>
    </div>
</section>
@endif

<!-- Contact Section -->
<section class="py-20 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">Get in Touch</h2>
            <p class="text-xl">Have questions? We'd love to hear from you</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <i class="fas fa-phone text-3xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Phone</h3>
                <p>+1 (555) 123-4567</p>
            </div>
            <div class="text-center">
                <i class="fas fa-envelope text-3xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Email</h3>
                <p>info@sapphirehotel.com</p>
            </div>
            <div class="text-center">
                <i class="fas fa-map-marker-alt text-3xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Address</h3>
                <p>123 Paradise Lane, Beach City, BC 12345</p>
            </div>
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('contact') }}"
                class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition inline-block">Contact
                Us</a>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('availabilityForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const resultsDiv = document.getElementById('availabilityResults');
    
    try {
        const response = await fetch('{{ route("api.check-availability") }}?' + new URLSearchParams({
            check_in: formData.get('check_in'),
            check_out: formData.get('check_out'),
        }));
        const data = await response.json();
        
        if (data.available && data.available.length > 0) {
            let html = '<div class="bg-green-50 border border-green-200 rounded-lg p-4"><h3 class="font-semibold text-green-800 mb-2">Available Rooms:</h3><ul class="space-y-2">';
            data.available.forEach(room => {
                html += `<li class="text-green-700">${room.room_type_name}: ${room.available_count} available from $${room.base_price}/night</li>`;
            });
            html += '</ul><a href="{{ route("booking.rooms.index") }}?check_in=' + formData.get('check_in') + '&check_out=' + formData.get('check_out') + '" class="mt-4 inline-block text-blue-600 hover:underline">Book Now →</a></div>';
            resultsDiv.innerHTML = html;
            resultsDiv.classList.remove('hidden');
        } else {
            resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">No rooms available for the selected dates. Please try different dates.</div>';
            resultsDiv.classList.remove('hidden');
        }
    } catch (error) {
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">Error checking availability. Please try again.</div>';
        resultsDiv.classList.remove('hidden');
    }
});
</script>
@endpush
@endsection
