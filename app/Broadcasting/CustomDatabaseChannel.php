<?php

namespace App\Broadcasting;

use App\Models\Notification;
use Illuminate\Notifications\Notification as LaravelNotification;

class CustomDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, LaravelNotification $notification)
    {
        $data = $notification->toArray($notifiable);
        
        // Extract title and message from the data array
        $title = $data['title'] ?? 'Notification';
        $message = $data['message'] ?? '';
        $type = $data['type'] ?? 'general';
        
        // Get the notifiable model (booking, payment, etc.)
        $notifiableModel = null;
        $notifiableType = null;
        $notifiableId = null;
        
        // Try to get the notifiable model from the notification
        if (property_exists($notification, 'booking')) {
            $notifiableModel = $notification->booking;
            $notifiableType = \App\Models\Booking::class;
            $notifiableId = $notification->booking->id;
        } elseif (property_exists($notification, 'payment')) {
            $notifiableModel = $notification->payment;
            $notifiableType = \App\Models\Payment::class;
            $notifiableId = $notification->payment->id;
        } elseif (property_exists($notification, 'activityBooking')) {
            $notifiableModel = $notification->activityBooking;
            $notifiableType = \App\Models\ActivityBooking::class;
            $notifiableId = $notification->activityBooking->id;
        } elseif (property_exists($notification, 'room')) {
            $notifiableModel = $notification->room;
            $notifiableType = \App\Models\Room::class;
            $notifiableId = $notification->room->id;
        } elseif (property_exists($notification, 'inventoryItem')) {
            $notifiableModel = $notification->inventoryItem;
            $notifiableType = \App\Models\InventoryItem::class;
            $notifiableId = $notification->inventoryItem->id;
        }
        
        Notification::create([
            'user_id' => $notifiable->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
            'data' => $data,
        ]);
    }
}
