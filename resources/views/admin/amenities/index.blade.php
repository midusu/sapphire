@extends('admin.layout')

@section('title', 'Amenities Management - Sapphire Hotel')
@section('header', 'Amenities Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Amenities</h2>
        <p class="text-gray-600">Manage hotel amenities</p>
    </div>
    <a href="{{ route('admin.amenities.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Add Amenity
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon/Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($amenities as $amenity)
                <tr>
                    <td class="px-6 py-4">
                        @if($amenity->icon)
                        <i class="{{ $amenity->icon }} text-2xl text-blue-600"></i>
                        @elseif($amenity->image_path)
                        <img src="{{ asset('storage/' . $amenity->image_path) }}" alt="{{ $amenity->name }}"
                            class="h-12 w-12 object-cover rounded">
                        @else
                        <i class="fas fa-star text-gray-400 text-2xl"></i>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $amenity->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $amenity->category }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $amenity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $amenity->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('admin.amenities.edit', $amenity) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}" class="inline" onsubmit="return confirm('Delete this amenity?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No amenities found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-3 border-t">{{ $amenities->links() }}</div>
</div>
@endsection
