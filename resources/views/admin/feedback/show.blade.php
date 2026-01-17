@extends('admin.layout')

@section('title', 'Feedback Details - Sapphire Hotel Management')
@section('header', 'Feedback Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <div>
                    <span
                        class="px-2 py-1 text-xs rounded-full {{ $feedback->type == 'complaint' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($feedback->type) }}
                    </span>
                    <span class="text-sm text-gray-500 ml-2">Category: {{ ucfirst($feedback->category) }}</span>
                </div>
                <span class="text-sm text-gray-500">Submitted on {{ $feedback->created_at->format('M d, Y H:i') }}</span>
            </div>

            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $feedback->subject }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Guest Detail</h3>
                        @if($feedback->user)
                            <p class="font-medium">{{ $feedback->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $feedback->user->email }}</p>
                            <a href="{{ route('admin.guests.show', $feedback->user) }}"
                                class="text-blue-600 text-sm hover:underline mt-1 inline-block">View Profile</a>
                        @else
                            <p class="font-medium text-gray-500 italic">Anonymous Guest</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Stay Detail</h3>
                        @if($feedback->booking)
                            <p class="font-medium">Booking #{{ str_pad($feedback->booking_id, 5, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-sm text-gray-600">{{ $feedback->booking->room->roomType->name }} -
                                {{ $feedback->booking->room->room_number }}</p>
                        @else
                            <p class="text-sm text-gray-500 italic">No booking linked</p>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Rating</h3>
                    <div class="text-xl text-yellow-400">
                        @if($feedback->rating)
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $feedback->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                            <span class="text-gray-600 text-sm ml-2">({{ $feedback->rating }}/5)</span>
                        @else
                            <span class="text-gray-400 italic text-sm">No rating provided</span>
                        @endif
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Message</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border text-gray-700 whitespace-pre-wrap">{{ $feedback->message }}
                    </div>
                </div>

                <hr class="my-8">

                <!-- Admin Response Section -->
                <form action="{{ route('admin.feedback.update', $feedback) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Internal Status</label>
                            <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pending" {{ $feedback->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="reviewed" {{ $feedback->status == 'reviewed' ? 'selected' : '' }}>Reviewed
                                </option>
                                <option value="resolved" {{ $feedback->status == 'resolved' ? 'selected' : '' }}>Resolved
                                </option>
                                <option value="ignored" {{ $feedback->status == 'ignored' ? 'selected' : '' }}>Ignored
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Internal Notes (Staff
                                Only)</label>
                            <textarea name="internal_notes" rows="3"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add notes about this complaint for other staff...">{{ $feedback->internal_notes }}</textarea>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Response to Guest (Sends email if
                            filled)</label>
                        <textarea name="response_message" rows="5"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Write your response to the guest here...">{{ $feedback->response_message }}</textarea>
                        @if($feedback->responded_at)
                            <p class="text-xs text-gray-500 mt-1 italic">Last response sent on
                                {{ $feedback->responded_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.feedback.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Update & Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection