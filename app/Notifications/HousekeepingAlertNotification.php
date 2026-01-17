<?php

namespace App\Notifications;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\VonageMessage; // Uncomment when SMS is configured

class HousekeepingAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Room $room,
        public string $alertType = 'cleaning' // cleaning, maintenance, check_out
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
        $subject = match($this->alertType) {
            'cleaning' => 'Room Cleaning Required',
            'maintenance' => 'Room Maintenance Alert',
            'check_out' => 'Room Check-out Alert',
            default => 'Housekeeping Alert'
        };

        $message = (new MailMessage)
            ->subject($subject . ' - Room ' . $this->room->room_number)
            ->greeting('Housekeeping Alert');

        if ($this->alertType === 'cleaning') {
            $message->line('Room ' . $this->room->room_number . ' requires cleaning.')
                ->line('Room Type: ' . ($this->room->roomType->name ?? 'Standard'))
                ->line('Status: ' . ucfirst($this->room->status));
        } elseif ($this->alertType === 'maintenance') {
            $message->line('Room ' . $this->room->room_number . ' requires maintenance attention.')
                ->line('Please schedule maintenance as soon as possible.');
        } else {
            $message->line('Room ' . $this->room->room_number . ' has been checked out.')
                ->line('Please prepare the room for the next guest.');
        }

        return $message->action('View Room', route('admin.rooms.show', $this->room));
    }

    // Uncomment when SMS/Vonage is configured
    // public function toVonage(object $notifiable): VonageMessage
    // {
    //     $content = match($this->alertType) {
    //         'cleaning' => "Room {$this->room->room_number} requires cleaning.",
    //         'maintenance' => "Room {$this->room->room_number} requires maintenance.",
    //         'check_out' => "Room {$this->room->room_number} checked out. Please prepare.",
    //         default => "Housekeeping alert for Room {$this->room->room_number}."
    //     };
    //
    //     return (new VonageMessage)->content($content);
    // }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'housekeeping_alert',
            'title' => ucfirst($this->alertType) . ' Alert',
            'message' => 'Room ' . $this->room->room_number . ' - ' . ucfirst($this->alertType) . ' required',
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'alert_type' => $this->alertType,
        ];
    }
}
