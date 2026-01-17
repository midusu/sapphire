<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'duration_minutes',
        'price',
        'max_participants',
        'location',
        'requirements',
        'status',
        'is_active'
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
        'is_active' => 'boolean'
    ];

    public function bookings()
    {
        return $this->hasMany(ActivityBooking::class);
    }

    public function todayBookings()
    {
        return $this->bookings()->whereDate('scheduled_time', today());
    }

    public function upcomingBookings()
    {
        return $this->bookings()->where('scheduled_time', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getAvailableSlotsCount($scheduledTime)
    {
        $bookedCount = $this->bookings()
            ->where('scheduled_time', $scheduledTime)
            ->where('status', 'confirmed')
            ->sum('participants');
        
        return $this->max_participants - $bookedCount;
    }

    public function getTotalRevenue()
    {
        return $this->bookings()
            ->where('status', 'completed')
            ->sum('total_price');
    }
}
