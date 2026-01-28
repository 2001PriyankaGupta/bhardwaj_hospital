<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
        'last_occupancy_date',
        'next_availability_date',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'last_occupancy_date' => 'date',
        'next_availability_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Room relationship
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}