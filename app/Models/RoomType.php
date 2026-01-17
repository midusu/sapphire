<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'max_occupancy',
        'amenities',
        'image_url',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
