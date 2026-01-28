<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateSlot extends Model
{
    protected $fillable = [
        'doctor_id',
        'slot_date',
        'start_time',
        'end_time',
        'slot_duration',
        'max_patients',
        'booked_slots',
        'is_available',
        'time_slots'
    ];

    protected $casts = [
        'slot_date' => 'date',
        'time_slots' => 'array',
        'is_available' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'slot_id');
    }

    /**
     * Check if slot has available capacity
     */
    public function hasAvailableSlots()
    {
        return $this->is_available && $this->booked_slots < $this->max_patients;
    }

    /**
     * Get available slots count
     */
    public function getAvailableCountAttribute()
    {
        return max(0, $this->max_patients - $this->booked_slots);
    }

    /**
     * Format date for display
     */
    public function getFormattedDateAttribute()
    {
        return $this->slot_date->format('D, M d, Y');
    }
}