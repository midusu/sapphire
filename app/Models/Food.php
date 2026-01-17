<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'available',
        'image_url',
        'preparation_time',
        'preparation_time',
        'menu_type',
        'inventory_item_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available' => 'boolean',
        'preparation_time' => 'integer',
        'menu_type' => 'string',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function getMenuTypeLabel()
    {
        return match ($this->menu_type) {
            'room_service' => 'Room Service',
            'restaurant' => 'Restaurant',
            default => 'Both'
        };
    }

    public function scopeForMenuType($query, $menuType)
    {
        return $query->where('menu_type', $menuType)
            ->orWhere('menu_type', 'both');
    }
}
