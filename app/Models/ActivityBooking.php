<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'scheduled_time',
        'participants',
        'total_price',
        'status',
        'notes',
        'special_requirements'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'participants' => 'integer',
        'total_price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_time', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_time', '>', now());
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getFormattedScheduledTime()
    {
        return $this->scheduled_time->format('M d, Y H:i');
    }

    public function isToday()
    {
        return $this->scheduled_time->isToday();
    }

    public function isPast()
    {
        return $this->scheduled_time->isPast();
    }

    public function isFuture()
    {
        return $this->scheduled_time->isFuture();
    }
}
