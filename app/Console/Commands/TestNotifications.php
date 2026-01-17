<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ActivityBooking;
use App\Models\Room;
use App\Models\InventoryItem;
use App\Notifications\BookingConfirmationNotification;
use App\Notifications\PaymentAlertNotification;
use App\Notifications\ActivityReminderNotification;
use App\Notifications\HousekeepingAlertNotification;
use App\Notifications\LowInventoryNotification;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test {type=all}';
    protected $description = 'Test notification system by sending sample notifications';

    public function handle()
    {
        $type = $this->argument('type');
        
        $admin = User::whereHas('role', function($query) {
            $query->where('slug', 'admin');
        })->first();

        if (!$admin) {
            $this->error('No admin user found. Please create an admin user first.');
            return Command::FAILURE;
        }

        $this->info("Sending test notifications to: {$admin->name} ({$admin->email})");

        if ($type === 'all' || $type === 'booking') {
            $booking = Booking::with(['room.roomType'])->first();
            if ($booking) {
                $admin->notify(new BookingConfirmationNotification($booking));
                $this->info('✓ Booking confirmation notification sent');
            } else {
                $this->warn('No booking found for testing');
            }
        }

        if ($type === 'all' || $type === 'payment') {
            $payment = Payment::first();
            if ($payment) {
                $admin->notify(new PaymentAlertNotification($payment, 'completed'));
                $this->info('✓ Payment alert notification sent');
            } else {
                $this->warn('No payment found for testing');
            }
        }

        if ($type === 'all' || $type === 'activity') {
            $activityBooking = ActivityBooking::with('activity')->first();
            if ($activityBooking) {
                $admin->notify(new ActivityReminderNotification($activityBooking));
                $this->info('✓ Activity reminder notification sent');
            } else {
                $this->warn('No activity booking found for testing');
            }
        }

        if ($type === 'all' || $type === 'housekeeping') {
            $room = Room::first();
            if ($room) {
                $admin->notify(new HousekeepingAlertNotification($room, 'cleaning'));
                $this->info('✓ Housekeeping alert notification sent');
            } else {
                $this->warn('No room found for testing');
            }
        }

        if ($type === 'all' || $type === 'inventory') {
            $inventoryItem = InventoryItem::first();
            if ($inventoryItem) {
                $admin->notify(new LowInventoryNotification($inventoryItem));
                $this->info('✓ Low inventory notification sent');
            } else {
                $this->warn('No inventory item found for testing');
            }
        }

        $this->info("\nAll test notifications sent! Check your notifications page.");
        return Command::SUCCESS;
    }
}
