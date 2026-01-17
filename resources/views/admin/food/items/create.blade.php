@extends('admin.layout')

@section('title', 'Add Food Item - Sapphire Hotel Management')
@section('header', 'Add New Food Item')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.food.items.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.food.items.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2"
                            required>{{ old('description') }}</textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="">Select Category</option>
                            <option value="appetizer" {{ old('category') == 'appetizer' ? 'selected' : '' }}>Appetizer
                            </option>
                            <option value="main_course" {{ old('category') == 'main_course' ? 'selected' : '' }}>Main Course
                            </option>
                            <option value="dessert" {{ old('category') == 'dessert' ? 'selected' : '' }}>Dessert</option>
                            <option value="beverage" {{ old('category') == 'beverage' ? 'selected' : '' }}>Beverage</option>
                            <option value="breakfast" {{ old('category') == 'breakfast' ? 'selected' : '' }}>Breakfast
                            </option>
                        </select>
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Preparation Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prep Time (mins)</label>
                        <input type="number" name="preparation_time" value="{{ old('preparation_time', 15) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('preparation_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Menu Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability Type</label>
                        <select name="menu_type" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="both" {{ old('menu_type') == 'both' ? 'selected' : '' }}>Remote Service &
                                Restaurant</option>
                            <option value="room_service" {{ old('menu_type') == 'room_service' ? 'selected' : '' }}>Room
                                Service Only</option>
                            <option value="restaurant" {{ old('menu_type') == 'restaurant' ? 'selected' : '' }}>Restaurant
                                Only</option>
                        </select>
                        @error('menu_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Image URL -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image URL (Optional)</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}"
                            class="w-full border rounded-lg px-3 py-2" placeholder="https://example.com/image.jpg">
                        @error('image_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-span-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="available" value="1" {{ old('available', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">Available for ordering</span>
                        </label>
                    </div>

                    <!-- Linked Inventory Item -->
                    <div class="col-span-2 border-t pt-4 mt-2">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Inventory Link (Optional)</h4>
                        <p class="text-sm text-gray-500 mb-2">Link this food item to an inventory product to automatically
                            deduct stock when ordered.</p>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Item</label>
                        <select name="inventory_item_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">None</option>
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} ({{ $item->quantity }} {{ $item->unit }} available)
                                </option>
                            @endforeach
                        </select>
                        @error('inventory_item_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Create Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection