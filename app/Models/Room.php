<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 'room_type_id', 'floor_number', 'ward_name',
        'status', 'bed_count', 'current_occupancy', 
        'additional_amenities', 'notes', 'is_active'
    ];

    protected $casts = [
        'additional_amenities' => 'array'
    ];

    // Relationship with room type
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Check if room is available
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available' && $this->current_occupancy < $this->bed_count;
    }

    // Get all amenities (room type + room specific)
    public function getAllAmenitiesAttribute()
    {
        $baseAmenities = $this->roomType->amenities ?? [];
        $additional = $this->additional_amenities ?? [];
        return array_merge($baseAmenities, $additional);
    }
}