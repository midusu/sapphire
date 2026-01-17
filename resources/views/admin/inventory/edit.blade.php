@extends('admin.layout')

@section('title', 'Edit Inventory Item - Sapphire Hotel Management')
@section('header', 'Edit Item: ' . $item->name)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.inventory.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
        </div>

        <!-- Quick Stock Adjustment -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Adjust Stock Manually</h3>
            <p class="text-sm text-gray-600 mb-4">Any change to the "Quantity" field below will be automatically logged as a
                transaction.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.inventory.update', $item) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="food" {{ old('category', $item->category) == 'food' ? 'selected' : '' }}>Food &
                                Beverage</option>
                            <option value="toiletries" {{ old('category', $item->category) == 'toiletries' ? 'selected' : '' }}>Toiletries</option>
                            <option value="cleaning" {{ old('category', $item->category) == 'cleaning' ? 'selected' : '' }}>
                                Cleaning Supplies</option>
                            <option value="equipment" {{ old('category', $item->category) == 'equipment' ? 'selected' : '' }}>
                                Equipment</option>
                        </select>
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Quantity (The key field) -->
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Current Quantity</label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity', $item->quantity) }}"
                            class="w-full border-2 border-yellow-400 rounded-lg px-3 py-2 text-lg font-mono" required>
                        <p class="text-xs text-yellow-700 mt-1">Changing this value will update stock.</p>
                        @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit', $item->unit) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Min Stock Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Alert Level</label>
                        <input type="number" step="0.01" name="min_stock_level"
                            value="{{ old('min_stock_level', $item->min_stock_level) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('min_stock_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price Per Unit ($)</label>
                        <input type="number" step="0.01" name="cost_price"
                            value="{{ old('cost_price', $item->cost_price) }}" class="w-full border rounded-lg px-3 py-2">
                        @error('cost_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Supplier (Optional)</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full border rounded-lg px-3 py-2">{{ old('notes', $item->notes) }}</textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.inventory.index') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection