<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'status',
        'notes'
    ];

    protected $casts = [
        'floor' => 'integer'
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function currentBooking()
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeCleaning($query)
    {
        return $query->where('status', 'cleaning');
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isOccupied()
    {
        return $this->status === 'occupied';
    }

    public function isUnderMaintenance()
    {
        return $this->status === 'maintenance';
    }

    public function needsCleaning()
    {
        return $this->status === 'cleaning';
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status]);
        
        // Log the status change
        // Log the status change
        \Illuminate\Support\Facades\Log::info('Room status changed', [
            'room_id' => $this->id,
            'room_number' => $this->room_number,
            'old_status' => $this->getOriginal('status'),
            'new_status' => $status,
            'user_id' => auth()->id()
        ]);
    }
}
