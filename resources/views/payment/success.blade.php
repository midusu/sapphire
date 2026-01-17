@extends('layouts.public')

@section('title', 'Payment Successful - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
            <p class="text-gray-600 mb-8">Thank you for your booking. Your payment has been processed successfully.</p>
            
            <div class="bg-gray-50 rounded p-4 mb-6 text-left">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-semibold">${{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Transaction ID:</span>
                    <span class="font-mono text-sm">{{ $payment->transaction_id }}</span>
                </div>
                @if($payment->booking)
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking Reference:</span>
                    <span class="font-semibold">#{{ $payment->booking->id }}</span>
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <a href="{{ route('booking.rooms.index') }}" class="block w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
