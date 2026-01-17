<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sapphire Hotel - Luxury Accommodation & Adventures</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-gem text-blue-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">Sapphire</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">Register</a>
                    @else
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <a href="{{ route('admin.dashboard') }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">Admin
                                Panel</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

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
                <a href="#rooms"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition">Explore
                    Rooms</a>
                <a href="#activities"
                    class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white hover:text-gray-900 transition">View
                    Activities</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose Sapphire?</h2>
                <p class="text-xl text-gray-600">Discover our world-class amenities and services</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bed text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Luxury Rooms</h3>
                    <p class="text-gray-600">Spacious and elegantly designed rooms with modern amenities</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hiking text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Adventure Activities</h3>
                    <p class="text-gray-600">Experience thrilling zipline and refreshing swimming activities</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-concierge-bell text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Premium Service</h3>
                    <p class="text-gray-600">24/7 concierge service to make your stay memorable</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Rooms</h2>
                <p class="text-xl text-gray-600">Choose from our selection of luxurious accommodations</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset('images/rooms/standard-room.jpg') }}"
                        alt="Standard Room" class="h-48 w-full object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Standard Room</h3>
                        <p class="text-gray-600 mb-4">Comfortable room with basic amenities</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">$99.99</span>
                            <span class="text-gray-500">per night</span>
                        </div>
                        <a href="{{ route('booking.rooms.create') }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset('images/rooms/deluxe-room.jpg') }}"
                        alt="Deluxe Room" class="h-48 w-full object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Deluxe Room</h3>
                        <p class="text-gray-600 mb-4">Spacious room with premium amenities</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">$149.99</span>
                            <span class="text-gray-500">per night</span>
                        </div>
                        <a href="{{ route('booking.rooms.create') }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset('images/rooms/suite.jpg') }}"
                        alt="Suite" class="h-48 w-full object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Suite</h3>
                        <p class="text-gray-600 mb-4">Luxury suite with separate living area</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">$249.99</span>
                            <span class="text-gray-500">per night</span>
                        </div>
                        <a href="{{ route('booking.rooms.create') }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset('images/rooms/presidential-suite.jpg') }}"
                        alt="Presidential Suite" class="h-48 w-full object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Presidential Suite</h3>
                        <p class="text-gray-600 mb-4">Ultimate luxury experience</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">$499.99</span>
                            <span class="text-gray-500">per night</span>
                        </div>
                        <a href="{{ route('booking.rooms.create') }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section id="activities" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Adventure Activities</h2>
                <p class="text-xl text-gray-600">Exciting experiences for thrill-seekers</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="bg-gray-50 rounded-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-parachute-box text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Zipline Adventure</h3>
                            <p class="text-gray-600">Experience the thrill of ziplining</p>
                        </div>
                    </div>
                    <ul class="space-y-2 text-gray-600 mb-6">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Scenic mountain views</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Professional guides</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Safety equipment included</li>
                    </ul>
                    <div class="flex justify-between items-center">
                        <span class="text-3xl font-bold text-blue-600">$49.99</span>
                        <a href="{{ route('booking.activities.create') }}?activity=1"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-swimmer text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Swimming Pool</h3>
                            <p class="text-gray-600">Relax in our Olympic-size pool</p>
                        </div>
                    </div>
                    <ul class="space-y-2 text-gray-600 mb-6">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Olympic-size pool</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Life guards on duty</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Swimming lessons available</li>
                    </ul>
                    <div class="flex justify-between items-center">
                        <span class="text-3xl font-bold text-blue-600">$19.99</span>
                        <a href="{{ route('booking.activities.create') }}?activity=2"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Book
                            Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <i class="fas fa-gem text-blue-400 text-2xl mr-2"></i>
                    <span class="text-xl font-bold">Sapphire Hotel</span>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="mt-8 text-center text-gray-400">
                <p>&copy; 2024 Sapphire Hotel. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>