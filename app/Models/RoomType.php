<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'base_price', 'hourly_rate', 
        'seasonal_pricing', 'discounts', 'amenities', 
        'max_capacity', 'current_utilization', 'available_rooms',
        'capacity_forecast', 'is_active'
    ];

    protected $casts = [
        'seasonal_pricing' => 'array',
        'discounts' => 'array',
        'amenities' => 'array',
        'capacity_forecast' => 'array',
        'base_price' => 'decimal:2',
        'hourly_rate' => 'decimal:2'
    ];

    // Relationship with rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Calculate available capacity
    public function getAvailableCapacityAttribute()
    {
        return $this->max_capacity - $this->current_utilization;
    }

    // Get current price with seasonal adjustment
    public function getCurrentPriceAttribute()
    {
        $seasonalMultiplier = 1.0;
        if ($this->seasonal_pricing) {
            // Add logic to determine current season multiplier
            $seasonalMultiplier = $this->seasonal_pricing['standard'] ?? 1.0;
        }
        return $this->base_price * $seasonalMultiplier;
    }
}