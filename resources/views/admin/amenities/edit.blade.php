@extends('admin.layout')

@section('title', 'Edit Amenity - Sapphire Hotel')
@section('header', 'Edit Amenity')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.amenities.update', $amenity) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" required value="{{ old('name', $amenity->name) }}"
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('description', $amenity->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Font Awesome class)</label>
                <input type="text" name="icon" value="{{ old('icon', $amenity->icon) }}" placeholder="e.g., fas fa-wifi"
                    class="w-full border-gray-300 rounded-lg">
            </div>
            @if($amenity->image_path)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <img src="{{ asset('storage/' . $amenity->image_path) }}" alt="{{ $amenity->name }}"
                    class="h-32 w-32 object-cover rounded mb-2">
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Change Image</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category" required class="w-full border-gray-300 rounded-lg">
                    <option value="general" {{ $amenity->category == 'general' ? 'selected' : '' }}>General</option>
                    <option value="room" {{ $amenity->category == 'room' ? 'selected' : '' }}>Room</option>
                    <option value="hotel" {{ $amenity->category == 'hotel' ? 'selected' : '' }}>Hotel</option>
                    <option value="activity" {{ $amenity->category == 'activity' ? 'selected' : '' }}>Activity</option>
                    <option value="dining" {{ $amenity->category == 'dining' ? 'selected' : '' }}>Dining</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $amenity->display_order) }}"
                        class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="flex items-center space-x-4 pt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ $amenity->is_featured ? 'checked' : '' }}
                            class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $amenity->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('admin.amenities.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
