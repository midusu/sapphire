@extends('layouts.app')

@section('title', 'Submit Feedback - Sapphire Hotel')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-blue-900 px-8 py-10 text-white">
                    <h1 class="text-3xl font-bold">Your Feedback Matters</h1>
                    <p class="text-blue-100 mt-2">We strive to provide the best experience possible. Please let us know how
                        we're doing or if you have any concerns.</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('guest.feedback.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback Type</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="type" value="feedback"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                        <span class="ml-2 text-gray-700">General Feedback</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="type" value="complaint"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                        <span class="ml-2 text-gray-700">Complaint</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                <select name="category"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="general">General</option>
                                    <option value="room">Room & Stay</option>
                                    <option value="food">Food & Dining</option>
                                    <option value="service" selected>Service & Staff</option>
                                    <option value="activity">Activities & Facilities</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="A brief summary of your feedback" required>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Related Stay (Optional)</label>
                            <select name="booking_id"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">None / Not specific to a stay</option>
                                @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}">
                                        Stay on {{ $booking->check_in_date->format('M d, Y') }} - Room
                                        {{ $booking->room->room_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rating</label>
                            <div class="flex items-center space-x-2" x-data="{ rating: 0, hover: 0 }">
                                <template x-for="i in 5">
                                    <button type="button" @click="rating = i; $refs.ratingInput.value = i"
                                        @mouseenter="hover = i" @mouseleave="hover = 0"
                                        class="text-3xl focus:outline-none transition"
                                        :class="(hover || rating) >= i ? 'text-yellow-400' : 'text-gray-300'">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </template>
                                <input type="hidden" name="rating" x-ref="ratingInput" value="0">
                                <span class="ml-4 text-gray-500 text-sm"
                                    x-text="rating > 0 ? rating + '/5 Stars' : 'Select a rating'"></span>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Your Message</label>
                            <textarea name="message" rows="6"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Please provide as much detail as possible..." required></textarea>
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('guest.dashboard') }}" class="text-gray-500 hover:text-gray-700 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i> Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection