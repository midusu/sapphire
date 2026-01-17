@extends('admin.layout')

@section('title', 'Payment Reports - Sapphire Hotel Management')
@section('header', 'Payment Reports')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Payment Reports</h2>
    <div class="flex space-x-2">
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-print mr-2"></i>Print Report
        </button>
        <a href="{{ route('admin.payments.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            <i class="fas fa-filter mr-2"></i>Update Report
        </button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Payments</p>
                <p class="text-2xl font-bold text-gray-800">{{ $summary['total_payments'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-receipt text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($summary['total_amount'], 2) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Completed Amount</p>
                <p class="text-2xl font-bold text-blue-600">${{ number_format($summary['completed_amount'], 2) }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-check text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Refunded Amount</p>
                <p class="text-2xl font-bold text-red-600">${{ number_format($summary['refunded_amount'], 2) }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-undo text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Payment Methods Breakdown -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods Breakdown</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($summary['by_method'] as $method => $data)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-{{ $method === 'cash' ? 'money-bill' : ($method === 'card' ? 'credit-card' : ($method === 'bank_transfer' ? 'university' : 'globe')) }} mr-2"></i>
                        {{ ucfirst($method) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $data['count'] }} payments</span>
                </div>
                <p class="text-xl font-bold text-gray-800">${{ number_format($data['total'], 2) }}</p>
                <div class="mt-2">
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $summary['total_amount'] > 0 ? ($data['total'] / $summary['total_amount'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Revenue by Type -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Type</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">
                    <i class="fas fa-bed mr-2"></i>Room Bookings
                </span>
            </div>
            <p class="text-xl font-bold text-gray-800">${{ number_format($summary['by_type']['bookings'], 2) }}</p>
            <div class="mt-2">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $summary['total_amount'] > 0 ? ($summary['by_type']['bookings'] / $summary['total_amount'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">
                    <i class="fas fa-hiking mr-2"></i>Activities
                </span>
            </div>
            <p class="text-xl font-bold text-gray-800">${{ number_format($summary['by_type']['activities'], 2) }}</p>
            <div class="mt-2">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $summary['total_amount'] > 0 ? ($summary['by_type']['activities'] / $summary['total_amount'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Payment Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->booking?->user->name ?? $payment->activityBooking?->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($payment->booking_id)
                                <span class="text-blue-600">Room Booking</span>
                            @else
                                <span class="text-orange-600">Activity</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <i class="fas fa-{{ $payment->payment_method === 'cash' ? 'money-bill' : ($payment->payment_method === 'card' ? 'credit-card' : ($payment->payment_method === 'bank_transfer' ? 'university' : 'globe')) }} mr-2"></i>
                            {{ ucfirst($payment->payment_method) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No payments found in this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Report Summary -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Summary</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
        <div>
            <span class="text-gray-600">Report Period:</span>
            <p class="font-medium">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <div>
            <span class="text-gray-600">Net Revenue:</span>
            <p class="font-medium text-green-600">${{ number_format($summary['completed_amount'] - $summary['refunded_amount'], 2) }}</p>
        </div>
        <div>
            <span class="text-gray-600">Average Payment:</span>
            <p class="font-medium">${{ $summary['total_payments'] > 0 ? number_format($summary['total_amount'] / $summary['total_payments'], 2) : '0.00' }}</p>
        </div>
        <div>
            <span class="text-gray-600">Generated:</span>
            <p class="font-medium">{{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-white {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    }
}
</style>
@endsection
