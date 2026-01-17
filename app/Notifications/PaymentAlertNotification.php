<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\VonageMessage; // Uncomment when SMS is configured

class PaymentAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public string $alertType = 'completed' // completed, pending, failed
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
            'completed' => 'Payment Received',
            'pending' => 'Payment Pending',
            'failed' => 'Payment Failed',
            default => 'Payment Alert'
        };

        $message = (new MailMessage)
            ->subject($subject . ' - ' . config('app.name'))
            ->greeting('Hello!');

        if ($this->alertType === 'completed') {
            $message->line('Your payment of $' . number_format($this->payment->amount, 2) . ' has been received successfully.')
                ->line('Payment Method: ' . ucfirst($this->payment->payment_method))
                ->line('Transaction ID: ' . ($this->payment->transaction_id ?? 'N/A'));
        } elseif ($this->alertType === 'pending') {
            $message->line('You have a pending payment of $' . number_format($this->payment->amount, 2) . '.')
                ->action('Complete Payment', route('payment.checkout', $this->payment));
        } else {
            $message->line('Your payment of $' . number_format($this->payment->amount, 2) . ' has failed.')
                ->line('Please contact us or try again.');
        }

        return $message;
    }

    // Uncomment when SMS/Vonage is configured
    // public function toVonage(object $notifiable): VonageMessage
    // {
    //     $content = match($this->alertType) {
    //         'completed' => "Payment of $" . number_format($this->payment->amount, 2) . " received successfully.",
    //         'pending' => "Payment of $" . number_format($this->payment->amount, 2) . " is pending. Please complete payment.",
    //         'failed' => "Payment of $" . number_format($this->payment->amount, 2) . " failed. Please contact us.",
    //         default => "Payment alert: $" . number_format($this->payment->amount, 2)
    //     };
    //
    //     return (new VonageMessage)->content($content);
    // }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_alert',
            'title' => ucfirst($this->alertType) . ' Payment',
            'message' => 'Payment of $' . number_format($this->payment->amount, 2) . ' is ' . $this->alertType,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'status' => $this->payment->status,
            'alert_type' => $this->alertType,
        ];
    }
}
