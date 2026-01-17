<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'total_amount',
        'status',
        'special_requests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'coupon_id'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'checked_out');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getDurationAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getAmountPerNightAttribute()
    {
        $duration = $this->duration;
        return $duration > 0 ? $this->total_amount / $duration : 0;
    }
}
