@extends('layouts.public')

@section('title', 'Notifications - Sapphire Hotel')

@section('content')
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('guest.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        </div>

        @if($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <li class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-600' }}">
                                        @if($notification->type == 'order_status' || str_contains($notification->type, 'order'))
                                            <i class="fas fa-utensils"></i>
                                        @elseif($notification->type == 'booking_confirmation' || str_contains($notification->type, 'booking'))
                                            <i class="fas fa-calendar-check"></i>
                                        @else
                                            <i class="fas fa-bell"></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $notification->title }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $notification->message }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-bell-slash text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-600">You don't have any notifications at the moment.</p>
            </div>
        @endif
    </div>
@endsection