<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\VonageMessage; // Uncomment when SMS is configured

class LowInventoryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public InventoryItem $inventoryItem
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [\App\Broadcasting\CustomDatabaseChannel::class];
        
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        // SMS requires Vonage/Twilio package installation
        // if ($notifiable->phone && config('services.sms.enabled', false)) {
        //     $channels[] = 'vonage';
        // }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $currentStock = $this->inventoryItem->quantity;
        $minStock = $this->inventoryItem->min_stock_level;
        $supplier = $this->inventoryItem->supplier->name ?? 'No supplier assigned';
        
        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $this->inventoryItem->name)
            ->greeting('Inventory Alert')
            ->line('**Low Stock Alert**')
            ->line('Item: ' . $this->inventoryItem->name)
            ->line('Category: ' . ucfirst($this->inventoryItem->category))
            ->line('Current Stock: ' . $currentStock . ' ' . $this->inventoryItem->unit)
            ->line('Minimum Stock Level: ' . $minStock . ' ' . $this->inventoryItem->unit)
            ->line('Supplier: ' . $supplier)
            ->line('**Action Required:** Please restock this item immediately.')
            ->action('View Inventory', route('admin.inventory.index'))
            ->error();
    }

    // Uncomment when SMS/Vonage is configured
    // public function toVonage(object $notifiable): VonageMessage
    // {
    //     $currentStock = $this->inventoryItem->quantity;
    //     $minStock = $this->inventoryItem->min_stock_level;
    //     
    //     return (new VonageMessage)
    //         ->content("Low stock alert: {$this->inventoryItem->name} - Current: {$currentStock}, Min: {$minStock}. Please restock.");
    // }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_inventory',
            'title' => 'Low Stock Alert',
            'message' => $this->inventoryItem->name . ' is below minimum stock level (' . $this->inventoryItem->quantity . ' ' . $this->inventoryItem->unit . ')',
            'inventory_item_id' => $this->inventoryItem->id,
            'item_name' => $this->inventoryItem->name,
            'current_stock' => $this->inventoryItem->quantity,
            'min_stock_level' => $this->inventoryItem->min_stock_level,
            'category' => $this->inventoryItem->category,
        ];
    }
}
