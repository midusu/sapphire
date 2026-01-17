@extends('admin.layout')

@section('title', 'Add Inventory Item - Sapphire Hotel Management')
@section('header', 'Add New Inventory Item')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.inventory.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.inventory.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>Food & Beverage</option>
                            <option value="toiletries" {{ old('category') == 'toiletries' ? 'selected' : '' }}>Toiletries
                            </option>
                            <option value="cleaning" {{ old('category') == 'cleaning' ? 'selected' : '' }}>Cleaning Supplies
                            </option>
                            <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>Equipment
                            </option>
                        </select>
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Initial Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Initial Quantity</label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity', 0) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit (e.g., kg, pcs, liters)</label>
                        <input type="text" name="unit" value="{{ old('unit', 'pcs') }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Min Stock Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock Alert Level</label>
                        <input type="number" step="0.01" name="min_stock_level" value="{{ old('min_stock_level', 10) }}"
                            class="w-full border rounded-lg px-3 py-2" required>
                        @error('min_stock_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price Per Unit ($)</label>
                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}"
                            class="w-full border rounded-lg px-3 py-2">
                        @error('cost_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Supplier (Optional)</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                            class="w-full border rounded-lg px-3 py-2">{{ old('notes') }}</textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Add To Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection