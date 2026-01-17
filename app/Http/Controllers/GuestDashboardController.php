<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get current booking
        $currentBooking = $user->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->with(['room.roomType', 'payments'])
            ->first();

        // Calculate total spent (Current Booking Payments)
        $totalSpent = $currentBooking 
            ? $currentBooking->payments()->where('status', 'completed')->sum('amount') 
            : 0;

        // Get upcoming activities
        $upcomingActivities = $user->activityBookings()
            ->where('scheduled_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->with('activity')
            ->orderBy('scheduled_time')
            ->take(5)
            ->get();

        // Get recent food orders
        $recentOrders = $user->foodOrders()
            ->with(['food'])
            ->orderBy('order_time', 'desc')
            ->take(5)
            ->get();

        // Get unread notifications
        $unreadNotifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('guest.dashboard', compact(
            'currentBooking', 
            'recentOrders', 
            'unreadNotifications', 
            'totalSpent', 
            'upcomingActivities'
        ));
    }

    public function foodMenu(Request $request)
    {
        $menuType = $request->get('menu_type', 'room_service');
        
        $foods = Food::where('available', true)
            ->forMenuType($menuType)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('guest.food-menu', compact('foods', 'menuType'));
    }

    public function orderFood(Food $food)
    {
        $user = Auth::user();
        $currentBooking = $user->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->with(['room.roomType'])
            ->first();

        if (!$currentBooking && $food->menu_type === 'room_service') {
            return back()->with('error', 'You must have an active booking to order room service.');
        }

        return view('guest.order-food', compact('food', 'currentBooking'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required|integer|min:1|max:10',
            'special_instructions' => 'nullable|string|max:500',
            'order_type' => 'required|in:room_service,dine_in',
            'table_number' => 'nullable|required_if:order_type,dine_in|string|max:10',
            'scheduled_time' => 'nullable|date|after:now',
            'menu_type' => 'required|in:room_service,restaurant',
        ]);

        $user = Auth::user();
        $food = Food::findOrFail($validated['food_id']);
        $totalPrice = $food->price * $validated['quantity'];

        // Get current booking for room service
        $currentBooking = null;
        if ($validated['order_type'] === 'room_service') {
            $currentBooking = $user->bookings()
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->first();
            
            if (!$currentBooking) {
                return back()->with('error', 'You must have an active booking to order room service.');
            }
        }

        $isScheduled = !empty($validated['scheduled_time']);
        $orderTime = $isScheduled ? $validated['scheduled_time'] : now();

        $order = FoodOrder::create([
            'food_id' => $validated['food_id'],
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => $user->phone ?? '',
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'order_type' => $validated['order_type'],
            'table_number' => $validated['table_number'] ?? null,
            'booking_id' => $currentBooking?->id,
            'room_number' => $currentBooking?->room->room_number,
            'order_source' => 'guest',
            'menu_type' => $validated['menu_type'],
            'scheduled_time' => $validated['scheduled_time'] ?? null,
            'is_scheduled' => $isScheduled,
            'status' => $isScheduled ? 'scheduled' : 'pending',
            'order_time' => $orderTime,
        ]);

        // Generate KOT number
        $order->generateKotNumber();

        // Create notification for guest
        $order->createNotification(
            'order_placed',
            'Order Placed Successfully',
            "Your {$order->getMenuTypeLabel()} order for {$food->name} has been placed. " . 
            ($isScheduled ? "Scheduled for {$order->scheduled_time->format('M j, Y H:i')}" : "Being prepared now")
        );

        return redirect()->route('guest.dashboard')
            ->with('success', 'Your food order has been placed successfully!');
    }

    public function myOrders()
    {
        $user = Auth::user();
        
        $orders = $user->foodOrders()
            ->with(['food', 'booking.room'])
            ->orderBy('order_time', 'desc')
            ->paginate(10);

        return view('guest.my-orders', compact('orders'));
    }

    public function notifications()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Mark all as read
        $user->unreadNotifications()->update(['read_at' => now()]);

        return view('guest.notifications', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
}
