@extends('layouts.public')

@section('title', 'Payment Cancelled - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                <i class="fas fa-times text-red-600 text-3xl"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Cancelled</h2>
            <p class="text-gray-600 mb-8">You have cancelled the payment process. No charges were made.</p>
            
            <div class="space-y-4">
                <a href="{{ route('booking.rooms.index') }}" class="block w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    Try Again
                </a>
                <a href="{{ route('booking.rooms.index') }}" class="block w-full bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300 transition">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
