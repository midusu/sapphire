<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Notifications\LowInventoryNotification;
use App\Models\User;

class CheckLowInventory extends Command
{
    protected $signature = 'notifications:check-low-inventory';
    protected $description = 'Check for low inventory items and send notifications';

    public function handle()
    {
        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'min_stock_level')->get();
        
        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return Command::SUCCESS;
        }

        // Send notifications to admins and managers
        $admins = User::whereHas('role', function($query) {
            $query->whereIn('slug', ['admin', 'manager']);
        })->get();

        $sentCount = 0;
        
        foreach ($lowStockItems as $item) {
            foreach ($admins as $admin) {
                $admin->notify(new LowInventoryNotification($item));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} low inventory notifications for " . $lowStockItems->count() . " items.");
        
        return Command::SUCCESS;
    }
}
