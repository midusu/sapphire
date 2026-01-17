<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function dashboard()
    {
        $pendingOrders = FoodOrder::with(['food', 'booking'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('order_time', 'asc')
            ->get();

        $completedOrders = FoodOrder::with(['food', 'booking'])
            ->where('status', 'ready')
            ->orderBy('kitchen_completed_time', 'desc')
            ->take(10)
            ->get();

        return view('kitchen.dashboard', compact('pendingOrders', 'completedOrders'));
    }

    public function kotTicket(FoodOrder $foodOrder)
    {
        $foodOrder->load(['food', 'booking']);
        $foodOrder->generateKotNumber();
        
        return view('kitchen.kot-ticket', compact('foodOrder'));
    }

    public function updateOrderStatus(Request $request, FoodOrder $foodOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:preparing,ready,delivered',
        ]);

        $foodOrder->update($validated);

        if ($validated['status'] === 'ready') {
            $foodOrder->kitchen_completed_time = now();
            $foodOrder->save();
        }

        if ($validated['status'] === 'delivered' && $foodOrder->order_type === 'room_service') {
            $foodOrder->addToRoomBill();
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function printKot(FoodOrder $foodOrder)
    {
        $foodOrder->load(['food', 'booking']);
        $foodOrder->generateKotNumber();
        
        return view('kitchen.print-kot', compact('foodOrder'));
    }

    public function activeOrders()
    {
        $orders = FoodOrder::with(['food', 'booking'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('order_time', 'desc')
            ->paginate(20);

        return view('kitchen.active-orders', compact('orders'));
    }

    public function orderHistory()
    {
        $orders = FoodOrder::with(['food', 'booking'])
            ->whereIn('status', ['delivered', 'cancelled'])
            ->orderBy('order_time', 'desc')
            ->paginate(20);

        return view('kitchen.order-history', compact('orders'));
    }
}
