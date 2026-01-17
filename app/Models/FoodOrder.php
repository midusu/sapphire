<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically deduct inventory when order status changes to 'delivered'
        static::updated(function ($foodOrder) {
            if ($foodOrder->isDirty('status') && 
                $foodOrder->status === 'delivered' && 
                !$foodOrder->inventory_deducted) {
                $foodOrder->deductInventory();
            }
        });
    }
    protected $fillable = [
        'food_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'quantity',
        'total_price',
        'special_instructions',
        'status',
        'order_time',
        'delivery_time',
        'room_number',
        'order_type',
        'table_number',
        'booking_id',
        'added_to_room_bill',
        'kot_number',
        'kot_time',
        'kitchen_completed_time',
        'menu_type',
        'scheduled_time',
        'is_scheduled',
        'user_id',
        'order_source',
        'inventory_deducted',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'order_time' => 'datetime',
        'delivery_time' => 'datetime',
        'quantity' => 'integer',
        'added_to_room_bill' => 'boolean',
        'kot_time' => 'datetime',
        'kitchen_completed_time' => 'datetime',
        'scheduled_time' => 'datetime',
        'is_scheduled' => 'boolean',
        'inventory_deducted' => 'boolean',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColor()
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'preparing' => 'blue',
            'ready' => 'green',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function generateKotNumber()
    {
        if (!$this->kot_number) {
            $this->kot_number = 'KOT-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->kot_time = now();
            $this->save();
        }
        return $this->kot_number;
    }

    public function addToRoomBill()
    {
        if ($this->order_type === 'room_service' && $this->booking && !$this->added_to_room_bill) {
            // Create payment record for the room bill
            Payment::create([
                'booking_id' => $this->booking_id,
                'amount' => $this->total_price,
                'payment_method' => 'cash', // Default to cash for room charges, or 'charge_to_room' if enum supports it
                'status' => 'completed',
                'notes' => "Room Service: {$this->food->name} x{$this->quantity}",
                'created_at' => now(),
            ]);

            $this->added_to_room_bill = true;
            $this->save();
        }
    }

    public function getOrderTypeLabel()
    {
        return match ($this->order_type) {
            'room_service' => 'Room Service',
            'dine_in' => 'Dine-in',
            default => 'Unknown'
        };
    }

    public function getMenuTypeLabel()
    {
        return match ($this->menu_type) {
            'room_service' => 'Room Service',
            'restaurant' => 'Restaurant',
            default => 'Both'
        };
    }

    public function isScheduled()
    {
        return $this->is_scheduled && $this->scheduled_time && $this->scheduled_time > now();
    }

    public function getDeliveryTimeLabel()
    {
        if ($this->isScheduled()) {
            return 'Scheduled for ' . $this->scheduled_time->format('M j, Y H:i');
        }

        if ($this->delivery_time) {
            return 'Delivered at ' . $this->delivery_time->format('H:i');
        }

        $estimatedTime = $this->order_time->copy()->addMinutes($this->food->preparation_time);
        return 'Estimated ' . $estimatedTime->format('H:i');
    }

    public function createNotification($type, $title, $message)
    {
        if ($this->user_id) {
            $this->user->notifications()->create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'notifiable_type' => FoodOrder::class,
                'notifiable_id' => $this->id,
                'data' => [
                    'order_id' => $this->id,
                    'food_name' => $this->food->name,
                    'status' => $this->status
                ]
            ]);
        }
    }

    /**
     * Deduct inventory when order is delivered
     */
    public function deductInventory()
    {
        if ($this->inventory_deducted) {
            return; // Already deducted
        }

        if (!$this->food || !$this->food->inventory_item_id) {
            return; // No inventory item linked
        }

        $inventoryItem = InventoryItem::find($this->food->inventory_item_id);
        if (!$inventoryItem) {
            return; // Inventory item not found
        }

        // Calculate deduction amount (1 unit per food item ordered)
        // This can be enhanced later to support recipes with multiple ingredients
        $deductionAmount = $this->quantity;

        // Check if we have enough stock
        if ($inventoryItem->quantity < $deductionAmount) {
            \Illuminate\Support\Facades\Log::warning('Insufficient inventory stock', [
                'food_order_id' => $this->id,
                'inventory_item_id' => $inventoryItem->id,
                'required' => $deductionAmount,
                'available' => $inventoryItem->quantity
            ]);
            // Still deduct what we have, but log the issue
            $deductionAmount = $inventoryItem->quantity;
        }

        if ($deductionAmount > 0) {
            // Deduct from inventory
            $inventoryItem->decrement('quantity', $deductionAmount);

            // Create transaction record
            InventoryTransaction::create([
                'inventory_item_id' => $inventoryItem->id,
                'type' => 'out',
                'quantity' => $deductionAmount,
                'reason' => 'Food Order: ' . ($this->kot_number ?? 'Order #' . $this->id) . ' - ' . $this->food->name,
                'reference_id' => $this->id,
                'user_id' => auth()->id() ?? null,
                'unit_price' => $inventoryItem->cost_price
            ]);

            // Mark as deducted
            $this->update(['inventory_deducted' => true]);

            \Illuminate\Support\Facades\Log::info('Inventory deducted for food order', [
                'food_order_id' => $this->id,
                'inventory_item_id' => $inventoryItem->id,
                'quantity_deducted' => $deductionAmount
            ]);
        }
    }
}
