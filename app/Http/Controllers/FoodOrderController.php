<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodOrder;
use Illuminate\Http\Request;

class FoodOrderController extends Controller
{
    public function menu()
    {
        $foods = Food::where('available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('food.menu', compact('foods'));
    }

    public function orderForm(Food $food)
    {
        return view('food.order', compact('food'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:food,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'quantity' => 'required|integer|min:1|max:10',
            'special_instructions' => 'nullable|string|max:500',
            'room_number' => 'nullable|string|max:10',
            'order_type' => 'required|in:room_service,dine_in',
            'table_number' => 'nullable|required_if:order_type,dine_in|string|max:10',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $food = Food::findOrFail($validated['food_id']);
        $totalPrice = $food->price * $validated['quantity'];

        $order = FoodOrder::create([
            'food_id' => $validated['food_id'],
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'room_number' => $validated['room_number'] ?? null,
            'order_type' => $validated['order_type'],
            'table_number' => $validated['table_number'] ?? null,
            'booking_id' => $validated['booking_id'] ?? null,
            'status' => 'pending',
            'order_time' => now(),
        ]);

        // Generate KOT number
        $order->generateKotNumber();

        return redirect()->route('food.confirmation', $order)
            ->with('success', 'Your food order has been placed successfully!');
    }

    public function confirmation(FoodOrder $order)
    {
        return view('food.confirmation', compact('order'));
    }

    public function myOrders(Request $request)
    {
        $email = $request->get('email');
        $orders = collect();

        if ($email) {
            $orders = FoodOrder::where('guest_email', $email)
                ->with('food')
                ->orderBy('order_time', 'desc')
                ->get();
        }

        return view('food.my-orders', compact('orders', 'email'));
    }

    // Admin methods for food management
    public function adminIndex()
    {
        $foods = Food::orderBy('category')->orderBy('name')->paginate(10);
        return view('admin.food.items.index', compact('foods'));
    }

    public function adminCreate()
    {
        $inventoryItems = \App\Models\InventoryItem::where('category', 'food')->orderBy('name')->get();
        return view('admin.food.items.create', compact('inventoryItems'));
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'available' => 'boolean',
            'image_url' => 'nullable|url',
            'preparation_time' => 'required|integer|min:1',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
        ]);

        Food::create($validated);

        return redirect()->route('admin.food.items.index')
            ->with('success', 'Food item created successfully!');
    }

    public function adminShow(Food $food)
    {
        return view('admin.food.items.show', compact('food'));
    }

    public function adminEdit(Food $food)
    {
        $inventoryItems = \App\Models\InventoryItem::where('category', 'food')->orderBy('name')->get();
        return view('admin.food.items.edit', compact('food', 'inventoryItems'));
    }

    public function adminUpdate(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'available' => 'boolean',
            'image_url' => 'nullable|url',
            'preparation_time' => 'required|integer|min:1',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
        ]);

        $food->update($validated);

        return redirect()->route('admin.food.items.index')
            ->with('success', 'Food item updated successfully!');
    }

    public function adminDestroy(Food $food)
    {
        $food->delete();
        return redirect()->route('admin.food.items.index')
            ->with('success', 'Food item deleted successfully!');
    }

    // Admin methods for order management
    public function adminOrders()
    {
        $orders = FoodOrder::with('food')
            ->orderBy('order_time', 'desc')
            ->paginate(15);
        return view('admin.food.orders.index', compact('orders'));
    }

    public function adminOrderShow(FoodOrder $foodOrder)
    {
        $foodOrder->load('food');
        return view('admin.food.orders.show', compact('foodOrder'));
    }

    public function updateStatus(Request $request, FoodOrder $foodOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled',
        ]);

        $oldStatus = $foodOrder->status;
        $foodOrder->update($validated);

        if ($validated['status'] === 'delivered') {
            $foodOrder->update(['delivery_time' => now()]);
            // Inventory deduction will be handled automatically by the model event
        }

        return redirect()->back()
            ->with('success', 'Order status updated successfully!');
    }

    public function completeOrder(FoodOrder $foodOrder)
    {
        $foodOrder->update([
            'status' => 'delivered',
            'delivery_time' => now(),
        ]);

        // Inventory deduction will be handled automatically by the model event
        // The deductInventory() method is called via the model's updated event

        return redirect()->back()
            ->with('success', 'Order marked as delivered and stock updated!');
    }

    public function cancelOrder(FoodOrder $foodOrder)
    {
        $foodOrder->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Order cancelled successfully!');
    }
}
