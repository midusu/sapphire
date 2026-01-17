<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Mail\BookingConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\VonageMessage; // Uncomment when SMS is configured

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = [\App\Broadcasting\CustomDatabaseChannel::class];
        
        // Add email if user has email
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        // Add SMS if user has phone and SMS is enabled
        // Note: SMS requires Vonage/Twilio package installation
        // if ($notifiable->phone && config('services.sms.enabled', false)) {
        //     $channels[] = 'vonage';
        // }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $guestName = $this->booking->guest_name ?? $notifiable->name;
        $roomNumber = $this->booking->room->room_number ?? 'TBD';
        $roomType = $this->booking->room->roomType->name ?? 'Standard';
        
        return (new MailMessage)
            ->subject('Booking Confirmation - ' . config('app.name'))
            ->greeting('Hello ' . $guestName . '!')
            ->line('Your booking has been confirmed!')
            ->line('**Booking Details:**')
            ->line('Room: ' . $roomNumber . ' (' . $roomType . ')')
            ->line('Check-in: ' . $this->booking->check_in_date->format('F j, Y'))
            ->line('Check-out: ' . $this->booking->check_out_date->format('F j, Y'))
            ->line('Total Amount: $' . number_format($this->booking->total_amount, 2))
            ->line('Duration: ' . $this->booking->duration . ' night(s)')
            ->action('View Booking', route('admin.bookings.show', $this->booking))
            ->line('Thank you for choosing us! We look forward to hosting you.');
    }

    /**
     * Get the SMS representation of the notification.
     * Uncomment when SMS/Vonage is configured
     */
    // public function toVonage(object $notifiable): VonageMessage
    // {
    //     $roomNumber = $this->booking->room->room_number ?? 'TBD';
    //     $checkIn = $this->booking->check_in_date->format('M j, Y');
    //     
    //     return (new VonageMessage)
    //         ->content("Your booking is confirmed! Room: {$roomNumber}, Check-in: {$checkIn}. Thank you!");
    // }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmation',
            'title' => 'Booking Confirmed',
            'message' => 'Your booking for ' . ($this->booking->room->room_number ?? 'room') . ' has been confirmed.',
            'booking_id' => $this->booking->id,
            'check_in_date' => $this->booking->check_in_date->format('Y-m-d'),
            'check_out_date' => $this->booking->check_out_date->format('Y-m-d'),
            'room_number' => $this->booking->room->room_number ?? null,
        ];
    }
}
