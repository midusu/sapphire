<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::with('supplier')
            ->orderBy('name')
            ->paginate(15);

        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'min_stock_level')->count();

        return view('admin.inventory.index', compact('items', 'lowStockItems'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:toiletries,cleaning,food,equipment',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'min_stock_level' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $item = InventoryItem::create($request->all());

        // Log initial stock
        if ($item->quantity > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => 'in',
                'quantity' => $item->quantity,
                'reason' => 'Initial Stock',
                'user_id' => auth()->id(),
                'unit_price' => $item->cost_price
            ]);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item added successfully!');
    }

    public function edit(InventoryItem $item)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.inventory.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:toiletries,cleaning,food,equipment',
            'quantity' => 'required|numeric|min:0', // This is for manual adjustment
            'unit' => 'required|string|max:20',
            'min_stock_level' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        // Check if quantity changed manually
        if ($request->quantity != $item->quantity) {
            $diff = $request->quantity - $item->quantity;
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'reason' => 'Manual Adjustment',
                'user_id' => auth()->id(),
                'unit_price' => $request->cost_price ?? $item->cost_price
            ]);
        }

        $item->update($request->all());

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully!');
    }

    public function history(InventoryItem $item)
    {
        $transactions = $item->transactions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.history', compact('item', 'transactions'));
    }

    public function lowStockAlerts()
    {
        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'min_stock_level')
            ->select('id', 'name', 'quantity', 'min_stock_level', 'unit')
            ->get();

        // Send notifications to admins if there are low stock items
        if ($lowStockItems->isNotEmpty()) {
            $admins = \App\Models\User::whereHas('role', function($query) {
                $query->whereIn('slug', ['admin', 'manager']);
            })->get();

            foreach ($lowStockItems as $item) {
                $fullItem = InventoryItem::find($item->id);
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\LowInventoryNotification($fullItem));
                }
            }
        }

        return response()->json($lowStockItems);
    }
}
