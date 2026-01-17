@extends('admin.layout')

@section('title', 'Inventory - Sapphire Hotel Management')
@section('header', 'Inventory Management')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Items</h2>
            <p class="text-gray-600">Track stock levels for all hotel assets</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.suppliers.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-truck mr-2"></i>Suppliers
            </a>
            <a href="{{ route('admin.inventory.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Item
            </a>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($lowStockItems > 0)
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Low Stock Alert</h3>
                    <p class="text-sm text-red-700 mt-1">
                        There are <strong>{{ $lowStockItems }}</strong> items below minimum stock level. Please restock
                        immediately.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock
                            Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="{{ $item->isLowStock() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                </div>
                                @if($item->isLowStock())
                                    <span class="text-xs text-red-600 font-bold">Low Stock!</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst($item->category) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span
                                        class="text-sm font-bold {{ $item->isLowStock() ? 'text-red-700' : 'text-gray-900' }}">
                                        {{ $item->quantity }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-1">{{ $item->unit }}</span>
                                </div>
                                <div class="text-xs text-gray-400">Min: {{ $item->min_stock_level }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->supplier->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($item->cost_price)
                                    ${{ number_format($item->cost_price * $item->quantity, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.inventory.history', $item) }}"
                                        class="text-gray-600 hover:text-gray-900" title="History">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <a href="{{ route('admin.inventory.edit', $item) }}"
                                        class="text-blue-600 hover:text-blue-900" title="Edit/Adjust">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No inventory items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t">
            {{ $items->links() }}
        </div>
    </div>
@endsection