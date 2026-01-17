@extends('admin.layout')

@section('title', 'Edit Gallery Image - Sapphire Hotel')
@section('header', 'Edit Gallery Image')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $gallery->title) }}"
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('description', $gallery->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}"
                    class="h-32 w-32 object-cover rounded mb-2">
                <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">Change Image</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category" required class="w-full border-gray-300 rounded-lg">
                    <option value="general" {{ $gallery->category == 'general' ? 'selected' : '' }}>General</option>
                    <option value="rooms" {{ $gallery->category == 'rooms' ? 'selected' : '' }}>Rooms</option>
                    <option value="activities" {{ $gallery->category == 'activities' ? 'selected' : '' }}>Activities</option>
                    <option value="amenities" {{ $gallery->category == 'amenities' ? 'selected' : '' }}>Amenities</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $gallery->display_order) }}"
                        class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="flex items-center space-x-4 pt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ $gallery->is_featured ? 'checked' : '' }}
                            class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $gallery->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('admin.gallery.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
