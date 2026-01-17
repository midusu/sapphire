<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityBooking;
use App\Notifications\ActivityReminderNotification;
use Carbon\Carbon;

class SendActivityReminders extends Command
{
    protected $signature = 'notifications:activity-reminders';
    protected $description = 'Send activity reminder notifications to guests';

    public function handle()
    {
        // Send reminders 24 hours before activity
        $reminderTime = Carbon::now()->addDay();
        
        $activityBookings = ActivityBooking::where('status', 'confirmed')
            ->whereBetween('scheduled_time', [
                $reminderTime->copy()->startOfDay(),
                $reminderTime->copy()->endOfDay()
            ])
            ->with(['user', 'activity'])
            ->get();

        $sentCount = 0;
        
        foreach ($activityBookings as $booking) {
            if ($booking->user) {
                // Check if reminder was already sent (you might want to add a field for this)
                $booking->user->notify(new ActivityReminderNotification($booking));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} activity reminder notifications.");
        
        return Command::SUCCESS;
    }
}
