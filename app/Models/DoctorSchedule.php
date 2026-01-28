<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time', 'end_time', 
        'slot_duration', 'max_patients', 'is_available'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getTimeSlotsAttribute()
    {
        $slots = [];
        $current = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        while ($current < $end) {
            $slotEnd = $current->copy()->addMinutes($this->slot_duration);
            if ($slotEnd <= $end) {
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'display' => $current->format('h:i A') . ' - ' . $slotEnd->format('h:i A')
                ];
            }
            $current->addMinutes($this->slot_duration);
        }

        return $slots;
    }
}