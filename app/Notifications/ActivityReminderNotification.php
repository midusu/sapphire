<?php

namespace App\Notifications;

use App\Models\ActivityBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\VonageMessage; // Uncomment when SMS is configured

class ActivityReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ActivityBooking $activityBooking
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
        $activityName = $this->activityBooking->activity->name ?? 'Activity';
        $scheduledTime = $this->activityBooking->scheduled_time->format('F j, Y \a\t g:i A');
        $location = $this->activityBooking->activity->location ?? 'Hotel';
        
        return (new MailMessage)
            ->subject('Activity Reminder - ' . $activityName)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder about your upcoming activity booking.')
            ->line('**Activity Details:**')
            ->line('Activity: ' . $activityName)
            ->line('Scheduled Time: ' . $scheduledTime)
            ->line('Location: ' . $location)
            ->line('Participants: ' . $this->activityBooking->participants)
            ->line('Please arrive 15 minutes before your scheduled time.')
            ->action('View Booking', route('admin.activities.bookings.show', $this->activityBooking))
            ->line('We look forward to seeing you!');
    }

    // Uncomment when SMS/Vonage is configured
    // public function toVonage(object $notifiable): VonageMessage
    // {
    //     $activityName = $this->activityBooking->activity->name ?? 'Activity';
    //     $scheduledTime = $this->activityBooking->scheduled_time->format('M j, Y g:i A');
    //     
    //     return (new VonageMessage)
    //         ->content("Reminder: {$activityName} scheduled for {$scheduledTime}. Please arrive 15 minutes early.");
    // }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'activity_reminder',
            'title' => 'Activity Reminder',
            'message' => 'Your ' . ($this->activityBooking->activity->name ?? 'activity') . ' is scheduled for ' . $this->activityBooking->scheduled_time->format('M j, Y g:i A'),
            'activity_booking_id' => $this->activityBooking->id,
            'activity_name' => $this->activityBooking->activity->name ?? null,
            'scheduled_time' => $this->activityBooking->scheduled_time->format('Y-m-d H:i:s'),
        ];
    }
}
