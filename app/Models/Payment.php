<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'activity_booking_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'notes',
        'coupon_code',
        'discount_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function user()
    {
        if ($this->booking) {
            return $this->booking->user;
        } elseif ($this->activityBooking) {
            return $this->activityBooking->user;
        }
        return null;
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function activityBooking()
    {
        return $this->belongsTo(ActivityBooking::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function hasFailed()
    {
        return $this->status === 'failed';
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed', 'payment_date' => now()]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
}
