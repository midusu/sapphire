@extends('admin.layout')

@section('title', 'Coupons - Sapphire')
@section('header', 'Coupon Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Coupons</h2>
    <a href="{{ route('admin.coupons.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Create Coupon
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($coupons as $coupon)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $coupon->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ ucfirst($coupon->type) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                    {{ $coupon->type == 'fixed' ? '$' . $coupon->value : $coupon->value . '%' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                    {{ $coupon->used }} / {{ $coupon->limit ?? '∞' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                    {{ $coupon->start_date ? $coupon->start_date->format('M d') : 'Any' }} - 
                    {{ $coupon->end_date ? $coupon->end_date->format('M d') : 'Any' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $coupons->links() }}
    </div>
</div>
@endsection
